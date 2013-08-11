<?php
/*
@name Radio Relay
@author Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@description Radio Relay plugin
*/

 include('RadioRelay.class.php');
 


function radioRelay_plugin_setting_page(){
	global $_,$myUser;
	if(isset($_['section']) && $_['section']=='radioRelay' ){

		if($myUser!=false){
			$radioRelayManager = new RadioRelay();
			$radioRelays = $radioRelayManager->populate();
			$roomManager = new Room();
			$rooms = $roomManager->populate();

		if (isset($_['id'])){
			$id_mod = $_['id'];
			$selected = $radioRelayManager->getById($id_mod);
			$addormodify_text = $selected->GetName();
			$action = "action.php?action=radioRelay_mod_radioRelay&id=".$id_mod;
			$addormodify_buttontext = "Modifier";
		}
		else
		{
			$addormodify_text =  "Ajout d'un relais";
			$action = "action.php?action=radioRelay_add_radioRelay";
			$addormodify_buttontext = "Ajouter";
		}
	?>

		<div class="span9 userBloc">


		<h1>Relais</h1>
		<p>Gestion des relais radio</p>  


		<form action="<? echo "$action" ?>" method="POST">
		<fieldset>
		    <legend><? echo $addormodify_text ?></legend>

		    <div class="left">
			    <label for="nameRadioRelay">Nom</label>
			    <input type="text" id="nameRadioRelay" value="<? if(isset($selected)){echo $selected->getName();} ?>" onkeyup="$('#vocalCommand').html($(this).val());" name="nameRadioRelay" placeholder="Lumiere Canapé…"/>
			    <small>Commande vocale associée : "YANA, allume <span id="vocalCommand"></span>"</small>
			    <label for="descriptionRadioRelay">Description</label>
			    <input type="text" value="<? if(isset($selected)){echo $selected->getDescription();} ?>" name="descriptionRadioRelay" id="descriptionRadioRelay" placeholder="Relais sous le canapé…" />
			    <label for="radioCodeRadioRelay">Code radio</label>
			    <input type="text" value="<? if(isset($selected)){echo $selected->getRadioCode();} ?>" name="radioCodeRadioRelay" id="radioCodeRadioRelay" placeholder="0,1,2…" />
			    <label for="roomRadioRelay">Pièce</label>
			    <select name="roomRadioRelay" id="roomRadioRelay">
			    	<?php foreach($rooms as $room){ 
			    	if (isset($selected)){$selected_room = ($selected->getRoom());}
			    	else{$selected_room = null;}			    		
			    	?>
		    	
			    	<option <? if ($selected_room == $room->getId()){echo "selected";} ?> value="<?php echo $room->getId(); ?>"><?php echo $room->getName(); ?></option>
			    	<?php } ?>
			    </select>
			</div>

  			<div class="clear"></div>
		    <br/><button type="submit" class="btn"><? echo $addormodify_buttontext; ?></button>
	  	</fieldset>
		<br/>
	</form>

		<table class="table table-striped table-bordered table-hover">
	    <thead>
	    <tr>
	    	<th>Nom</th>
		    <th>Description</th>
		    <th>Code radio</th>
		    <th>Pièce</th>
		    <th></th>
	    </tr>
	    </thead>
	    
	    <?php foreach($radioRelays as $radioRelay){ 

	    	$room = $roomManager->load(array('id'=>$radioRelay->getRoom())); 
	    	?>
	    <tr>
	    	<td><?php echo $radioRelay->getName(); ?></td>
		    <td><?php echo $radioRelay->getDescription(); ?></td>
		    <td><?php echo $radioRelay->getRadioCode(); ?></td>
		    <td><?php echo $room->getName(); ?></td>
		    <td><a class="btn" href="action.php?action=radioRelay_delete_radioRelay&id=<?php echo $radioRelay->getId(); ?>"><i class="icon-remove"></i></a>
	   		<a class="btn" href="setting.php?section=radioRelay&id=<?php echo $radioRelay->getId(); ?>"><i class="icon-edit"></i></a></td>
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

function radioRelay_plugin_setting_menu(){
	global $_;
	echo '<li '.(isset($_['section']) && $_['section']=='radioRelay'?'class="active"':'').'><a href="setting.php?section=radioRelay"><i class="icon-chevron-right"></i> Relais radio</a></li>';
}




function radioRelay_display($room){
	global $_;


	$radioRelayManager = new RadioRelay();
	$radioRelays = $radioRelayManager->loadAll(array('room'=>$room->getId()));
	
	foreach ($radioRelays as $radioRelay) {
			
	?>

	<div class="span3">
          <h5><?php echo $radioRelay->getName() ?></h5>
		   
		   <p><?php echo $radioRelay->getDescription() ?>
		  	</p><ul>
		  		<li>Code radio : <code><?php echo $radioRelay->getRadioCode() ?></code></li>
		  		<li>Type : <code>Interrupteur radio</code></li>
		  		<li>Emplacement : <code><?php echo $room->getName() ?></code></li>
		  	</ul>
		  <p></p>
		  	 <div class="btn-toolbar">
				<div class="btn-group">
				<a class="btn btn-success" href="action.php?action=radioRelay_change_state&engine=<?php echo $radioRelay->getId() ?>&amp;code=<?php echo $radioRelay->getRadioCode() ?>&amp;state=on"><i class="icon-thumbs-up icon-white"></i></a>
				<a class="btn" href="action.php?action=radioRelay_change_state&engine=<?php echo $radioRelay->getId() ?>&amp;code=<?php echo $radioRelay->getRadioCode() ?>&amp;state=off"><i class="icon-thumbs-down "></i></a>
				</div>
			</div>
        </div>


	<?php
	}
}

function radioRelay_vocal_command(&$response,$actionUrl){
	$radioRelayManager = new RadioRelay();

	$radioRelays = $radioRelayManager->populate();
	foreach($radioRelays as $radioRelay){
		$response['commands'][] = array('command'=>VOCAL_ENTITY_NAME.', allume '.$radioRelay->getName(),'url'=>$actionUrl.'?action=radioRelay_change_state&engine='.$radioRelay->getId().'&state=on&webservice=true','confidence'=>'0.9');
		$response['commands'][] = array('command'=>VOCAL_ENTITY_NAME.', eteint '.$radioRelay->getName(),'url'=>$actionUrl.'?action=radioRelay_change_state&engine='.$radioRelay->getId().'&state=off&webservice=true','confidence'=>'0.9');
	}
}

function radioRelay_action_radioRelay(){
	global $_,$conf,$myUser;

	switch($_['action']){
		case 'radioRelay_delete_radioRelay':
			if($myUser->can('radio relais','d')){
				$radioRelayManager = new RadioRelay();
				$radioRelayManager->delete(array('id'=>$_['id']));
			}
			header('location:setting.php?section=radioRelay');
		break;
		case 'radioRelay_plugin_setting':
			$conf->put('plugin_radioRelay_emitter_pin',$_['emiterPin']);
			$conf->put('plugin_radioRelay_emitter_code',$_['emiterCode']);
			header('location: setting.php?section=preference&block=radioRelay');
		break;

		case 'radioRelay_mod_radioRelay':
			if($myUser->can('radio relais','c')){
				$radioRelay = new RadioRelay();
				$radioRelay->change(array(
					'name'=> $_['nameRadioRelay'],
					'description'=> $_['descriptionRadioRelay'],
					'radiocode'=> $_['radioCodeRadioRelay'],
					'room'=> $_['roomRadioRelay']
					),
					array('id'=>$_['id'])
					);
			}
			header('location:setting.php?section=radioRelay');

		break;

		case 'radioRelay_add_radioRelay':
			if($myUser->can('radio relais','c')){
				$radioRelay = new RadioRelay();
				$radioRelay->setName($_['nameRadioRelay']);
				$radioRelay->setDescription($_['descriptionRadioRelay']);
				$radioRelay->setRadioCode($_['radioCodeRadioRelay']);
				$radioRelay->setRoom($_['roomRadioRelay']);
				$radioRelay->save();
			}
			header('location:setting.php?section=radioRelay');
		break;

		case 'radioRelay_change_state':
			global $_,$myUser;

			
			if($myUser->can('radio relais','u')){
				$radioRelay = new RadioRelay();
				$radioRelay = $radioRelay->getById($_['engine']);
				$cmd = dirname(__FILE__).'/radioEmission '.$conf->get('plugin_radioRelay_emitter_pin').' '.$conf->get('plugin_radioRelay_emitter_code').' '.$radioRelay->getRadioCode().' '.$_['state'];
				
				//TODO change bdd state
				system($cmd,$out);
				if(!isset($_['webservice'])){
					header('location:index.php?module=room&id='.$radioRelay->getRoom());
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


function radioRelay_plugin_preference_menu(){
	global $_;
	echo '<li '.(@$_['block']=='radioRelay'?'class="active"':'').'><a  href="setting.php?section=preference&block=radioRelay"><i class="icon-chevron-right"></i> Radio Relais</a></li>';
}
function radioRelay_plugin_preference_page(){
	global $myUser,$_,$conf;
	if((isset($_['section']) && $_['section']=='preference' && @$_['block']=='radioRelay' )  ){
		if($myUser!=false){
	?>

		<div class="span9 userBloc">
			<form class="form-inline" action="action.php?action=radioRelay_plugin_setting" method="POST">

			    <p>Pin du raspberry PI branché à l'émetteur radio: </p>
			    <input type="text" class="input-large" name="emiterPin" value="<?php echo $conf->get('plugin_radioRelay_emitter_pin');?>" placeholder="Pin wiring PI...">
			    
			    <p>Code de la télécommande pris par le raspberry pi: </p>
			    <input type="text" class="input-large" name="emiterCode" value="<?php echo $conf->get('plugin_radioRelay_emitter_code');?>" placeholder="par exemple 8217034...">

			    <button type="submit" class="btn">Enregistrer</button>
		    </form>
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


Plugin::addHook("preference_menu", "radioRelay_plugin_preference_menu"); 
Plugin::addHook("preference_content", "radioRelay_plugin_preference_page"); 

Plugin::addCss("/css/style.css"); 
Plugin::addHook("action_post_case", "radioRelay_action_radioRelay"); 

Plugin::addHook("node_display", "radioRelay_display");   
Plugin::addHook("setting_bloc", "radioRelay_plugin_setting_page");
Plugin::addHook("setting_menu", "radioRelay_plugin_setting_menu");  
Plugin::addHook("vocal_command", "radioRelay_vocal_command");
?>