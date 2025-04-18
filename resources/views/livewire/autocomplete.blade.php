<div class="relative w-full" @keydown.down="$focus.wrap().next()" @keydown.up="$focus.wrap().previous()"
    >
    <!--Input del No de socio -->
    <div>
        <div class="absolute inset-y-0 start-0 flex items-center p-2.5 pointer-events-none">
            <!--Icono por defecto-->
            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                fill="currentColor" viewBox="0 0 24 24" wire:loading.remove wire:target='search'>
                <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                    d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
            </svg>
            <!--Loading indicator-->
            <div wire:loading wire:target='search'>
                @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
            </div>
        </div>
        <input type="text" wire:model.live.debounce.500ms ="search"
            class=" bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            placeholder="Nombre o No. de socio ">
    </div>

    <!--Sugerencias de autocompletado -->
    @if ($this->results)
        <div class="relative z-40">
            <div class="absolute w-full overflow-y-auto max-h-64 h-auto bg-white border">
                @foreach ($this->results as $result)
                    <button type="button" class="text-start w-full p-2 hover:bg-slate-100 focus:bg-slate-300"
                        wire:click="select({{ $result->$primaryKey }})">
                        @foreach ($params['table']['columns'] as $column)
                            {{ $result->$column }}
                        @endforeach
                    </button>
                @endforeach
            </div>
        </div>
    @endif
</div>
