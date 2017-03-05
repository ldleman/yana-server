<?php
define('SLASH',DIRECTORY_SEPARATOR);
define('__ROOT__',dirname(__FILE__).SLASH);
define('TIME_ZONE','Europe/Paris');
define('ENTITY_PREFIX', '');
define('DATABASE_PATH',__ROOT__.'database/.db');
define('LOG_PATH',__ROOT__.'log');
define('FILE_PATH','file'.SLASH);
define('PLUGIN_PATH','plugin'.SLASH);

define('AVATAR_PATH',FILE_PATH.'avatar'.SLASH);

define('BASE_SGBD','Mysql');
define('BASE_HOST','127.0.0.1');
define('BASE_NAME','yana');
define('BASE_LOGIN','yana');
define('BASE_PASSWORD','yana');

/* Port du serveur socket */
define('SOCKET_PORT',9999);
/* Nombre maxium de clients sur le serveur socket */
define('SOCKET_MAX_CLIENTS',20);

define('PROGRAM_NAME','Core');
define('SOURCE_VERSION','4.0');
define('BASE_VERSION','2.0');


/* 
* <!> Laisser à vide sauf si vous souhaitez vous auto-logguer avec un compte sans mot de passe 
*     Ceci peut être utile pour les yana-server accessible uniquement depuis votre réseau interne
*     Dans tous les autres cas, il serait insécurisé et donc déconseillé d'utiliser cette option.
*     Pour vous auto-loguer avec un compte de la base yana, ecrivez le login de ce compte dans cette constante
*/
define('AUTO_LOGIN','');

?>