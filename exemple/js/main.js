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
    if (row.id == "") {
        var cellStateDate = row.insertCell(4);
        cellStateDate.innerHTML = ticket.state["libEtatTicket"];
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
    console.log("format ticket to form")
    for (let d in data[0]) {
        console.log(d);
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
//Fonctionn liées aux vues
const handleRole = (role) => {
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
                document.components["TypeTicket"].innerHTML += `<option value=${el.refTypeTicket}>${el.libTypeTicket}</option>`;
            });
        }, (err) => {
            console.error(err);
        });

        getService(token, null, (data) => {
            data.forEach(el => {
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
            loadTypeAndService();
            break;
    }
};
function LoadMainView(token,role,user) {
    switch (role) {
        case "dev":{
            break;
        }
        case "manager": {
            GetTicketFrom(token, user.service_idService, (data) => {
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
                            getTicket(JSON.parse(localStorage.getItem("user")).token,
                                ticket.ticket.uuidTicket,
                                (data) => {
                                    console.dir(data);
                                    document.querySelector("[name=idTicket]").innerText = data[0].uuidTicket;
                                    setTicket(document.forms[0], data)
                                    let dataTickets = JSON.parse(data[0].dataticket);
                                    document.components.dataTicket.innerHTML = "";
                                    Object.entries(dataTickets).forEach(el => {
                                        let li = document.createElement("li");
                                        let sname = document.createElement("span");
                                        sname.addEventListener("click", function () {
                                            let i = document.createElement("input");
                                            i.value = this.innerText;
                                            i.addEventListener("blur", function () {
                                                let s = document.createElement("span");
                                                s.innerText = this.value;
                                                this.replaceWith(s);
                                            })
                                            this.replaceWith(i);
                                        })
                                        sname.innerText = el[0];
                                        li.appendChild(sname);
                                        let svalue = document.createElement("span");
                                        svalue.addEventListener("click", function () {
                                            let i = document.createElement("input");
                                            i.value = this.innerText;
                                            i.addEventListener("blur", function () {
                                                let s = document.createElement("span");
                                                s.innerText = this.value;
                                                this.replaceWith(s);
                                            })
                                            this.replaceWith(i);
                                        })
                                        svalue.innerText = el[1];
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
                        var btnValid = document.createElement("button");
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
                        cellBtnValid.appendChild(btnValid);
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

