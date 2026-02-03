window.onload = () => {
    const form = document.querySelector('.form-container form');
    
    if (form) {
        const button = form.querySelector('.btn-invia');
        const feedback = button.nextElementSibling;
        
        form.addEventListener('submit', (event) => {
            event.preventDefault();

            const formData = new FormData(form);

            fetch(window.location.pathname, {
                method: 'POST',
                body: formData
            }).then((response) => {
                response.text().then(text => {
                    if (response.ok && text.trim() === "Success") {
                        window.location.href = 'area-personale.php';
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