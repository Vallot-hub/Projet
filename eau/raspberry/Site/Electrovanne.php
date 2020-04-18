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

    $requete = "SELECT Date, Electrovanne FROM `Eau` ;"; 
    
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
              alert(data);
         },
         'text'
      );
  }
</script>



<!-- permet de gérer le boutons slide depuis le programme n'est pas utilisé pour l'instant -->
    <script type="text/javascript">  
      $(document).ready(function(){  //ancien bouton (n'existe plus)
        
          $("#switch").prop("checked", true);  // met le slider en position ON  ( ne déclanche pas la fonction envoiMqtt() )
        });
          $("#switch").prop("checked", false);  // met le slider en position OFF
        });
      });
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
    <p> L'état OFF correspond à un circuit ouvert: l'eau ne peut pas passer. <br>
    L'état ON correspond à un circuit fermé: l'eau peut passer. </p></topic>
    </agir>
    <historique> <!-- style css -->
      <h2> Historique de l'état de l'électrovanne </h2>
      <table class="tabcenter"> <!-- tableau remplie en php -->
        <tr> 
        <th> Date </th> <th> Etat de l'électrovanne </th>  <!-- Titre du tableau -->

        </tr>
        <?php 
        /** remplie le tableau */
        foreach($resultat as $cle=>$valeur)
        {
          echo "<tr>"; // debut ligne
          foreach($valeur as $cle=>$value)
          {
            echo "<td>".$value."</td>" ;  //une case de remplie
          }
        echo "</tr>"; // fin de la ligne
        }  
      ?> 
  </historique>
</div>
</body>
</html>


