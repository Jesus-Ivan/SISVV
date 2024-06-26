@props(['name', 'title'])
<!-- Main modal -->
<div x-data="{ show: false, name: '{{ $name }}' }" x-show= "show" x-on:open-modal.window= "show = ($event.detail.name === name)"
    x-on:close-modal.window= "show = false" x-on:keydown.escape.window="show = false" style= "display: none;"
    class="fixed z-50 inset-0">

    <!-- Gray Background -->
    <div @click="$dispatch('onClick-Outside', { data: '{{ $name }}' })" x-on:click="show=false"
        class="fixed inset-0 bg-gray-400 opacity-40"></div>

    <!-- Modal body -->
    <div class="bg-white rounded m-auto fixed inset-0 max-w-fit max-h-fit">
        <!-- Modal header -->
        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
            @if (isset($title))
                <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                    {{ $title }}
                </h3>
            @endif
            <button @click="$dispatch('onClick-Outside', { data: '{{ $name }}' })" x-on:click="show = false"
                type="button"
                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                </svg>
            </button>
        </div>
        <!-- Modal body -->
        <div class="p-4 md:p-5 space-y-4">
            {{ $body }}
        </div>
        <!-- Modal footer -->
        @if (isset($footer))
            <div
                class="flex items-center p-4 md:p-5 space-x-3 rtl:space-x-reverse border-t border-gray-200 rounded-b dark:border-gray-600">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
