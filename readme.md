# Pour init le projet:

## Etapes :

1. Changer les paramètres de la bdd dans le .env
1. faire **composer i**
1. faire **php bin/console d:d:c**
1. faire **php bin/console d:m:m**
1. faire **php bin/console d:f:l**
1. aller sur l'url <chemin_du_projet>/public/api ( pour le front )
1. curl --request POST --url http://localhost/webservice_1/public/api/books/0/reserve   --header 'Authorization: Basic YWRtaW5Ac3ltZi5mcjphZG1pbg=='
1. Sinon les infos de connexion pour la requetes sont username : admin@symf.fr mdp : admin 
\
L'endpoint est documenté sur l'api