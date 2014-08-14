<?php
/*
@name Fichier
@author Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@description Gestion des fichiers et medias
@type component
*/

//Check et creation de la table si non existente
require_once('FileUploaded.class.php');
$fileManager = new FileUploaded();
$fileManager->checkTable(true);

Plugin::addCss("/css/style.css",true); 
Plugin::addJs("/js/dropzone.min.js",true); 
Plugin::addJs("/js/main.js",true); 

?>