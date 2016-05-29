<?php
/*
@name Camera
@author Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@description Plugin permettant de prendres des photos avec la camera PI depuis l'interface web
*/

function camera_plugin_menu(&$menuItems){
	global $_;
	$menuItems[] = array('sort'=>3,'content'=>'<a href="index.php?module=camera"><i class="fa fa-eye"></i> Camera</a>');
}


function camera_plugin_page($_){
	if(isset($_['module']) && $_['module']=='camera'){
	?>


<div class="row">
	<div class="span12">


	</div>
	</div>
	<div class="row">

	<div class="span12">
	
		<div class="span5">
			<h5>Photo PI</h5>
			<button class="btn" onclick="camera_refresh();">Prendre une photo</button><br/>
			<img class="img-polaroid img-rounded" id="cameraPI" src="action.php?action=camera_get_stream" ><br/>
        </div>
        <div class="span6">
        <p>
		  	Avant de pouvoir utiliser ce plugin, vous devez avoir branché la caméra RPI, puis vous devez executer les commandes suivantes dans le terminal du raspberry pi :
		  	<br/><code>
		  		sudo apt-get update && sudo apt-get upgrade
		  	</code><br/>
		  	Puis tapez<br/>
		  	<code>
		  		sudo raspi-config 
		  	</code><br/>
		  	Puis allez dans "camera" et sélectionnez "enable", redemarrez et tapez<br/>
		  	<code>
		  		sudo usermod -a -G video www-data
		  	</code><br/>
		  	Puis<br/>
		  	<code>
		  		sudo echo 'SUBSYSTEM=="vchiq",GROUP="video",MODE="0660"' > /etc/udev/rules.d/10-vchiq-permissions.rules
		  	</code><br/>
		  	Et enfin<br/>
		  	<code>
		  		sudo chown -R www-data:www-data <?php echo __DIR__ ?>
		  	</code><br/>
		  		Redémarrez et c'est ok :)
		  	
		  	
		  </p>
		</div>
	
	</div>
</div>
<?php
	}
}


function camera_action_camera(){
	global $_,$conf;

    
	switch($_['action']){
		case 'camera_refresh':
			system('raspistill -hf -w 400 -h 400  -o '.__DIR__.SLASH.'stream'.SLASH.'stream.jpg');
		break;

		case 'camera_get_stream':
			global $myUser;
			if($myUser->getId()==0) throw new Exception("Permissions insuffisantes");
			header("Content-Type: image/jpeg");
			ob_end_clean();
			$file = __DIR__.SLASH.'stream'.SLASH.'stream.jpg';
			if(!file_exists($file)) $file = __DIR__.SLASH.'stream'.SLASH.'default.jpg';
			echo file_get_contents($file);
		break;
	}
}

Plugin::addJs('/js/main.js');
Plugin::addHook("action_post_case", "camera_action_camera");  
Plugin::addHook("menubar_pre_home", "camera_plugin_menu");  
Plugin::addHook("home", "camera_plugin_page")
?>
