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