#! /bin/bash


#..............Nom d'hote..........Nom du topic......copie les donnes dans la variable..#
mosquitto_sub -h 127.0.0.1 -t compteur_connecte/conso | while read donnee

do


#echo $donnee                         //test

Compteur=$(echo $donnee | cut -d: -f1)     #prend que la 1er partie de la variable donnée (ce qu'il y a avant les :)
Electrovanne=$(echo $donnee | cut -d: -f2)   #prend que la 2éme partie de la variable donnée
Debit=$(echo $donnee | cut -d: -f3)			#prend que la 3éme partie de la variable donnée

#echo $Compteur et $Electrovanne          //test

#Ce connecte à la base de donnée a l'aide de la commande mysql(mysql -u root --password=root -e) et sql(USE Projet; INSERT INTO Eau (Conso,Total,Electrovanne) VALUES ($donnee,2,1);)

mysql -u api --password=snir -e "USE Projet; INSERT INTO Eau (Conso,Total,Electrovanne) VALUES ($Compteur,$Debit,$Electrovanne);"

done
