<?php
	/* Nom du programme */ 
	define('PROGRAM_NAME','Yana Server');
	/* Nom de l'auteur principal */
	define('PROGRAM_AUTHOR','Valentin CARRUESCO');
	/* Préfixe de la base de données */ 
	define('MYSQL_PREFIX','yana_');
	/* Chemin vers la base SQLITE */
	define('DB_NAME','db/.database.db');
	/* Chemin vers le fichier de logs */
	define('LOG_FILE','log/.log.txt');
	/* Chemin vers le cache des avatars */
	define('AVATAR_FOLDER','cache/avatar');
	/* Chemin http vers yana */
	define('YANA_URL','http://127.0.0.1:80/yana-server');
	/* 
	* <!> Laisser à vide sauf si vous souhaitez vous auto-logguer avec un compte sans mot de passe 
	*     Ceci peut être utile pour les yana-server accessible uniquement depuis votre réseau interne
	*     Dans tous les autres cas, il serait insécurisé et donc déconseillé d'utiliser cette option.
	*     Pour vous auto-loguer avec un compte de la base yana, ecrivez le login de ce compte dans cette constante
	*/
	define('AUTO_LOGIN','');
?>