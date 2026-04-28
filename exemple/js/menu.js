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
        case "dev": {
            links.push(...[
                {
                    "label": "main",
                    "fnc": function () {
                        alert("retour sur la vue principale");
                        window.location.href = "main.php";
                    }
                }, {
                    "label": "voir les log",
                    "fnc": () => {
                        logAccess()
                    }
                }, {
                    "label": "Voir les statistiques",
                    "fnc": () => {
                        loadView("../async/view_statView", () => {
                            console.log("stat");
                        }, (err) => {
                            console.error(err);
                        })
                    }
                }
            ])
            break;
        }
        case "manager": {
            links.push(...[{
                "label": "Gérer les agents",
                "fnc": function () {

                }
            }, {
                "label": "Gerer les tickets",
                "fnc": function () { manageTicket(); }
            }, {
                "label": "main",
                "fnc": function () {
                    alert("retour sur la vue principale");
                    window.location.href = "main.php";
                }
            }]);
            break;
        }
        case "admin": {
            links.push(...[{
                "label": "main", fnc: function () {
                    _alert("retour à la vue principale", () => {
                        window.location.href = "main.php";
                    })
                }
            }]);
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
function logAccess() {
    loadView("../async/view_logView", () => {
        console.log("log");
    }, (err) => {
        console.error(err);
    })
}
function manageTicket() {
    alert("Gerer les ticket !");
}
function manageAgent() {
    alert("gerer les agents !");
}