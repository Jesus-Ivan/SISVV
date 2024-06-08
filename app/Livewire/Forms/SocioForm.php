<?php

namespace App\Livewire\Forms;

use App\Models\IntegrantesSocio;
use App\Models\Membresias;
use App\Models\Socio;
use App\Models\SocioMembresia;
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
    public $clave_membresia;
    public $estado_membresia;

    // -- Informacion de los nuevos integrantes -- //
    public $nombre_integrante;
    public $apellido_p_integrante;
    public $apellido_m_integrante;
    public $img_path_integrante;
    public $fecha_nac;
    public $parentesco;
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

    // Propiedad auxiliar para eliminar un integrante
    public $integrante_eliminar;
    //Propiedad axiliar para definir el bloqueo de registro de integrantes
    public $registro_permitido = false;

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
        'clave_membresia' => 'max:10',
    ];

    //Setear los valores a editar
    public function setSocio(Socio $socio)
    {
        //Guardamos el objeto del socio
        $this->socio = $socio;
        $resultMembresia = SocioMembresia::where('id_socio', $socio->id)->get()[0];

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
        $this->clave_membresia = $resultMembresia->clave_membresia;
        $this->estado_membresia = $resultMembresia->estado;

        //Comprobamos el tipo de membresia para bloquear campos de integrantes
        $this->comprobar($resultMembresia->clave_membresia);
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
            'editando_img_path_integrante' => "max:255"
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

        //Agregamos la regla de la imagen, Validamos las entradas
        $this->socio_rules['img_path'] = 'image|max:255';
        $validated = $this->validate($this->socio_rules);

        //Iniciamos transaccion
        DB::transaction(function () use ($validated) {
            //Guardamos la imagen y obtenemos la ruta relativa
            if ($this->img_path) {
                $validated['img_path'] = $this->img_path->store('fotos', 'public');
            }
            //Retiramos la clave de la membresia, antes de crear el socio
            unset($validated['clave_membresia']);
            //Creamos el socio
            $socio = Socio::create($validated);
            //Creamos la relacion del socio-membresia
            SocioMembresia::create([
                'id_socio' => $socio->id,
                'clave_membresia' => $this->clave_membresia,
            ]);

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
            'img_path_integrante' => "max:255"
        ]);
        //Agregamos marca de tiempo como id temporal
        $validated['temp'] =  time();
        //Agregramos al array de miembros
        array_push($this->integrantes, $validated);

        //Limpiamos los campos 
        $this->reset('nombre_integrante', 'apellido_p_integrante', 'apellido_m_integrante', 'img_path_integrante', 'fecha_nacimiento', 'parentesco');
    }
    //Elimina un miembro de memoria (utilizado en la vista socios-nuevo.blade.php)
    public function quitarMiembro($temp)
    {
        //Quitamos el miembro correspondiente al $temp
        $this->integrantes = array_filter($this->integrantes, function ($miembro) use ($temp) {
            return $miembro['temp'] != $temp;
        });
    }

    //Esta funcion actualiza la informacion del socio
    public function update()
    {
        //Agregamos la regla del estado de la membresia
        $this->socio_rules['estado_membresia'] = 'required';
        //Validamos las entradas (sin la imagen)
        $validated = $this->validate($this->socio_rules);

        //Si se sube una nueva imagen
        if ($this->img_path) {
            //Guardamos la imagen y obtenemos la ruta relativa
            $validated['img_path'] = $this->img_path->store('fotos', 'public');
            //Comprobamos si existia ruta registrada en la DB, de la imagen, para eliminarla
            if($this->socio->img_path){
                //Eliminamos la imagen anterior
                Storage::disk('public')->delete($this->socio->img_path);
            }
        } else {
            //De lo contrario, conservamos la ruta anterior de la imagen
            $validated['img_path'] = $this->socio->img_path;
        }
        //Actualizamos su membresia
        SocioMembresia::where('id_socio', $this->socio->id)
            ->update([
                'clave_membresia' => $validated['clave_membresia'],
                'estado' => $validated['estado_membresia'],
            ]);
        //Retiramos la clave de la membresia, antes de ACTUALIZAR el socio
        unset($validated['clave_membresia'], $validated['estado_membresia']);
        //Actualizamos el socio.
        $this->socio->update($validated);
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
            'img_path_integrante' => "max:255"
        ]);
        //Creamos el integrante
        $this->crearIntegranteBD($validated, $this->socio->id);
        //Buscamos los nuevos integrantes del socio
        $this->setIntegrantes($this->socio);
        $this->reset('nombre_integrante', 'apellido_p_integrante', 'apellido_m_integrante', 'fecha_nac', 'parentesco', 'img_path_integrante');
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
            'img_path_integrante' => $ruta
        ]);
    }

    //Se ejecuta para saber el nuevo tipo de membresia seleccionada
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
}
