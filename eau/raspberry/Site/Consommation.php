<!DOCTYPE html> <html>
    <head>
        <meta charset="utf-8" />
        <title> Interface web du compteur connecté </title>
         <link rel="stylesheet" href="style.css" />
         <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    </head>
        <entete>
        <nav>
            <ul>
                <li><a href="Accueil.php">Accueil</a></li>
                <li><a href="#" style="background:#F00000">Consommation</a></li>
                <li><a href="Electrovanne.php">Gestion de l'électrovanne</a></li>
            </ul>
        </nav>
        </entete>

        <?php
        $ip="127.0.0.1";       //@ IP du serveur, ici localhost
        $utilisateur="api";     //Nom d'utilisateur de la base de donnée
        $mot_de_passe="snir";     // Mot de passe de la base de donnée
        $base_de_donne="Projet";   //Nom de la base de donnée
        $i=1;



        $base_donne = new mysqli($ip,$utilisateur,$mot_de_passe,$base_de_donne);   //connection a la base de donnée


        if($base_donne->connect_error==true)   // test la connection
        {
            echo "Echec de la connexion: ".$base_donne->connect_error;   //message d'erreur
        }

        $requete = "SELECT Date, Conso ,Debit FROM `Eau` ;"; 

        $reçu = $base_donne->query($requete);



        if($reçu->num_rows>0)  //Si on reçoit quelle que chose
        {
            while($resultat[$i]=$reçu->fetch_assoc())
            {
                $i++;
            }
        }
        else
        {
            $resultat=NULL;
        }
        $base_donne->close();
        ?>

        <div id="conteneur">
        
            <recap>
                <h2> Historique de la Consommation </h2>
                <table class="tabcenter">
                    <tr> 
                        <th> Date </th> <th> Consommation </th> <th> Débit </th>
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
                </table> 
            </recap>
            <graphique>
                <h2> Graphique </h2> 
                <canvas id="myChart"></canvas>  

            </graphique>
        
        
        
        
        
        
        

        </div>
        <script>
                Chart.defaults.global.title.display = true
                Chart.defaults.global.title.text = "Affichage des mesures du compteur d'eau connecté Wifi"
                Chart.defaults.global.elements.point.radius = 7
        </script>
        <?php
        
        $ip="127.0.0.1";       //@ IP du serveur, ici localhost
        $utilisateur="api";     //Nom d'utilisateur de la base de donnée
        $mot_de_passe="snir";     // Mot de passe de la base de donnée
        $base_de_donne="Projet";   //Nom de la base de donnée
        $i=1;



        $base_donne = new mysqli($ip,$utilisateur,$mot_de_passe,$base_de_donne);   //connection a la base de donnée


        if($base_donne->connect_error==true)   // test la connection
        {
            echo "Echec de la connexion: ".$base_donne->connect_error;   //message d'erreur
        }

        $requete = "SELECT Date, Conso FROM `Eau` ;"; 

        $reçu = $base_donne->query($requete);



        if($reçu->num_rows>0)  //Si on reçoit quelle que chose
        {
            while($resultat[$i]=$reçu->fetch_assoc())
            {
                $i++;
            }
        }
        else
        {
            $resultat=NULL;
        }
        $base_donne->close();
        
        
        echo "<script>

                var ctx = document.getElementById('myChart').getContext('2d');
                var chart = new Chart(ctx, 
                {
                // Type de tableau que nous voulons
                        type: 'line',
                // Les données de la base de donnée
                data: 
                {
                    labels: [";
                    foreach($resultat as $valeur)
                    {
                        if ($valeur!=NULL)   //suprimme une valeur NULL recupere a la fin
                        {
                        echo "'".$valeur["Date"]."',";
                        }
                    }
                    echo "'test'";  //Une virgule est mise en trop donc rajoute une valeur (a suprimer) 

                    echo "],
                    datasets: 
                    [
                        {
                        label: 'Consommation d\'eau',
                        //backgroundColor: 'rgb(255, 0, 0)',
                        borderColor: 'rgb(255, 0, 0)',
                        data: [";
                        foreach($resultat as $valeur)
                        {
                            if ($valeur!=NULL)   //suprimme une valeur NULL recupere a la fin
                            {
                            echo $valeur["Conso"].",";
                            }
                        }

                        echo "51";  //toujours virgule en trop
                        echo "],
                        }
                    ]
                },


                // La configuration des options se passe ici !
                options: {}

                });
         </script>";
         ?>