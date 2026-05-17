Array.prototype.last = function () {
    return this[this.length - 1];
}
function _alert(_msg, callback) {
    var _container = document.createElement("div");
    _container.classList.add("overlay");
    _container.addEventListener("click", function () {
        this.remove();
        if (callback != undefined)
            callback.call();

    })
    var msg = document.createElement("p");
    msg.classList.add("alert_msg");
    msg.innerHTML = _msg
    _container.appendChild(msg);
    document.body.prepend(_container);
}
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
function updateListTicket(row, ticket) {
    var cellID = row.insertCell(0);
    cellID.innerHTML = ticket.ticket.uuidTicket;
    var cellDate = row.insertCell(1);
    cellDate.innerHTML = ticket.ticket.dateTicket;
    var cellUser = row.insertCell(2);
    cellUser.innerHTML = ticket.ticket["Auteur"];
    var cellDest = row.insertCell(3);
    if (row.id == "")
        cellDest.innerHTML = ticket.ticket["service"];
    else {
        getUser(ticket.ticket.Auteur, token, (data) => {
            cellDest.innerHTML = data.uuidService;
        })
    }
    console.log("last state",ticket.state.last()["state"]);
    if (row.id == "") {
        var cellStateDate = row.insertCell(4);
        cellStateDate.innerHTML = ticket.state.last()["state"]["libEtatTicket"];
    }

}
function formatDataSpan(list) {
    let lisItems = list.querySelectorAll("li");
    lisItems.forEach(item => {
        let s = item.querySelectorAll("span");
        s[0].addEventListener("click", function () {
            let i = document.createElement("input");
            i.type = "text";
            i.value = this.innerText;
            i.addEventListener("blur", function () {
                let s = document.createElement("span");
                s.innerText = this.value;
                this.replaceWith(s);
                formatDataSpan(list)
            })
            this.replaceWith(i);
        });
        s[1].addEventListener("click", function () {
            let i = document.createElement("input");
            i.type = "text";
            i.value = this.innerText;
            i.addEventListener("blur", function () {
                let s = document.createElement("span");
                s.innerText = this.value;
                this.replaceWith(s);
                formatDataSpan(list)
            })
            this.replaceWith(i);
        })
    })
}

function setTicket(form, data) {
    console.log("format ticket form")
    for (let d in data[0]) {
        //  console.log(d);
        let c = document.querySelector(`[manage-ticket='${d}']`);
        if (c != undefined)
            c.value = data[0][d];
    }
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
async function displayModalTicket(ticket, action = "see", labels = { "service": "UpdateService", "categorie": "TypeTicket",data:"dataTicket" }) {
   console.log("Tiket",ticket);
    loadServiceAndCategorie(document.components[labels.service], document.components[labels.categorie]);
    getTicket(token,
        ticket.uuidTicket,
        (data) => {
            if (action === "see") {
                if (document.components.ValidTicket)
                    document.components.ValidTicket.disabled = true;
                if (document.components.RejectTicket)
                    document.components.RejectTicket.disabled = true;
                if (document.components.UpdateTicket)
                    document.components.UpdateTicket.disabled = true;
            }
            if (document.components.ValidTicket)
                document.components.ValidTicket.removeAttribute("disable");
            //  console.dir(data);
            document.querySelector("[manage-ticket=uuidTicket]").innerText = data[0].uuidTicket;
            setTicket(document.forms[0], data)
            let s = data[0].states[data[0].states.length - 1];
            data[0].states.forEach(state=>{
                let li = document.createElement("li");
                li.innerHTML=`<span>${state.EtatTicket}</span><span>${state.dateEtatTicket}</span><span>${state.comment}</span>`;
                document.components[labels.states].appendChild(li);
            });
            if (s.refEtatTicket === "REJE-K1E32") {
                //Disable le bouton de validation 
                if (document.components.ValidTicket)
                    document.components.ValidTicket.setAttribute("disable", "disable");
            }
            let dataTickets = JSON.parse(data[0].dataticket);
            document.components[labels.data].innerHTML = "";
            Object.entries(dataTickets).forEach(el => {
                let li = document.createElement("li");
                let sname = createEditableSpan(el[0]);
                let svalue = createEditableSpan(el[1]);

                li.appendChild(sname);
                li.appendChild(svalue);
                //  li.innerHTML += `<span>${el[1]}</span>`;
                document.components[labels.data].appendChild(li);
            });
        }, (err) => {
            console.error(err);
            document.console.innerText = err;
        })

}
//Fonctionn liées aux vues
async function handleRole(role) {
    getUser(JSON.parse(localStorage.user).user_id, token, (user_data) => {
        c_user = user_data;
        mainService = user_data.service_idService;
        getService(token, mainService, (data) => {
            document.components.nomService.innerText = data.nomService;
        }, (err) => {
            document.console.innerText = err;
        })
        entreprise = user_data.Entreprise[0].idEntreprise;
        document.components.nomEntreprise.innerText = user_data.Entreprise[0].nomEntreprise;
    }, (err) => {
        document.console.innerText = err;
    });
    const loadActivityView = (viewPath, handleActity) => {
        loadView(viewPath, () => {
            if (handleActity)
                handleActity.call();
        }, (err) => {
            document.console.innerText = err;
        });
    };

    const loadTypeAndService = () => {
        getTypeTicket(token, (data) => {
            data.forEach(el => {
                if (document.components["TypeTicket"])
                    document.components["TypeTicket"].innerHTML += `<option value=${el.refTypeTicket}>${el.libTypeTicket}</option>`;
            });
        }, (err) => {
            console.error(err);
        });

        getService(token, null, (data) => {
            if (data.length == 0) {
                if (document.components["Service"])
                    document.components["Service"].innerHTML = "<option value=''>Aucun service disponible</option>";
                return;
            }
            data.forEach(el => {
                if (document.components["Service"])
                    document.components["Service"].innerHTML += `<option value=${el.uuidService}>${el.nomService}</option>`;
            });
        }, (err) => {
            console.error(err);
            if (err === "Invalid token") {
                alert("Votre token n'est plus valide");
                localStorage.removeItem("user");
                token = "";
                navigator.Location.reload();
            }
        });
    };

    switch (role) {
        case "dev":
            loadActivityView("../async/view_devActivity");
            break;
        case "admin":
            loadActivityView("../async/view_adminActivity");
            break;
        case "agent":
        case "manager":
            loadActivityView(role === "agent" ? "../async/view_agentActivity" : "../async/view_managerActivity");
            
            break;
    }
};
function LoadMainView(token, role, user) {

    switch (role) {
        case "dev": {
            break;
        }
        case "manager": {
            getTicketFrom(token, user.service_idService, (data) => {
                document.components["listTicket"].innerHTML="<tr></tr>";
                if (data.length > 0)
                    data.forEach(ticket => {

                        var row = document.components["listTicket"].insertRow(1);
                        updateListTicket(row, ticket)
                        var cellSeeTicket = row.insertCell(5);
                        let btnSee = document.createElement("button");
                        btnSee.classList.add("btn");
                        btnSee.classList.add("btn-primary");
                        btnSee.setAttribute("data-bs-toggle", "modal");
                        btnSee.setAttribute("data-bs-target", "#updateTicket");
                        btnSee.innerHTML = "Voir";
                        btnSee?.addEventListener("click", function () {
                            document.components.ValidTicket.disabled = false;
                            document.components.RejectTicket.disabled = false;
                            document.querySelector('.commentSection').innerHTML = "";
                            getTicket(JSON.parse(localStorage.getItem("user")).token,
                                ticket.ticket.uuidTicket,
                                (data) => {

                                    document.querySelector("[name=idTicket]").innerText = data[0].uuidTicket;
                                    setTicket(document.forms[0], data);
                                    let s = data[0].states[data[0].states.length - 1];
                                    console.log(s);
                                    if (s.EtatTicket === 4) {
                                        //Disable le bouton de validation 
                                        document.components.ValidTicket.disabled = true;
                                        document.components.RejectTicket.disabled = true;
                                        document.querySelector('.commentSection').innerHTML = s.comment
                                    } else {
                                        document.components.RejectTicket.disabled = true;
                                    }
                                    let dataTickets = JSON.parse(data[0].dataticket);
                                    document.components.dataTicket.innerHTML = "";
                                    Object.entries(dataTickets).forEach(el => {
                                        let li = document.createElement("li");
                                        let sname = createEditableSpan(el[0]);
                                        let svalue = createEditableSpan(el[1]);

                                        li.appendChild(sname);
                                        li.appendChild(svalue);
                                        //  li.innerHTML += `<span>${el[1]}</span>`;
                                        document.components.dataTicket.appendChild(li);
                                    });
                                }, (err) => {
                                    console.error(err);
                                })
                        });
                        cellSeeTicket.appendChild(btnSee)
                    });
            }, (err) => {
                console.error(err);
                if (err === "Invalid token") {
                    document.console.innerText = "Votre token n'est plus valide";
                    localStorage.removeItem("user");
                    navigator.reload();
                }
            });
            getTicketTo(token, user.service_idService, (data) => {
                console.log("tiket_to", data);
                data.forEach(ticket => {
                    if (ticket.state.idEtatTicket == 2) {
                        var row = document.components["tblTicketTo"].insertRow(1);
                        row.id = "TicketToTable";
                        updateListTicket(row, ticket)
                        var cellAgent = row.insertCell(4);
                        let selectUser = document.createElement("select");
                        serviceUser.forEach(u => {
                            console.log(u.uuidUser)
                            if (u.uuiUser != JSON.parse(localStorage.getItem("user")).user_id) {
                                let opt = document.createElement("option");
                                opt.value = u.uuidUser;
                                opt.innerHTML = u.nomUser + " " + u.prenomUser;
                                selectUser.appendChild(opt);
                            }
                        });
                        cellAgent.appendChild(selectUser);
                        var cellBtnValid = row.insertCell(5);
                        /* var btnValid = document.createElement("button");
                         btnValid.addEventListener("click", function () {
                             alert(`Ticket ${ticket.ticket.uuidTicket} assign to ${selectUser.value}`);
                             AssignTicket(token, ticket.ticket.uuidTicket, selectUser.value, (data) => {
                                 changeStatut(token, ticket.ticket.uuidTicket, "PEND-8IO36", "Assigné par " + JSON.parse(localStorage.getItem("user")).user_id + " à " + selectUser.value, (data) => {
                                     alert("Ticket assigné !");
                                 }, (err) => {
                                     document.console.innerText += err;
                                     console.error(err);
                                 })
                             }, (err) => {
                                 document.console.innerText += err;
                                 console.error(err);
                             })
                         });
                         btnValid.classList.add("btn");
                         btnValid.classList.add("btn-success");
                         btnValid.textContent = "Valider";
                         cellBtnValid.appendChild(btnValid);*/
                    }
                });
            }, (err) => {
                document.console.innerText += err;
                console.error(err);
            })
            break;
        }
    }
}

