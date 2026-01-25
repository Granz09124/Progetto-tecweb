window.onload = () => {
    const form = document.querySelector('.form-container form');
    
    if (form) {
        const button = form.querySelector('.btn-invia');
        const feedback = button.nextElementSibling;
        
        form.addEventListener('submit', (event) => {
            event.preventDefault();

            const formData = new FormData(form);

            fetch('login.php', {
                method: 'POST',
                body: formData
            }).then((response) => {
                response.text().then(text => {
                    if (response.ok) {
                        window.location.href = text;
                    } else {
                        feedback.textContent = text;
                    }
                });
            });
        });
    }
};