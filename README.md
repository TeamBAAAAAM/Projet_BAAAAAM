<h1># Projet_BAAAAAM</h1>
Projet de conception et développement d'une application web pour la dématérialisation des arrêts de travail avec la CPAM de la Haute-Garonne.

Visible ici https://teambaaaaam.github.io/Projet_BAAAAAM/
<h2>Configuration minimale</h2>
<p>SGBD : MySQL (version 5.7.56 ou ultérieure)</p>
<p>Bootstrap 3 (version 3.4.1 ou ultérieure)</p>
<p>PHP (version 5.4 ou ultérieure)</p>

<h2>Installation</h2>
  <h3>Clonage du répertoire en local</h3>
<p>Dans un terminal, taper la commande suivante :
  git clone <a href="https://github.com/TeamBAAAAAM/Projet_BAAAAAM.git">https://github.com/TeamBAAAAAM/Projet_BAAAAAM.git</a> 
</p>
<p>Il est également possible d’utiliser le lien suivant https://github.com/TeamBAAAAAM/Projet_BAAAAAM.git pour cloner le dossier contenant les différentes pages à l’aide d’un IDE.</p>

<h3>Implémentation de la base de données</h3>
<p> D'abord lancer le script de création : <a href="bd_cpam/ScriptCreationBD.sql">ScriptCreationBD.sql</a></p>
<p> Puis insérer les données initiales nécessaires (catégories et mnémoniques) : <a href="bd_cpam/ScriptDonnees.sql">ScriptDonnees.sql</a></p>
<h3>Accès à la base de données</h3>
<p>Pour la connexion à la BD, il est nécéssaire de renseigner les données de connexion dans un fichier de nom ".env" (cf. <strong>Gestion du fichier ENV</strong></p>

<h2>Gestion du fichier ENV</h2>
<p>Créer un fichier appelé ".env".</p>
<p>Ce fichier contient les paramètres de connexions au différents serveurs. Ces paramètres correspondent à des variables d'environnement qui sont supprimés à la fin de l'exécution du script PHP.</p>

<p><strong><em>Important : Il est nécessaire d'ajouter le nom de ce fichier au ".gitignore" ! 
Il suffit de copier le nom du fichier ".env", et le disposer sur une seule ligne dans le fichier ".gitignore". 
Cette oprération est importante car sans cela, le fichier ".env" qui contient des données très sensibles sera récupérable à partir de la commande de clonnage de GIT. De plus, ce fichier est propre à une machine en particulier, c'est-à-dire que chaque paramètre du fichier ".env" ne fonctionnera pas forcément sur autre machine (cela peut être dû à des différences au niveau du système d'exploitation, des serveurs utilisés, etc.).</em></strong></p>

<p>Pour le contenu de ce fichier, voici quelques règles à respecter :</p>
<p>
- Une clé "KEY" correspond au nom d'une variable à laquelle est affectée une valeur "VALUE".<br>
- Une variable d'environnement est déclarée de cette façon : KEY=VALUE.<br>
- Les commentaires sont indiqués par le caractère "#".<br>
- Il est possible d'ajouter un commentaire après la déclaration d'une variable d'environnement.<br>
<em>Exemple :<br>KEY=VALUE   #Ceci est un commentaire</em><br>
- Les noms de variables doivent impérativement correspondre à ceux précisés dans l'exemple ci-dessous !<br>
</p>
  
<h3>--- Exemple de fichier ".env" ---</h3>
<p><strong><em>NB : Les "_____" doivent être remplacées par les valeurs correspondantes !</em></strong></p>
<p>
# ------------------------------------------------------------------</br>
#  VARIABLES D'ENVIRONNEMENT DE CONNEXION À LA BASE DE DONNÉES</br>
# ------------------------------------------------------------------</br>
# Nom du host [MYSQL_HOST]</br>
MYSQL_HOST=</br>
# Nom d'utilisateur [MYSQL_USER]</br>
MYSQL_USER=</br>
# Mot de passe [MYSQL_PWD]</br>
MYSQL_PWD=</br>
# Nom de la base de données [MYSQL_BD]</br>
MYSQL_BD=</br>
# Numéro du port de connexion [MYSQL_PORT]</br>
MYSQL_PORT=</br>
</br>
# ------------------------------------------------------------------</br>
# 	VARIABLES D'ENVIRONNEMENT DE CONNEXION AU SERVEUR FTP</br>
# ------------------------------------------------------------------</br>
# Nom du host [FTP_HOST]</br>
FTP_HOST=</br>
# Nom d'utilisateur [FTP_USER]</br>              
FTP_USER=</br>
# Mot de passe [FTP_PWD]</br>
FTP_PWD=</br>
# Numéro du port de connexion [FTP_PORT]</br>
FTP_PORT=</br>
</br>
# ------------------------------------------------------------------</br>
# 	VARIABLE D'ENVIRONNEMENT DU CHEMIN VERS L'ESPACE DE </br>
#   STOCKAGE DES PIECES (sur le serveur FTP)</br>
# ------------------------------------------------------------------</br>
# N.B. : À partir de la racine [STORAGE_PATH]</br>
STORAGE_PATH=</br>
</br>
# ------------------------------------------------------------------</br>
# 	VARIABLES D'ENVIRONNEMENT POUR L'ENVOI DES MAILS</br>
# ------------------------------------------------------------------</br>
# Nom du host [SMTP_HOST]</br>
SMTP_HOST=</br>
# Numéro du port [SMTP_PORT]</br>			
SMTP_PORT=</br>
# Nom d'utilisateur [SMTP_USER]</br>
SMTP_USER=</br>
# Mot de passe [SMTP_PWD]</br>
SMTP_PWD=</br>
# Adresse mail de l'expéditeur [SENDMAIL_FROM]</br>
SENDMAIL_FROM=</br>
# Nom de l'expéditeur [SENDMAIL_NAME]</br>
SENDMAIL_NAME=</br>
</br>
# ------------------------------------------------------------------</br>
# 	VARIABLES D'ENVIRONNEMENT POUR L'ENREGISTREMENT DES FICHIERS CSV</br>
# ------------------------------------------------------------------</br>
# Nom du fichier CSV d'injection dans DIADEME [CSV_INJECTION_NAME_FILE]</br>
CSV_INJECTION_NAME_FILE=injection_file.csv</br>
# Entête du fichier CSV pour l'injection des pièces dans DIADEME [CSV_INJECTION_HEADER]</br>
CSV_INJECTION_HEADER="ADDICT;Processus;Archivage;Date Réception;Index Métier;Date Événement;Commentaire;DocPorteur"</br>
# Nom du fichier CSV de sauvegarde des dossiers [CSV_FOLDERS_NAME_FILE]</br>
CSV_FOLDERS_NAME_FILE=list_folders_file.csv</br>
</br>
# Nom du host [CSV_FTP_HOST]</br>
CSV_FTP_HOST=</br>
# Nom d'utilisateur [CSV_FTP_USER]</br>
CSV_FTP_USER=</br>
# Mot de passe [CSV_FTP_PWD]</br>
CSV_FTP_PWD=</br>
# Numéro du port de connexion [CSV_FTP_PORT]</br>
CSV_FTP_PORT=21</br>
</br>
# N.B. : À partir de la racine [CSV_INJECTION_FILE_PATH]</br>
CSV_INJECTION_FILE_PATH="Injection DIADEME"
# N.B. : À partir de la racine [CSV_FOLDERS_FILE_PATH]</br>
CSV_FOLDERS_FILE_PATH="Liste des Dossiers"</br>
</br>
# Adresse mail de l'expéditeur [CSV_SENDMAIL_TO]</br>
CSV_SENDMAIL_TO=</br>
</p>
<p><em>La variable globale 'STORAGE_PATH' correspond au chemin menant au dossier de destination des fichiers enregistrés.</em></p>
