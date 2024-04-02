<div>
    <!-- Registro del socio -->
    <div class="flex gap-4 items-start">
        <!-- IMAGEN DEL SOCIO -->
        <img class="h-auto w-auto" src="https://placehold.co/400" alt="image description">
        <!-- columna 2 -->
        <div class="w-full">
            <div>
                <label for="nombre"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre(s)</label>
                <input type="text" id="nombre"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    required />
            </div>
            <div>
                <label for="calle" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Calle</label>
                <input type="text" id="calle"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>
            <div class="flex gap-4">
                <div class="w-full">
                    <label for="num-exterior"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Num.Exterior</label>
                    <input type="text" id="num-exterior"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                </div>
                <div class="w-full">
                    <label for="CP" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Codigo
                        postal</label>
                    <input type="number" id="CP"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                </div>
            </div>
            <div>
                <label for="colonia"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Colonia</label>
                <input type="text" id="colonia"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>
            <div>
                <label for="estado"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estado</label>
                <input type="text" id="estado"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Oaxaca" />
            </div>
            <div>
                <label for="ciudad"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ciudad</label>
                <input type="text" id="ciudad"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>
            <div>
                <label for="estado-membresia"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estado de membresia</label>
                <select id="estado-membresia"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option>Activa</option>
                    <option>Inactiva</option>
                    <option>Cancelada</option>
                </select>
            </div>
        </div>
        <!-- columna 3 -->
        <div class="w-full">
            <div>
                <label for="estado-civil" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estado
                    civil</label>
                <input type="text" id="estado-civil"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Soltero(a)" />
            </div>
            <div>
                <label for="tel-fijo" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Telefono
                    fijo</label>
                <input type="number" id="tel-fijo"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>
            <div>
                <label for="tel-celular" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Telefono
                    celular</label>
                <input type="number" id="tel-celular"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>
            <div>
                <div>
                    <label for="membresias"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Membresia</label>
                    <select id="membresias"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option>01-Casa club Individual</option>
                        <option>02-Casa club Familiar</option>
                        <option>03-Golf Individual</option>
                        <option>04-Golf Familiar</option>
                    </select>
                </div>
            </div>
            <div>
                <label for="correo"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Correo</label>
                <input type="email" id="correo"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="someone@example.com" />
            </div>
            <div class="flex gap-3 items-end">
                <div class="w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Subir
                        foto</label>
                    <input
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                        id="file_input" type="file">
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
    <!-- Registros miembros del socio -->
    <div>
        <!-- Inputs -->
        <div class="flex gap-4">
            <div class="w-full">
                <label for="nombre-miembro"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre</label>
                <input type="text" id="nombre-miembro"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>
            <div class="w-full">
                <label for="fecha-nac" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha
                    nacimiento</label>
                <input type="text" id="fecha-nac"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Soltero(a)" />
            </div>
            <div class="w-full">
                <div>
                    <label for="membresias"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Parentesco</label>
                    <select id="membresias"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option>Padre</option>
                        <option>Madre</option>
                        <option>Hijo(a)</option>
                        <option>Hermano(a)</option>
                        <option>Esposo(a)</option>
                    </select>
                </div>
            </div>
        </div>
        <!-- Buton y Image -->
        <div class="flex items-end">
            <button type="button"
                class="max-h-11 w-full text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">Añadir
                miembro
            </button>
            <div class="w-full"></div>
            <div class="w-full gap-3 items-end flex">
                <div class="w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Subir
                        foto</label>
                    <input
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                        id="file_input" type="file">
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
    <!-- TABLA miembros del socio -->
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3 w-full">
                        NOMBRE
                    </th>
                    <th scope="col" class="px-6 py-3 min-w-72">
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
                <tr
                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                    <td scope="row" class="w-full px-6 py-4">
                        <div class="flex items-center gap-4">
                            <!-- IMAGEN DE PERFIL -->
                            <div>
                                <img class="w-20 h-20 rounded-full" src="https://placehold.co/400"
                                    alt="Rounded avatar">
                            </div>
                            <!-- INFO -->
                            <div class="dark:text-white">
                                <div class="font-medium">JUANITO MASAFECIO HERNESTINO DE LA CRUZ</div>
                            </div>
                        </div>
                    </td>
                    <td class="min-w-72 px-6 py-4">
                        12/09/2000
                    </td>
                    <td class="min-w-72 px-6 py-4">
                        Hijo(a)
                    </td>
                    <td class="flex max-w-fit px-6 py-4">
                        <button type="button"
                            class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-5 h-5">
                                <path fill-rule="evenodd"
                                    d="M11.32 6.176H5c-1.105 0-2 .949-2 2.118v10.588C3 20.052 3.895 21 5 21h11c1.105 0 2-.948 2-2.118v-7.75l-3.914 4.144A2.46 2.46 0 0 1 12.81 16l-2.681.568c-1.75.37-3.292-1.263-2.942-3.115l.536-2.839c.097-.512.335-.983.684-1.352l2.914-3.086Z"
                                    clip-rule="evenodd" />
                                <path fill-rule="evenodd"
                                    d="M19.846 4.318a2.148 2.148 0 0 0-.437-.692 2.014 2.014 0 0 0-.654-.463 1.92 1.92 0 0 0-1.544 0 2.014 2.014 0 0 0-.654.463l-.546.578 2.852 3.02.546-.579a2.14 2.14 0 0 0 .437-.692 2.244 2.244 0 0 0 0-1.635ZM17.45 8.721 14.597 5.7 9.82 10.76a.54.54 0 0 0-.137.27l-.536 2.84c-.07.37.239.696.588.622l2.682-.567a.492.492 0 0 0 .255-.145l4.778-5.06Z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="sr-only">Editar</span>
                        </button>
                        <button type="button"
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
            </tbody>
        </table>
    </div>
    <!-- Boton de registro-->
    <button type="button"
        class="m-2 w-64 focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Guardar cambios</button>
</div>
