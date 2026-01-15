<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="../public/mapper.js"></script>
    <script src="../public/talariaLib.js"></script>
    <script src="js/main.js"></script>
    <title>Exemple Talaria V2</title>
</head>
<body>
    <div class="container" id="MainView">
        <header>
            <h1>Model d'application Talaria</h1>
        </header>
        <menu class="list-group">
            <a href="#" class="list-group-item list-group-item-action">A second link item</a>
            <a href="#" class="list-group-item list-group-item-action">A second link item</a>
            <a href="#" class="list-group-item list-group-item-action">A second link item</a>
            <a href="#" class="list-group-item list-group-item-action">A second link item</a>
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
        <footer>
            copyright @CecilCordheley 2026
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script>
        setMapper();
        var token="";
        document.getElementById("connexionTrigger").addEventListener("click",function(){
            let mdp=document.components["Mdp_Connexion"].value;
            let mail=document.components["Mail_Connexion"].value;
            connexion(mail, mdp, (data)=>{
                localStorage.setItem("user",JSON.stringify(data));
                token=data.token;
                switch(data.role){
                    case "agent":{
                            loadView("../async/view_agentActivity",()=>{
                    getTypeTicket(token,(data)=>{
                         data.forEach(el=>{
                            document.components["TypeTicket"].innerHTML+=`<option value=${el.refTypeTicket}>${el.libTypeTicket}</option>`;
                        });
                    },(err)=>{
                        console.error(err);
                    })
                    getService(token,null,(data)=>{
                        data.forEach(el=>{
                            document.components["Service"].innerHTML+=`<option value=${el.uuidService}>${el.nomService}</option>`;
                        });
                    },(err)=>{  
                        console.error(err);
                    })
                },(err)=>{
                    console.error(err);
                });
                        break;
                    }
                    case "manager":{
                            loadView("../async/view_managerActivity",()=>{
                    getTypeTicket(token,(data)=>{
                         data.forEach(el=>{
                            document.components["TypeTicket"].innerHTML+=`<option value=${el.refTypeTicket}>${el.libTypeTicket}</option>`;
                        });
                    },(err)=>{
                        console.error(err);
                    })
                    getService(token,null,(data)=>{
                        data.forEach(el=>{
                            document.components["Service"].innerHTML+=`<option value=${el.uuidService}>${el.nomService}</option>`;
                        });
                    },(err)=>{  
                        console.error(err);
                    })
                },(err)=>{
                    console.error(err);
                });
                        break;
                    }
                }
            
            }, (err)=>{
                document.querySelector(".result").innerText="Une erreur s'est produite";
                console.error(err);
            })
        });
    </script>
</body>
</html>