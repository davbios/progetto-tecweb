/* --- FILE: script.js --- */

// Recuperiamo gli elementi dalla pagina
const toggleBtn = document.getElementById('theme-toggle');
const themeIcon = document.getElementById('theme-icon');
const body = document.body;

// Percorsi delle tue icone (Assicurati che siano corretti!)
const iconaSole = "../images/brightness.png"; 
const iconaLuna = "../images/moon.png";

// 1. Al caricamento della pagina: controlliamo la memoria
const currentTheme = localStorage.getItem('theme');

if (currentTheme === 'light') {
    // Se l'utente aveva scelto il tema chiaro
    body.classList.add('light-mode');
    themeIcon.src = iconaLuna; // Mostriamo la luna (per tornare al buio)
    themeIcon.alt = "Passa alla modalità scura";
}

// 2. Quando l'utente clicca il bottone
toggleBtn.addEventListener('click', () => {
    // Aggiunge o toglie la classe 'light-mode'
    body.classList.toggle('light-mode');

    if (body.classList.contains('light-mode')) {
        // È diventato GIORNO -> Mostra la LUNA
        themeIcon.src = iconaLuna;
        themeIcon.alt = "Passa alla modalità scura";
        localStorage.setItem('theme', 'light'); // Salva la scelta
    } else {
        // È diventata NOTTE -> Mostra il SOLE
        themeIcon.src = iconaSole;
        themeIcon.alt = "Passa alla modalità chiara";
        localStorage.setItem('theme', 'dark'); // Salva la scelta
    }
});
