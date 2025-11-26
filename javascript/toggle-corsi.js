const corsiData = {
    arrampicata: {
        mensile: {
            prezzo: "€ 69,90/mese",
            include: [
                "Attrezzatura inclusa (imbragatura, scarpe)",
                "Istruttore certificato",
                "3 sessioni a settimana",
                "Accesso libero alle pareti nei weekend"
            ]
        },
        trimestrale: {
            prezzo: "€ 189,00 (€ 63,00/mese)",
            include: [
                "Tutti i vantaggi del mensile",
                "Risparmio del 10%",
                "Validità 3 mesi",
                "1 sessione extra gratuita"
            ]
        },
        semestrale: {
            prezzo: "€ 359,00 (€ 59,83/mese)",
            include: [
                "Tutti i vantaggi del mensile",
                "Risparmio del 14%",
                "Validità 6 mesi",
                "Workshop gratuito mensile"
            ]
        },
        annuale: {
            prezzo: "€ 671,00 (€ 55,92/mese)",
            include: [
                "Tutti i vantaggi del mensile",
                "Risparmio del 20%",
                "Validità 12 mesi",
                "Corso avanzato gratuito"
            ]
        }
    },
    yoga: {
        mensile: {
            prezzo: "€ 59,90/mese",
            include: [
                "Tappetino e cuscino inclusi",
                "Sessioni per tutti i livelli",
                "3 sessioni a settimana",
                "Accesso a workshop mensili gratuiti"
            ]
        },
        trimestrale: {
            prezzo: "€ 161,70 (€ 53,90/mese)",
            include: [
                "Tutti i vantaggi del mensile",
                "Risparmio del 10%",
                "Validità 3 mesi",
                "1 lezione privata gratuita"
            ]
        },
        semestrale: {
            prezzo: "€ 305,40 (€ 50,90/mese)",
            include: [
                "Tutti i vantaggi del mensile",
                "Risparmio del 15%",
                "Validità 6 mesi",
                "2 lezioni private gratuite"
            ]
        },
        annuale: {
            prezzo: "€ 574,80 (€ 47,90/mese)",
            include: [
                "Tutti i vantaggi del mensile",
                "Risparmio del 20%",
                "Validità 12 mesi",
                "Ritiro yoga gratuito"
            ]
        }
    },
    pilates: {
        mensile: {
            prezzo: "€ 54,90/mese",
            include: [
                "Tappetino incluso",
                "Piccoli gruppi (max 12 persone)",
                "3 sessioni a settimana",
                "Valutazione posturale iniziale"
            ]
        },
        trimestrale: {
            prezzo: "€ 148,23 (€ 49,41/mese)",
            include: [
                "Tutti i vantaggi del mensile",
                "Risparmio del 10%",
                "Validità 3 mesi",
                "1 sessione di stretching gratuita"
            ]
        },
        semestrale: {
            prezzo: "€ 280,26 (€ 46,71/mese)",
            include: [
                "Tutti i vantaggi del mensile",
                "Risparmio del 15%",
                "Validità 6 mesi",
                "Valutazione posturale trimestrale"
            ]
        },
        annuale: {
            prezzo: "€ 527,04 (€ 43,92/mese)",
            include: [
                "Tutti i vantaggi del mensile",
                "Risparmio del 20%",
                "Validità 12 mesi",
                "Piano personalizzato completo"
            ]
        }
    },
    crossfit: {
        mensile: {
            prezzo: "€ 89,90/mese",
            include: [
                "Sessioni illimitate",
                "Coach certificati Crossfit",
                "WOD personalizzabili",
                "Community attiva e sfide mensili"
            ]
        },
        trimestrale: {
            prezzo: "€ 242,73 (€ 80,91/mese)",
            include: [
                "Tutti i vantaggi del mensile",
                "Risparmio del 10%",
                "Validità 3 mesi",
                "1 sessione PT gratuita"
            ]
        },
        semestrale: {
            prezzo: "€ 458,31 (€ 76,39/mese)",
            include: [
                "Tutti i vantaggi del mensile",
                "Risparmio del 15%",
                "Validità 6 mesi",
                "3 sessioni PT gratuite"
            ]
        },
        annuale: {
            prezzo: "€ 862,56 (€ 71,88/mese)",
            include: [
                "Tutti i vantaggi del mensile",
                "Risparmio del 20%",
                "Validità 12 mesi",
                "Piano alimentare personalizzato"
            ]
        }
    },
    fitbox: {
        mensile: {
            prezzo: "€ 64,90/mese",
            include: [
                "Guanti e bendaggi inclusi",
                "3 sessioni a settimana",
                "Allenamento cardio e forza",
                "Tecnica e sparring controllato"
            ]
        },
        trimestrale: {
            prezzo: "€ 175,23 (€ 58,41/mese)",
            include: [
                "Tutti i vantaggi del mensile",
                "Risparmio del 10%",
                "Validità 3 mesi",
                "1 lezione privata gratuita"
            ]
        },
        semestrale: {
            prezzo: "€ 331,17 (€ 55,20/mese)",
            include: [
                "Tutti i vantaggi del mensile",
                "Risparmio del 15%",
                "Validità 6 mesi",
                "2 lezioni private gratuite"
            ]
        },
        annuale: {
            prezzo: "€ 623,04 (€ 51,92/mese)",
            include: [
                "Tutti i vantaggi del mensile",
                "Risparmio del 20%",
                "Validità 12 mesi",
                "Kit attrezzatura professionale"
            ]
        }
    },
    zumba: {
        mensile: {
            prezzo: "€ 49,90/mese",
            include: [
                "3 sessioni a settimana",
                "Coreografie sempre nuove",
                "Atmosfera divertente e inclusiva",
                "Adatto a tutti i livelli"
            ]
        },
        trimestrale: {
            prezzo: "€ 134,73 (€ 44,91/mese)",
            include: [
                "Tutti i vantaggi del mensile",
                "Risparmio del 10%",
                "Validità 3 mesi",
                "Bottiglia d'acqua in omaggio"
            ]
        },
        semestrale: {
            prezzo: "€ 254,49 (€ 42,42/mese)",
            include: [
                "Tutti i vantaggi del mensile",
                "Risparmio del 15%",
                "Validità 6 mesi",
                "T-shirt Zumba in omaggio"
            ]
        },
        annuale: {
            prezzo: "€ 478,80 (€ 39,90/mese)",
            include: [
                "Tutti i vantaggi del mensile",
                "Risparmio del 20%",
                "Validità 12 mesi",
                "Kit completo Zumba"
            ]
        }
    }
};

function cambiaCorso(corsoSelezionato) {
    const container = document.getElementById('corsi-abbonamenti');
    
    if (!corsoSelezionato || !corsiData[corsoSelezionato]) {
        container.innerHTML = '<p class="messaggio-selezione">Seleziona un corso dal menu per visualizzare gli abbonamenti disponibili.</p>';
        return;
    }
    
    const corso = corsiData[corsoSelezionato];
    
    container.innerHTML = `
        <div class="abbonamento-item">
            <div class="abbonamento-header" onclick="toggleAbbonamento(this)">
                <h3>Mensile</h3>
                <svg class="toggle-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
            <div class="abbonamento-content">
                <p><strong>${corso.mensile.prezzo}</strong></p>
                <ul>
                    ${corso.mensile.include.map(item => `<li>${item}</li>`).join('')}
                </ul>
            </div>
        </div>
        
        <div class="abbonamento-item">
            <div class="abbonamento-header" onclick="toggleAbbonamento(this)">
                <h3>Trimestrale</h3>
                <svg class="toggle-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
            <div class="abbonamento-content">
                <p><strong>${corso.trimestrale.prezzo}</strong></p>
                <ul>
                    ${corso.trimestrale.include.map(item => `<li>${item}</li>`).join('')}
                </ul>
            </div>
        </div>
        
        <div class="abbonamento-item">
            <div class="abbonamento-header" onclick="toggleAbbonamento(this)">
                <h3>Semestrale</h3>
                <svg class="toggle-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
            <div class="abbonamento-content">
                <p><strong>${corso.semestrale.prezzo}</strong></p>
                <ul>
                    ${corso.semestrale.include.map(item => `<li>${item}</li>`).join('')}
                </ul>
            </div>
        </div>
        
        <div class="abbonamento-item">
            <div class="abbonamento-header" onclick="toggleAbbonamento(this)">
                <h3>Annuale</h3>
                <svg class="toggle-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
            <div class="abbonamento-content">
                <p><strong>${corso.annuale.prezzo}</strong></p>
                <ul>
                    ${corso.annuale.include.map(item => `<li>${item}</li>`).join('')}
                </ul>
            </div>
        </div>
    `;
}

function toggleAbbonamento(header) {
    const abbonamentoItem = header.parentElement;
    const wasActive = abbonamentoItem.classList.contains('active');
    
    const parent = abbonamentoItem.parentElement;
    parent.querySelectorAll('.abbonamento-item').forEach(item => {
        item.classList.remove('active');
    });
    
    if (!wasActive) {
        abbonamentoItem.classList.add('active');
    }
}

function togglePersonalTrainer() {
    const checkbox = document.getElementById('personal-trainer-checkbox');
    
    if (checkbox.checked) {
        window.location.href = 'personaltrainers.html';
    }
}