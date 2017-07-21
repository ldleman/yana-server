<?php
	
try {
	
	mb_internal_encoding('UTF-8');
	require_once(__DIR__.'/function.php');
	$_ = array_map('secure_user_vars', array_merge($_POST, $_GET));
	require_once('class/Plugin.class.php');
	$entityFolder = __DIR__.'/class/';
	

?>
<!doctype html>
<html class="no-js" lang="">
    <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
      <title>Installation</title>
      <meta name="description" content="">
      <meta name="viewport" content="width=device-width, initial-scale=1">
  	  <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,800,700,400italic,600italic,700italic,800italic,300italic" rel="stylesheet" type="text/css">
  	  <link rel="stylesheet" href="css/bootflat.min.css">
      <link rel="stylesheet" href="css/font-awesome.min.css">
      <link rel="stylesheet" href="css/main.css">
	  <?php echo Plugin::callCss("css"); ?>
    </head>
    <body>
        <div id="wrap">
    
		<!-- menu -->
		<nav role="navigation" class="navbar navbar-inverse">
                  <div class="container-fluid">
                    <div class="navbar-header">
                      <button data-target="#menuBar" data-toggle="collapse" class="navbar-toggle" type="button">
                        <span class="sr-only">Toggle navigation</span>
                        <i class="fa fa-bars"></i>
                      </button>
                      <a href="index.php" class="navbar-brand">Installation</a>
                    </div>

					<div id="menuBar" class="collapse navbar-collapse">
                    </div><!-- /.navbar-collapse -->
					
					
					
                  </div><!-- /.container-fluid -->
                </nav>
		<!-- menu -->
		

		
		<!-- body -->
		<div class="container-fluid">
		
		
		<!-- messages -->
		<div id="message" class="alert alert-{{type.class}} {{#closable}}alert-dismissable{{/closable}} noDisplay">
            <strong>{{type.label}}</strong> <span>{{label}}</span>
        </div>
<?php
	
	
	$entities = array();
	
	foreach(glob(__DIR__.'/connector/*.class.php') as $classFile):
		require_once($classFile);
		$className = str_replace('.class.php','',basename($classFile));
		$entities[$className] = $className::label.' - '.$className::description;
	endforeach;
	
	//check prerequisite
	if(file_exists(__DIR__.'/constant.php')) throw new Exception('Le script est déja installé, pour recommencer l\'installation, supprimez le fichier constant.php');
	if(!is_writable (__DIR__)) throw new Exception('Le dossier '.__DIR__.' doit être accessible en ecriture, merci de taper la commande linux <br/><code>sudo chown -R www-data:www-data '.__ROOT__.'</code><br/> ou de régler le dossier en écriture via votre client ftp');
	if(!file_exists(__DIR__.'/file')) mkdir(__DIR__.'/file',0755,true);
	if(!file_exists(__DIR__.'/file/avatar')) mkdir(__DIR__.'/file/avatar',0755,true);
	
	//if(!extension_loaded('gd') || !function_exists('gd_info'))  throw new Exception('L\'extension php GD2  est requise, veuillez installer GD2 (sous linux : <code>sudo apt-get install php5-gd && service apache2 restart</code>)');
	//if(!in_array('sqlite',PDO::getAvailableDrivers())) throw new Exception('Le driver SQLITE est requis, veuillez installer sqlite3 (sous linux : <code>sudo apt-get install php5-sqlite && service apache2 restart</code>)');
	
	
	
	if(isset($_['install'])){
	

	$constantStream = file_get_contents(__DIR__.'/constant.sample.php');
	

	if(!isset($_['host'])) $_['host'] = '';
	if(!isset($_['login'])) $_['login'] = '';
	if(!isset($_['password'])) $_['password'] = '';
	if(!isset($_['database'])) $_['database'] = '';

	$constantStream = str_replace(
	array("{{BASE_SGBD}}","{{BASE_HOST}}","{{BASE_NAME}}","{{BASE_LOGIN}}","{{BASE_PASSWORD}}","{{ROOT_URL}}"),
	array($_['entity'],$_['host'],$_['database'],$_['login'],$_['password'],$_['root']),$constantStream);

	file_put_contents(__DIR__.'/constant.php',$constantStream);
	
	require_once(__DIR__.'/constant.php');
	require_once(__ROOT__.'class'.SLASH.'Entity.class.php');

	//install entities
	Entity::install(__ROOT__.'class');

	foreach(array(
		'Salon'=>"",
		'Cuisine'=>"L'endroit ou on dégomme les coockies",
		'Chambre'=>"Le coins pour dormir (entre autre...)",
		'WC'=>"Oui..bon...voilà quoi.",
		'Bureau'=>"Le coin ou ça bosse dur !",
		'Garage'=>"L'espace qui sert a tout stocker sauf une voiture"
		) as $label=>$description){
		$room = new Room();
		$room->label = $label;
		$room->description = $description;
		$room->save();
	}

	//Activation des plugins par défaut
	foreach(array(
		"fr.idleman.story",
		"fr.idleman.monitoring",
		"fr.idleman.sensor") as $plugin):
		Plugin::state($plugin,true);
	endforeach;
	//create admin rank
    $rank = new Rank();
    $rank->label = 'Administrateur';
    $rank->description = 'That\'s a fucking god dude !';
    $rank->save();

	//create default user
    $admin = new User();
    $admin->login = 'admin';
    $admin->password = User::password_encrypt('admin');
    $admin->firstname = 'Chuck';
    $admin->name = 'NORRIS'; 
    $admin->superadmin = true; 
    $admin->rank = $rank->id;
    $admin->save();
  

    $sections = array();
    Plugin::callHook('section',array(&$sections));
    foreach($sections as $section=>$description): 
    	$right = new Right();
    	$right->rank = $rank->id;
    	$right->section = $section;
    	$right->read = true;
    	$right->edit = true;
    	$right->delete = true;
    	$right->configure = true;
    	$right->save();
    endforeach;
	
	$firstDash = false;
	foreach(array(
		'Général'=>"fa-bars",
		'Salon'=>"fa-tv",
		'Cuisine'=>"fa-spoon",
		'Monitoring'=>"fa-bar-chart",
		'Garage'=>"fa-car"
		) as $label=>$icon){
	    //Create default dashboard
	    $dashboard = new Dashboard();
	    $dashboard->user = $admin->id;
	    $dashboard->label = $label;
	    $dashboard->icon = $icon;
	    $dashboard->default = !$firstDash;
	    $dashboard->save();
	    $firstDash = !$firstDash ? $dashboard->id: $firstDash;
    }

    //Create clock widget
    $widget = new Widget();
    $widget->model = 'clock';
    $widget->position = 2;
    $widget->minified = false;
    $widget->dashboard = $firstDash;
    $widget->save();

    //Create clock widget
    $widget = new Widget();
    $widget->model = 'profile';
    $widget->position = 1;
    $widget->minified = false;
    $widget->dashboard = $firstDash;
    $widget->save();

    ?>

	<div class="alert alert-success alert-dismissable">
		<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
		<strong>Succès!</strong> La base est bien installée, l'utilisateur par défaut est <code>admin:admin</code>, pensez à changer le mot de passe rapidemment. 
	</div>
	<a class="btn btn-primary" href="index.php">Revenir à l'index</a>
	<?php 

	}else{
		
		$root = 'http'.((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')|| $_SERVER['SERVER_PORT'] == 443?'s':'').'://'.$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
    	$root = str_replace("/install.php", "", $root );
    	$parts = explode('?',$root);
    	$root = array_shift($parts);
	?>
	<div class="row">
	<form class="col-md-3" action="install.php" method="POST">
		<h3>Installation</h3>
		<p>Merci de bien vouloir remplir les champs çi dessous</p>
		<label for="entity">Base de donnée</label>
		<select class="form-control" name="entity" onchange="window.location='install.php?sgbd='+$(this).val()">
		<option value="">-</option>
		<?php foreach($entities as $class=>$label): ?>
		<option <?php echo (isset($_['sgbd']) && $_['sgbd']==$class ? 'selected="selected"': '') ?> value="<?php echo $class ?>"><?php echo $label; ?></option>
		<?php endforeach; ?>
		</select><br/>

		<?php if(isset($_['sgbd']) && $_['sgbd']!=''): 
			require_once(__DIR__.'/connector/'.$_['sgbd'].'.class.php');
			foreach($_['sgbd']::fields() as $field):
		?>
			<label for="<?php echo $field['id']; ?>"><?php echo $field['label']; ?></label><br/>
			<?php if(!isset($field['comment'])): ?><small><?php echo $field['comment']; ?></small><br/><?php endif; ?>
			<input type="text" class="form-control" value="<?php echo $field['default']; ?>" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>"/><br/>
		<?php endforeach;  ?>


		<label for="root">Adresse web de YANA</label><br/>
		<input type="text" class="form-control" name="root" id="root" value="<?php echo $root; ?>"/><br/>
		
		<input type="submit" class="btn btn-primary" value="Installer" name="install"><br/><br/>
		<?php endif; ?>
	</form>
	</div>
	<?php
	}
} catch (Exception $e) { 

	unlink(__DIR__.'/constant.php');
	?>
<div class="alert alert-danger">
	<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
	 <strong>Oops!</strong> <?php echo $e->getMessage().' - '.$e->getFile().' L'.$e->getLine().'<hr/><pre>'.$e->getTraceAsString().'</pre>';
    ?> 
</div>
<?php 
} ?>

</div>
</div>
		<!-- body -->
 
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.min.js"><\/script>')</script>
        <script src="js/vendor/bootflat.min.js"></script>
        <script src="js/vendor/mustache.min.js"></script>
		<script src="js/plugins.js"></script>
        <script src="js/vendor/jquery-ui.min.js"></script>
        <script src="js/main.js"></script>
		<div class="footer"></div>
    </body>
</html>
