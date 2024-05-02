<div class="relative w-full">
    <!--Input del No de socio -->
    <div>
        <div class="absolute inset-y-0 start-0 flex items-center p-2.5 pointer-events-none">
            <!--Icono por defecto-->
            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                fill="currentColor" viewBox="0 0 24 24" wire:loading.remove wire:target='search'>
                <path fill-rule="evenodd"
                    d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z"
                    clip-rule="evenodd" />
            </svg>
            <!--Loading indicator-->
            <div wire:loading wire:target='search' >
                @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
            </div>
        </div>
        <input type="text" id="search" wire:model.live.debounce.500ms ="search"
            class=" bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            placeholder="Nombre o No. de socio ">
    </div>
    <!--Sugerencias de autocompletado -->
    @if ($this->results)
        <div class="relative z-40">
            <div class="absolute w-full bg-white border">
                @foreach ($this->results as $result)
                    <div class="p-2 hover:bg-slate-200" wire:click="select({{ $result->id }})">
                        {{ $result->nombre }}</div>
                @endforeach
            </div>
        </div>
    @endif
</div>
