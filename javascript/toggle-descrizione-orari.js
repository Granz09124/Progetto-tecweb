// toggle-orari.js
const orariCorsi = {
    zumba: {
        lunedì: "18:00 - 19:00",
        mercoledì: "18:00 - 19:00",
        venerdì: "18:00 - 19:00"
    },
    pilates: {
        martedì: "10:00 - 11:00",
        giovedì: "10:00 - 11:00",
        sabato: "09:00 - 10:00"
    },
    yoga: {
        lunedì: "19:00 - 20:00",
        mercoledì: "19:00 - 20:00",
        venerdì: "19:00 - 20:00"
    },
    crossfit: {
        martedì: "20:00 - 21:30",
        giovedì: "20:00 - 21:30",
        sabato: "10:00 - 11:30"
    },
    fitboxe: {
        lunedì: "20:00 - 21:00",
        mercoledì: "20:00 - 21:00",
        venerdì: "20:00 - 21:00"
    },
    arrampicata: {
        martedì: "19:00 - 20:30",
        giovedì: "19:00 - 20:30",
        domenica: "14:00 - 16:00"
    }
};

function toggleOrari(corso) {
    const article = document.getElementById(corso);
    const descrizione = article.querySelector('.corso-descrizione p');
    const button = article.querySelector('button');

    if (descrizione.style.display === 'none') {
        // Mostra descrizione
        descrizione.style.display = 'block';
        const orariDiv = article.querySelector('.corso-orari');
        if (orariDiv) orariDiv.remove();
        button.textContent = 'vedi gli orari ➥';
    } else {
        // Mostra orari
        descrizione.style.display = 'none';
        const orariDiv = document.createElement('div');
        orariDiv.className = 'corso-orari';
        orariDiv.innerHTML = `<h3>Orari ${corso.charAt(0).toUpperCase() + corso.slice(1)}</h3>` +
            Object.entries(orariCorsi[corso]).map(([giorno, orario]) => `<p><strong>${giorno}:</strong> ${orario}</p>`).join('');
        if (article.classList.contains('alternato')) {
            article.appendChild(orariDiv);
        } else {
            article.querySelector('.corso-descrizione').appendChild(orariDiv);
        }
        button.textContent = 'torna alla descrizione ➥';
    }
}
