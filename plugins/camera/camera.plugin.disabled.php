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
	$menuItems[] = array('sort'=>3,'content'=>'<a href="index.php?module=camera"><i class="icon-th-large"></i> Camera</a>');
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
		 <button class="btn" onclick="window.location='action.php?action=camera_refresh'">Prendre une photo</button><br/>
		 <img class="img-polaroid img-rounded" id="cameraPI" src="plugins/camera/view.jpg<?php echo '?'.time(); ?>" ><br/>
		  
        <!--<video controls="controls" width="200" height="200" autoplay="autoplay" >
      			<source src="stream.m3u8" type="application/x-mpegURL" />
    		</video>-->
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
		  		sudo chown -R www-data:www-data /var/www/yana-server/plugins
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
			$absolute_path = getcwd()."/plugins/camera/";
			system('raspistill -hf -w 400 -h 400  -o '.$absolute_path.'view.jpg -t 0');
			header('location:index.php?module=camera');
		break;
	}
}

Plugin::addJs('/js/main.js');
Plugin::addHook("action_post_case", "camera_action_camera");  
Plugin::addHook("menubar_pre_home", "camera_plugin_menu");  
Plugin::addHook("home", "camera_plugin_page")
?>