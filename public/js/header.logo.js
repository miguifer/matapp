import './variables.js';
import { RUTA_URL } from './variables.js';

document.getElementById('logo').addEventListener('click', function() {
    window.location.href = `${RUTA_URL}`;
});