# Projet_BAAAAAM

![](https://img.shields.io/github/last-commit/TeamBAAAAAM/Projet_BAAAAAM?color=yellow&style=for-the-badge)
![](https://img.shields.io/github/repo-size/TeamBAAAAAM/Projet_BAAAAAM?style=for-the-badge)
![](https://img.shields.io/github/contributors/TeamBAAAAAM/Projet_BAAAAAM?style=for-the-badge)
![](https://img.shields.io/github/license/TeamBAAAAAM/Projet_BAAAAAM?color=Red&style=for-the-badge)

Projet de conception et développement d'une application web pour la dématérialisation des arrêts de travail avec la CPAM de la Haute-Garonne.

**Visible ici : https://www.pjpe.cpam31.fr/**

## 1 - Configuration minimale

[![HTML](https://img.shields.io/badge/HTML--blue?logo=HTML5&color=darkorange)](https://www.w3schools.com/html/)
[![CSS](https://img.shields.io/badge/CSS--blue?logo=CSS3&color=blue)](https://www.w3schools.com/css/)
[![jQuery](https://img.shields.io/badge/jQuery-v3.4.1-blue?logo=jQuery)](https://jquery.com/)
[![PHP](https://img.shields.io/badge/PHP-v5.4-purple?logo=PHP)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-v5.7.56-orange?logo=MySQL)](https://www.mysql.com/fr/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-v3.4.1-violet?logo=Bootstrap)](https://getbootstrap.com/docs/3.3/)

Pour une installation en local, la configuration suivante est suffisante :

| Type de logiciel | Version |
| --- | --- |
| Client FTP | [![](https://raster.shields.io/badge/FileZilla_Client-3.49.1-red?style=for-the-badge&logo=FileZilla)](https://filezilla-project.org/download.php?type=client)|
| Serveur FTP (Windows uniquement) | [![](https://raster.shields.io/badge/FileZilla_Server-0.9.60.2-darkred?style=for-the-badge&logo=FileZilla)](https://filezilla-project.org/download.php?type=server)|
| Plate-forme de développement Web (Windows) | [![](https://raster.shields.io/badge/WampsServer-3.2.0-pink?style=for-the-badge&logo=Apache)](https://www.wampserver.com/)|
| Plate-forme de développement Web (Mac OS) | [![](https://raster.shields.io/badge/Mamp-5.7-lightgrey?style=for-the-badge&logo=Apache)](https://www.mamp.info/fr/downloads/)|
| Client SMTP (ID de la CPAM) | [![](https://raster.shields.io/badge/OVH_Cloud-5.7-darkblue?style=for-the-badge&logo=OVH)](https://www.ovh.com/fr/)|

## 2 - Installation

Dans un terminal gitbash ouvert à l'endroit où cloner le repository, lancer la commande suivante : 
```sh
git clone https://github.com/TeamBAAAAAM/Projet_BAAAAAM.git
```
ou si vous ne vous trouvez pas dans le répertoire où vous souhaitez cloner le repository : 
```sh
git clone https://github.com/TeamBAAAAAM/Projet_BAAAAAM.git <chemin vers le dossier dépot>
```

Il est également possible d’utiliser le lien suivant https://github.com/TeamBAAAAAM/Projet_BAAAAAM.git pour cloner le dossier contenant les différentes pages à l’aide d’un IDE.

### 2.1 - Implémentation de la base de données

D'abord lancer le script de création : [ScriptCreationBD.sql](bd_cpam/ScriptCreationBD.sql).
Puis insérer les données initiales nécessaires (catégories et mnémoniques) : [ScriptDonnees.sql](bd_cpam/ScriptDonnees.sql).

### 2.2 - Accès à la base de données
Pour la connexion à la BD, il est nécéssaire de renseigner les données de connexion dans un fichier de nom ".env" (cf. <strong>Gestion du fichier ENV</strong>).

## 3 - Gestion du fichier ENV

Créer un fichier appelé ".env".
Ce fichier contient les paramètres de connexions au différents serveurs.
Ces paramètres correspondent à des variables d'environnement qui sont supprimés à la fin de l'exécution du script PHP.

> ***Important : Il est nécessaire d'ajouter le nom de ce fichier au ".gitignore" ! 
> Il suffit de copier le nom du fichier ".env", et le disposer sur une seule ligne dans le fichier ".gitignore". 
> Cette oprération est importante car sans cela, le fichier ".env" qui contient des données très sensibles sera récupérable à partir de la commande de clonnage de GIT. De plus, > ce fichier est propre à une machine en particulier, c'est-à-dire que chaque paramètre du fichier ".env" ne fonctionnera pas forcément sur autre machine (cela peut être dû à
> des différences au niveau du système d'exploitation, des serveurs utilisés, etc.).***

Pour le contenu de ce fichier, voici quelques règles à respecter :

* Une clé "KEY" correspond au nom d'une variable à laquelle est affectée une valeur "VALUE".
* Une variable d'environnement est déclarée de cette façon : KEY=VALUE.
* Les commentaires sont indiqués par le caractère "#".
* Il est possible d'ajouter un commentaire après la déclaration d'une variable d'environnement.
** Exemple :<br>KEY=VALUE   #Ceci est un commentaire **
* Les noms de variables doivent impérativement correspondre à ceux précisés dans l'exemple ci-dessous !

### 3.1 - Fichier ".env"

#### Dictionnaire des noms de variables d'environnement
| Nom de la variable | Définition |
| --- | --- |
| CSV_FOLDERS_NAME_FILE | Nom du fichier CSV de sauvegarde des dossiers |
| CSV_FOLDERS_FILE_PATH | Chemin d'accès vers la zone de dépôt de la liste des dossiers "En cours" ou "À traiter" dans DIADEME sur le serveur FTP (à partir de la racine) |
| CSV_INJECTION_FILE_PATH | Chemin d'accès vers la zone de dépôt du fichier d'injection dans DIADEME sur le serveur FTP (à partir de la racine) |
| CSV_INJECTION_HEADER | Entête du fichier CSV pour l'injection des pièces dans DIADEME (colonnes séparées par des points-virgules)|
| CSV_INJECTION_NAME_FILE | Nom du fichier CSV d'injection dans DIADEME |
| CSV_HOST | Nom de l'hôte pour la connexion au serveur FTP pour l'enregistrement des fichiers CSV |
| CSV_PORT | Numéro du port de connexion au serveur FTP pour l'enregistrement des fichiers CSV |
| CSV_PWD | Mot de passe pour la connexion au serveur FTP pour l'enregistrement des fichiers CSV |
| CSV_USER | Nom d'utilisateur pour la connexion au serveur FTP pour l'enregistrement des fichiers CSV |
| CSV_SENDMAIL_TO | Adresse mail de destination du mail contenant l'un des fichiers CSV |
| FTP_HOST | Nom de l'hôte pour la connexion au serveur FTP pour l'enregistrement des justificatifs |
| FTP_PORT | Numéro du port de connexion au serveur FTP pour l'enregistrement des justificatifs |
| FTP_PWD | Mot de passe pour la connexion au serveur FTP pour l'enregistrement des justificatifs |
| FTP_USER | Nom d'utilisateur pour la connexion au serveur FTP pour l'enregistrement des justificatifs |
| MYSQL_BD | # Nom de la base de données |
| MYSQL_HOST | Nom de l'hôte pour la connexion à la base de données |
| MYSQL_PORT | Numéro du port de connexion à la base de données |
| MYSQL_PWD | Mot de passe pour la connexion à la base de données |
| MYSQL_USER | Nom d'utilisateur pour la connexion à la base de données |
| SENDMAIL_FROM | Adresse mail de l'expéditeur des mails automatiques |
| SENDMAIL_NAME | Nom de l'expéditeur des mails automatiques  |
| SMTP_HOST | Nom de l'hôte pour la connexion au serveur SMTP |
| SMTP_PORT | Numéro du port de connexion au serveur SMTP |
| SMTP_PWD | Mot de passe pour la connexion au serveur SMTP |
| SMTP_USER | Nom d'utilisateur pour la connexion au serveur SMTP |
| STORAGE_PATH | Chemin d'accès vers la zone de dépôt des justificatifs sur le serveur FTP (à partir de la racine) |

#### Exemple de fichier ".env"

```txt
# ------------------------------------------------------------------
#  VARIABLES D'ENVIRONNEMENT DE CONNEXION À LA BASE DE DONNÉES
# ------------------------------------------------------------------
# Nom de l'hôte [MYSQL_HOST]
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
# Nom de l'hôte [FTP_HOST]
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
#  VARIABLES D'ENVIRONNEMENT POUR L'ENVOI DES MAILS
# ------------------------------------------------------------------
# Nom de l'hôte [SMTP_HOST]
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

## Équipe de développement :
- [![Aïssatou Diop](https://img.shields.io/badge/Aïssatou_Diop-M1_MIAGE_parcours_IM_(UT1_2019)-blue?style=for-the-badge&color=red)](https://github.com/aissatou1702) 
- [![Axel Toa](https://img.shields.io/badge/Axel_Toa-M1_MIAGE_parcours_IM_(UT1_2019)-blue?style=for-the-badge&color=red)](https://github.com/AxelToa)
- [![Brice_Jones](https://img.shields.io/badge/Brice_Jones-Responsable_Prospective_et_Innovation_(CPAM)-blue?style=for-the-badge&color=red)](https://github.com/notmoebius)
- [![Malaïka Teuhi ](https://img.shields.io/badge/Malaïka_Teuhi-M1_MIAGE_parcours_IM_(UT1_2019)-blue?style=for-the-badge&color=red)](https://github.com/malaikateuhi)
- [![Yaye Astou Diaw](https://img.shields.io/badge/Yaye_Astou_Diaw-M1_MIAGE_parcours_IM_(UT1_2019)-blue?style=for-the-badge&color=red)](https://github.com/Astou-Thierno)
