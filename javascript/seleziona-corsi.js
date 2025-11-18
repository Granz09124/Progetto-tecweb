const corsiDettagli = {
    arrampicata: {
        titolo: "Arrampicata",
        descrizione: "Scala le nostre pareti di arrampicata con istruttori esperti. Perfetto per tutti i livelli, dal principiante all'esperto.",
        orari: "Lunedì, Mercoledì, Venerdì: 18:00 - 20:00",
        durata: "90 minuti per sessione",
        prezzo: "€ 69,90/mese",
        include: [
            "Attrezzatura inclusa (imbragatura, scarpe)",
            "Istruttore certificato",
            "3 sessioni a settimana",
            "Accesso libero alle pareti nei weekend"
        ]
    },
    yoga: {
        titolo: "Yoga e Meditazione",
        descrizione: "Trova il tuo equilibrio interiore con le nostre sessioni di yoga e meditazione guidate.",
        orari: "Martedì, Giovedì: 7:00 - 8:30 | Sabato: 10:00 - 11:30",
        durata: "90 minuti per sessione",
        prezzo: "€ 59,90/mese",
        include: [
            "Tappetino e cuscino inclusi",
            "Sessioni per tutti i livelli",
            "3 sessioni a settimana",
            "Accesso a workshop mensili gratuiti"
        ]
    },
    pilates: {
        titolo: "Pilates",
        descrizione: "Rafforza il tuo core e migliora la postura con il metodo Pilates.",
        orari: "Lunedì, Mercoledì, Venerdì: 19:00 - 20:00",
        durata: "60 minuti per sessione",
        prezzo: "€ 54,90/mese",
        include: [
            "Tappetino incluso",
            "Piccoli gruppi (max 12 persone)",
            "3 sessioni a settimana",
            "Valutazione posturale iniziale"
        ]
    },
    crossfit: {
        titolo: "Crossfit",
        descrizione: "Allenamento funzionale ad alta intensità per costruire forza, resistenza e agilità.",
        orari: "Tutti i giorni: 6:00 - 7:00 | 18:00 - 19:00 | 20:00 - 21:00",
        durata: "60 minuti per sessione",
        prezzo: "€ 89,90/mese",
        include: [
            "Sessioni illimitate",
            "Coach certificati Crossfit",
            "WOD personalizzabili",
            "Community attiva e sfide mensili"
        ]
    },
    fitbox: {
        titolo: "Fit Box",
        descrizione: "Combina tecniche di boxe con fitness ad alta intensità per un allenamento completo.",
        orari: "Martedì, Giovedì, Sabato: 19:00 - 20:00",
        durata: "60 minuti per sessione",
        prezzo: "€ 64,90/mese",
        include: [
            "Guanti e bendaggi inclusi",
            "3 sessioni a settimana",
            "Allenamento cardio e forza",
            "Tecnica e sparring controllato"
        ]
    },
    zumba: {
        titolo: "Zumba",
        descrizione: "Balla e brucia calorie con i ritmi latinoamericani della Zumba!",
        orari: "Lunedì, Mercoledì, Venerdì: 18:30 - 19:30",
        durata: "60 minuti per sessione",
        prezzo: "€ 49,90/mese",
        include: [
            "3 sessioni a settimana",
            "Coreografie sempre nuove",
            "Atmosfera divertente e inclusiva",
            "Adatto a tutti i livelli"
        ]
    }
};

function mostraCorso(corso) {
    const dettagliDiv = document.getElementById('corso-dettagli');
    
    if (!corso || !corsiDettagli[corso]) {
        dettagliDiv.innerHTML = '<p>Seleziona un corso dal menu per visualizzare i dettagli.</p>';
        return;
    }
    
    const dettagli = corsiDettagli[corso];
    
    dettagliDiv.innerHTML = `
        <h3>${dettagli.titolo}</h3>
        <p>${dettagli.descrizione}</p>
        <p><strong>Prezzo: ${dettagli.prezzo}</strong></p>
        <p><strong>Orari:</strong> ${dettagli.orari}</p>
        <p><strong>Durata:</strong> ${dettagli.durata}</p>
        <h4>Include:</h4>
        <ul>
            ${dettagli.include.map(item => `<li>${item}</li>`).join('')}
        </ul>
    `;
}