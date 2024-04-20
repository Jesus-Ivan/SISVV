 <x-app-layout>
     {{-- Sub barra de navegacion --}}
     <x-slot name="header">
         @include('recepcion.nav')
     </x-slot>

     {{-- Contenido --}}
     <div>
         <!-- Title -->
         <h4 class="text-2xl font-bold dark:text-white mx-2">Nueva venta-Recepcion</h4>
         <!-- Search Bar -->
         <livewire:recepcion.ventas.nueva.search-bar />
         <!--Boton de articulos -->
         <div class="flex">
             <div class="flex-grow"></div>
             <button x-data x-on:click="$dispatch('open-modal', {name:'agregar-productos'})"
                 class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700 inline-flex items-center">
                 <svg class="w-5 h-5 text-white me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                     fill="currentColor" viewBox="0 0 24 24">
                     <path fill-rule="evenodd"
                         d="M5 3a1 1 0 0 0 0 2h.687L7.82 15.24A3 3 0 1 0 11.83 17h2.34A3 3 0 1 0 17 15H9.813l-.208-1h8.145a1 1 0 0 0 .979-.796l1.25-6A1 1 0 0 0 19 6h-2.268A2 2 0 0 1 15 9a2 2 0 1 1-4 0 2 2 0 0 1-1.732-3h-1.33L7.48 3.796A1 1 0 0 0 6.5 3H5Z"
                         clip-rule="evenodd" />
                     <path fill-rule="evenodd"
                         d="M14 5a1 1 0 1 0-2 0v1h-1a1 1 0 1 0 0 2h1v1a1 1 0 1 0 2 0V8h1a1 1 0 1 0 0-2h-1V5Z"
                         clip-rule="evenodd" />
                 </svg>
                 Añadir
             </button>
         </div>
         <!-- Tabla de productos -->
         <livewire:recepcion.ventas.nueva.productos-table />
         <!--Linea -->
         <hr class="h-px my-4 bg-gray-300 border-0 dark:bg-gray-700">
         <!-- Titulo y Boton Metodo de pagos -->
         <div class="flex items-center">
             <!--Titulo de metodo de pago-->
             <div class="flex flex-grow">
                 <h5 class="text-xl font-bold dark:text-white">Metodo de pago: </h5>
             </div>
             <!--Boton de metodos de pago -->
             <button type="button" data-modal-target="modal-pagos" data-modal-toggle="modal-pagos"
                 class=" inline-flex items-center focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                 <svg class="w-6 h-6 text-white dark:text-gray-800 me-2" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                     viewBox="0 0 24 24">
                     <path fill-rule="evenodd"
                         d="M12 14a3 3 0 0 1 3-3h4a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-4a3 3 0 0 1-3-3Zm3-1a1 1 0 1 0 0 2h4v-2h-4Z"
                         clip-rule="evenodd" />
                     <path fill-rule="evenodd"
                         d="M12.293 3.293a1 1 0 0 1 1.414 0L16.414 6h-2.828l-1.293-1.293a1 1 0 0 1 0-1.414ZM12.414 6 9.707 3.293a1 1 0 0 0-1.414 0L5.586 6h6.828ZM4.586 7l-.056.055A2 2 0 0 0 3 9v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2h-4a5 5 0 0 1 0-10h4a2 2 0 0 0-1.53-1.945L17.414 7H4.586Z"
                         clip-rule="evenodd" />
                 </svg>
                 Añadir pago
             </button>
         </div>
         <!-- Tabla de metodos de pago-->
         <livewire:recepcion.ventas.nueva.pagos-table />
         <!-- Boton Acciones -->
         <livewire:recepcion.ventas.nueva.acciones />
     </div>
     <!--Modal productos -->
     <x-modal name="agregar-productos" title="Agregar productos">
         <x-slot name='body'>
             <livewire:recepcion.ventas.nueva.productos-modal-body />
         </x-slot>
         <x-slot name="footer"></x-slot>
     </x-modal>
     <!--Modal pagos -->
     <div id="modal-pagos" tabindex="-1" aria-hidden="true"
         class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
         <div class="relative p-4 w-full max-w-2xl max-h-full">
             <!-- Modal content -->
             <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                 <!-- Modal header -->
                 <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                     <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                         Agregar metodo de pago
                     </h3>
                     <button type="button"
                         class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                         data-modal-hide="modal-pagos">
                         <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 14 14">
                             <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                 d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                         </svg>
                         <span class="sr-only">Close modal</span>
                     </button>
                 </div>
                 <!-- Modal body -->
                 <div class="p-4 md:p-5 space-y-4">
                     <div class="grid grid-flow-col">
                         <input type="email" id="email"
                             class=" bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                             placeholder="NO.Socio" required />
                         <input type="email" id="email"
                             class=" bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                             placeholder="Nombre" required />
                     </div>
                     <div>
                         <label for="countries"
                             class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Metodo</label>
                         <select id="countries"
                             class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                             <option>01-EFECTIVO</option>
                             <option>02-TARJETA DE CREDITO</option>
                             <option>03-TARJETA DE DEBITO</option>
                             <option>04-FIRMA</option>
                         </select>
                     </div>
                     <div class="grid grid-flow-col">
                         <input type="email" id="email"
                             class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                             placeholder="Monto" required />
                         <input type="email" id="email"
                             class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                             placeholder="Propina" required />
                     </div>
                 </div>
                 <!-- Modal footer -->
                 <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                     <button data-modal-hide="modal-pagos" type="button"
                         class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Aceptar</button>
                 </div>
             </div>
         </div>
     </div>
     <!--Alerts-->
     <div class="fixed bottom-0 left-1">
         <div id="alert-border-3"
             class="flex items-center p-4 mb-4 text-green-800 border-t-4 border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800"
             role="alert">
             <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                 fill="currentColor" viewBox="0 0 20 20">
                 <path
                     d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
             </svg>
             <div class="ms-3 text-sm font-medium">
                 A simple success alert with an <a href="#"
                     class="font-semibold underline hover:no-underline">example link</a>. Give it a click if you like.
             </div>
             <button type="button"
                 class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700"
                 data-dismiss-target="#alert-border-3" aria-label="Close">
                 <span class="sr-only">Dismiss</span>
                 <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 14 14">
                     <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                 </svg>
             </button>
         </div>
     </div>
 </x-app-layout>
