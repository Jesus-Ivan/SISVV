<?php

namespace App\Livewire\Forms;

use App\Models\IntegrantesSocio;
use App\Models\Socio;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Form;

class NuevoSocio extends Form
{

    #[Validate('required|min:5|max:80')]
    public $nombre;

    #[Validate('image|max:255')]
    public $img_path;

    #[Validate('max:10')]
    public $fecha_registro;

    #[Validate('max:20')]
    public $estado_civil;

    #[Validate('max:50')]
    public $calle;

    #[Validate('max:5')]
    public $num_exterior;

    #[Validate('max:6')]
    public $codigo_postal;

    #[Validate('max:30')]
    public $colonia;

    #[Validate('max:20')]
    public $ciudad;

    #[Validate('max:20')]
    public $estado;

    #[Validate('max:10')]
    public $tel_fijo;

    #[Validate('max:10')]
    public $tel_celular;

    #[Validate('max:50')]
    public $correo;

    #[Validate('required')]
    public $clave_membresia;

    // -- Informacion de los integrantes -- //
    public $nombre_integrante;
    public $fecha_nac;
    public $parentesco;
    public $img_path_integrante;

    public $integrantes = [];

    public function store()
    {
        //Agregamos la fecha del dia actual del registro
        $this->fecha_registro = date('Y-m-d');

        //Validamos las entradas
        $validated = $this->validate();

        //Guardamos la imagen y obtenemos la ruta relativa
        if ($this->img_path) {
            $validated['img_path'] = $this->img_path->store('fotos', 'public');
        }

        //Iniciamos transaccion
        DB::transaction(function () use ($validated) {
            //Creamos el socio
            $socio = Socio::create($validated);

            //Creamos cada uno de los socios
            foreach ($this->integrantes as $integrante) {
                $ruta = null;
                if ($integrante['img_path_integrante'])
                    $ruta = $integrante['img_path_integrante']->store('fotos/integrantes', 'public');

                IntegrantesSocio::create([
                    'id_socio' => $socio->id,
                    'nombre_integrante' => $integrante['nombre_integrante'],
                    'fecha_nac' => $integrante['fecha_nac'],
                    'parentesco' => $integrante['parentesco'],
                    'img_path_integrante' => $ruta
                ]);
            }
            //Limpiamos la pagina
            $this->reset();
        });
        //Al finalizar la transaccion devolvemos true
        return true;
    }

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

    public function quitarMiembro($temp)
    {
        //Quitamos el miembro correspondiente al $temp
        $this->integrantes = array_filter($this->integrantes, function ($miembro) use ($temp) {
            return $miembro['temp'] != $temp;
        });
    }
}
