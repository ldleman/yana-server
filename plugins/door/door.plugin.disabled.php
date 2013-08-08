<?php
/*
@name Door
@author Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@description Plugin de gestion des portes avec gache éléctrique + detection ouverture/fermeture (capteur a effet de hall)
*/

 include('Door.class.php');
 


function door_plugin_setting_page(){
	global $_,$myUser;
	if(isset($_['section']) && $_['section']=='door' ){

		if($myUser!=false){
			$doorManager = new Door();
			$doors = $doorManager->populate();
			$roomManager = new Room();
			$rooms = $roomManager->populate();
	?>

		<div class="span9 userBloc">


		<h1>Porte</h1>
		<p>Gestion des portes</p>  

		<form action="action.php?action=door_add_door" method="POST">
		<fieldset>
		    <legend>Ajout d'un porte</legend>

		    <div class="left">
			    <label for="nameDoor">Nom</label>
			    <input type="text" id="nameDoor" onkeyup="$('#vocalCommand').html($(this).val());" name="nameDoor" placeholder="Lumiere Canapé…"/>
			    <small>Commande vocale associée : "YANA, ouvre <span id="vocalCommand"></span>"</small>
			    <label for="descriptionDoor">Description</label>
			    <input type="text" name="descriptionDoor" id="descriptionDoor" placeholder="Porte de l'entrée…" />
			    <label for="pinDoorRelay">Pin GPIO verouillage (relais)</label>
			    <input type="text" name="pinDoorRelay" id="pinDoorRelayRelay" placeholder="0,1,2…" />
			    <label for="pinDoorRelay">Pin GPIO etat (capteur hall)</label>
			    <input type="text" name="pinDoorCaptor" id="pinDoorCaptor" placeholder="0,1,2…" />
			    <label for="roomDoor">Pièce</label>
			    <select name="roomDoor" id="roomDoor">
			    	<?php foreach($rooms as $room){ ?>
			    	<option value="<?php echo $room->getId(); ?>"><?php echo $room->getName(); ?></option>
			    	<?php } ?>
			    </select>
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
		    <th>Pin GPIO verouillage</th>
		    <th>Pin GPIO etat</th>
		    <th>Pièce</th>
	    </tr>
	    </thead>
	    
	    <?php foreach($doors as $door){ 

	    	$room = $roomManager->load(array('id'=>$door->getRoom())); 
	    	?>
	    <tr>
	    	<td><?php echo $door->getName(); ?></td>
		    <td><?php echo $door->getDescription(); ?></td>
		    <td><?php echo $door->getPinRelay(); ?></td>
		    <td><?php echo $door->getPinCaptor(); ?></td>
		    <td><?php echo $room->getName(); ?></td>
		    <td><a class="btn" href="action.php?action=door_delete_door&id=<?php echo $door->getId(); ?>"><i class="icon-remove"></i></a></td>
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

function door_plugin_setting_menu(){
	global $_;
	echo '<li '.(isset($_['section']) && $_['section']=='door'?'class="active"':'').'><a href="setting.php?section=door"><i class="icon-chevron-right"></i> Porte</a></li>';
}




function door_display($room){
	global $_;


	$doorManager = new Door();
	$doors = $doorManager->loadAll(array('room'=>$room->getId()));
	
	foreach ($doors as $door) {
			
	?>

	<div class="span3">
          <h5><?php echo $door->getName() ?></h5>
		   
		   <p><?php echo $door->getDescription() ?>
		  	</p>
		  	<div id="state<?php echo $door->getId() ?>">
		  	</div>
		  	</div>
		  		<ul>
		  		<li>PIN GPIO verouillage : <code><?php echo $door->getPinRelay() ?></code></li>
		  		<li>PIN GPIO état : <code><?php echo $door->getPinCaptor() ?></code></li>
		  		<li>Type : <code>Verrou</code></li>
		  		<li>Emplacement : <code><?php echo $room->getName() ?></code></li>
		  	</ul>
		  
		  	 <div class="btn-toolbar">
				<div class="btn-group">
				<a class="btn btn-success" href="action.php?action=door_change_state&engine=<?php echo $door->getId() ?>&amp;code=<?php echo $door->getPinRelay() ?>&amp;state=1"><i class="icon-thumbs-up icon-white"></i></a>
				<a class="btn" href="action.php?action=door_change_state&engine=<?php echo $door->getId() ?>&amp;code=<?php echo $door->getPinRelay() ?>&amp;state=0"><i class="icon-thumbs-down "></i></a>
				</div>
			</div>
        </div>


	<?php
	}
}

function door_vocal_command(&$response,$actionUrl){
	$doorManager = new Door();

	$doors = $doorManager->populate();
	foreach($doors as $door){
		$response['commands'][] = array('command'=>VOCAL_ENTITY_NAME.', ouvre '.$door->getName(),'url'=>$actionUrl.'?action=door_change_state&engine='.$door->getId().'&state=1&webservice=true','confidence'=>'0.9');
		$response['commands'][] = array('command'=>VOCAL_ENTITY_NAME.', ferme '.$door->getName(),'url'=>$actionUrl.'?action=door_change_state&engine='.$door->getId().'&state=0&webservice=true','confidence'=>'0.9');
		$response['commands'][] = array('command'=>VOCAL_ENTITY_NAME.', etat '.$door->getName(),'url'=>$actionUrl.'?action=door_get_state&engine='.$door->getId().'&webservice=true','confidence'=>'0.9');
	}
}

function door_action_door(){
	global $_,$conf,$myUser;

	switch($_['action']){
		case 'door_delete_door':
			if($myUser->can('porte','d')){
				$doorManager = new Door();
				$doorManager->delete(array('id'=>$_['id']));
			}
			header('location:setting.php?section=door');
		break;

		case 'door_add_door':
			if($myUser->can('porte','c')){
				$door = new Door();
				$door->setName($_['nameDoor']);
				$door->setDescription($_['descriptionDoor']);
				$door->setPinRelay($_['pinDoorRelay']);
				$door->setPinCaptor($_['pinDoorCaptor']);
				$door->setRoom($_['roomDoor']);
				$door->save();
			}
			header('location:setting.php?section=door');

		break;

		case 'door_get_state':
		if($myUser->can('porte','r')){
			$door = new Door();
			$door = $door->getById($_['engine']);
			$cmd = '/usr/local/bin/gpio mode '.$door->getPinCaptor().' in';
			system($cmd,$out);
			$cmd = '/usr/local/bin/gpio read '.$door->getPinCaptor();
			exec($cmd,$out);
		
			if(!isset($_['webservice'])){
				echo $out[0];
			}else{
				$affirmation = (trim($out[0])?'Ouvert':'Fermé');
				$response = array('responses'=>array(
											array('type'=>'talk','sentence'=>$affirmation)
														)
									);

				$json = json_encode($response);
				echo ($json=='[]'?'{}':$json);
			}
		}
		break;

		case 'door_change_state':
			global $_,$myUser;

			
			if($myUser->can('porte','u')){
				$door = new Door();
				$door = $door->getById($_['engine']);
				$cmd = '/usr/local/bin/gpio mode '.$door->getPinRelay().' out';
				system($cmd,$out);
				$cmd = '/usr/local/bin/gpio write '.$door->getPinRelay().' '.$_['state'];
				system($cmd,$out);
				//TODO change bdd state
				
				if(!isset($_['webservice'])){
					header('location:index.php?module=room&id='.$door->getRoom());
				}else{
					$affirmations = array(	'A vos ordres!',
								'Bien!',
								'Oui commandant!',
								'Avec plaisir!',
								'J\'aime vous obéir!',
								'Avec plaisir!',
								'Certainement!',
								'Je fais ça sans tarder!',
								'Avec plaisir!',
								'Oui chef!');
					$affirmation = $affirmations[rand(0,count($affirmations)-1)];
					$response = array('responses'=>array(
											array('type'=>'talk','sentence'=>$affirmation)
														)
									);

					$json = json_encode($response);
					echo ($json=='[]'?'{}':$json);
				}
			}else{
				$response = array('responses'=>array(
											array('type'=>'talk','sentence'=>'Je ne vous connais pas, je refuse de faire ça!')
														)
									);
				echo json_encode($response);
			}
		break;
	}
}



Plugin::addJs("/js/main.js"); 

Plugin::addCss("/css/style.css"); 
Plugin::addHook("action_post_case", "door_action_door"); 

Plugin::addHook("node_display", "door_display");   
Plugin::addHook("setting_bloc", "door_plugin_setting_page");
Plugin::addHook("setting_menu", "door_plugin_setting_menu");  
Plugin::addHook("vocal_command", "door_vocal_command");
?>