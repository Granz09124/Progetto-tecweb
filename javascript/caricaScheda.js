window.onload = () => {
    const forms = document.getElementsByClassName('upload-form');

    for (const form of forms) {
        form.addEventListener('submit', (event) => {
            event.preventDefault();

            const formData = new FormData(form);
            const feedback = form.nextSibling;
            const uploading = form.querySelector('[type="file"]');

            if (uploading.files[0].size > "1048576") {
                feedback.textContent = "La dimensione del file deve essere inferiore a 1MB";
                return;
            }

            fetch('upload-scheda.php', {
                method: 'POST',
                body: formData
            }).then((response) => {
                if (response.ok) {
                    feedback.textContent = "File caricato con successo.";
                    uploading.value = "";
                } else {
                    response.text().then(text => feedback.textContent = text);
                }
            })
        })
    }
}