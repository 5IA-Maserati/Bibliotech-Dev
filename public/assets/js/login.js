document.getElementById("login-form").addEventListener("submit", function (e) {
    e.preventDefault();

    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;

    if (username === "" || password === "") {
        alert("Compila tutti i campi");
        return;
    }

    // Simulazione login
    alert("Login effettuato con successo!");
    
    // Qui in futuro potrai collegare un backend
});
