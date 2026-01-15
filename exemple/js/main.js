function executeScript(container) {
    container.querySelectorAll("script").forEach(oldScript => {
        //  console.log("set New Script " + oldScript.textContent);
        const newScript = document.createElement("script");
        if (oldScript.src) {
            newScript.src = oldScript.src;
        } else {
            newScript.textContent = oldScript.textContent;
        }
        document.head.appendChild(newScript).remove(); // Évite les doublons
    });
}
function updateTicket(service){

}
async function loadView(view, onload, onFailed) {
    try {
        let token = JSON.parse(localStorage.getItem("user")).token;
        const response = await fetch(view, {
            method: "GET",
            headers: {
                "Authorization": 'Bearer : ' + token
            }
        });
        const result = await response.json();

        if (result.status === "success") {
            let mainInterface = document.getElementById("MainActivity");
            mainInterface.style.opacity = 0;
            setTimeout(() => {
                mainInterface.innerHTML = result.data;
                mainInterface.style.opacity = 1;
                executeScript(mainInterface);
                onload?.(); // si défini


            }, 1000);

        } else {
            if (result.code == "401") {
                _alert("Problème sur le token de connexion", function () {
                    window.location.href = "deconnexion";
                }, 1);
            }
            onFailed?.(result.message);
        }
    } catch (err) {
        onFailed?.(err);
    }
}