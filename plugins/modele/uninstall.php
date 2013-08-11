<?php
/* 
	Le code contenu dans cette page ne sera éxecuté qu'à la désactivation du plugin 
	Vous pouvez donc l'utiliser pour supprimer des tables SQLite, des dossiers, ou executer une action
	qui ne doit se lancer qu'à la désinstallation ex :
*/
$table = new modele();
$table->drop();
?>