/**
 * Retrouve le ou les services
 * @param {string} token Token de session
 * @param {string} id UUID du service
 * @param {CallableFunction} success Fonction en cas de success
 * @param {CallableFunction} fail Fonction en cas d'échec
 */
async function getService(token, id = null, success, fail) {
    const myHeaders = new Headers();
    myHeaders.append("Authorization", "Bearer " + token);

    const requestOptions = {
        method: "GET",
        headers: myHeaders,
        redirect: "follow"
    };
    let url = "http://localhost/Talaria_API/async/service_getServices";
    if (id != null)
        url += "&idService=" + id;
    fetch(url, requestOptions)
        .then((response) => response.json())
        .then((result) => {
            if (result.status == "success") {
                success.call(this, result.data);
            } else {
                fail.call(this, result.message)
            }
        })
        .catch((error) => {
            fail.call(this, result.message)
        });

}
/**
 * Associe l'utilisateur avec un service
*  @param {string} token Token de session
 * @param {string} idUser uuid de l'utilisateur
 * @param {string} idService uuid du service
 * @param {CallableFunction} success fonction en cas de réussite
 * @param {CallableFunction} fail fonction en cas d'echec
 */
async function associateUserService(token, idUser, idService, success, fail) {
    const myHeaders = new Headers();
    myHeaders.append("Authorization", "Bearer " + token);

    const requestOptions = {
        method: "GET",
        headers: myHeaders,
        redirect: "follow"
    };

    fetch(`http://localhost/Talaria_API/async/users_associateService?idUser=${idUser}&idService=${idService}`, requestOptions)
        .then((response) => response.json())
        .then((result) => {
            if (result.status == "success") {
                success.call(this, result.data);
            } else {
                fail.call(this, result.message)
            }
        })
        .catch((error) => {
            fail.call(this, result.message)
        });
}
/**
 * Créer un utilisateur (Manager ou Agent)
 * @param {string} token token de session
 * @param {json} data Objet JSON des donnée de l'utilisateur
 * @param {CallableFunction} success Fonction en cas de réussite
 * @param {CallableFunction} fail Fonction en cas d'échec
 */
async function createUser(token, data, success, fail) {
    const myHeaders = new Headers();
    myHeaders.append("Content-Type", "application/x-www-form-urlencoded");
    myHeaders.append("Authorization", "Bearer " + token);

    const urlencoded = new URLSearchParams();
    urlencoded.append("nom", data["nom"]);
    urlencoded.append("prenom", data["prenom"]);
    urlencoded.append("mail", data["mail"]);
    urlencoded.append("mdp", data["mdp"]);

    const requestOptions = {
        method: "POST",
        headers: myHeaders,
        body: urlencoded,
        redirect: "follow"
    };

    fetch("http://localhost/Talaria_API/async/users_createAgent&type=" + data["type"], requestOptions)
        .then((response) => response.json())
        .then((result) => {
            if (result.status == "success") {
                success.call(this, result.data);
            } else {
                fail.call(this, result.message)
            }
        })
        .catch((error) => {
            fail.call(this, result.message)
        });
}
/**
 * 
 * @param {string} token Token de session
 * @param {json} data Objet contenant les données nécessaire à la création du service
 * @param {CallableFunction} success Fonction en cas de success
 * @param {CallableFunction} fail Fonction en cas d'échec
 */
async function createService(token, data, success, fail) {
    const myHeaders = new Headers();
    myHeaders.append("Content-Type", "application/x-www-form-urlencoded");
    myHeaders.append("Authorization", "Bearer " + token);

    const urlencoded = new URLSearchParams();
    urlencoded.append("nom", data["nom"]);
    urlencoded.append("desc", data["desc"]);
    urlencoded.append("entreprise", data["entreprise"]);

    const requestOptions = {
        method: "POST",
        headers: myHeaders,
        body: urlencoded,
        redirect: "follow"
    };

    fetch("http://localhost/Talaria_API/async/service_createService", requestOptions)
        .then((response) => response.json())
        .then((result) => {
            if (result.status == "success") {
                success.call(this, result.data);
            } else {
                fail.call(this, result.message)
            }
        })
        .catch((error) => {
            fail.call(this, result.message)
        });
}
/**
 * Récupère les informations d'un utilisateur
 * @param {string} uuid UUID de l'utilisateur 
 * @param {string} token Token de session 
 * @param {CallableFunction} success Fonction en cas de succes
 * @param {CallableFunction} fail Fonction en cas d'échec 
 */
async function getUser(uuid, token, success, fail) {
    const myHeaders = new Headers();
    myHeaders.append("Authorization", `Bearer ${token}`);

    const requestOptions = {
        method: "GET",
        headers: myHeaders,
        redirect: "follow"
    };

    fetch("http://localhost/Talaria_API/async/users_getUser&id="+uuid, requestOptions)
        .then((response) => response.json())
        .then((result) => {
            if (result.status == "success")
                success.call(this, result.data);
            else
                fail.call(this, result.message);
        })
        .catch((error) => {
            fail.call(this, error);
        });
}
/**
 * Tente de connecter un utilisateur
 * @param {string} mail Mail de l'utilisateur
 * @param {string} mdp Mot de passe
 * @param {CallableFunction} success Fonction en cas de succes
 * @param {CallableFunction} fail Fonction en cas d'échec
 */
async function connexion(mail, mdp, success, fail) {
    const myHeaders = new Headers();
    myHeaders.append("Content-Type", "application/x-www-form-urlencoded");

    const urlencoded = new URLSearchParams();
    urlencoded.append("mail", mail);
    urlencoded.append("secret", mdp);

    const requestOptions = {
        method: "POST",
        headers: myHeaders,
        body: urlencoded,
        redirect: "follow"
    };

    fetch("http://localhost/Talaria_API/async/users_connexion", requestOptions)
        .then((response) => response.json())
        .then((result) => {
            if (result.status == "success")
                success.call(this, result.data);
            else
                fail.call(this, result.message);
        })
        .catch((error) => {
            fail.call(this, error);
        });
}