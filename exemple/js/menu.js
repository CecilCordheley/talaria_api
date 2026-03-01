function generateMenu(profile, container) {
    let links = [];
    links.push({
        "label": "voir le compte",
        "fnc": () => {
            loadView("../async/view_account", () => {

            }, (err) => {
                document.console.innerText = err;
            });
        }
    })
    switch (profile) {
        case "manager": {
            links.push(...[{
                "label": "Gérer les agents",
                "fnc": function(){
                    
                }
            }, {
                "label": "Gerer les tickets",
                "fnc":function(){ manageTicket();}
            },{
                "label":"main",
                "fnc":function(){
                    alert("retour sur la vue principale");
                    window.location.href="index.php";
                }
            }]);
            break;
        }
        case "admin": {
            break;
        }
    }
    links.forEach(el => {
        let l = document.createElement("a");
        l.innerHTML = el.label,
            ["list-group-item", "list-group-item-action"].forEach(c => {
                l.classList.add(c);
            })
        l.href = '#',
            l.addEventListener("click", function () {
                el.fnc();
                return false;
            });
        container.appendChild(l);
    })
}
function manageTicket() {
    alert("Gerer les ticket !");
}
function manageAgent() {
    alert("gerer les agents !");
}