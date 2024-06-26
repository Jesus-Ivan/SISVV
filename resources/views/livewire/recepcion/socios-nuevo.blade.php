<div>
    <div class="ms-3 mx-3">
        <!-- Registro del socio -->
        <div class="flex gap-2">
            <!-- IMAGEN DEL SOCIO -->
            <div class="w-full">
                @if ($formSocio->img_path)
                    <img class="size-96" src="{{ $formSocio->img_path->temporaryUrl() }}">
                @else
                    <!--Placeholder-->
                    <img class="size-96" src="https://placehold.co/400" alt="image description">
                @endif
                <!--loading state image-->
                <div wire:loading wire:target="formSocio.img_path">
                    <div class="flex gap-3 items-center">
                        @include('livewire.utils.loading', ['w' => 8, 'h' => 8])
                        <span>Cargando imagen...</span>
                    </div>
                </div>
            </div>

            <!-- columna 2 -->
            <div class="w-full">
                <div class="flex gap-2">
                    <!-- APELLIDO PATERNO -->
                    <div class="w-full">
                        <label for="apellido_p"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Apellido
                            Paterno</label>
                        <input type="text" id="apellido_p" wire:model="formSocio.apellido_p"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            required />
                        @error('formSocio.apellido_p')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    <!-- APELLIDO MATERNO -->
                    <div class="w-full">
                        <label for="apellido_m"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Apellido
                            Materno</label>
                        <input type="text" id="apellido_m" wire:model="formSocio.apellido_m"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            required />
                        @error('formSocio.apellido_m')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                </div>
                <!-- NOMBRE -->
                <div>
                    <label for="nombre"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre(s)</label>
                    <input type="text" id="nombre" wire:model="formSocio.nombre"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        required />
                    @error('formSocio.nombre')
                        <x-input-error messages="{{ $message }}" />
                    @enderror
                </div>
                <!-- CALLE -->
                <div>
                    <label for="calle"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Calle</label>
                    <input type="text" id="calle" wire:model="formSocio.calle"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                </div>
                <div class="flex gap-2">
                    <!-- NUMERO EXTERIOR -->
                    <div class="w-full">
                        <label for="num-exterior"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No.
                            Exterior</label>
                        <input type="text" id="num-exterior" wire:model="formSocio.num_exterior"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>
                    <!-- NUMERO INTERIOR -->
                    <div class="w-full">
                        <label for="num-interior"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No.
                            Interior</label>
                        <input type="text" id="num-interior" wire:model="formSocio.num_interior"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>
                    <!-- CODIGO POSTAL -->
                    <div class="w-full">
                        <label for="CP"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Codigo
                            Postal</label>
                        <input type="number" id="CP" wire:model="formSocio.codigo_postal"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>
                </div>
                <!-- COLONIA -->
                <div>
                    <label for="colonia"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Colonia</label>
                    <input type="text" id="colonia" wire:model="formSocio.colonia"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                </div>
                <div class="flex gap-2">
                    <!-- ESTADO -->
                    <div class="w-full">
                        <label for="estado"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estado</label>
                        <input type="text" id="estado" wire:model="formSocio.estado"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Oaxaca" />
                    </div>
                    <!-- CIUDAD -->
                    <div class="w-full">
                        <label for="ciudad"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ciudad</label>
                        <input type="text" id="ciudad" wire:model="formSocio.ciudad"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>
                </div>
            </div>

            <!-- columna 3 -->
            <div class="w-full">
                <!-- ESTADO CIVIL -->
                <div>
                    <label for="estado-civil"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estado civil</label>
                    <input type="text" id="estado-civil" wire:model="formSocio.estado_civil"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Soltero(a)" />
                </div>
                <!-- TELEFONOS -->
                <div class="flex gap-2">
                    <div class="w-full">
                        <label for="tel-1"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Telefono 1</label>
                        <input type="number" id="tel-1" wire:model="formSocio.tel_1"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>
                    <div class="w-full">
                        <label for="tel-celular"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Telefono 2</label>
                        <input type="number" id="tel-celular" wire:model="formSocio.tel_celular"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>
                </div>
                <!-- EMAILS -->
                <div class="flex gap-2">
                    <div class="w-full">
                        <label for="correo"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Correo</label>
                        <input type="email" id="correo" wire:model="formSocio.correo1"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="someone@example.com" />
                    </div>
                    <div class="w-full">
                        <label for="correo2"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Correo
                            Adicional</label>
                        <input type="email" id="correo2" wire:model="formSocio.correo2"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="someone@example.com" />
                    </div>
                </div>
                <div class="flex gap-2">
                    <!-- CURP -->
                    <div class="w-full">
                        <label for="curp"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">CURP</label>
                        <input type="text" id="curp" wire:model="formSocio.curp"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="" />

                    </div>
                    <!-- RFC -->
                    <div class="w-full">
                        <label for="rfc"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">RFC</label>
                        <input type="text" id="rfc" wire:model="formSocio.rfc"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="" />
                    </div>
                </div>
                <div>
                    <div>
                        <label for="membresias"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Membresia</label>
                        <select id="membresias" wire:model="formSocio.clave_membresia"
                            wire:change="comprobarMembresia($event.target.value)"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option selected value="{{ null }}">Seleccione</option>
                            @foreach ($this->membresias as $membresia)
                                <option value="{{ $membresia->clave }}">{{ $membresia->descripcion }}</option>
                            @endforeach
                        </select>
                        @error('formSocio.clave_membresia')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                </div>
                <div class="flex gap-3 items-end">
                    <div class="w-full">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            for="file_input">Subir
                            foto</label>
                        <input wire:model="formSocio.img_path"
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                            id="file_input" type="file" accept=".jpg, .png">
                        @error('formSocio.img_path')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    <button type="button"
                        class="max-h-11 rounded-full text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium text-sm p-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                        <svg class="w-6 h-6 dark:text-gray-800 text-white" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path fill-rule="evenodd"
                                d="M7.5 4.586A2 2 0 0 1 8.914 4h6.172a2 2 0 0 1 1.414.586L17.914 6H19a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h1.086L7.5 4.586ZM10 12a2 2 0 1 1 4 0 2 2 0 0 1-4 0Zm2-4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">Tomar foto</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Linea -->
        <hr class="h-px my-4 bg-gray-300 border-0 dark:bg-gray-700">
        <h4 class="text-2xl font-bold dark:text-white">Integrantes</h4>

        <!-- Registros miembros del socio -->
        <div aria-disabled="true"
            class="{{ $formSocio->registro_permitido ? '' : 'pointer-events-none opacity-50' }}">
            <!-- Inputs -->
            <div class="w-full">
                <div class="flex gap-2">
                    <!-- APELLIDO PATERNO -->
                    <div class="w-full">
                        <label for="aPaterno-integrante"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Apellido
                            Paterno</label>
                        <input type="text" id="apellido_p_integrante" wire:model="formSocio.apellido_p_integrante"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            required />
                    </div>
                    <!-- APELLIDO MATERNO -->
                    <div class="w-full">
                        <label for="aMaterno"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Apellido
                            Materno</label>
                        <input type="text" id="apellido_m_integrante" wire:model="formSocio.apellido_m_integrante"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            required />
                    </div>

                    <div class="w-full">
                        <label for="nombre-miembro"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre</label>
                        <input wire:model='formSocio.nombre_integrante' type="text" id="nombre-miembro"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        @error('formSocio.nombre_integrante')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                </div>
                <div class="flex gap-2">
                    <div class="w-full">
                        <label for="fecha-nac"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha
                            nacimiento</label>
                        <input wire:model='formSocio.fecha_nac' type="date" id="fecha-nac"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Soltero(a)" />
                        @error('formSocio.fecha_nac')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    <div class="w-full">
                        <div>
                            <label for="membresias"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Parentesco</label>
                            <select wire:model='formSocio.parentesco' id="membresias"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option selected value="{{ null }}">Seleccione</option>
                                <option value="Padre">Padre</option>
                                <option value="Madre">Madre</option>
                                <option value="Esposo">Esposo</option>
                                <option value="Esposa">Esposa</option>
                                <option value="Hijo/a">Hijo/a</option>
                                <option value="Hermano/a">Hermano/a</option>
                                <option value="Yerno/nuera">Yerno/nuera</option>
                                <option value="Sobrino/a">Sobrino/a</option>
                                <option value="Nieto/a">Nieto/a</option>
                                <option value="Tio/a">Tio/a</option>
                                <option value="Suegro/a">Suegro/a</option>
                            </select>
                            @error('formSocio.parentesco')
                                <x-input-error messages="{{ $message }}" />
                            @enderror
                        </div>
                    </div>
                    <!-- Subir foto -->
                    <div class="w-full gap-3 items-end flex">
                        <!--INPUT-->
                        <div class="w-full">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                for="file_input">Subir
                                foto</label>
                            <input wire:model='formSocio.img_path_integrante'
                                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                                id="file_input" type="file" accept=".jpg, .png">
                            @error('formSocio.img_path_integrante')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <!--CAMARA-->
                        <button type="button"
                            class="max-h-11 rounded-full text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium text-sm p-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                            <svg class="w-6 h-6 dark:text-gray-800 text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path fill-rule="evenodd"
                                    d="M7.5 4.586A2 2 0 0 1 8.914 4h6.172a2 2 0 0 1 1.414.586L17.914 6H19a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h1.086L7.5 4.586ZM10 12a2 2 0 1 1 4 0 2 2 0 0 1-4 0Zm2-4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="sr-only">Tomar foto</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Button -->
            <div class="flex my-2">
                <!-- Agregar miembro -->
                <button type="button" wire:click='agregarMiembro'
                    class="max-h-11 w-96 text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">Añadir
                    Miembro
                </button>
            </div>
        </div>

        <!-- TABLA miembros del socio -->
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3 max-w-fit">
                            IMAGÉN
                        </th>
                        <th scope="col" class="px-6 py-3 w-96">
                            APELLIDO PATERNO
                        </th>
                        <th scope="col" class="px-6 py-3 w-96">
                            APELLIDO MATERNO
                        </th>
                        <th scope="col" class="px-6 py-3 w-96">
                            NOMBRE(S)
                        </th>
                        <th scope="col" class="px-6 py-3 min-w-52">
                            FECHA NACIMIENTO
                        </th>
                        <th scope="col" class="px-6 py-3 max-w-fit">
                            PARENTESCO
                        </th>
                        <th scope="col" class="px-6 py-3 max-w-fit">
                            ACCIONES
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($formSocio->integrantes as $integrante)
                        <tr id="{{ $integrante['temp'] }}"
                            class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                            <td scope="row" class="max-w-fit px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <!-- IMAGEN DE PERFIL -->
                                    <div>
                                        @if ($integrante['img_path_integrante'])
                                            <img class="size-20"
                                                src="{{ $integrante['img_path_integrante']->temporaryUrl() }}">
                                        @else
                                            <!--Placeholder-->
                                            <img class="size-20" src="https://placehold.co/400"
                                                alt="image description">
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                {{ $integrante['apellido_p_integrante'] }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $integrante['apellido_m_integrante'] }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $integrante['nombre_integrante'] }}
                            </td>
                            <td class=" px-6 py-4">
                                {{ $integrante['fecha_nac'] }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $integrante['parentesco'] }}
                            </td>
                            <td class="flex max-w-fit px-6 py-4">
                                <button wire:click="borrarMiembro({{ $integrante['temp'] }})" type="button"
                                    class="text-red-700 border border-red-700 hover:bg-red-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:focus:ring-red-800 dark:hover:bg-red-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="w-5 h-5">
                                        <path fill-rule="evenodd"
                                            d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4a2 2 0 0 1 .586-1.414ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="sr-only">Borrar</span>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="inline-flex">
            {{-- Boton de regresar --}}
            <a type="button" href="{{ route('recepcion.socios') }}"
                class="my-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                    height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 12h14M5 12l4-4m-4 4 4 4" />
                </svg>Regresar
            </a>
            <!-- Boton de registro-->
            <button type="button" wire:click="register"
                class="items-center gap-2 justify-center flex w-64 focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 my-2 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                <svg wire:loading.delay wire:loading.attr="disabled" wire:target="register"
                    class="inline w-5 h-4 text-white animate-spin" viewBox="0 0 100 101" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                        fill="#E5E7EB" />
                    <path
                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                        fill="currentColor" />
                </svg>
                Finalizar registro
            </button>
        </div>
    </div>

    <!--Alerts-->
    <x-action-message on='open-action-message'>
        @if (session('success'))
            <div id="alert-exito"
                class="flex items-center p-4 mb-4 text-green-800 border-t-4 border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800"
                role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <div class="ms-3 text-sm font-medium">
                    {{ session('success') }}
                </div>
            </div>
        @else
            <div id="alert-error"
                class="flex items-center p-4 mb-4 text-red-800 border-t-4 border-red-300 bg-red-50 dark:text-red-400 dark:bg-gray-800 dark:border-red-800"
                role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <div class="ms-3 text-sm font-medium">
                    {{ session('fail') }}
                </div>
            </div>
        @endif
    </x-action-message>
</div>
