window.onload = () => {
    const form = document.querySelector('.form-registrazione');
    
    if (form) {
        const button = form.querySelector('.btn-invia');
        const feedback = button.nextElementSibling;
        
        form.addEventListener('submit', (event) => {
            event.preventDefault();

            const formData = new FormData(form);

            fetch('/internal/registrati/processa-registrazione.php', {
                method: 'POST',
                body: formData
            }).then((response) => {
                response.text().then(text => {
                    if (response.ok) {
                        window.location.href = '/login.php';
                    } else {
                        feedback.textContent = text;
                    }
                });
            }).catch(error => {
                console.error('Errore fetch:', error);
            });
        });
    }
};