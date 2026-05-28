<?php

namespace App\Livewire\Forms;

use App\Models\Cuota;
use App\Models\IntegrantesSocio;
use App\Models\Membresias;
use App\Models\Socio;
use App\Models\SocioCuota;
use App\Models\SocioMembresia;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Form;

class SocioForm extends Form
{
    public $nombre;
    public $apellido_p;
    public $apellido_m;
    public $img_path;
    public $fecha_registro;
    public $estado_civil;
    public $calle;
    public $num_exterior;
    public $num_interior;
    public $colonia;
    public $ciudad;
    public $estado;
    public $codigo_postal;
    public $tel_1;
    public $tel_2;
    public $correo1;
    public $correo2;
    public $correo3;
    public $correo4;
    public $correo5;
    public $curp;
    public $rfc;
    public $clave_membresia;            //Se conserva para el flujo legacy (no se usa en la nueva UI)
    public $claves_membresia = [];      //Array de claves para registro/edicion multi-membresia (checkboxes)
    public $estados_membresia = [];     //Mapa clave_membresia => estado (MEN, INA, ANU) — usado en edicion
    public $estado_membresia;           //Se conserva para compatibilidad con codigo legacy

    // -- Informacion de los nuevos integrantes -- //
    public $nombre_integrante;
    public $apellido_p_integrante;
    public $apellido_m_integrante;
    public $img_path_integrante;
    public $fecha_nac;
    public $parentesco;
    public $tel_integrante;
    public $integrantes = [];       //Integrantes temporales (utilizado en para crearlos en el registro)

    public $integrantes_BD = [];    //Integrantes que ya estan registrados

    //Parametro opcional, que proviene del componente livewire 'editar-socio.blade.php'
    public ?Socio $socio;

    //--Propiedades axuiliares cuando el usuario hace click para editar un miembro/socio--//
    public $editando_miembro_id;
    public $editando_nombre_integrante;
    public $editando_apellido_p_integrante;
    public $editando_apellido_m_integrante;
    public $editando_fecha_nac;
    public $editando_parentesco;
    public $editando_img_path_integrante;
    public $editando_tel_integrante;

    // Propiedad auxiliar para eliminar un integrante
    public $integrante_eliminar;
    //Propiedad axiliar para definir el bloqueo de registro de integrantes
    public $registro_permitido = false;

    protected $messages = [
        'clave_membresia.required' => 'Selecciona al menos una membresía.',
        'claves_membresia.required' => 'Selecciona al menos una membresía.',
        'claves_membresia.array' => 'Formato inválido de membresías.',
        'claves_membresia.min' => 'Selecciona al menos una membresía.',
        'claves_membresia.*.distinct' => 'No puedes seleccionar la misma membresía dos veces.',
    ];

    protected $socio_rules = [
        'nombre' => 'required|min:3|max:255',
        'apellido_p' => 'required|min:3|max:100',
        'apellido_m' => 'required|min:3|max:100',
        'fecha_registro' => 'max:20',
        'estado_civil' => 'max:20',
        'calle' => 'max:255',
        'num_exterior' => 'max:20',
        'num_interior' => 'max:20',
        'colonia' => 'max:100',
        'ciudad' => 'max:100',
        'estado' => 'max:100',
        'codigo_postal' => 'max:30',
        'tel_1' => 'max:10',
        'tel_2' => 'max:10',
        'correo1' => 'max:50',
        'correo2' => 'max:50',
        'curp' => 'max:18',
        'rfc' => 'max:13',
    ];

    //Setear los valores a editar
    public function setSocio(Socio $socio)
    {
        $this->socio = $socio;

        $this->nombre = $socio->nombre;
        $this->apellido_p = $socio->apellido_p;
        $this->apellido_m = $socio->apellido_m;
        $this->img_path;
        $this->fecha_registro = $socio->fecha_registro;
        $this->estado_civil = $socio->estado_civil;
        $this->calle = $socio->calle;
        $this->num_exterior = $socio->num_exterior;
        $this->num_interior = $socio->num_interior;
        $this->colonia = $socio->colonia;
        $this->ciudad = $socio->ciudad;
        $this->estado = $socio->estado;
        $this->codigo_postal = $socio->codigo_postal;
        $this->tel_1 = $socio->tel_1;
        $this->tel_2 = $socio->tel_2;
        $this->correo1 = $socio->correo1;
        $this->correo2 = $socio->correo2;
        $this->curp = $socio->curp;
        $this->rfc = $socio->rfc;

        //Membresía principal desde socios_membresias
        $principal = $socio->socioMembresia;
        $this->clave_membresia = $principal?->clave_membresia;
        $this->estado_membresia = $principal?->estado;

        //Membresías adicionales desde socios_cuotas (estado derivado de cuota->tipo)
        $adicionales = $socio->cuotasMembresia()->with('cuota')->get();

        //Unimos principal + adicionales para los checkboxes
        $this->claves_membresia = collect()
            ->when($principal, fn($c) => $c->push($principal->clave_membresia))
            ->merge($adicionales->pluck('cuota.clave_membresia')->filter())
            ->unique()
            ->values()
            ->toArray();

        //Mapa clave => estado: principal usa socios_membresias.estado, adicionales usan cuota->tipo
        $this->estados_membresia = [];
        if ($principal) {
            $this->estados_membresia[$principal->clave_membresia] = $principal->estado;
        }
        foreach ($adicionales as $sc) {
            $clave = $sc->cuota->clave_membresia;
            if ($clave) {
                $this->estados_membresia[$clave] = $sc->cuota->tipo;
            }
        }

        $this->comprobarMultiples();
    }
    public function setIntegrantes(Socio $socio)
    {
        //Buscamos los integrantes del socio
        $this->integrantes_BD = IntegrantesSocio::where('id_socio', $socio->id)->get();
    }

    public function editMiembro(array $miembro)
    {
        /**
         * Clonamos los atributos del miembro para el modo de edicion
         */
        $this->editando_miembro_id = $miembro['id'];
        $this->editando_nombre_integrante = $miembro['nombre_integrante'];
        $this->editando_apellido_p_integrante = $miembro['apellido_p_integrante'];
        $this->editando_apellido_m_integrante = $miembro['apellido_m_integrante'];
        $this->editando_fecha_nac = $miembro['fecha_nac'];
        $this->editando_parentesco = $miembro['parentesco'];
        $this->editando_tel_integrante = $miembro['tel_integrante'];
    }
    public function selectMiembro(array $miembro)
    {
        //Setear el miembro a eliminar, para el dialog
        $this->integrante_eliminar = $miembro;
    }

    public function cleanEdit()
    {
        $this->reset(
            'editando_miembro_id',
            'editando_nombre_integrante',
            'editando_apellido_p_integrante',
            'editando_apellido_m_integrante',
            'editando_fecha_nac',
            'editando_parentesco',
            'editando_img_path_integrante'
        );
    }

    public function confirmEdit(int $index)
    {
        //Obtenemos la instacia a editar, con base al indice que ocupa en en array.
        $miembro = $this->integrantes_BD[$index];

        //Validamos la informacion que se modifico
        $validated = $this->validate([
            'editando_nombre_integrante' => "required|max:30",
            'editando_apellido_p_integrante' => "required|max:30",
            'editando_apellido_m_integrante' => "required|max:30",
            'editando_fecha_nac' => "max:10",
            'editando_parentesco' => "required|max:20",
            'editando_img_path_integrante' => "max:255",
            'editando_tel_integrante' => "max:10",
        ]);
        //verificamos si existe una imagen cargada en la edicion.
        if ($this->editando_img_path_integrante) {
            //Guardamos la imagen en el servidor y recuperamos su ruta relativa
            $validated['editando_img_path_integrante'] = $this->editando_img_path_integrante->store('fotos/integrantes', 'public');
            //Si existe una anterior, la borramos del servidor 
            if ($miembro->img_path_integrante)
                Storage::disk('public')->delete($miembro->img_path_integrante);
        } else {
            //Dejamos la ruta original de la imagen si no hay una nueva cargada
            $validated['editando_img_path_integrante'] = $miembro->img_path_integrante;
        }
        $miembro->update([
            'nombre_integrante' => $validated['editando_nombre_integrante'],
            'apellido_p_integrante' => $validated['editando_apellido_p_integrante'],
            'apellido_m_integrante' => $validated['editando_apellido_m_integrante'],
            'fecha_nac' => $validated['editando_fecha_nac'],
            'parentesco' => $validated['editando_parentesco'],
            'img_path_integrante' => $validated['editando_img_path_integrante'],
            'tel_integrante' => $validated['editando_tel_integrante'],
        ]);
        $this->cleanEdit();
    }

    public function confirmDelete()
    {
        //Si existe imagen del miembro, la borramos del servidor
        if ($this->integrante_eliminar['img_path_integrante']) {
            Storage::disk('public')->delete($this->integrante_eliminar['img_path_integrante']);
        }
        //Eliminamos el registro del miembro
        IntegrantesSocio::destroy($this->integrante_eliminar['id']);
        //Buscamos los nuevos integrantes del socio
        $this->setIntegrantes($this->socio);
        //Limipamos el integrante
        $this->reset('integrante_eliminar');
    }

    //se confirma la actualizacion de los datos del socio, incluyendo el cambio de membresia
    public function confirmUpdate()
    {
        //Buscamos todos los integrantes de la membresia
        $integrantes = IntegrantesSocio::where('id_socio', $this->socio->id)->get();

        DB::transaction(function () use ($integrantes) {
            //Actualizamos la informacion del socio
            $this->update();
            //Recorremos todos los integrantes
            foreach ($integrantes as $integrante) {
                //Si existe imagen del miembro, la borramos del servidor
                if ($integrante->img_path_integrante) {
                    Storage::disk('public')->delete($integrante->img_path_integrante);
                }
                //Eliminamos el registro del miembro
                IntegrantesSocio::destroy($integrante->id);
                //Buscamos los nuevos integrantes del socio
                $this->setIntegrantes($this->socio);
            }
        });
    }

    //Finalizar registro del socio y guardar toda la informacion, junto con los integrantes
    public function store()
    {
        //Agregamos la fecha del dia actual del registro
        $this->fecha_registro = date('Y-m-d');

        //Reglas del socio sin la clave unica (registro nuevo usa array de membresias)
        $reglas = $this->socio_rules;
        unset($reglas['clave_membresia']);
        $reglas['img_path'] = 'image|max:255';
        //Reglas exclusivas del registro multi-membresia (RF 2.1 / RF 2.6)
        $reglas['claves_membresia'] = 'required|array|min:1';
        $reglas['claves_membresia.*'] = 'string|max:10|distinct';

        $validated = $this->validate($reglas);

        //Iniciamos transaccion
        DB::transaction(function () use ($validated) {
            //Guardamos la imagen y obtenemos la ruta relativa
            if ($this->img_path) {
                $validated['img_path'] = $this->img_path->store('fotos', 'public');
            }
            //Retiramos los campos que no pertenecen al modelo Socio
            unset($validated['clave_membresia'], $validated['claves_membresia']);
            //Creamos el socio
            $socio = Socio::create($validated);

            //Obtenemos las cuotas MEN del catalogo para cada membresia seleccionada
            $cuotasPorClave = collect($this->claves_membresia)
                ->map(fn($clave) => Cuota::where('clave_membresia', $clave)->where('tipo', 'MEN')->first())
                ->filter()
                ->sortByDesc('monto');

            //Principal = la de mayor monto → va a socios_membresias
            $cuotaPrincipal = $cuotasPorClave->first();
            if ($cuotaPrincipal) {
                SocioMembresia::create([
                    'id_socio' => $socio->id,
                    'clave_membresia' => $cuotaPrincipal->clave_membresia,
                    'estado' => 'MEN',
                ]);
            }

            //Adicionales → van a socios_cuotas (sin campo estado)
            foreach ($cuotasPorClave->skip(1) as $cuota) {
                SocioCuota::create([
                    'id_socio' => $socio->id,
                    'id_cuota' => $cuota->id,
                    'auto_delete' => true,
                ]);
            }

            //Creamos cada uno de los miembros del socio
            foreach ($this->integrantes as $integrante) {
                $this->crearIntegranteBD($integrante, $socio->id);
            }
            //Limpiamos la pagina
            $this->reset();
        });
    }

    //Crea un miembro de forma temporal(utilizado en la vista socios-nuevo.blade.php)
    public function crearMiembro()
    {
        //Validamos las entradas
        $validated = $this->validate([
            'nombre_integrante' => "required|max:30",
            'apellido_p_integrante' => "required|max:30",
            'apellido_m_integrante' => "required|max:30",
            'fecha_nac' => "max:10",
            'parentesco' => "required|max:20",
            'img_path_integrante' => "max:255",
            'tel_integrante' => "max:10"
        ]);
        //Agregamos marca de tiempo como id temporal
        $validated['temp'] =  time();
        //Agregramos al array de miembros
        array_push($this->integrantes, $validated);

        //Limpiamos los campos 
        $this->reset('nombre_integrante', 'apellido_p_integrante', 'apellido_m_integrante', 'img_path_integrante', 'fecha_nacimiento', 'parentesco', 'tel_integrante');
    }
    //Elimina un miembro de memoria (utilizado en la vista socios-nuevo.blade.php)
    public function quitarMiembro($temp)
    {
        //Quitamos el miembro correspondiente al $temp
        $this->integrantes = array_filter($this->integrantes, function ($miembro) use ($temp) {
            return $miembro['temp'] != $temp;
        });
    }

    //Actualiza la informacion del socio aplicando el diff de dropdowns de estado
    public function update()
    {
        $reglas = $this->socio_rules;
        unset($reglas['clave_membresia']);
        $reglas['claves_membresia'] = 'required|array|min:1';
        $reglas['claves_membresia.*'] = 'string|max:10|distinct';
        $reglas['estados_membresia'] = 'array';
        $reglas['estados_membresia.*'] = 'in:MEN,INA,ANU,CAN';

        $validated = $this->validate($reglas);

        if ($this->img_path) {
            $validated['img_path'] = $this->img_path->store('fotos', 'public');
            if ($this->socio->img_path) {
                Storage::disk('public')->delete($this->socio->img_path);
            }
        } else {
            $validated['img_path'] = $this->socio->img_path;
        }

        DB::transaction(function () use ($validated) {
            $idSocio = $this->socio->id;

            $principalActual = SocioMembresia::where('id_socio', $idSocio)->first();
            $claveAnteriorPrincipal = $principalActual?->clave_membresia;

            $adicionalesActuales = SocioCuota::where('id_socio', $idSocio)
                ->whereIn('id_cuota', Cuota::whereNotNull('clave_membresia')->pluck('id'))
                ->with('cuota')
                ->get()
                ->keyBy(fn($sc) => $sc->cuota->clave_membresia);

            //Todos los candidatos ordenados por monto (para determinar la principal por valor)
            $todosLosCandidatos = collect($this->claves_membresia)
                ->filter()
                ->map(fn($clave) => [
                    'clave'    => $clave,
                    'estado'   => $this->estados_membresia[$clave] ?? 'MEN',
                    'cuotaMEN' => Cuota::where('clave_membresia', $clave)->where('tipo', 'MEN')->first(),
                ])
                ->filter(fn($item) => $item['cuotaMEN'] !== null)
                ->sortByDesc(fn($item) => $item['cuotaMEN']->monto)
                ->values();

            //Separar activos (MEN/INA/ANU) de cancelados (CAN)
            $candidatosActivos = $todosLosCandidatos->filter(fn($item) => $item['estado'] !== 'CAN')->values();

            //--- Caso cancelación total: todos en CAN ---
            if ($candidatosActivos->isEmpty() && $todosLosCandidatos->isNotEmpty()) {
                //La de mayor monto queda como CAN en socios_membresias para dejar rastro
                $mayorValor = $todosLosCandidatos->first();
                SocioMembresia::updateOrCreate(
                    ['id_socio' => $idSocio],
                    ['clave_membresia' => $mayorValor['clave'], 'estado' => 'CAN']
                );
                //Eliminar todas las adicionales de socios_cuotas
                SocioCuota::where('id_socio', $idSocio)
                    ->whereIn('id_cuota', Cuota::whereNotNull('clave_membresia')->pluck('id'))
                    ->delete();
                unset($validated['clave_membresia'], $validated['claves_membresia'], $validated['estado_membresia'], $validated['estados_membresia']);
                $this->socio->update($validated);
                return;
            }

            //--- Caso normal: al menos una activa; las CAN se tratan como borradas ---
            $nuevaPrincipal    = $candidatosActivos->first();
            $nuevosAdicionales = $candidatosActivos->skip(1);
            $claveNuevaPrincipal = $nuevaPrincipal['clave'] ?? null;
            //Solo las claves activas cuentan como "seleccionadas" para el diff
            $clavesMarcadas = $candidatosActivos->pluck('clave')->toArray();

            //--- 1. Actualizar socios_membresias con la nueva principal ---
            if ($nuevaPrincipal) {
                $estadoPrincipal = $nuevaPrincipal['estado'];
                $cuotaDeseada = Cuota::where('clave_membresia', $claveNuevaPrincipal)
                    ->where('tipo', $estadoPrincipal)
                    ->first() ?? $nuevaPrincipal['cuotaMEN'];

                SocioMembresia::updateOrCreate(
                    ['id_socio' => $idSocio],
                    ['clave_membresia' => $claveNuevaPrincipal, 'estado' => $cuotaDeseada->tipo]
                );
            } else {
                SocioMembresia::where('id_socio', $idSocio)->delete();
            }

            //--- 2. Si la principal rotó: la anterior pasa a socios_cuotas (si aún está activa) ---
            //Se guarda la clave movida para evitar que el paso 4 intente insertarla de nuevo (duplicado)
            $claveMovidaAdicional = null;
            if ($claveAnteriorPrincipal && $claveAnteriorPrincipal !== $claveNuevaPrincipal
                && in_array($claveAnteriorPrincipal, $clavesMarcadas, true)
                && !isset($adicionalesActuales[$claveAnteriorPrincipal])
            ) {
                $estadoDeseado = $this->estados_membresia[$claveAnteriorPrincipal] ?? 'MEN';
                $cuotaDeseada  = Cuota::where('clave_membresia', $claveAnteriorPrincipal)
                    ->where('tipo', $estadoDeseado)->first()
                    ?? Cuota::where('clave_membresia', $claveAnteriorPrincipal)->where('tipo', 'MEN')->first();
                if ($cuotaDeseada) {
                    SocioCuota::create(['id_socio' => $idSocio, 'id_cuota' => $cuotaDeseada->id, 'auto_delete' => true]);
                    $claveMovidaAdicional = $claveAnteriorPrincipal;
                }
            }

            //--- 3. Si la nueva principal estaba en socios_cuotas, eliminarla de ahí ---
            if ($claveNuevaPrincipal && isset($adicionalesActuales[$claveNuevaPrincipal])) {
                $adicionalesActuales[$claveNuevaPrincipal]->delete();
                unset($adicionalesActuales[$claveNuevaPrincipal]);
            }

            //--- 4. Actualizar o crear cada membresía adicional activa en socios_cuotas ---
            foreach ($nuevosAdicionales as $item) {
                $clave = $item['clave'];
                //Evitar duplicado: esta clave ya fue insertada en el paso 2 al rotar la principal
                if ($clave === $claveMovidaAdicional) continue;

                $estadoDeseado = $item['estado'];
                $cuotaDeseada  = Cuota::where('clave_membresia', $clave)->where('tipo', $estadoDeseado)->first()
                    ?? Cuota::where('clave_membresia', $clave)->where('tipo', 'MEN')->first();
                if (!$cuotaDeseada) continue;

                if (isset($adicionalesActuales[$clave])) {
                    $adicionalesActuales[$clave]->update(['id_cuota' => $cuotaDeseada->id, 'auto_delete' => true]);
                } else {
                    SocioCuota::create(['id_socio' => $idSocio, 'id_cuota' => $cuotaDeseada->id, 'auto_delete' => true]);
                }
            }

            //--- 5. Eliminar adicionales no activas (incluye las marcadas como CAN) ---
            foreach ($adicionalesActuales as $clave => $sc) {
                if (!in_array($clave, $clavesMarcadas, true)) {
                    $sc->delete();
                }
            }

            unset($validated['clave_membresia'], $validated['claves_membresia'], $validated['estado_membresia'], $validated['estados_membresia']);
            $this->socio->update($validated);
        }, 3);
    }

    //Este metodo sirve para registrar un integrante, hacia un socio existente
    public function registerIntegrante()
    {
        //Validamos las entradas
        $validated = $this->validate([
            'nombre_integrante' => "required|max:50",
            'apellido_p_integrante' => "required|max:50",
            'apellido_m_integrante' => "required|max:50",
            'fecha_nac' => "max:10",
            'parentesco' => "required|max:20",
            'img_path_integrante' => "max:255",
            'tel_integrante' => "max:10",
        ]);
        //Creamos el integrante
        $this->crearIntegranteBD($validated, $this->socio->id);
        //Buscamos los nuevos integrantes del socio
        $this->setIntegrantes($this->socio);
        $this->reset('nombre_integrante', 'apellido_p_integrante', 'apellido_m_integrante', 'fecha_nac', 'parentesco', 'img_path_integrante', 'tel_integrante');
    }

    //Encargada de insertar el registro en la BD y almacenar la imagen
    private function crearIntegranteBD(array $integrante, string $socioId)
    {
        $ruta = null;
        if ($integrante['img_path_integrante'])
            $ruta = $integrante['img_path_integrante']->store('fotos/integrantes', 'public');

        IntegrantesSocio::create([
            'id_socio' => $socioId,
            'nombre_integrante' => $integrante['nombre_integrante'],
            'apellido_p_integrante' => $integrante['apellido_p_integrante'],
            'apellido_m_integrante' => $integrante['apellido_m_integrante'],
            'fecha_nac' => $integrante['fecha_nac'],
            'parentesco' => $integrante['parentesco'],
            'img_path_integrante' => $ruta,
            'tel_integrante' => $integrante['tel_integrante']
        ]);
    }

    //Se ejecuta para saber el nuevo tipo de membresia seleccionada (flujo legacy con un solo dropdown)
    public function comprobar($value)
    {
        //Si el value no es null
        if ($value) {
            //Buscar la membresia
            $membresia = Membresias::find($value);
            //Comprobar si la membresia es individual, sin importar la clave
            if (strpos($membresia->descripcion, "INDIVIDUAL")) {
                //Limpiamos los campos
                $this->reset('nombre_integrante', 'apellido_p_integrante', 'apellido_m_integrante', 'img_path_integrante', 'fecha_nacimiento', 'parentesco', 'integrantes');
                //Deshabilitamos el registro de miembros el formulario
                $this->registro_permitido = false;
            } else {
                $this->registro_permitido = true;
            }
        }
    }

    //Sincroniza claves_membresia basado en estados_membresia, y evalúa si se permite registrar integrantes
    public function comprobarMultiples(): void
    {
        //Construir claves_membresia a partir de estados no vacíos (excepto las canceladas, que se eliminan)
        $this->claves_membresia = array_keys(array_filter(
            $this->estados_membresia,
            fn($estado) => !empty($estado)
        ));

        if (empty($this->claves_membresia)) {
            $this->registro_permitido = false;
            return;
        }

        //Solo las claves activas (no CAN) se consideran para bloquear integrantes
        $clavesActivas = array_keys(array_filter(
            $this->estados_membresia,
            fn($estado) => !empty($estado) && $estado !== 'CAN'
        ));

        if (empty($clavesActivas)) {
            $this->registro_permitido = false;
            return;
        }

        $todasIndividuales = true;
        foreach ($clavesActivas as $clave) {
            $membresia = Membresias::find($clave);
            if ($membresia && strpos($membresia->descripcion, "INDIVIDUAL") === false) {
                $todasIndividuales = false;
                break;
            }
        }

        if ($todasIndividuales) {
            $this->reset('nombre_integrante', 'apellido_p_integrante', 'apellido_m_integrante', 'img_path_integrante', 'fecha_nac', 'parentesco', 'integrantes');
            $this->registro_permitido = false;
        } else {
            $this->registro_permitido = true;
        }
    }
}
