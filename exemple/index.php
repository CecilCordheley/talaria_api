<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="../public/mapper.js"></script>
    <script src="../public/talariaLib.js"></script>
    <script src="js/main.js"></script>
    <script src="js/menu.js"></script>
    <title>Exemple Talaria V2</title>
</head>
<body>
    <div class="container" id="MainView">
        <header>
            <h1>Model d'application Talaria</h1>
        </header>
        <menu class="list-group">
            <a href="#" id="disconectBtn" class="list-group-item list-group-item-action">Déconnexion</a>
        </menu>
        <main id="MainActivity">
            <div class="row">
                <div class="col-6 offset-2">
                     <h2>Votre groupe est déjà inscit</h2>
                    <div class="mb-3">
                        <label for="Mail_Connexion" class="form-label">Mail</label>
                        <input type="mail" id="Mail_Connexion" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="Mdp_Connexion" class="form-label">Mot de passe</label>
                    <input type="password" id="Mdp_Connexion" class="form-control">
                </div>
                <button class="btn btn-primary" id="connexionTrigger">Connexion</button>
                <a href="./firstConnexion" class="btn btn-primary" >Première connexion</a>
                <a href="./newClient">Inscrire mon entreprise</a>
                <div class="mb-3 result">

                </div>
            </div>
        </div>
        </main>
        <console></console>
        <footer>
            copyright @CecilCordheley 2026
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script>
    setMapper();

    document.components.disconectBtn.addEventListener("click", function() {
        localStorage.removeItem("user");
        _alert("Vous êtes déconnecté", () => {
            navigator.reload();
        });
    });

    var token = localStorage.getItem("user") ? JSON.parse(localStorage.getItem("user")).token : "";

   

    if (!token) {
    document.getElementById("connexionTrigger").addEventListener("click", function() {
        const mdp = document.components["Mdp_Connexion"].value;
        const mail = document.components["Mail_Connexion"].value;

        connexion(mail, mdp, (data) => {
            localStorage.setItem("user", JSON.stringify(data));
            handleRole(data.role);
            token=data.token;
            generateMenu(data.role, document.querySelector("menu"));
            startTokenCheck(data.token);
        }, (err) => {
            document.querySelector(".result").innerText = "Une erreur s'est produite";
            console.error(err);
        });
    });
} else {
    const data = JSON.parse(localStorage.user);
    token = data.token;
    handleRole(data.role);
    generateMenu(data.role, document.querySelector("menu"));
    startTokenCheck(data.token);
}

function startTokenCheck(token) {
    const intervalCheck = setInterval(() => {
        checkToken(token, () => {
            document.console.innerText = "token still valid";
        }, (err) => {
            clearInterval(intervalCheck);
            _alert(err, () => {
                token = "";
                
                localStorage.removeItem("user");
                alert("Le token n'est plus valide. La page va se recharger.");
                location.reload();
            });
        });
    }, (60 * 1000) * 5);
}

    </script>
</body>
</html>