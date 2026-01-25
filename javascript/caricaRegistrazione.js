window.onload = () => {
    const form = document.querySelector('.form-registrazione');
    
    if (form) {
        const button = form.querySelector('.btn-invia');
        const feedback = button.nextElementSibling;
        
        form.addEventListener('submit', (event) => {
            event.preventDefault();

            const formData = new FormData(form);

            fetch('registrati.php', {
                method: 'POST',
                body: formData
            }).then((response) => {
                if (response.ok) {
                    window.location.href = 'login.html';
                } else {
                    response.text().then(text => feedback.textContent = text);
                }
            });
        });
    }
};