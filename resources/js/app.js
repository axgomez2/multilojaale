import './bootstrap';
import Alpine from 'alpinejs';
import 'flowbite';
import './vinyl-actions';
import './vinyl-player';
import './vinyl-description-ai';

window.Alpine = Alpine;

// Inicializar o Alpine.js
Alpine.start();

// Definir variável global para verificar autenticação
window.isAuthenticated = document.body.classList.contains('user-authenticated');
