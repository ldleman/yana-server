<?php
/*
*/

session_start();
date_default_timezone_set('Europe/Paris'); 
//TODO cron auto install
// echo "*/1 * * * * root wget http://127.0.0.1/yana-server/action.php?action=crontab -O /dev/null 2>&1" > /etc/cron.d/yana-server
unset($myUser);
error_reporting(E_ALL);
ini_set('display_errors','On');
if(!file_exists(__DIR__.'/constant.php')) copy(__DIR__.'/constant.sample.php', __DIR__.'/constant.php');
require_once(__DIR__.'/constant.php');

function __autoload($class_name) {
    include 'classes/'.$class_name . '.class.php';
}
?>
<!DOCTYPE html>
<html lang="fr">
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
          </button>
          <a class="brand" href="index.php"><?php echo PROGRAM_NAME; ?></a>
          <div class="nav-collapse collapse">
            <ul class="nav">
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
<div id="body" class="container">
<?php

//On récupère le chemin http de yana
$path_yana =  substr($_SERVER['SCRIPT_FILENAME'],0,-11);

if(isset($_POST['install'])){
 
    try{
    if(!isset($_POST['password']) || trim($_POST['password'])=='' || !isset($_POST['login']) || trim($_POST['login'])=='' ) 
      throw new Exception("L'identifiant et le mot de passe ne peuvent être vide");
    
	  //Supression de l'ancienne base si elle existe
	  if(file_exists(DB_NAME) && filesize(DB_NAME)>0) throw new Exception("La base ".DB_NAME." existe déjà, pour recommencer l'installation, merci de supprimer le fichier ".DB_NAME." puis de revenir sur cette page");
    
      //Instanciation des managers d'entités
      $user = new User();
      $configuration = new Configuration();
      $right = new Right();
      $rank = new Rank();
      $section = new Section();
      $event = new Event();
      $client = new Client();
	    $device = new Device();
      $personnality = new Personality();

      if(isset($_POST['url'])){
        $const = file_get_contents("constant.php");
        file_put_contents('constant.php', (preg_replace("/(define\(\'YANA_URL\'\,\')(.*)('\)\;)/", "$1".$_POST['url']."$3", $const)));
      }


      //Création des tables SQL
      $configuration->create();
      $user->create();
      $right->create();
      $rank->create();
      $section->create();
      $event->create();
  
	    $device->create();
      $personnality->create();
      $personnality->birth();

      $configuration->put('UPDATE_URL','http://update.idleman.fr/yana?callback=?');
      $configuration->put('DEFAULT_THEME','default');
      $configuration->put('COOKIE_NAME','yana');
      $configuration->put('COOKIE_LIFETIME','7');
      $configuration->put('VOCAL_ENTITY_NAME','YANA');
      $configuration->put('PROGRAM_VERSION','3.0.6');
	  $configuration->put('HOME_PAGE','index.php');
	  $configuration->put('VOCAL_SENSITIVITY','0.0');
      $configuration->put('YANA_LATITUDE','24.8235817');
	  $configuration->put('YANA_LONGITUDE','-75.5070352');
	  
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
    	
      $personalities = array('John Travolta','Jeff Buckley','Tom Cruise','John Lennon','Emmet Brown','Geo trouvetou','Luke Skywalker','Mac Gyver','Marty McFly','The Doctor');
      $im = $personalities[rand(0,count($personalities)-1)];
      list($fn,$n) = explode(' ',$im);
      //Creation du premier compte et assignation en admin
    	$user->setMail($_POST['email']);
    	$user->setPassword($_POST['password']);
    	$user->setLogin($_POST['login']);
      $user->setFirstName($fn);
      $user->setName($n);
    	$user->setToken(sha1(time().rand(0,1000)));
    	$user->setState(1);
    	$user->setRank($rank->getId());
    	$user->save();

      global $myUser;
      $myUser = $user;

      foreach(array('radioRelay','wireRelay','vocal_infos','speechcommands','profile','room','story','dashboard','dashboard-monitoring') as $plugin):
        Plugin::enabled($plugin.'-'.$plugin);
      endforeach;
	
      $notices = array();
      if(function_exists('curl_init')){
        $url="http://idleman.fr/yana/notice.php?code=justavoidspamrequest";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        $html = curl_exec($ch);
        curl_close($ch);
  	    if($html!==false)
          $notices = json_decode($html,true);
        
        if(!is_array($notices)) $notices = array();
      }

?>
	
    <div class="alert alert-info">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>Installation terminée: </strong> L'installation est terminée, vous devez supprimer le fichier <code>yana-server/install.php</code> par mesure de sécurité, puis revenir sur <a class="brand" href="index.php">l'accueil</a>.
    </div>


    <?php foreach($notices as $notice): ?>
      <div class="alert alert-<?php  echo $notice['type']; ?>">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong><?php  echo $notice['title']; ?>: </strong> <?php  echo $notice['content']; ?>.
      </div
    <?php endforeach; ?>

<?php }catch(Exception $e){ ?>
  <div class="alert alert-error">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Echec de l'Installation : </strong> <?php echo $e->getMessage(); ?> <a class="brand" href="install.php">Réessayer</a>.
      </div>
<?php
}
}else{ 
?>
      
	  
	  <?php 
	  
	  /*tests*/
		$tests = array();
		
		if(!is_writable($path_yana)) $tests['error'][] = "Le dossier <b>".$path_yana."</b> n'est pas accessible en écriture. <br/>Pour résoudre ce problème, merci de taper la commande suivante dans le shell <code>sudo chown -R www-data:www-data ".$path_yana."</code> ";
		if(!class_exists('SQLite3')) $tests['error'][] = "Le pré-requis SQLITE3 n'est pas installé. <br/>Pour résoudre ce problème, merci de taper la commande suivante dans le shell <code>sudo apt-get install sqlite3 php5-sqlite</code> ";
		


		$out = exec('whereis gpio',$out);
		if($out == ''){ 
      $tests['warning'][] = "La librairie Wiring pi ne semble pas installé sur le rpi, merci de vérifier l'existence du binaire GPIO sur la machine.";
		}else{
      require_once(__DIR__.'/classes/Gpio.class.php');
      $out = trim(str_replace('gpio: ','',$out));
      if($out != GPIO::GPIO_DEFAULT_PATH) $tests['warning'][] = "Le chemin de l'executable de wiring pi est à modifier dans classes/Gpio.class.php, remplacer <code>".GPIO::GPIO_DEFAULT_PATH."</code> par <code>".$out."</code>.";
    
    }

    
    

		if(function_exists('posix_getpwuid')){
			
			$permissions = array('root:www-data'=>'plugins/relay/radioEmission');
			foreach($permissions as $key=>$file){
				if(file_exists($file)){
					list($o,$g) = explode(':',$key);
					$owner = posix_getpwuid(fileowner($file));
					$group = posix_getgrgid(filegroup($file));
				  if($owner['name']!=$o || $group['name'] !=$g) $tests['warning'][] = 'Le fichier <strong>'.$path_yana.$file.'</strong> devrait avoir <i>'.$o.'</i> comme proprietaire et <i>'.$g.'</i> comme groupe, <strong>'.$path_yana.$file.'</strong> pourrait ne pas fonctionner comme attendu, pour résoudre le problème, tapez la commande <code>sudo chown root:www-data '.$path_yana.$file.' && sudo chmod +s '.$path_yana.$file.'</code>';
        }
			}
		}else{
			$tests['warning'][] = 'Impossible de vérifier les droits sur les fichiers sensibles, librairie posix manquante';
		}
		
		foreach($tests as $type=>$messages){
			foreach($messages as $message){
			echo 
			'<div class="alert alert-'.$type.'">
				<strong>'.$type.': </strong> '.$message.' 
			 </div>';
			}
		}
		
		if(!isset($tests['error'])){
		

    if(strpos($_SERVER['HTTP_HOST'], ':') !==false){
      list($host,$port) = explode(':',$_SERVER['HTTP_HOST']);
    }else{
      $host = $_SERVER['HTTP_HOST'];
      $port = 80;
    }
    $actionUrl = 'http://'.$host.':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
    $actionUrl = str_replace("/install.php", "", $actionUrl );
 
	  ?>
	  
          <div class="alert alert-info">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <strong>Installation: </strong> Vous devez remplir le formulaire ci dessous pour installer l'application.
        </div>

        <form class="form-horizontal" action="install.php" method="POST">
          
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
          <label class="control-label" for="inputEmail">Email</label>
          <div class="controls">
            <input type="text" name="email" id="inputEmail" placeholder="Email">
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="inputUrl">Adresse de yana</label>
          <div class="controls">

            <input type="text" name="url" id="inputUrl" placeholder="http://" value="<?php echo $actionUrl; ?>">
          </div>
        </div>

        <div class="control-group">
          <div class="controls">
            <button type="submit" name="install" class="btn">Installer</button>
          </div>
        </div>
      </form>
	<?php }} ?>
  

 <div class="navbar navbar-inverse navbar-fixed-bottom" id="footer">CC by nc sa <?php echo PROGRAM_NAME ?>

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
