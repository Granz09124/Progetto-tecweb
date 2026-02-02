window.onload = () => {
  const form = document.getElementById('form-ricerca-istruttore');
  form.onsubmit = async (event) => {
    event.preventDefault();

    const formData = new FormData(event.target);
    const params = new URLSearchParams(formData).toString();
    const errorMsg = form.querySelector('span');
    const fetchUrl = `${window.location.pathname}?${params}`;

    try {
      fetch(fetchUrl, {
          method: "GET",
        })
        .then((response) => {
          if (response.ok) {
             response.text().then(resultsHtml => {
               document.body.innerHTML = resultsHtml
               document.getElementById('risultati-ricerca').focus();
               window.history.replaceState({ params }, '', fetchUrl);
             });
          }
          else {
            errorMsg.textContent = "Si è verificato un errore. Prova a ricaricare la pagina.";
            errorMsg.focus();
          }
        });
    } catch (error) {
      errorMsg.textContent = "Si è verificato un errore. Prova a ricaricare la pagina.";
      errorMsg.focus();
    }   
  }
}
