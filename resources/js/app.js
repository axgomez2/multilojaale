import './bootstrap';
import Alpine from 'alpinejs';
import 'flowbite';
import './vinyl-actions';

window.Alpine = Alpine;
Alpine.start();

// Definir variável global para verificar autenticação
window.isAuthenticated = document.body.classList.contains('user-authenticated');
