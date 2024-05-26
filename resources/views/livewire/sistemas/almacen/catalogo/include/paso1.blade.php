<div class="my-2">
    <div class="justify-center">
        {{-- Naturaleza del articulo --}}
        <label for="naturaleza" class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Naturaleza</label>
        <select id="naturaleza"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-52 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option selected>SELECCIONAR</option>
            <option value="INV">INVENTARIABLE</option>
            <option value="NINV">NO INVENTARIABLE</option>
        </select>

        {{-- Disponiblilidad del articulo --}}
        <label for="disponiblilidad" class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Disponibilidad</label>
        <select id="disponiblilidad"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-52 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option selected>SELECCIONAR</option>
            <option value="VE">VENDIBLE</option>
            <option value="NVE">NO VENDIBLE</option>
            <option value="SE">SERVICIOS</option>
            <option value="PP">PRODUCTO PREPARADO</option>
        </select>
    </div>
</div>
