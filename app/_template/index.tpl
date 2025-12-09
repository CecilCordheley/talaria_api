<div class="row">
  <div id="sysContent" class="col-4">
    <h2>Bienvenue {var:user.nomUser} {var:user.prenomUser} </h2>
     TEST {var:user.roleUser}
    <canvas id="panneChart" height="200"></canvas>
   
    {:IF {var:user.roleUser}=admin}
    <button name="seeStat" class="btn btn-secondary">Voir les stats</button>
    {:/IF}
  </div>
  <div class="col-8" id="MainActivity">
    Ici les composant d'activité
  </div>
  <script>
    getMainActivity();
    countByPanne();
    {:IF {var:user.roleUser}=admin}
      document.querySelector("[name=seeStat]")?.addEventListener("click",function(){
        _alert("voici les stat ?");
      })
    {:/IF}
  </script>
</div>