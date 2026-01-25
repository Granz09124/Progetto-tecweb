function mostraPassword() {
    const inputPass = document.getElementById("password");
    const btn = document.querySelector(".toggle-password");

    if (inputPass.type === "password") {
        inputPass.type = "text";
        btn.textContent = "Nascondi password";
        btn.setAttribute("aria-label", "Nascondi la password");
    } else {
        inputPass.type = "password";
        btn.textContent = "Mostra password";
        btn.setAttribute("aria-label", "Mostra la password in chiaro");
    }
}