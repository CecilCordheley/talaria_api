function generateMenu(profile, container) {
    console.log(profile);
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
    switch (profile.toLowerCase()) {
        case "agent": {
            links.push(...[
                {
                    "label": "main",
                    "fnc": function () {
                        alert("retour sur la vue principale");
                        window.location.href = "main.php";
                    }
                }, {
                    "label": "Rechercher les tickets",
                    "fnc": function () { searchTicket(); }
                }
            ]);
            break;
        }
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
                "label": "kpi",
                "fnc": function () {
                    KPI_access("manager");
                }
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
            }, {
                "label": "KPI",
                "fnc": function () {
                    KPI_access("admin");
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
function KPI_access(role) {
    loadView("../async/view_KPI", () => {
        KPI.role=role;
        switch (role) {
            case "manager":{
                _alert("Vous avez accès au ticket émis par votre service");
                break;
            }
            case "admin": {
                _alert("En tant qu'admin vous avez accès à tout les tickets de tout les service");
                break;
            }
        }
    }, (err) => {
        console.error(err);
    });
}
function logAccess() {
    loadView("../async/view_logView", () => {
        console.log("log");
    }, (err) => {
        console.error(err);
    })
}
function searchTicket() {
    alert("rechercher les tickets !");
    loadView("../async/view_searchTicket", () => {
        console.log("search");
    }, (err) => {
        console.error(err);
    });
}
function manageTicket() {
    alert("Gerer les ticket !");
}
function manageAgent() {
    alert("gerer les agents !");
}