function createEditableSpan(value) {
    const span = document.createElement("span");
    span.innerText = value;

    span.addEventListener("click", () => {
        const input = document.createElement("input");
        input.value = span.innerText;

        input.addEventListener("blur", () => {
            span.innerText = input.value;
            input.replaceWith(span);
        });

        span.replaceWith(input);
        input.focus();
    });

    return span;
}
function generateBtn(label, Sclasses = []) {
    let styleClass = ["btn"];
    styleClass.push(...Sclasses);
    let b = document.createElement("button");
    b.innerHTML = label;
    styleClass.forEach(c => { b.classList.add(c) });
    return b;
}
function renderKeyValueList(data, container) {
    container.innerHTML = "";

    Object.entries(data).forEach(([key, value]) => {
        const li = document.createElement("li");

        li.appendChild(createEditableSpan(key));
        li.appendChild(createEditableSpan(value));

        container.appendChild(li);
    });
}
/**
 * 
 * @param {HTMLButtonElement} button Bouton trigger
 * @param {HTMLInputElement} nameInput Champ text du nom de la valeur
 * @param {HTMLInputElement} valueInput Champ text de la valeur
 * @param {HTMLUListElement} targetList Liste des Data Enregistré
 */
function setupAddDataButton(button, nameInput, valueInput, targetList) {

    button.addEventListener("click", () => {

        if (!nameInput.value && !valueInput.value)
            return;

        const li = document.createElement("li");

        li.appendChild(createEditableSpan(nameInput.value));
        li.appendChild(createEditableSpan(valueInput.value));

        targetList.appendChild(li);

        nameInput.value = "";
        valueInput.value = "";
    });
}
function fillHTMLTableRow(htmlArray, data, fields) {
    let tr = document.createElement("tr");
    fields.forEach(d => {
        let cell = document.createElement("td");
        cell.innerText = data[d];
        tr.appendChild(cell);
    });
    htmlArray.appendChild(tr);
    return tr;
}
/**
     * Charger les services et les catégories de ticket dans les select du formulaire de mise à jour du ticket
     * @param {HTMLSelectElement} selectService 
     * @param {HTMLSelectElement} selectCategorie 
     */
function loadServiceAndCategorie(selectService, selectCategorie) {
    console.log("load service and categorie");
    getService(token, null, (services) => {
        services.forEach(s => {
            let opt = document.createElement("option");
            opt.value = s.uuidService;
            opt.innerText = s.nomService;
            selectService.appendChild(opt);
        })
    }, (err) => {
        console.error(err);
    });
    getTypeTicket(token, (types) => {
        types.forEach(t => {
            let opt = document.createElement("option");
            opt.value = t.refTypeTicket;
            opt.innerText = t.libTypeTicket;
            selectCategorie.appendChild(opt);
        })
    }, (err) => {
        console.error(err);
    });
}
function fillSelect(select, data, v, clear = "Selectionnez une valeur") {
    if (clear) {
        select.innerHTML = "";
        let nullOpt = document.createElement("option");
        nullOpt.value = "null";
        nullOpt.innerText = clear;
        select.appendChild(nullOpt);
    }
    data.forEach(d => {
        let opt = document.createElement("option");
        opt.value = d[v.value];
        opt.innerText = d[v.name];
        select.appendChild(opt);
    })
}
async function loadUser(callBack = undefined) {
    try {
        const user_data = await getUserAsync(
            JSON.parse(localStorage.user).user_id,
            token
        );

        const main = user_data.service_idService;

        const service = await getServiceAsync(token, main);

        document.components.nomService.innerText = service.nomService;

        entreprise = user_data.Entreprise[0].idEntreprise;
        document.components.nomEntreprise.innerText =
            user_data.Entreprise[0].nomEntreprise;

        if (callBack) callBack();

    } catch (err) {
        document.console.innerText = err;
    }
}