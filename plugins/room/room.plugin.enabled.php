<?php
/*
@name Room
@author Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@type module
@description Modele de plugin pour les modules
*/

 include('Room.class.php');

function room_plugin_menu(&$menuItems){
	global $_;
	$menuItems[] = array('sort'=>1,'content'=>'<a href="index.php?module=room"><i class="icon-th-large"></i> Pieces</a>');
}


function room_plugin_page($_){
	if(isset($_['module']) && $_['module']=='room'){
		$roomManager = new Room();
		$rooms = $roomManager->populate();
	?>


<div class="row">
	<div class="span12">
	<ul class="nav nav-tabs">
		<?php foreach($rooms as $room){ ?>
	      <li <?php echo (isset($_['id']) && $room->getId()==$_['id']?'class="active"':''); ?>><a href="index.php?module=room&id=<?php echo $room->getId(); ?>"><i class="icon-chevron-right"></i><?php echo $room->getName(); ?></a></li>
	      <?php } ?>
	</ul>

	</div>
	</div>
	<div class="row">

	<div class="span12">
	
		<?php 

		if(isset($_['id'])){
			$room = $roomManager->getById($_['id']);
			Plugin::callHook("node_display", array($room));
		}
		?>
	
	</div>
</div>


<?php
	}
}


function room_plugin_setting_menu(){
	global $_;
	echo '<li '.(isset($_['section']) && $_['section']=='room'?'class="active"':'').'><a href="setting.php?section=room"><i class="icon-chevron-right"></i> Pièces</a></li>';
	
}


function room_plugin_setting_page(){
	global $myUser,$_;
	
	if(isset($_['section']) && $_['section']=='room' ){
		if($myUser!=false){
			$roomManager = new Room();
			$rooms = $roomManager->populate();
	?>

		<div class="span9 userBloc">


		<h1>Pièces</h1>
		<p>Gestion des pièces</p>  

		<form action="action.php?action=room_add_room" method="POST">
		<fieldset>
		    <legend>Ajout d'une pièce</legend>

		    <div class="left">
			    <label for="nameRoom">Nom</label>
			    <input type="text" id="nameRoom" name="nameRoom" placeholder="Cuisine,salon…"/>
			    <label for="descriptionRoom">Description</label>
			    <input type="text" name="descriptionRoom" id="descriptionRoom" />
			</div>

  			<div class="clear"></div>
		    <br/><button type="submit" class="btn">Ajouter</button>
	  	</fieldset>
		<br/>
	</form>

		<table class="table table-striped table-bordered table-hover">
	    <thead>
	    <tr>
	    	<th>Nom</th>
		    <th>Description</th>
	    </tr>
	    </thead>
	    
	    <?php foreach($rooms as $room){ ?>
	    <tr>
	    	<td><?php echo $room->getName(); ?></td>
		    <td><?php echo $room->getDescription(); ?></td>
		    <td><a class="btn" href="action.php?action=room_delete_room&id=<?php echo $room->getId(); ?>"><i class="icon-remove"></i></a></td>
	    </tr>
	    <?php } ?>
	    </table>
		</div>

<?php }else{ ?>

		<div id="main" class="wrapper clearfix">
			<article>
					<h3>Vous devez être connecté</h3>
			</article>
		</div>
<?php
		}
	}





}




function room_delete_room(){
	global $_;
	if($_['action']=='room_delete_room'){
		$roomManager = new Room();
		$roomManager->delete(array('id'=>$_['id']));
		header('location:setting.php?section=room');
	}
}
function room_add_room(){
	global $_;
	if($_['action']=='room_add_room'){
		$room = new Room();
		$room->setName($_['nameRoom']);
		$room->setDescription($_['descriptionRoom']);
		$room->save();
		header('location:setting.php?section=room');
	}
}

Plugin::addCss("/css/style.css"); 
//Plugin::addJs("/js/main.js"); 

Plugin::addHook("setting_menu", "room_plugin_setting_menu");  
Plugin::addHook("setting_bloc", "room_plugin_setting_page"); 
Plugin::addHook("action_post_case", "room_delete_room"); 
Plugin::addHook("action_post_case", "room_add_room"); 

Plugin::addHook("menubar_pre_home", "room_plugin_menu");  
Plugin::addHook("home", "room_plugin_page");  
?>