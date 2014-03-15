<?php
session_start();
unset($myUser);
error_reporting(E_ALL);
ini_set('display_errors','On');
require_once('constant.php');
function __autoload($class_name) {
    include 'classes/'.$class_name . '.class.php';
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Installation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">

    <!-- Le styles -->
    <link href="templates/default/css/bootstrap.min.css" rel="stylesheet">
    <link href="templates/default/css/jquery-ui-1.10.3.custom.css" rel="stylesheet">
    <link href="templates/default/css/style.css" rel="stylesheet">

    <link href="templates/default/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="ico/favicon.png">
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top" id="header">
      
      <div class="navbar-inner">

        <div class="container">

          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="index.php"><?php echo PROGRAM_NAME; ?></a>
          <div class="nav-collapse collapse">
            <ul class="nav">
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>





<?php
if(isset($_POST['install'])){
 //On récupère le chemin de yana
  $path_yana =  substr($_SERVER['SCRIPT_FILENAME'],0,-11);
 
    if(is_writable($path_yana)) {
      //Instanciation des managers d'entités
    	$user = new User();
    	$configuration = new Configuration();
    	$right = new Right();
    	$rank = new Rank();
    	$section = new Section();
      $event = new Event();

      //Création des tables SQL
    	$configuration->create();
    	$user->create();
    	$right->create();
    	$rank->create();
    	$section->create();
      $event->create();

      $configuration->put('UPDATE_URL','http://update.idleman.fr/yana?callback=?');
      $configuration->put('DEFAULT_THEME','default');
      $configuration->put('COOKIE_NAME','yana');
      $configuration->put('COOKIE_LIFETIME','7');
      $configuration->put('VOCAL_ENTITY_NAME',(isset($_POST['entityName'])?$_POST['entityName']:'YANA'));
      $configuration->put('PROGRAM_VERSION','3.0.6');


      //Création du rang admin
    	$rank = new Rank();
    	$rank->setLabel('admin');
    	$rank->save();

      //Déclaration des sections du programme
      $sections = array('event','vocal','user','plugin','configuration','admin');

      //Création des sections déclarées et attribution de tous les droits sur toutes ces sections pour l'admin
      foreach($sections as $sectionName){
        $s = New Section();
        $s->setLabel($sectionName);
        $s->save();  

      	$r = New Right();
      	$r->setSection($s->getId());
      	$r->setRead('1');
      	$r->setDelete('1');
      	$r->setCreate('1');
      	$r->setUpdate('1');
      	$r->setRank($rank->getId());
      	$r->save();
      }
    	
      //Creation du premier compte et assignation en admin
    	$user->setMail($_POST['email']);
    	$user->setName($_POST['name']);
    	$user->setFirstName($_POST['firstname']);
    	$user->setPassword($_POST['password']);
    	$user->setLogin($_POST['login']);
    	$user->setToken(sha1(time().rand(0,1000)));
    	$user->setState(1);
    	$user->setRank($rank->getId());
    	$user->save();

    	Plugin::enabled('relay-relay');
      Plugin::enabled('wireRelay-relay');
    	Plugin::enabled('vocal_infos-vocalinfo');
    	Plugin::enabled('room-room');
      Plugin::enabled('eventManager-eventmanager');
      
  }else{
    ?>
        <div id="body" class="container">
        <div class="alert alert-error">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Echec de l'Installation : </strong>Vous n'avez pas la permission d'écrire sur le serveur web! (avez vous fait <code>chown -R www-data:www-data <?echo $path_yana;?> </code>?) <a class="brand" href="install.php">Réessayer</a>.
      </div>
<?php exit(); } ?>
	 <div id="body" class="container">
    <div class="alert alert-info">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>Installation terminée: </strong> L'installation est terminée, vous devez supprimer le fichier <code>yana-server/install.php</code> par mesure de sécurité, puis revenir sur <a class="brand" href="index.php">l'accueil</a>.
  </div>
<?php }else{ ?>
      <div id="body" class="container">
          <div class="alert alert-error">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <strong>Installation: </strong> Vous devez remplir le formulaire ci dessous pour installer l'application.
        </div>

        <form class="form-horizontal" action="install.php" method="POST">
      	<div class="control-group">
          <label class="control-label" for="inputName">Nom</label>
          <div class="controls">
            <input type="text" name="name" id="inputName" placeholder="">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="inputFirstName">Prenom</label>
          <div class="controls">
            <input type="text" name="firstname" id="inputFirstName" placeholder="">
          </div>
        </div>
          <div class="control-group">
          <label class="control-label" for="inputEmail">Email</label>
          <div class="controls">
            <input type="text" name="email" id="inputEmail" placeholder="Email">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="inputLogin">Login</label>
          <div class="controls">
            <input type="text" name="login" id="inputLogin" placeholder="Login">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="inputPassword">Password</label>
          <div class="controls">
            <input type="password" name="password" name="inputPassword" placeholder="Password">
          </div>
        </div>

         <div class="control-group">
          <label class="control-label" for="inputPassword">Nom de l'entité vocale</label>
          <div class="controls">
            <input type="text" name="entityName" value="Yana">
          </div>
        </div>


        

        <div class="control-group">
          <div class="controls">
            <button type="submit" name="install" class="btn">Installer</button>
          </div>
        </div>
      </form>
	<?php } ?>
  

 <div class="well well-small" id="footer">CC by nc sa <?php echo PROGRAM_NAME ?>

 </div>
 </div> <!-- /container -->



    <!-- Le javascript
    ================================================== -->
    <script src="templates/default/js/jquery.min.js"></script>
    <script src="templates/default/js/bootstrap.min.js"></script>
    <script src="templates/default/js/jquery.ui.custom.min.js"></script>
    <script src="templates/default/js/jquery.yana.js"></script>
	<script src="templates/default/js/script.js"></script>
  </body>
</html>
