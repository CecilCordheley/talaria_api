<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="../public/mapper.js"></script>
    <script src="../public/talariaLib.js"></script>
    <script src="js/IHM_talaria.js"></script>
    <script src="js/main.js"></script>
    
    <script src="js/menu.js"></script>
    <title>Exemple Talaria V2</title>
</head>
<body>
    <div class="container" id="MainView">
        <header>
            <h1>Model d'application Talaria <span id="nomEntreprise">NOM DE L'ENTREPRISE</span></h1>
            
        </header>
        <menu class="list-group">
            <span id="nomService">NOM DU SERVICE</span>
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
    var c_user=null;
    var mainService=null;
    document.components.disconectBtn.addEventListener("click", function() {
        localStorage.removeItem("user");
        _alert("Vous êtes déconnecté", () => {
             window.location.href="index.php";
        });
    });
    var token = localStorage.getItem("user") ? JSON.parse(localStorage.getItem("user")).token : "";

   

    if (!token) {
    window.location.href="index.php";
} else {
    const data = JSON.parse(localStorage.user);
    token = data.token;
    handleRole(data.role,async ()=>{
                await loadUser();
            });
    generateMenu(data.role, document.querySelector("menu"));
    startTokenCheck(data.token);
}
//Vérifie le contenu de la console pour voir si le token est encore valide, si une erreur est détectée, le token est supprimé et l'utilisateur est redirigé vers la page de connexion
document.console.innerText = "Vérification du token en cours...";
//Si document.console change, cela signifie que le token est encore valide, sinon une erreur est survenue et le token a été supprimé
let observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
        if (mutation.type === "childList") {
            console.log("Token check: " + document.console.innerText);
            if(document.console.innerText.includes("Invalid token") || document.console.innerText.includes("Token is invalid") || document.console.innerText.includes("Token has expired")){
            localStorage.removeItem("user");    
            window.location.href="index.php";
            }
        }
    });
});
observer.observe(document.console, { childList: true });
document.console.addEventListener("change", function() {
    console.log("Token check: " + document.console.innerText);
});


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