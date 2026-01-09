// toggle-orari.js
function toggleOrari(corso) {
    const article = document.getElementById(corso);
    const descrizioneDiv = article.querySelector('.corso-descrizione');
    const descrizione = descrizioneDiv.querySelector('p');
    const orariDiv = article.querySelector('.corso-orari');
    const button = article.querySelector('button');

    if (descrizione.style.display === 'none') {
        // Mostra descrizione
        descrizione.style.display = 'block';
        orariDiv.style.display = 'none';
        button.textContent = 'vedi gli orari ➥';
        // Move button back to descrizione
        descrizioneDiv.appendChild(button);
    } else {
        // Mostra orari
        descrizione.style.display = 'none';
        orariDiv.style.display = 'block';
        button.textContent = 'torna alla descrizione ➥';
        // Move button to orari after the last p
        const lastP = orariDiv.querySelector('p:last-of-type');
        if (lastP) {
            lastP.insertAdjacentElement('afterend', button);
        } else {
            orariDiv.appendChild(button);
        }
    }
}
