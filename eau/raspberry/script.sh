#! /bin/bash


#..............Nom d'hote..........Nom du topic......copie les donnes dans la variable..#
mosquitto_sub -h 127.0.0.1 -t compteur_connecte/conso | while read donnee

do


#echo $donnee                         //test
Compteur=$(echo $donnee | cut -d: -f1)
Electrovanne=$(echo $donnee | cut -d: -f2)
#echo $Compteur et $Electrovanne          //test

#Ce connecte à la base de donnée a l'aide de commande mysql(mysql -u root --password=root -e) et sql(USE Projet; INSERT INTO Eau (Conso,Total,Electrovanne) VALUES ($donnee,2,1);)

mysql -u root --password=root -e "USE Projet; INSERT INTO Eau (Conso,Total,Electrovanne) VALUES ($Compteur,2,$Electrovanne);"

done

