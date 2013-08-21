<?php
session_start();
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
    <link href="templates/default/css/bootstrap.css" rel="stylesheet">
    <link href="templates/default/css/jquery-ui-1.10.3.custom.css" rel="stylesheet">
    <link href="templates/default/css/style.css" rel="stylesheet">

    <link href="templates/default/css/bootstrap-responsive.css" rel="stylesheet">
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

  if(is_writable($path_yana))
  {
	$user = new User();
	$configuration = new Configuration();
	$right = new Right();
	$rank = new Rank();
	$section = new Section();

	$configuration->create();
	$user->create();
	$right->create();
	$rank->create();
	$section->create();

	$rank = new Rank();
	$rank->setLabel('admin');
	$rank->save();

	$s1 = New Section();
	$s1->setLabel('configuration');
	$s1->save();	

	$s2 = New Section();
	$s2->setLabel('plugin');
	$s2->save();	

	$s3 = New Section();
	$s3->setLabel('user');
	$s3->save();


  $s4 = New Section();
  $s4->setLabel('vocal');
  $s4->save();  

	$r1 = New Right();
	$r1->setSection('1');
	$r1->setRead('1');
	$r1->setDelete('1');
	$r1->setCreate('1');
	$r1->setUpdate('1');
	$r1->setRank('1');
	$r1->save();

	$r2 = New Right();
	$r2->setSection('2');
	$r2->setRead('1');
	$r2->setDelete('1');
	$r2->setCreate('1');
	$r2->setUpdate('1');
	$r2->setRank('1');
	$r2->save();

	$r3 = New Right();
	$r3->setSection('3');
	$r3->setRead('1');
	$r3->setDelete('1');
	$r3->setCreate('1');
	$r3->setUpdate('1');
	$r3->setRank('1');
	$r3->save();

  $r4 = New Right();
  $r4->setSection('4');
  $r4->setRead('1');
  $r4->setDelete('1');
  $r4->setCreate('1');
  $r4->setUpdate('1');
  $r4->setRank('1');
  $r4->save();
								
	$user->setMail($_POST['email']);
	$user->setName($_POST['name']);
	$user->setFirstName($_POST['firstname']);
	$user->setPassword($_POST['password']);
	$user->setLogin($_POST['login']);
	$user->setToken(sha1(time().rand(0,1000)));
	$user->setState(1);
	$user->setRank(1);
	$user->save();

	Plugin::enabled('relay-relay');
	Plugin::enabled('vocal_infos-vocal_infos');
	Plugin::enabled('room-room');
  }
  else
  {
    ?>
    <div id="body" class="container">
    <div class="alert alert-error">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>Echec de l'Installation : </strong>Vous n'avez pas la permission d'écrire sur le serveur web! (avez vous fait <code>chown -R www-data:www-data <?echo $path_yana;?></code>?) <a class="brand" href="install.php">Réessayer</a>.
  </div>
    <?
    exit();

  }
	?>
	 <div id="body" class="container">
    <div class="alert alert-info">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>Installation terminée: </strong> L'installation est terminée, vous pouvez supprimer ce fichier, puis revenir sur <a class="brand" href="index.php">l'accueil</a>.
  </div> 
	<?php
}else{
	?>
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
    <div class="controls">
      <button type="submit" name="install" class="btn">Installer</button>
    </div>
  </div>
</form>
	<?php
}



?>
  

 








 <div class="well well-small" id="footer">Copyright <?php echo PROGRAM_NAME.' '.PROGRAM_VERSION; ?>

 </div>
 </div> <!-- /container -->



    <!-- Le javascript
    ================================================== -->
    <script src="templates/default/js/jquery.min.js"></script>
    <script src="templates/default/js/bootstrap.min.js"></script>
    <script src="templates/default/js/jquery.ui.custom.min.js"></script>
    <script src="templates/default/js/jquery.sys1.js"></script>
	<script src="templates/default/js/script.js"></script>
  </body>
</html>
