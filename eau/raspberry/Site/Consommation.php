<!DOCTYPE html> <html>
    <head>
        <meta charset="utf-8" />
        <title> Interface web du compteur connecté </title>
         <link rel="stylesheet" href="style.css" />
         <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
         <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
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
            $date_fin = Date("Y-m-d");
            $date_debut = date("Y-m-d", strtotime('-1 month', strtotime($date_fin)));
        ?>

        <script>
        function db()
        {


            $.post(
                'db_site.php', // script PHP
                {
                    debut : document.getElementById('debut').value, // POST
                    fin : document.getElementById('fin').value
                },

            function(data)
            {
                var abs = new Array();
                var ord = new Array();
                var ar = JSON.parse(data);

                for (var i=0; i < ar.length; i++)
                {
                    abs[i]=ar[i].Date;
                    ord[i]=ar[i].Conso;
                }
                up_grafike(abs, ord);
            },
            'text'
            );
        }
        function up_grafike(abcisse,ordonne)
        {
            chart.data.datasets[0].data = ordonne;
            chart.data.labels = abcisse;

            chart.update();
        }
</script>

<div id="conteneur">

    <recap>
        <h2> Paramètre du graphique </h2>

        <h3> Date </h3>
        <label for="debut">Debut :</label>
        <?php
            echo "<input type='date' id='debut' name='debut' value='".$date_debut."'onChange='db()'>";
        ?>
        <br>
        <label for="fin">Fin :</label>
        <?php
            echo "<input type='date' id='fin' name='fin' value='".$date_fin."' onChange='db()'>";
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

$virgule=0;
echo "<script>
        var ctx = document.getElementById('graphe').getContext('2d');
        var chart = new Chart(ctx,
        {
        // Type de tableau que nous voulons
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
                    echo ",'".$valeur["Date"]."'";
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
                $virgule=0;
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

