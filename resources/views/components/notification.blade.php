<div x-data="{
    notifications: [],
    styles: {
        success: 'border-green-500 text-green-700 bg-green-50',
        error: 'border-red-500 text-red-700 bg-red-50',
        warning: 'border-yellow-500 text-yellow-700 bg-yellow-50',
        info: 'border-blue-500 text-blue-700 bg-blue-50'
    },
    add(e) {
        const id = Date.now();
        this.notifications.push({ id, message: e.detail.message, type: e.detail.type });
        setTimeout(() => this.remove(id), 20000); // Se va en 20 segundos
    },
    remove(id) {
        this.notifications = this.notifications.filter(n => n.id !== id);
    }
}" @notify.window="add($event)"
    class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 w-full max-w-xs">
    <template x-for="notification in notifications" :key="notification.id">
        <div x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-8"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0 scale-90"
            class="border-l-4 shadow-md rounded-r-lg p-4 flex items-center justify-between"
            :class="styles[notification.type]">
            <div class="flex items-center gap-2">
                {{-- Icono dinámico opcional --}}
                <span x-show="notification.type === 'error'">⚠️</span>
                <span x-show="notification.type === 'success'">✅</span>
                <div class="text-sm font-bold" x-text="notification.message"></div>
            </div>
            <button @click="remove(notification.id)" class="text-gray-400 hover:text-gray-600 ml-4">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </template>
</div>