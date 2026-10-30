import './bootstrap';
import 'flowbite';

import { initFlowbite } from 'flowbite';

import focus from '@alpinejs/focus'
Alpine.plugin(focus)

Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
    succeed(({ snapshot, effect }) => {
        queueMicrotask(() => {
            initFlowbite();
        })
    })
})

document.addEventListener('livewire:navigated', () => {
    initFlowbite();
})

// Prevenir que el scroll del mouse modifique el valor de inputs numéricos
window.addEventListener('wheel', (e) => {
    if (document.activeElement && document.activeElement.type === 'number') {
        document.activeElement.blur();
    }
}, { passive: true });