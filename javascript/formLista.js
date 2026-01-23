function showAddForm(tipo) {
    document.getElementById('formTitle').textContent = 'Aggiungi Nuovo ' + tipo;
    
    const form = document.querySelector('#formSection form');
    if(form) form.reset();

    document.getElementById('userId').value = '';
    
    const btnDelete = document.getElementById('btnDelete');
    if(btnDelete) btnDelete.style.display = 'none';
    
    const formSection = document.getElementById('formSection');
    formSection.style.display = 'block';
    formSection.scrollIntoView({behavior: 'smooth'});
}

function editUser(id) {
    fetch('?edit=' + id)
        .then(response => response.json())
        .then(data => {
            if(data.error) {
                alert('Errore: ' + data.error);
                return;
            }

            document.getElementById('formTitle').textContent = 'Modifica: ' + data.nome + ' ' + (data.cognome || '');
            
            for (const key in data) {
                const input = document.getElementById(key);
                if (input) {
                    input.value = data[key];
                }
            }

            document.getElementById('userId').value = data.id_utente || id;

            const passInput = document.getElementById('password');
            if(passInput) passInput.value = '';

            const btnDelete = document.getElementById('btnDelete');
            if(btnDelete) btnDelete.style.display = 'inline-block';
            
            const formSection = document.getElementById('formSection');
            formSection.style.display = 'block';
            formSection.scrollIntoView({behavior: 'smooth'});
        })
        .catch(err => {
            console.error('Errore nel caricamento dati:', err);
            alert('Impossibile caricare i dati.');
        });
}

function deleteUser(tipo) {
    const id = document.getElementById('userId').value;
    const messaggio = "Sei sicuro di voler eliminare DEFINITIVAMENTE questo " + tipo + "?\nQuesta operazione non è annullabile.";
    
    if (id && confirm(messaggio)) {
        window.location.href = '?delete=' + id;
    }
}

function hideForm() {
    document.getElementById('formSection').style.display = 'none';
}