window.onload = () => {
    const formContainers = document.getElementsByClassName('form-contatto');
    const forms = [];
    for (const container of formContainers) {
        forms.push(container.getElementsByTagName('form')[0]);
    }

    for (const form of forms) {
        form.addEventListener('submit', (event) => {
            event.preventDefault();

            const formData = new FormData(form);
            const feedback = form.nextSibling;

            fetch('upload-messaggio.php', {
                method: 'POST',
                body: formData
            }).then((response) => {
                if (response.ok) {
                    feedback.textContent = "Messaggio inviato.";
                } else {
                    response.text().then(text => feedback.textContent = text);
                }
            })
        })
    }
}