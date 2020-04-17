<?php
        $ip="127.0.0.1";       //@ IP du serveur, ici localhost
        $utilisateur="api";     //Nom d'utilisateur de la base de donnée
        $mot_de_passe="snir";     // Mot de passe de la base de donnée
        $base_de_donne="Projet";   //Nom de la base de donnée



        $base_donne = new mysqli($ip,$utilisateur,$mot_de_passe,$base_de_donne);   //connection a la base de donnée

        $debut = $_POST['debut'];
        $fin = $_POST['fin'];
        if($base_donne->connect_error==true)   // test la connection
        {
            echo "Echec de la connexion: ".$base_donne->connect_error;   //message d'erreur
        }

        $requete = "SELECT Date, Conso FROM `Test` WHERE Date>='".$debut."' AND Date<='".$fin."';";

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
        echo json_encode($resultat);
?>