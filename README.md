# Projet_BAAAAAM
Projet de conception et développement d'une application web pour la dématérialisation des arrêts de travail avec la CPAM de la Haute-Garonne.

Visible ici https://teambaaaaam.github.io/Projet_BAAAAAM/
<h3>Configuration minimale</h3>
<p>SGBD : MySQL (version 5.7.56 ou ultérieure)</p>
<p>Bootstrap 3 (version 3.4.1 ou ultérieure)</p>
<p>PHP (version 5.4 ou ultérieure)</p>

<h3>Installation</h3>
<ul><li>Clonage du répertoire en local</li>
<p>Dans un terminal, taper la commande suivante :
  git clone <a href="https://github.com/TeamBAAAAAM/Projet_BAAAAAM.git">https://github.com/TeamBAAAAAM/Projet_BAAAAAM.git</a> 
</p>
<p>Il est également possible d’utiliser le lien suivant https://github.com/TeamBAAAAAM/Projet_BAAAAAM.git pour cloner le dossier contenant les différentes pages à l’aide d’un IDE.</p>

<li>Création de la base de données</li>
<p> Script de création : <a href="bd_cpam/ScriptCreationBD.sql">ScriptCreationBD.sql</a></p>
<li>Accès à la base de données</li>
<p>Dans le fichier <a href="fonctions.php">fonctions.php</a>, vérifier les variables de connexion et les modifier si nécessaire</p>
<img src="README_img/var_connexion.png" width="350px">
</ul>

<p> La variable globale 'STORAGE_PATH' correspond au chemin menant au dossier de destination des fichiers enregistrés. </p>
<p> La valeur de 'STORAGE_PATH' est à définir au début du fichier 'fonctions.php'. </p>
