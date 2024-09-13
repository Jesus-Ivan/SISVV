<div>
    <div class="relative">
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 20 20" wire:loading.remove wire:target='search'>
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
            </svg>
            <!--Loading indicator-->
            <div wire:loading wire:target='search'>
                @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
            </div>
        </div>
        <input type="search" id="default-search" wire:model.live.debounce.500ms='search'
            class="block w-full  ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            placeholder="{{ $tittle_bar }}" />
    </div>
    <!--Sugerencias de autocompletado -->
    @if ($this->results)
        <div class="relative z-40">
            <div class="absolute w-full overflow-y-auto max-h-64 h-auto bg-white border">
                @foreach ($this->results as $result)
                    <div class="p-2 hover:bg-slate-200" wire:click="select({{ $result->$primary_key }})">
                        @foreach ($table_columns as $column)
                            {{ $result->$column }}
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
