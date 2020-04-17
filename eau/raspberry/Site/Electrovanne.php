<!DOCTYPE html> <html>
    <head>
        <meta charset="utf-8" />
        <title> Interface web du compteur connecté </title>
         <link rel="stylesheet" href="style.css" />
    </head>
      <entete>
        <nav>
          <ul>
            <li><a href="Accueil.php">Accueil</a></li>
            <li><a href="Consommation.php">Consommation</a></li>
            <li><a href="#" style="background:#F00000">Gestion de l'électrovanne</a></li>
          </ul>
        </nav>
      </entete>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<body>
 
<?php
$ip="127.0.0.1";       //@ IP du serveur, ici localhost
$utilisateur="root";     //Nom d'utilisateur de la base de donnée
$mot_de_passe="";     // Mot de passe de la base de donnée
$base_de_donne="projet";   //Nom de la base de donnée
$i=1;



$base_donne = new mysqli($ip,$utilisateur,$mot_de_passe,$base_de_donne);   //connection a la base de donnée


if($base_donne->connect_error==true)   // test la connection
{
echo "Echec de la connexion: ".$base_donne->connect_error;   //message d'erreur
}

    $requete = "SELECT Date, Electrovanne FROM `eau` ;"; 

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
?>


<script>
function envoiMqtt() 
{
  // Get the checkbox
  var checkBox = document.getElementById("switch");
  // Get the output text
  var message;

  
    if ($('#switch').attr('checked')=='checked')
    {
       message=1;
    }
    else
    {
      message=0;
    }

    $.post(
         'Mqtt_site.php', // script PHP 
         {
             Etat : message,  // POST
         },

         function(data)
         {
              alert(data);
         },
         'text'
      );
  }
</script>



<!-- permet de gérer le boutons slide depuis le programme n'est pas utilisé pour l'instant -->
    <script type="text/javascript">
      $(document).ready(function(){
        
          $("#switch").prop("checked", true);
        });
          $("#switch").prop("checked", false);
        });
      });
    </script>



<!-- Bouton switch -->

<div id="conteneur">
  <agir>
  <h2>Agir sur l'électrovanne </h2>
  <topic><p> Etat de l'électrovanne : 
  <label class="switch">
  <input type="checkbox" id="switch" onClick="envoiMqtt()"/>
  <span class="slider round"></span>
  </p></topic>
</label>
    <p> L'état OFF correspond à un circuit ouvert: l'eau ne peut pas passer. <br>
    L'état ON correspond à un circuit fermé: l'eau peut passer. </p></topic>
    </agir>
    <historique>
      <h2> Historique de l'état de l'électrovanne </h2>
      <table class="tabcenter">
        <tr> 
        <th> Date </th> <th> Etat de l'électrovanne </th>

        </tr>
        <?php 
        foreach($resultat as $cle=>$valeur)
        {
          echo "<tr>";
          foreach($valeur as $cle=>$value)
          {
            echo "<td>".$value."</td>" ;
          }
      echo "</tr>";

      }  
      ?> 
  </historique>
</div>
</body>


