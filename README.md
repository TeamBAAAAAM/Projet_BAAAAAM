# Projet_BAAAAAM

> Projet de conception et développement d'une application web pour la dématérialisation des arrêts de travail avec la CPAM de la Haute-Garonne.
Visible ici https://teambaaaaam.github.io/Projet_BAAAAAM/.

## Configuration minimale

* [MySQL](https://www.mysql.com/fr/) - Système de gestion de base de données (version 5.7.56 ou ultérieure)
* [Bootstrap 3](https://getbootstrap.com/docs/3.3/) - Framework CSS (version 3.4.1 ou ultérieure)
* [PHP](https://www.php.net/) - Langage de programmation BACK (version 5.4 ou ultérieure)
* [jQuery](https://jquery.com/) - Framework JS (version 3.4.1 ou ultérieure)

## Installation

Dans un terminal gitbash, taper la commande suivante : 
```sh
git clone https://github.com/TeamBAAAAAM/Projet_BAAAAAM.git
```

Il est également possible d’utiliser le lien suivant https://github.com/TeamBAAAAAM/Projet_BAAAAAM.git pour cloner le dossier contenant les différentes pages à l’aide d’un IDE.

### Implémentation de la base de données

> D'abord lancer le script de création : <a href="bd_cpam/ScriptCreationBD.sql">ScriptCreationBD.sql</a>.
> Puis insérer les données initiales nécessaires (catégories et mnémoniques) : <a href="bd_cpam/ScriptDonnees.sql">ScriptDonnees.sql</a>.

### Accès à la base de données
> Pour la connexion à la BD, il est nécéssaire de renseigner les données de connexion dans un fichier de nom ".env" (cf. <strong>Gestion du fichier ENV</strong>).

## Gestion du fichier ENV

> Créer un fichier appelé ".env".
> Ce fichier contient les paramètres de connexions au différents serveurs.
> Ces paramètres correspondent à des variables d'environnement qui sont supprimés à la fin de l'exécution du script PHP.

<strong><em>Important : Il est nécessaire d'ajouter le nom de ce fichier au ".gitignore" ! 
Il suffit de copier le nom du fichier ".env", et le disposer sur une seule ligne dans le fichier ".gitignore". 
Cette oprération est importante car sans cela, le fichier ".env" qui contient des données très sensibles sera récupérable à partir de la commande de clonnage de GIT. De plus, ce fichier est propre à une machine en particulier, c'est-à-dire que chaque paramètre du fichier ".env" ne fonctionnera pas forcément sur autre machine (cela peut être dû à des différences au niveau du système d'exploitation, des serveurs utilisés, etc.).</em></strong>

<p>Pour le contenu de ce fichier, voici quelques règles à respecter :

* Une clé "KEY" correspond au nom d'une variable à laquelle est affectée une valeur "VALUE".
* Une variable d'environnement est déclarée de cette façon : KEY=VALUE.
* Les commentaires sont indiqués par le caractère "#".
* Il est possible d'ajouter un commentaire après la déclaration d'une variable d'environnement.
**<em>Exemple :<br>KEY=VALUE   #Ceci est un commentaire</em>
* Les noms de variables doivent impérativement correspondre à ceux précisés dans l'exemple ci-dessous !

  
### --- Exemple de fichier ".env" ---
```txt
# ------------------------------------------------------------------
#  VARIABLES D'ENVIRONNEMENT DE CONNEXION À LA BASE DE DONNÉES
# ------------------------------------------------------------------
# Nom du host [MYSQL_HOST]
MYSQL_HOST=
# Nom d'utilisateur [MYSQL_USER]
MYSQL_USER=
# Mot de passe [MYSQL_PWD]
MYSQL_PWD=
# Nom de la base de données [MYSQL_BD]
MYSQL_BD=
# Numéro du port de connexion [MYSQL_PORT]
MYSQL_PORT=

# ------------------------------------------------------------------
#  VARIABLES D'ENVIRONNEMENT DE CONNEXION AU SERVEUR FTP
# ------------------------------------------------------------------
# Nom du host [FTP_HOST]
FTP_HOST=
# Nom d'utilisateur [FTP_USER]            
FTP_USER=
# Mot de passe [FTP_PWD]
FTP_PWD=
# Numéro du port de connexion [FTP_PORT]
FTP_PORT=

# ------------------------------------------------------------------
#  VARIABLE D'ENVIRONNEMENT DU CHEMIN VERS L'ESPACE DE
#  STOCKAGE DES PIECES (sur le serveur FTP)
# ------------------------------------------------------------------
# N.B. : À partir de la racine [STORAGE_PATH]
STORAGE_PATH=

# ------------------------------------------------------------------
#  VARIABLES D'ENVIRONNEMENT POUR L'ENVOI DES MAILS</br>
# ------------------------------------------------------------------
# Nom du host [SMTP_HOST]
SMTP_HOST=
# Numéro du port [SMTP_PORT]		
SMTP_PORT=
# Nom d'utilisateur [SMTP_USER]
SMTP_USER=
# Mot de passe [SMTP_PWD]
SMTP_PWD=
# Adresse mail de l'expéditeur [SENDMAIL_FROM]
SENDMAIL_FROM=
# Nom de l'expéditeur [SENDMAIL_NAME]
SENDMAIL_NAME=

# ------------------------------------------------------------------
#  VARIABLES D'ENVIRONNEMENT POUR L'ENREGISTREMENT DES FICHIERS CSV
# ------------------------------------------------------------------
# Nom du fichier CSV d'injection dans DIADEME [CSV_INJECTION_NAME_FILE]
CSV_INJECTION_NAME_FILE=injection_file.csv
# Entête du fichier CSV pour l'injection des pièces dans DIADEME [CSV_INJECTION_HEADER]
CSV_INJECTION_HEADER="ADDICT;Processus;Archivage;Date Réception;Index Métier;Date Événement;Commentaire;DocPorteur"
# Nom du fichier CSV de sauvegarde des dossiers [CSV_FOLDERS_NAME_FILE]
CSV_FOLDERS_NAME_FILE=list_folders_file.csv

# Nom du host [CSV_FTP_HOST]
CSV_FTP_HOST=
# Nom d'utilisateur [CSV_FTP_USER]
CSV_FTP_USER=
# Mot de passe [CSV_FTP_PWD]
CSV_FTP_PWD=
# Numéro du port de connexion [CSV_FTP_PORT]
CSV_FTP_PORT=

# N.B. : À partir de la racine [CSV_INJECTION_FILE_PATH]
CSV_INJECTION_FILE_PATH="Injection DIADEME"
# N.B. : À partir de la racine [CSV_FOLDERS_FILE_PATH]
CSV_FOLDERS_FILE_PATH="Liste des Dossiers"

# Adresse mail de l'expéditeur [CSV_SENDMAIL_TO]
CSV_SENDMAIL_TO=
```
