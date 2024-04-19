<?php

namespace App\Livewire\Forms;

use App\Models\IntegrantesSocio;
use App\Models\Socio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Form;

class SocioForm extends Form
{
    public $nombre;
    public $img_path;
    public $fecha_registro;
    public $estado_civil;
    public $calle;
    public $num_exterior;
    public $codigo_postal;
    public $colonia;
    public $ciudad;
    public $estado;
    public $tel_fijo;
    public $tel_celular;
    public $correo;
    public $clave_membresia;

    // -- Informacion de los nuevos integrantes -- //
    public $nombre_integrante;
    public $fecha_nac;
    public $parentesco;
    public $img_path_integrante;
    public $integrantes = [];       //Integrantes temporales (utilizado en para crearlos en el registro)

    public $integrantes_BD = [];    //Integrantes que ya estan registrados

    //Parametro opcional, que proviene del componente livewire 'editar-socio.blade.php'
    public ?Socio $socio;

    //--Propiedades axuiliares cuando el usuario hace click para editar un miembro/socio--//
    public $editando_miembro_id;
    public $editando_nombre_integrante;
    public $editando_fecha_nac;
    public $editando_parentesco;
    public $editando_img_path_integrante;

    protected $socio_rules = [
        'nombre' => 'required|min:5|max:80',
        'fecha_registro' => 'max:10',
        'estado_civil' => 'max:20',
        'calle' => 'max:50',
        'num_exterior' => 'max:5',
        'codigo_postal' => 'max:6',
        'colonia' => 'max:30',
        'ciudad' => 'max:20',
        'estado' => 'max:20',
        'tel_fijo' => 'max:10',
        'tel_celular' => 'max:10',
        'correo' => 'max:50',
        'clave_membresia' => 'required',
    ];

    //setear los valores a editar
    public function setSocio(Socio $socio)
    {
        //Guardamos el objeto del socio
        $this->socio = $socio;

        $this->nombre = $socio->nombre;
        $this->img_path;
        $this->fecha_registro = $socio->fecha_registro;
        $this->estado_civil = $socio->estado_civil;
        $this->calle = $socio->calle;
        $this->num_exterior = $socio->num_exterior;
        $this->codigo_postal = $socio->codigo_postal;
        $this->colonia = $socio->colonia;
        $this->ciudad = $socio->ciudad;
        $this->estado = $socio->estado;
        $this->tel_fijo = $socio->tel_fijo;
        $this->tel_celular = $socio->tel_celular;
        $this->correo = $socio->correo;
        $this->clave_membresia = $socio->clave_membresia;
    }
    public function setIntegrantes(Socio $socio)
    {
        //Buscamos los integrantes del socio
        $this->integrantes_BD = IntegrantesSocio::where('id_socio', $socio->id)->get();
    }

    public function editMiembro(array $miembro)
    {
        $this->editando_miembro_id = $miembro['id'];
        $this->editando_nombre_integrante = $miembro['nombre_integrante'];
        $this->editando_fecha_nac = $miembro['fecha_nac'];
        $this->editando_parentesco = $miembro['parentesco'];
    }

    public function cleanEdit()
    {
        $this->reset(
            'editando_miembro_id',
            'editando_nombre_integrante',
            'editando_fecha_nac',
            'editando_parentesco',
            'editando_img_path_integrante'
        );
    }

    public function confirmEdit(int $index)
    {
        //Obtenemos la instacia a editar, con base al indice que ocupa en en array.
        $miembro = $this->integrantes_BD[$index];

        $validated = $this->validate([
            'editando_nombre_integrante' => "required|max:50",
            'editando_fecha_nac' => "max:10",
            'editando_parentesco' => "required|max:20",
            'editando_img_path_integrante' => "max:255"
        ]);

        if ($this->editando_img_path_integrante) {
            $validated['editando_img_path_integrante'] = $this->editando_img_path_integrante->store('fotos/integrantes', 'public');
            if ($miembro->img_path_integrante)
                Storage::disk('public')->delete($miembro->img_path_integrante);
        } else {
            $validated['editando_img_path_integrante'] = $miembro->img_path_integrante;
        }
        $miembro->update([
            'nombre_integrante'=>$validated['editando_nombre_integrante'],
            'fecha_nac'=>$validated['editando_fecha_nac'],
            'parentesco'=>$validated['editando_parentesco'],
            'img_path_integrante'=>$validated['editando_img_path_integrante'],
        ]);
        $this->cleanEdit();
    }

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
            //Creamos el socio
            $socio = Socio::create($validated);

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
            'nombre_integrante' => "required|max:50",
            'fecha_nac' => "max:10",
            'parentesco' => "required|max:20",
            'img_path_integrante' => "max:255"
        ]);
        //Agregamos marca de tiempo como id temporal
        $validated['temp'] =  time();
        //Agregramos al array de miembros
        array_push($this->integrantes, $validated);

        //Limpiamos los campos 
        $this->reset('nombre_integrante', 'img_path_integrante', 'fecha_nacimiento', 'parentesco');
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
        //Validamos las entradas (sin la imagen)
        $validated = $this->validate($this->socio_rules);

        //Si se sube una nueva imagen
        if ($this->img_path) {
            //Guardamos la imagen y obtenemos la ruta relativa
            $validated['img_path'] = $this->img_path->store('fotos', 'public');
            //Eliminamos la imagen anterior
            Storage::disk('public')->delete($this->socio->img_path);
        } else {
            //De lo contrario, conservamos la ruta anterior de la imagen
            $validated['img_path'] = $this->socio->img_path;
        }
        //Actualizamos el socio.
        $this->socio->update($validated);
    }

    //Este metodo sirve para registrar un integrante, hacia un socio existente
    public function registerIntegrante()
    {
        //Validamos las entradas
        $validated = $this->validate([
            'nombre_integrante' => "required|max:50",
            'fecha_nac' => "max:10",
            'parentesco' => "required|max:20",
            'img_path_integrante' => "max:255"
        ]);
        //Creamos el integrante
        $this->crearIntegranteBD($validated, $this->socio->id);
        //Buscamos los nuevos integrantes del socio
        $this->setIntegrantes($this->socio);
        $this->reset('nombre_integrante', 'fecha_nac', 'parentesco', 'img_path_integrante');
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
            'fecha_nac' => $integrante['fecha_nac'],
            'parentesco' => $integrante['parentesco'],
            'img_path_integrante' => $ruta
        ]);
    }
}
