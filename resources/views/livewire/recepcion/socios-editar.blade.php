<div>
    <div class="ms-3 mx-3">
        <!-- Registro del socio -->
        <div class="flex gap-4 items-start">
            <!-- IMAGEN DEL SOCIO -->
            <div class="w-full">
                @if ($form->img_path)
                    <!--Imagen temporal-->
                    <img class="size-96" src="{{ $form->img_path->temporaryUrl() }}">
                @else
                    <!--Imagen del socio-->
                    <img class="h-96 w-96" src="{{ asset($form->socio->img_path) }}" alt="image description">
                @endif
                <!--loading state image-->
                <div wire:loading wire:target="form.img_path">
                    <div class="flex gap-3 items-center">
                        <svg class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                            viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                fill="currentColor" />
                            <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                fill="currentFill" />
                        </svg>
                        <span>Cargando imagen...</span>
                    </div>
                </div>
                <!-- Boton de actualizacion DEL SOCIO-->
                <button type="button" wire:click='saveSocio' wire:loading.attr="disabled"
                    class="m-2 w-full focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                    <svg wire:loading.delay wire:target="saveSocio" class="inline w-5 h-4 text-white animate-spin"
                        viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                            fill="#E5E7EB" />
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentColor" />
                    </svg>
                    Actualizar Información
                </button>
            </div>

            <!-- columna 2 -->
            <div class="w-full">
                <div class="flex gap-2">
                    <!-- APELLIDO PATERNO -->
                    <div class="w-full">
                        <label for="apellido_p"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Apellido
                            Paterno</label>
                        <input type="text" id="apellido_p" wire:model="form.apellido_p"
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
                        <input type="text" id="apellido_m" wire:model="form.apellido_m"
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
                    <input type="text" id="nombre" wire:model="form.nombre"
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
                    <input type="text" id="calle" wire:model="form.calle"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                </div>
                <div class="flex gap-2">
                    <!-- NUMERO EXTERIOR -->
                    <div class="w-full">
                        <label for="num-exterior"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No.
                            Exterior</label>
                        <input type="text" id="num-exterior" wire:model="form.num_exterior"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>
                    <!-- NUMERO INTERIOR -->
                    <div class="w-full">
                        <label for="num-interior"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No.
                            Interior</label>
                        <input type="text" id="num-interior" wire:model="form.num_interior"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>
                    <!-- CODIGO POSTAL -->
                    <div class="w-full">
                        <label for="CP"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Codigo
                            Postal</label>
                        <input type="number" id="CP" wire:model="form.codigo_postal"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>
                </div>
                <!-- COLONIA -->
                <div>
                    <label for="colonia"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Colonia</label>
                    <input type="text" id="colonia" wire:model="form.colonia"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                </div>
                <div class="flex gap-2">
                    <!-- ESTADO -->
                    <div class="w-full">
                        <label for="estado"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estado</label>
                        <input type="text" id="estado" wire:model="form.estado"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Oaxaca" />
                    </div>
                    <!-- CIUDAD -->
                    <div class="w-full">
                        <label for="ciudad"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ciudad</label>
                        <input type="text" id="ciudad" wire:model="form.ciudad"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>
                </div>
                <!-- ESTADO MEMBRESIA -->
                <div>
                    <label for="estado-membresia"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estado de
                        membresia</label>
                    <select id="estado-membresia" wire:model='form.estado_membresia'
                        class="{{ $form->estado_membresia == 'ANU' ? 'pointer-events-none opacity-70' : '' }} bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="MEN">Activa</option>
                        <option value="INA">Inactiva</option>
                        @if ($form->estado_membresia == 'ANU')
                            <option value="ANU">Anual</option>
                        @endif
                        <option value="CAN">Cancelada</option>
                    </select>
                    @error('form.estado_membresia')
                        <x-input-error messages="{{ $message }}" />
                    @enderror
                </div>
            </div>

            <!-- columna 3 -->
            <div class="w-full">
                <!-- ESTADO CIVIL -->
                <div>
                    <label for="estado-civil"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estado civil</label>
                    <input type="text" id="estado-civil" wire:model="form.estado_civil"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Soltero(a)" />
                </div>
                <!-- TELEFONOS -->
                <div class="flex gap-2">
                    <div class="w-full">
                        <label for="tel-1"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Telefono 1</label>
                        <input type="number" id="tel-1" wire:model="form.tel_1"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        @error('formSocio.tel_1')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    <div class="w-full">
                        <label for="tel-celular"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Telefono 2</label>
                        <input type="number" id="tel-celular" wire:model="form.tel_2"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>
                </div>
                <!-- EMAILS -->
                <div class="flex gap-2">
                    <div class="w-full">
                        <label for="correo"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Correo</label>
                        <input type="email" id="correo" wire:model="form.correo1"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="someone@example.com" />
                        @error('formSocio.correo1')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    <div class="w-full">
                        <label for="correo2"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Correo
                            Adicional</label>
                        <input type="email" id="correo2" wire:model="form.correo2"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="someone@example.com" />
                    </div>
                </div>
                <div class="flex gap-2">
                    <!-- CURP -->
                    <div class="w-full">
                        <label for="curp"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">CURP</label>
                        <input type="text" id="curp" wire:model="form.curp"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="" />
                    </div>
                    <!-- RFC -->
                    <div class="w-full">
                        <label for="rfc"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">RFC</label>
                        <input type="text" id="rfc" wire:model="form.rfc"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="" />
                    </div>
                </div>
                <div>
                    <!-- MEMBRESIA -->
                    <div>
                        <label for="membresias"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Membresia</label>
                        <select id="membresias" wire:model="form.clave_membresia"
                            wire:change="comprobarMembresia($event.target.value)"
                            class="{{ $form->estado_membresia == 'ANU' ? 'pointer-events-none opacity-70' : '' }} bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
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
                    <!-- SUBIR FOTO SOCIO -->
                    <div class="w-full">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            for="file_input">Subir
                            foto</label>
                        <input wire:model="form.img_path"
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                            id="file_input" type="file">
                        @error('form.img_path')
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
        <hr class="h-px my-3 bg-gray-300 border-0 dark:bg-gray-700">
        <h4 class="text-2xl font-bold dark:text-white mb-4">Integrantes</h4>

        <!-- Registros integrantes del socio -->
        <div class="{{ $form->registro_permitido ? '' : 'pointer-events-none opacity-50' }}">
            <!-- Inputs -->
            <div class="w-full">
                <div class="flex gap-2">
                    <!-- APELLIDO PATERNO -->
                    <div class="w-full">
                        <label for="aPaterno-integrante"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Apellido
                            Paterno</label>
                        <input type="text" id="aPaterno" wire:model="form.apellido_p_integrante"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            required />
                        @error('formSocio.apellido_p_integrante')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    <!-- APELLIDO MATERNO -->
                    <div class="w-full">
                        <label for="aMaterno"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Apellido
                            Materno</label>
                        <input type="text" id="nombre" wire:model="form.apellido_m_integrante"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            required />
                        @error('formSocio.apellido_m_integrante')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>

                    <div class="w-full">
                        <label for="nombre-miembro"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre</label>
                        <input wire:model='form.nombre_integrante' type="text" id="nombre-miembro"
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
                        <input wire:model='form.fecha_nac' type="date" id="fecha-nac"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Soltero(a)" />
                        @error('formSocio.fecha_nac')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    <div class="flex gap-3 w-full">
                        <div class="w-full">
                            <label for="membresias"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Parentesco</label>
                            <select wire:model='form.parentesco' id="membresias"
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
                        <div class="w-full">
                            <label for="tel_integrante"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Celular</label>
                            <input type="tel" id="tel_integrante" wire:model="formSocio.tel_integrante"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                required />
                        </div>
                    </div>
                    <!-- Subir foto -->
                    <div class="w-full gap-3 items-end flex">
                        <!--INPUT-->
                        <div class="w-full">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                for="file_input">Subir
                                foto</label>
                            <input wire:model='form.img_path_integrante'
                                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                                id="file_input" type="file">
                            @error('form.img_path_integrante')
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

            <!-- Buton y Image -->
            <div class="flex my-2">
                <!-- Agregar integrante -->
                <button type="button" wire:click='registrarIntegrante' wire:loading.attr="disabled"
                    class="max-h-11 w-96 text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">
                    <!--loading state image-->
                    <div wire:loading.delay wire:target="registrarIntegrante">
                        @include('livewire.utils.loading', ['w' => 4, 'h' => 4])
                    </div>
                    Agregar nuevo integrante
                </button>
            </div>
        </div>

        <!-- TABLA integrantes del socio -->
        <div
            class="{{ $form->registro_permitido ? 'overflow-x-auto shadow-md sm:rounded-lg' : 'pointer-events-none opacity-50' }}">
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
                        <th scope="col" class="px-6 py-3 w-96">
                            CELULAR
                        </th>
                        <th scope="col" class="px-6 py-3 min-w-40">
                            FECHA NACIMIENTO
                        </th>
                        <th scope="col" class="px-6 py-3 max-w-fit">
                            PARENTESCO
                        </th>
                        <th scope="col" class="flex max-w-fit px-6 py-4">
                            ACCIONES
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($form->integrantes_BD as $index => $integrante)
                        <tr wire:key='{{ $integrante->id }}'
                            class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                            @if ($form->editando_miembro_id == $integrante->id)
                                <td scope="row" class="max-w-fit px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <!-- IMAGEN DE PERFIL -->
                                        <div class="w-20 h-20">
                                            <div class="flex items-center justify-center w-full h-full">
                                                <label for="dropzone-file"
                                                    class="flex flex-col items-center justify-center border-2 w-full h-full border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                                    <div class="flex flex-col items-center justify-center">
                                                        @if ($form->editando_img_path_integrante)
                                                            <!--Imagen temporal-->
                                                            <img class="size-20" wire:loading.remove
                                                                wire:target='form.editando_img_path_integrante'
                                                                src="{{ $form->editando_img_path_integrante->temporaryUrl() }}">
                                                        @endif
                                                        <!--loading state spin-->
                                                        <svg wire:loading
                                                            wire:target="form.editando_img_path_integrante"
                                                            class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                                                            viewBox="0 0 100 101" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                                                fill="currentColor" />
                                                            <path
                                                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                                                fill="currentFill" />
                                                        </svg>
                                                    </div>
                                                    <input wire:model='form.editando_img_path_integrante'
                                                        id="dropzone-file" type="file" class="hidden" />
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3 w-96">
                                    <!-- APELLIDO PATERNO -->
                                    <div class="dark:text-white">
                                        <input type="text" id="apellido_p_integrante"
                                            wire:model='form.editando_apellido_p_integrante'
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                                        @error('form.editando_apellido_p_integrante')
                                            <x-input-error messages="{{ $message }}" />
                                        @enderror
                                    </div>
                                </td>
                                <td class="px-6 py-3 w-96">
                                    <!-- APELLIDO PATERNO -->
                                    <div class="dark:text-white">
                                        <input type="text" id="apellido_m_integrante"
                                            wire:model='form.editando_apellido_m_integrante'
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                                        @error('form.editando_apellido_m_integrante')
                                            <x-input-error messages="{{ $message }}" />
                                        @enderror
                                    </div>
                                </td>
                                <td class="px-6 py-3 w-96">
                                    <div class="dark:text-white">
                                        <!-- NOMBRE -->
                                        <input type="text" id="nombre_integrante"
                                            wire:model='form.editando_nombre_integrante'
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                                        @error('form.editando_nombre_integrante')
                                            <x-input-error messages="{{ $message }}" />
                                        @enderror
                                    </div>
                                </td>
                                <td class="px-6 py-3 w-96">
                                    <div class="dark:text-white">
                                        <!-- CELULAR -->
                                        <input type="text" id="nombre_integrante"
                                            wire:model='form.editando_tel_integrante'
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                                        @error('form.editando_tel_integrante')
                                            <x-input-error messages="{{ $message }}" />
                                        @enderror
                                    </div>
                                </td>
                                <td class="px-6 py-3 min-w-52">
                                    <input type="date" id="fecha_nac" wire:model='form.editando_fecha_nac'
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                                    @error('form.editando_fecha_nac')
                                        <x-input-error messages="{{ $message }}" />
                                    @enderror
                                </td>
                                <td class="px-6 py-3 max-w-fit">
                                    <select id="parentesco" wire:model='form.editando_parentesco'
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
                                    @error('form.editando_parentesco')
                                        <x-input-error messages="{{ $message }}" />
                                    @enderror
                                </td>
                                <td class="flex max-w-fit px-6 py-4">
                                    <button type="button" wire:click='confirmarEdicion({{ $index }})'
                                        class="text-gr-700 border border-gray-700 hover:bg-gray-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-gray-500 dark:text-gray-500 dark:hover:text-white dark:focus:ring-gray-800 dark:hover:bg-gray-500">
                                        <svg class="w-5 h-5 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd"
                                                d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span class="sr-only">Listo</span>
                                    </button>
                                    <button type="button" wire:click='cancelarEdicion'
                                        class="text-gray-700 border border-gray-700 hover:bg-gray-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-gray-500 dark:text-gray-500 dark:hover:text-white dark:focus:ring-gray-800 dark:hover:bg-gray-500">
                                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M14.502 7.046h-2.5v-.928a2.122 2.122 0 0 0-1.199-1.954 1.827 1.827 0 0 0-1.984.311L3.71 8.965a2.2 2.2 0 0 0 0 3.24L8.82 16.7a1.829 1.829 0 0 0 1.985.31 2.121 2.121 0 0 0 1.199-1.959v-.928h1a2.025 2.025 0 0 1 1.999 2.047V19a1 1 0 0 0 1.275.961 6.59 6.59 0 0 0 4.662-7.22 6.593 6.593 0 0 0-6.437-5.695Z" />
                                        </svg>
                                        <span class="sr-only">Cancelar</span>
                                    </button>
                                </td>
                            @else
                                <td scope="row" class="max-w-fit px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <!-- IMAGEN DE PERFIL -->
                                        <div>
                                            <img class="w-20 h-20 rounded-full"
                                                src="{{ asset($integrante->img_path_integrante) }}"
                                                alt="Rounded avatar">
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3 w-96">
                                    <!-- INFO -->
                                    <div class="dark:text-white">
                                        <div class="font-medium">{{ $integrante->apellido_p_integrante }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-3 w-96">
                                    <!-- INFO -->
                                    <div class="dark:text-white">
                                        <div class="font-medium">{{ $integrante->apellido_m_integrante }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-3 w-96">
                                    <!-- INFO -->
                                    <div class="dark:text-white">
                                        <div class="font-medium">{{ $integrante->nombre_integrante }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-3 w-96">
                                    <!-- INFO -->
                                    <div class="dark:text-white">
                                        <div class="font-medium">{{ $integrante->tel_integrante }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-3 min-w-52">
                                    {{ $integrante->fecha_nac }}
                                </td>
                                <td class="px-6 py-3 max-w-fit">
                                    {{ $integrante->parentesco }}
                                </td>
                                <td class="flex max-w-fit px-6 py-4">
                                    <button type="button" wire:click='editarMiembro({{ $integrante }})'
                                        class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                            fill="currentColor" class="w-5 h-5">
                                            <path fill-rule="evenodd"
                                                d="M11.32 6.176H5c-1.105 0-2 .949-2 2.118v10.588C3 20.052 3.895 21 5 21h11c1.105 0 2-.948 2-2.118v-7.75l-3.914 4.144A2.46 2.46 0 0 1 12.81 16l-2.681.568c-1.75.37-3.292-1.263-2.942-3.115l.536-2.839c.097-.512.335-.983.684-1.352l2.914-3.086Z"
                                                clip-rule="evenodd" />
                                            <path fill-rule="evenodd"
                                                d="M19.846 4.318a2.148 2.148 0 0 0-.437-.692 2.014 2.014 0 0 0-.654-.463 1.92 1.92 0 0 0-1.544 0 2.014 2.014 0 0 0-.654.463l-.546.578 2.852 3.02.546-.579a2.14 2.14 0 0 0 .437-.692 2.244 2.244 0 0 0 0-1.635ZM17.45 8.721 14.597 5.7 9.82 10.76a.54.54 0 0 0-.137.27l-.536 2.84c-.07.37.239.696.588.622l2.682-.567a.492.492 0 0 0 .255-.145l4.778-5.06Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span class="sr-only">Editar</span>
                                    </button>
                                    <button type="button" wire:click='eliminarIntegrante({{ $integrante }})'
                                        class="text-red-700 border border-red-700 hover:bg-red-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:focus:ring-red-800 dark:hover:bg-red-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                            fill="currentColor" class="w-5 h-5">
                                            <path fill-rule="evenodd"
                                                d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4a2 2 0 0 1 .586-1.414ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span class="sr-only">Borrar</span>
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Boton de regresar --}}
        <a type="button" href="{{ route('recepcion.socios') }}"
            class="my-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
            <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 12h14M5 12l4-4m-4 4 4 4" />
            </svg>Regresar
        </a>

        <!--Modal de eliminacion -->
        <x-modal title="Eliminar integrante" name="modalEliminar">
            <x-slot name='body'>
                <div class="text-center">
                    <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <h3 class="mb-5 text-xl font-normal text-gray-500 dark:text-gray-400">¿Desea eliminar a:
                        {{ $form->integrante_eliminar ? $form->integrante_eliminar['nombre_integrante'] : '' }}?
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400">Esta accion eliminara al integrante de la membresia
                        actual
                    </p>
                </div>
            </x-slot>
            <x-slot name='footer'>
                <button type="button" wire:click="confirmarEliminacion()"
                    class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                    Eliminar
                </button>
            </x-slot>
        </x-modal>
    </div>

    {{-- Modal de Advertencia --}}
    <x-modal title="Advertencia" name="modalAdvertencia">
        <x-slot name='body'>
            <div class="text-center">
                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <h3 class="mb-5 text-xl font-normal text-gray-500 dark:text-gray-400">¡¡ Cambio de membresia !!
                </h3>
                <p class="text-gray-500 dark:text-gray-400">
                    Has modificado tu membresia, esta accion eliminara todos
                    los integrantes registrados en tu membresia actual.
                </p>
                <p class="text-gray-500 dark:text-gray-400">Deseas continuar?</p>
            </div>
        </x-slot>
        <x-slot name='footer'>
            <button type="button" wire:click="confirmarActualizacion()"
                class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                Continuar
            </button>
        </x-slot>
    </x-modal>

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
