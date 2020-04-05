<!DOCTYPE html> <html>
    <head>
        <meta charset="utf-8" />
        <title> Site web d'affichage des mesures </title>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    </head>
    <body background="image1.jpg">
        <section>
                <h1><strong>COMPTEUR D'EAU CONNECTE WIFI</strong></h1>
                <h2>Bienvenue sur le site web d'affichage des mesures en PHP, CSS, HTML et Javascript.</h2>
                <h3>Voici le contexte :</h3>
                <p>Une maison secondaire de bord de mer est equipée d'une installation domotique. Celle-ci nécessite la mesure de la consommation d'eau et la détection d'éventuelles fuites d'eau. <br>
                Dans le cas où une fuite est détectée, une electrovanne coupe l'alimentation en eau. La fonction est assurée par un compteur d'eau à impulsion et une electrovanne connectés
                sur une carte ESP8266 nodeMCU V3. Les données sont transmises à un server web, broker, mqtt en wifi.<br>
                Ces données seront affichées par l'intermédiaire du navigateur du téléviseur du salon et depuis n'importe quel poste connecté sur internet. De plus, elles seront accessibles depuis une application Android.</p>
        </section>
        <div style="width: 75%">
                <canvas id="myChart"></canvas>
        </div>

        <script>
                Chart.defaults.global.title.display = true
                Chart.defaults.global.title.text = "Affichage des mesures du compteur d'eau connecté Wifi"
                Chart.defaults.global.elements.point.radius = 7
        </script>
        <script>

                var ctx = document.getElementById('myChart').getContext('2d');
                var chart = new Chart(ctx, {
                // Type de tableau que nous voulons
                        type: 'line',

                // Les données de la base de donnée
                data: {
                        labels: ["A", "B", "C","D", "E"],
                        datasets: [{
                                label: "Consommation d'eau",
                                //backgroundColor: 'rgb(255, 0, 0)',
                                borderColor: 'rgb(255, 0, 0)',
                                data: [0, 4, 1, 6, 2],
                                }]
                },


                // La configuration des options se passe ici !
                options: {}

                });
         </script>
         <?php
                $servername="127.0.0.1";
                $username="root";
                $password="root";
                $dbname="Projet";

                //Connexion à la base de données
                $conn= new mysqli($servername,$username,$password,$dbname);

                //Test de la connexion.
                if($conn->connect_error){
                        die("Echec de la connexion:".$conn->connect_error);
                }

                //Lire une table d'une base de donnée

                $sql="SELECT Conso, Date FROM Eau";
                $result=$conn->query($sql);

                if($result->num_rows>0){
                        //Lecture de chaque enregistrement de la table
                        while($row=$result->fetch_assoc()){
                                echo"Conso:".$row["Conso"]." ".$row["Date"]."<br>";
                        }
                }
                else{
                        echo "0 Resultat";
                }
                //fermer la connexion
                $conn->close();
         ?>

    </body>
</html>

