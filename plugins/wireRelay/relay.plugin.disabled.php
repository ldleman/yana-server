<?php
/*
@name Wire Relay
@author Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@description Plugin de gestion des relais filaires
*/

 include('WireRelay.class.php');
 


function wireRelay_plugin_setting_page(){
	global $_,$myUser;
	if(isset($_['section']) && $_['section']=='wireRelay' ){

		if($myUser!=false){
			$wireRelayManager = new WireRelay();
			$wireRelays = $wireRelayManager->populate();
			$roomManager = new Room();
			$rooms = $roomManager->populate();
			$selected =  new WireRelay();
			$selected->setPulse(0);

			//Si on est en mode modification
			if (isset($_['id']))
				$selected = $wireRelayManager->getById($_['id']);
			
			?>

		<div class="span9 userBloc">


		<h1>Relais</h1>
		<p>Gestion des relais filaires</p>  

		<form action="action.php?action=wireRelay_add_wireRelay" method="POST">
		<fieldset>
		    <legend>Formulaire du relais filaire</legend>

		    <div class="left">
			    <label for="nameWireRelay">Nom</label>
			    <input type="hidden" name="id" value="<?php echo $selected->getId(); ?>">
			    <input type="text" id="nameWireRelay" value="<? echo $selected->getName(); ?>" onkeyup="$('#vocalCommand').html($(this).val());" name="nameWireRelay" placeholder="Lumiere Canapé…"/>
			    <small>Commande vocale associée : "YANA, allume <span id="vocalCommand"></span>"</small>
			    <label for="descriptionWireRelay">Description</label>
			    <input type="text" name="descriptionWireRelay" value="<?echo $selected->getDescription(); ?>" id="descriptionWireRelay" placeholder="Relais sous le canapé…" />
			    <label for="pinWireRelay">Pin GPIO (Numéro Wiring PI)</label>
			    <input type="text" name="pinWireRelay" value="<? echo $selected->getPin(); ?>" id="pinWireRelay" placeholder="0,1,2…" />
			    <label for="roomWireRelay">Pièce</label>
			    <select name="roomWireRelay" id="roomWireRelay">
			    	<?php foreach($rooms as $room){ ?>
			    	<option <? if ($selected->getRoom()== $room->getId()){echo "selected";} ?> value="<?php echo $room->getId(); ?>"><?php echo $room->getName(); ?></option>
			    	<?php } ?>
			    </select>
			   <label for="pinWireRelay">Mode impulsion (laisser à zero pour desactiver le mode impulsion ou definir un temps d'impulsion en micro-seconde)</label>
			   <input type="text" name="pulseWireRelay" value="<? echo $selected->getPulse(); ?>" id="pulseWireRelay" placeholder="0" />
			     
			</div>

  			<div class="clear"></div>
		    <br/><button type="submit" class="btn">Enregistrer</button>
	  	</fieldset>
		<br/>
	</form>

		<table class="table table-striped table-bordered table-hover">
	    <thead>
	    <tr>
	    	<th>Nom</th>
		    <th>Description</th>
		    <th>Pin GPIO</th>
		    <th>Pièce</th>
		    <th>Impulsion</th>
		    <th></th>
	    </tr>
	    </thead>
	    
	    <?php foreach($wireRelays as $wireRelay){ 

	    	$room = $roomManager->load(array('id'=>$wireRelay->getRoom())); 
	    	?>
	    <tr>
	    	<td><?php echo $wireRelay->getName(); ?></td>
		    <td><?php echo $wireRelay->getDescription(); ?></td>
		    <td><?php echo $wireRelay->getPin(); ?></td>
		    <td><?php echo $room->getName(); ?></td>
		    <td><?php echo $wireRelay->getPulse(); ?></td>
		    <td><a class="btn" href="action.php?action=wireRelay_delete_wireRelay&id=<?php echo $wireRelay->getId(); ?>"><i class="icon-remove"></i></a>
		    <a class="btn" href="setting.php?section=wireRelay&id=<?php echo $wireRelay->getId(); ?>"><i class="icon-edit"></i></a></td>
		    </td>
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

function wireRelay_plugin_setting_menu(){
	global $_;
	echo '<li '.(isset($_['section']) && $_['section']=='wireRelay'?'class="active"':'').'><a href="setting.php?section=wireRelay"><i class="icon-chevron-right"></i> Relais filaire</a></li>';
}




function wireRelay_display($room){
	global $_;


	$wireRelayManager = new WireRelay();
	$wireRelays = $wireRelayManager->loadAll(array('room'=>$room->getId()));
	
	foreach ($wireRelays as $wireRelay) {
			
	?>

	<div class="span3">
          <h5><?php echo $wireRelay->getName() ?></h5>
		   
		   <p><?php echo $wireRelay->getDescription() ?>
		  	</p><ul>
		  		<li>PIN GPIO : <code><?php echo $wireRelay->getPin() ?></code></li>
		  		<li>Type : <code>Interrupteur filaire</code></li>
		  		<li>Emplacement : <code><?php echo $room->getName() ?></code></li>
		  	</ul>
		  <p></p>
		  	 <div class="btn-toolbar">
				<div class="btn-group">

				<a class="btn btn-success" href="action.php?action=wireRelay_change_state&engine=<?php echo $wireRelay->getId() ?>&amp;code=<?php echo $wireRelay->getPin() ?>&amp;state=on"><i class="icon-thumbs-up icon-white"></i></a>
				<?php if($wireRelay->getPulse()==0){ ?>
					<a class="btn" href="action.php?action=wireRelay_change_state&engine=<?php echo $wireRelay->getId() ?>&amp;code=<?php echo $wireRelay->getPin() ?>&amp;state=off"><i class="icon-thumbs-down "></i></a>
				<?php } ?>
				</div>
			</div>
        </div>


	<?php
	}
}

function wireRelay_vocal_command(&$response,$actionUrl){
	$wireRelayManager = new WireRelay();

	$wireRelays = $wireRelayManager->populate();
	foreach($wireRelays as $wireRelay){
		$response['commands'][] = array('command'=>VOCAL_ENTITY_NAME.', allume '.$wireRelay->getName(),'url'=>$actionUrl.'?action=wireRelay_change_state&engine='.$wireRelay->getId().'&state=1&webservice=true','confidence'=>'0.9');
		$response['commands'][] = array('command'=>VOCAL_ENTITY_NAME.', eteint '.$wireRelay->getName(),'url'=>$actionUrl.'?action=wireRelay_change_state&engine='.$wireRelay->getId().'&state=0&webservice=true','confidence'=>'0.9');
	}
}

function wireRelay_action_wireRelay(){
	global $_,$conf,$myUser;

	switch($_['action']){
		case 'wireRelay_delete_wireRelay':
			if($myUser->can('relais filaire','d')){
				$wireRelayManager = new WireRelay();
				$wireRelayManager->delete(array('id'=>$_['id']));
			}
			header('location:setting.php?section=wireRelay');
		break;
		case 'wireRelay_plugin_setting':
			$conf->put('plugin_wireRelay_emitter_pin',$_['emiterPin']);
			$conf->put('plugin_wireRelay_emitter_code',$_['emiterCode']);
			header('location: setting.php?section=preference&block=wireRelay');
		break;

		case 'wireRelay_add_wireRelay':
			if($myUser->can('relais filaire',$_['id']!=''? 'u' : 'c')){
				$wireRelayManager = new WireRelay();
				$wireRelay = $_['id']!=''?$wireRelayManager->getById($_['id']): new WireRelay();
				
				$wireRelay->setName($_['nameWireRelay']);
				$wireRelay->setDescription($_['descriptionWireRelay']);
				$wireRelay->setPin($_['pinWireRelay']);
				$wireRelay->setRoom($_['roomWireRelay']);
				$wireRelay->setPulse($_['pulseWireRelay']);
				$wireRelay->save();
			}
			header('location:setting.php?section=wireRelay');

		break;
		case 'wireRelay_change_state':
			global $_,$myUser;

			
			if($myUser->can('relais filaire','u')){
				$wireRelay = new WireRelay();
				$wireRelay = $wireRelay->getById($_['engine']);
				$cmd = '/usr/local/bin/gpio mode '.$wireRelay->getPin().' out';
				system($cmd,$out);

				if($wireRelay->getPulse()==0){
					$cmd = '/usr/local/bin/gpio write '.$wireRelay->getPin().' '.$_['state'];
					system($cmd,$out);
				}else{
					$cmd = '/usr/local/bin/gpio write '.$wireRelay->getPin().' 1';
					system($cmd,$out);
					usleep($wireRelay->getPulse());
					$cmd = '/usr/local/bin/gpio write '.$wireRelay->getPin().' 0';
					system($cmd,$out);
				}

				//TODO change bdd state
				
				if(!isset($_['webservice'])){
					header('location:index.php?module=room&id='.$wireRelay->getRoom());
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


Plugin::addCss("/css/style.css"); 
Plugin::addHook("action_post_case", "wireRelay_action_wireRelay"); 
Plugin::addHook("node_display", "wireRelay_display");   
Plugin::addHook("setting_bloc", "wireRelay_plugin_setting_page");
Plugin::addHook("setting_menu", "wireRelay_plugin_setting_menu");  
Plugin::addHook("vocal_command", "wireRelay_vocal_command");
?>