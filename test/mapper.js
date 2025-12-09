window.addEventListener("load", function () {
    let components={};
    const all = document.querySelectorAll('[id]');
    all.forEach(el => {
        components[el.id] = el;
    });
    // On attache l'objet au document
    document.components = components;
});