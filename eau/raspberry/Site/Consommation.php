<!DOCTYPE html> <html>
    <head>
        <link rel="icon" href="icone.ico" />
        <meta charset="utf-8" />
        <title> Interface web du compteur connecté </title>
         <link rel="stylesheet" href="style.css" />
         <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>  <!-- source pour le graphique chart js --> 
         <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>  <!-- source pour le AJAX --> 
    </head>
        <entete>
        <!-- Menu -->
        <nav>
            <ul>
                <li><a href="Accueil.php">Accueil</a></li>
                <li><a href="#" style="background:#F00000">Consommation</a></li>
                <li><a href="Electrovanne.php">Gestion de l'électrovanne</a></li>
            </ul>
        </nav>
        </entete>

        <?php
        /** récuperation de la date du jour et d'il y a un mois */
            $date_fin = Date("Y-m-d");
            $date_debut = date("Y-m-d", strtotime('-1 month', strtotime($date_fin)));
        ?>

        <script>
        /** fonction d'envoie des dates en ajax */
        function db()
        {
            /**  change les valeurs de bloquage des dates */
            document.getElementById("debut").max = document.getElementById("fin").value;
            document.getElementById("fin").min = document.getElementById("debut").value;
            $.post(
                'db_site.php', // script PHP
                {
                    /** paramétre POST les dates */
                    debut : document.getElementById('debut').value, 
                    fin : document.getElementById('fin').value
                },

            function(data)
            {
                var abs = new Array();  // tableau comptenant les données en bas (la date)
                var ord = new Array();  // tableau comptenant les valeurs corespondant aux dates (Consommation)
                var ar = JSON.parse(data);   //récupere les donnes de la base de données encodé en json
                /** remplie les tableaux de données */
                for (var i=0; i < ar.length; i++)
                {
                    abs[i]=ar[i].Date;
                    ord[i]=ar[i].Conso;
                }
                up_grafike(abs, ord);  // met à jour le graphique
            },
            'text'
            );
        }
        function up_grafike(abcisse,ordonne)
        {
            chart.data.datasets[0].data = ordonne; //  remplace les valeurs de consommation
            chart.data.labels = abcisse;  // remplace les dates
            chart.update();   // réaffichage du graphique avec les nouvelles valeurs 
        }
</script>

<div id="conteneur">

    <recap>
        <h2> Paramètre du graphique </h2>

        <h3> Date </h3>
        <label for="debut">Début :</label>
        <?php
            echo "<input type='date' id='debut' name='debut' value='".$date_debut."'onChange='db()' max='".$date_fin."'>";  //met au demarrage de la page la date d'il y a un mois et bloque les dates supperieur a la date de fin
        ?>
        <br>
        <label for="fin">Fin :</label>
        <?php
            echo "<input type='date' id='fin' name='fin' value='".$date_fin."' onChange='db()' min='".$date_debut."'>";   //met au demarrage de la page la date d'aujourd'hui et bloque les dates inferieur a la date du debut 
        ?>


    </recap>
    <graphique>
        <h2> Graphique de la consommation</h2>
        <canvas id="graphe"></canvas>

    </graphique>
</div>




<?php
$ip="127.0.0.1";       //@ IP du serveur, ici localhost
$utilisateur="api";     //Nom d'utilisateur de la base de donnée
$mot_de_passe="snir";     // Mot de passe de la base de donnée
$base_de_donne="Projet";   //Nom de la base de donnée




$base_donne = new mysqli($ip,$utilisateur,$mot_de_passe,$base_de_donne);   //connection a la base de donnée


if($base_donne->connect_error==true)   // test la connection
{
    echo "Echec de la connexion: ".$base_donne->connect_error;   //message d'erreur
}

$requete = "SELECT Date, Conso FROM `Test` WHERE Date>='".$date_debut."' AND Date<='".$date_fin."'";
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

$virgule=0;  //sert a ne pas afficher la 1er virgule entre les données
echo "<script>
        var ctx = document.getElementById('graphe').getContext('2d');
        var chart = new Chart(ctx,
        {
        // Type de graphique que nous voulons
                type: 'bar',
        // Les données de la base de donnée
        data:
        {
            labels: [";
            foreach($resultat as $cle=>$valeur)
            {
                if ($virgule==0)
                {
                    echo "'".$valeur["Date"]."'";
                    $virgule++;
                }
                else
                {
                    echo ",'".$valeur["Date"]."'";  //ajoute la virgule par la suite
                }
            }
            echo "],
            datasets:
            [
                {
                label: 'Consommation d\'eau',
                borderWidth: 4,
                backgroundColor: 'rgb(190, 0, 0)',
                borderColor: 'rgb(190, 0, 0)',
                data: [";
                $virgule=0;  // reinitialise la variable
                foreach($resultat as $cle=>$valeur)
                {
                    if ($virgule==0)
                    {
                        echo "'".$valeur["Conso"]."'";
                        $virgule++;
                    }
                    else
                    {
                        echo ",'".$valeur["Conso"]."'";
                    }
                }
                echo "],
                }
            ],
            fontColor: 'black'
        },
        // La configuration des options se passe ici !
        options:
        {
            scales:
            {
                xAxes:
                [
                    {
                    display: true,
                    scaleLabel:
                    {
                        display: true,
                        labelString: 'Date',
                        fontColor:'#000000',
                        fontSize:15
                    },
                    ticks:
                        {
                        fontColor: 'black'
                        }
                    }
                ],
                yAxes:
                [
                    {
                    display: true,
                    scaleLabel:
                    {
                        display: true,
                        labelString: 'Consommation',
                        fontColor: '#000000',
                        fontSize:15
                    },
                    ticks:
                        {
                          fontColor: 'black'
                        }
                    }
                ]
            },

            legend:
            {
                labels:
                {
                    // This more specific font property overrides the global property
                    fontColor: 'rgb(0, 0, 0)'
                }
            }
        }
    }
        );

    </script>";
    ?>
    </html>

