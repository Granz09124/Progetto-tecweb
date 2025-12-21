/* toggle-corsi.js */
const corsiData = {
    arrampicata: {
        mensile: { prezzo: "€ 69,90/mese", include: ["Attrezzatura inclusa", "Istruttore certificato", "3 sessioni a settimana", "Accesso weekend"] },
        trimestrale: { prezzo: "€ 189,00", include: ["Vantaggi mensile", "Risparmio 10%", "Validità 3 mesi", "1 sessione extra"] },
        semestrale: { prezzo: "€ 359,00", include: ["Vantaggi mensile", "Risparmio 14%", "Validità 6 mesi", "Workshop mensile"] },
        annuale: { prezzo: "€ 671,00", include: ["Vantaggi mensile", "Risparmio 20%", "Validità 12 mesi", "Corso avanzato"] }
    },
    yoga: {
        mensile: { prezzo: "€ 59,90/mese", include: ["Tappetino incluso", "Tutti i livelli", "3 sessioni a settimana", "Workshop mensili"] },
        trimestrale: { prezzo: "€ 161,70", include: ["Vantaggi mensile", "Risparmio 10%", "Validità 3 mesi", "1 lezione privata"] },
        semestrale: { prezzo: "€ 305,40", include: ["Vantaggi mensile", "Risparmio 15%", "Validità 6 mesi", "2 lezioni private"] },
        annuale: { prezzo: "€ 574,80", include: ["Vantaggi mensile", "Risparmio 20%", "Validità 12 mesi", "Ritiro yoga gratuito"] }
    },
    pilates: {
        mensile: { prezzo: "€ 54,90/mese", include: ["Tappetino incluso", "Piccoli gruppi", "3 sessioni a settimana", "Valutazione posturale"] },
        trimestrale: { prezzo: "€ 148,23", include: ["Vantaggi mensile", "Risparmio 10%", "Validità 3 mesi", "Sessione stretching"] },
        semestrale: { prezzo: "€ 280,26", include: ["Vantaggi mensile", "Risparmio 15%", "Validità 6 mesi", "Valutazione trimestrale"] },
        annuale: { prezzo: "€ 527,04", include: ["Vantaggi mensile", "Risparmio 20%", "Validità 12 mesi", "Piano personalizzato"] }
    },
    crossfit: {
        mensile: { prezzo: "€ 89,90/mese", include: ["Sessioni illimitate", "Coach certificati", "WOD personalizzabili", "Community attiva"] },
        trimestrale: { prezzo: "€ 242,73", include: ["Vantaggi mensile", "Risparmio 10%", "Validità 3 mesi", "1 sessione PT"] },
        semestrale: { prezzo: "€ 458,31", include: ["Vantaggi mensile", "Risparmio 15%", "Validità 6 mesi", "3 sessioni PT"] },
        annuale: { prezzo: "€ 862,56", include: ["Vantaggi mensile", "Risparmio 20%", "Validità 12 mesi", "Piano alimentare"] }
    },
    fitbox: {
        mensile: { prezzo: "€ 64,90/mese", include: ["Guanti inclusi", "3 sessioni a settimana", "Cardio e forza", "Sparring controllato"] },
        trimestrale: { prezzo: "€ 175,23", include: ["Vantaggi mensile", "Risparmio 10%", "Validità 3 mesi", "1 lezione privata"] },
        semestrale: { prezzo: "€ 331,17", include: ["Vantaggi mensile", "Risparmio 15%", "Validità 6 mesi", "2 lezioni private"] },
        annuale: { prezzo: "€ 623,04", include: ["Vantaggi mensile", "Risparmio 20%", "Validità 12 mesi", "Kit attrezzatura"] }
    },
    zumba: {
        mensile: { prezzo: "€ 49,90/mese", include: ["3 sessioni a settimana", "Coreografie nuove", "Divertente", "Tutti i livelli"] },
        trimestrale: { prezzo: "€ 134,73", include: ["Vantaggi mensile", "Risparmio 10%", "Validità 3 mesi", "Acqua omaggio"] },
        semestrale: { prezzo: "€ 254,49", include: ["Vantaggi mensile", "Risparmio 15%", "Validità 6 mesi", "T-shirt omaggio"] },
        annuale: { prezzo: "€ 478,80", include: ["Vantaggi mensile", "Risparmio 20%", "Validità 12 mesi", "Kit completo"] }
    }
};

function cambiaCorso(corsoSelezionato) {
    const container = document.getElementById('corsi-abbonamenti');
    
    if (!corsoSelezionato || !corsiData[corsoSelezionato]) {
        container.innerHTML = '<p class="messaggio-selezione">Seleziona un corso dal menu per visualizzare gli abbonamenti disponibili.</p>';
        return;
    }
    
    const corso = corsiData[corsoSelezionato];
    const durate = ['mensile', 'trimestrale', 'semestrale', 'annuale']; // Chiavi dell'oggetto
    
    // Genera l'HTML dinamicamente con .map()
    container.innerHTML = durate.map((durata, index) => {
        const info = corso[durata];
        const titolo = durata.charAt(0).toUpperCase() + durata.slice(1); // "mensile" -> "Mensile"
        const activeClass = index === 0 ? 'active' : ''; // Il primo è attivo di default
        
        return `
        <div class="abbonamento-item ${activeClass}">
            <div class="abbonamento-header" onclick="toggleAbbonamento(this)">
                <h3>${titolo}</h3>
                <svg class="toggle-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
            <div class="abbonamento-content">
                <p><strong>${info.prezzo}</strong></p>
                <ul>
                    ${info.include.map(item => `<li>${item}</li>`).join('')}
                </ul>
            </div>
        </div>`;
    }).join('');
}

function toggleAbbonamento(header) {
    const abbonamentoItem = header.parentElement;
    const isClosed = !abbonamentoItem.classList.contains('active');
    
    // Chiudi tutti gli altri nello stesso contenitore
    const parent = abbonamentoItem.parentElement;
    parent.querySelectorAll('.abbonamento-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Se era chiuso, aprilo (comportamento accordion classico)
    if (isClosed) {
        abbonamentoItem.classList.add('active');
    }
}