<?php
/*
@name Camera
@author Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@description Camera
*/



function camera_display($room){
	global $_;
	?>

	<div class="span3">
          <h5>Photo PI</h5>
		 
		 <img id="cameraPI" >
		  <button onclick="snapshot();">snapshot</button>
        <video controls="controls" width="200" height="200" autoplay="autoplay" >
      			<source src="stream.m3u8" type="application/x-mpegURL" />
    		</video>
        </div>


	<?php

}


function camera_action_camera(){
	global $_,$conf;

	switch($_['action']){
		case 'camera_refresh':
			system('raspistill -hf -w 512 -h 320  -o /var/www/yana-server/plugins/camera/view.jpg -t 0');
		break;
	}
}

Plugin::addJs('/js/main.js');
Plugin::addHook("action_post_case", "camera_action_camera"); 
Plugin::addHook("node_display", "camera_display");   
?>