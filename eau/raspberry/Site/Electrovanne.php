<!DOCTYPE html> <html>
    <head>
    <link rel="icon" href="icone.ico" />
        <meta charset="utf-8" />
        <title> Interface web du compteur connecté </title>
         <link rel="stylesheet" href="style.css" />
    </head>
      <entete>
        <!-- menu  -->
        <nav>
          <ul>
            <li><a href="Accueil.php">Accueil</a></li>
            <li><a href="Consommation.php">Consommation</a></li>
            <li><a href="#" style="background:#F00000">Gestion de l'électrovanne</a></li>
          </ul>
        </nav>
      </entete>
      <!--  lien du ajax -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<body>
 
<?php
/** Connection à la base de donnée **/
$ip="127.0.0.1";       //@ IP du serveur, ici localhost
$utilisateur="api";     //Nom d'utilisateur de la base de donnée
$mot_de_passe="snir";     // Mot de passe de la base de donnée
$base_de_donne="Projet";   //Nom de la base de donnée



$base_donne = new mysqli($ip,$utilisateur,$mot_de_passe,$base_de_donne);   //connection a la base de donnée


if($base_donne->connect_error==true)   // test la connection
{
echo "Echec de la connexion: ".$base_donne->connect_error;   //message d'erreur
}

    $requete = "SELECT Date, Electrovanne FROM `Electrovanne` ORDER BY Id_electrovanne DESC LIMIT 0,10;";

    
$reçu = $base_donne->query($requete);



if($reçu->num_rows>0)  //Si on reçoit quelle que chose
  {
      while($ligne=$reçu->fetch_assoc())
      {
        $resultat[]=$ligne;
      }
  }
  else
  {
      $resultat=NULL;
  }

  $base_donne->close();
  
  $resultat=array_reverse($resultat);  //inversement lecture de la base de donnée en sens inverse
    /** Fin de la connection à la base de donnée **/
?>


<script>
/** fonction lancé lorsque l'on click sur le slider  */
function envoiMqtt() 
{
  // Get the checkbox
  var checkBox = document.getElementById("switch");  //recupere le slider( c'est une checkbox pimper )
  // Get the output text
  var message;

  
    if ($('#switch').attr('checked')=='checked')  // regarde la position de du slider et la recupère
    {
       message=1;
    }
    else
    {
      message=0;
    }
    //envoi en AJAX
    $.post(
         'Mqtt_site.php', // script PHP executé 
         {
             Etat : message,  // paramètre POST 
         },

         function(data)  //Fonction executé a la reception de la réponse
         {
          var ar = JSON.parse(data);
          var tab = document.getElementById("Etat_historique");
          for (var i=0; i < 10;i++)
          {
            tab.deleteRow(1);
          }

          for (var i=0; i < 10; i++)
          {
            var nouvelleLigne = tab.insertRow(i+1);
            var nouvelleCellule = nouvelleLigne.insertCell(0);  // Insère une cellule dans la ligne à l'indice 0
            var nouveauTexte = document.createTextNode(ar[i].Date);
            nouvelleCellule.appendChild(nouveauTexte);
                      
            nouvelleCellule = nouvelleLigne.insertCell(1);
            var value = ar[i].Electrovanne;
            if (value == 0)
            {
              value = "ouvert";
            }
            else if (value == 1)
            {
              value = "fermée";
            }
            nouveauTexte = document.createTextNode(value);  // Ajoute un nœud texte à la cellule
            nouvelleCellule.appendChild(nouveauTexte);
          }

         },
         'text'
      );
  }
</script>






<!-- Bouton switch -->
<div id="conteneur">
  <agir>
  <h2>Agir sur l'électrovanne </h2>
  <topic><p> Etat de l'électrovanne : 
  <label class="switch">  <!-- chargement en css  -->
  <input type="checkbox" id="switch" onClick="envoiMqtt()"/>  <!-- déclenche la fonction lorsque l'on click -->
  <span class="slider round"></span>  <!-- idem chargent en css -->
  </p></topic>
</label>
    <p> L'état OFF correspond à un circuit ouvert: l'eau ne peut pas circuler. <br>
    L'état ON correspond à un circuit fermé: l'eau peut circuler. </p></topic>
    </agir>
    <historique> <!-- style css -->
      <h2> Historique de l'état de l'électrovanne </h2>
      <table id="Etat_historique" class="tabcenter"> <!-- tableau remplie en php -->
        <tr> 
        <th> Date </th> <th> Etat de l'électrovanne </th>  <!-- Titre du tableau -->

        </tr>
        <?php

        echo "<br><br>";
        
        /** remplie le tableau */
        foreach($resultat as $cle=>$valeur)
        {
          echo "<tr>"; // debut ligne
          foreach($valeur as $cle=>$value)
          {
            if ($value == 0)
            {
              echo "<td>ouvert</td>" ;  //une case de remplie
            }
            else if ($value == 1)
            {
              echo "<td>fermée</td>" ;
            }
            else
            {
            echo "<td>".$value."</td>" ;  //une case de remplie
            }
          }
        echo "</tr>"; // fin de la ligne
        }  
      ?> 
  </historique>
</div>

<!-- permet de gérer de donner la position initial du boutons slide  -->
<script type="text/javascript">  
    var etat = <?php echo $value; ?>;  //php envoi la valeur de l'état de l'electrovanne
        if (etat == 1)
        {
          document.getElementById("switch").checked = true; // met le slider en position ON  ( ne déclanche pas la fonction envoiMqtt() )
        }
        else
        {
          document.getElementById("switch").checked = false;  // met le slider en position OFF
        }

     </script>

</body>
</html>


