<?php
/*
@name Capteur mouvement
@author Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@description Plugin de gestion des capteurs de mouvements infra rouges
*/

 include('Mouvment.class.php');



function mouvment_plugin_setting_page(){
	global $_,$myUser;
	if(isset($_['section']) && $_['section']=='mouvment' ){

		if($myUser!=false){
			$mouvmentManager = new Mouvment();
			$mouvments = $mouvmentManager->populate();

	?>

		<div class="span9 userBloc">


		<h1>Relais</h1>
		<p>Gestion des capteurs de mouvements</p>  

		<form action="action.php?action=mouvment_add_mouvment" method="POST">
		<fieldset>
		    <legend>Ajout d'un capteur</legend>

		    <div class="left">
			    <label for="nameMouvment">Nom</label>
			    <input type="text" id="nameMouvment" name="nameMouvment" placeholder="Cuisine,salon…"/>
			    <label for="descriptionMouvment">Description</label>
			    <input type="text" name="descriptionMouvment" id="descriptionMouvment" />
			    <label for="pinMouvment">Pin</label>
			    <input type="text" name="pinMouvment" id="pinMouvment" />
			    <label for="roomMouvment">Pièce</label>
			    <input type="text" name="roomMouvment" id="roomMouvment" />
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
		    <th>Pin</th>
		    <th>Pièce</th>
	    </tr>
	    </thead>
	    
	    <?php foreach($mouvments as $mouvment){ ?>
	    <tr>
	    	<td><?php echo $mouvment->getName(); ?></td>
		    <td><?php echo $mouvment->getDescription(); ?></td>
		    <td><?php echo $mouvment->getPin(); ?></td>
		    <td><?php echo $mouvment->getRoom(); ?></td>
		    <td><a class="btn" href="action.php?action=mouvment_delete_mouvment&id=<?php echo $mouvment->getId(); ?>"><i class="icon-remove"></i></a></td>
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

function mouvment_plugin_setting_menu(){
	global $_;
	echo '<li '.(isset($_['section']) && $_['section']=='mouvment'?'class="active"':'').'><a href="index.php?module=setting&section=mouvment"><i class="icon-chevron-right"></i> Capteur mouvement</a></li>';
}




function mouvment_display($room){
	global $_;


	$mouvmentManager = new Mouvment();
	$mouvments = $mouvmentManager->loadAll(array('room'=>$room->getId()));
	foreach ($mouvments as $mouvment) {
			
	?>

	<div class="span3">
          <h5><?php echo $mouvment->getName() ?></h5>
		   
		   <p><?php echo $mouvment->getDescription() ?>
		  	</p>
		  	<ul>
		  		<li>Présence : <code><?php echo $mouvment->getState() ?></code></li>
		  		<li>Pin : <code><?php echo $mouvment->getPin() ?></code></li>
		  		<li>Type : <code>Capteur de mouvement</code></li>
		  		<li>Emplacement : <code><?php echo $room->getName() ?></code></li>
		  	</ul>
        </div>


	<?php
	}
}

function mouvment_vocal_command(&$response,$actionUrl){
	$mouvmentManager = new Mouvment();

	$mouvments = $mouvmentManager->populate();
	foreach($mouvments as $mouvment){
		$response['commands'][] = array('command'=>VOCAL_ENTITY_NAME.', présence '.$mouvment->getName(),'url'=>$actionUrl.'?action=mouvment_get_state&engine='.$mouvment->getId().'&webservice=true','confidence'=>'0.9');
	}
}

function mouvment_action_mouvment(){
	global $_,$conf;

	switch($_['action']){
		case 'mouvment_delete_mouvment':
			$mouvmentManager = new Mouvment();
			$mouvmentManager->delete(array('id'=>$_['id']));
			header('location:index.php?module=setting&section=mouvment');
		break;
		case 'mouvment_plugin_setting':
			$conf->put('plugin_mouvment_emitter_pin',$_['emiterPin']);
			header('location: index.php?module=setting&section=preference&block=mouvment');
		break;

		case 'mouvment_add_mouvment':
			$mouvment = new Mouvment();
			$mouvment->setName($_['nameMouvment']);
			$mouvment->setDescription($_['descriptionMouvment']);
			$mouvment->setPin($_['pinMouvment']);
			$mouvment->setRoom($_['roomMouvment']);
			$mouvment->save();
			header('location:index.php?module=setting&section=mouvment');
		break;
		case 'mouvment_set_state':
			global $_;
			$mouvment = new Mouvment();
			$mouvment = $mouvment->load(array('pin'=>$_SERVER['argv'][2]));
			
			if($_SERVER['argv'][3]!=$mouvment->getState()){
				$mouvment->setState($_SERVER['argv'][3]);
				$mouvment->setLastState(time());
				$mouvment->save();
			}
		break;
		case 'mouvment_get_state':
			global $_;
			$mouvment = new Mouvment();
			$mouvment = $mouvment->getById($_['engine']);

			if(isset($_['webservice'])){
					
				if($mouvment->getState()==1 && (time()-$mouvment->getLastState())>2){
					$affirmation = 'Présence détectée';
				}else{
					$affirmation = 'Aucune présence n\'est détectée';
				}
				$response = array('responses'=>array(
										array('type'=>'talk','sentence'=>$affirmation)

													)
								);

				$json = json_encode($response);
				echo ($json=='[]'?'{}':$json);
			}
		break;
	}
}


function mouvment_plugin_preference_menu(){
	global $_;
	echo '<li '.(@$_['block']=='warranty'?'class="active"':'').'><a  href="index.php?module=setting&section=preference&block=mouvment"><i class="icon-chevron-right"></i> Capteur mouvements</a></li>';
}
function mouvment_plugin_preference_page(){
	global $myUser,$_,$conf;
	if((isset($_['section']) && $_['section']=='preference' && @$_['block']=='mouvment' )  ){
		if($myUser!=false){
	?>

		<div class="span9 userBloc">
			<form class="form-inline" action="action.php?action=mouvment_plugin_setting" method="POST">
			   <!-- <p>Pin du raspberry PI branché à l'émetteur radio: </p>
			    <input type="text" class="input-large" name="emiterPin" value="<?php echo $conf->get('plugin_mouvment_emitter_pin');?>" placeholder="Pin wiring PI...">
			    <button type="submit" class="btn">Enregistrer</button>-->
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


Plugin::addHook("preference_menu", "mouvment_plugin_preference_menu"); 
Plugin::addHook("preference_content", "mouvment_plugin_preference_page"); 

Plugin::addCss("/css/style.css"); 
//Plugin::addJs("/js/main.js"); 
Plugin::addHook("action_post_case", "mouvment_action_mouvment"); 

Plugin::addHook("node_display", "mouvment_display");   
Plugin::addHook("setting_bloc", "mouvment_plugin_setting_page");
Plugin::addHook("setting_menu", "mouvment_plugin_setting_menu");  
Plugin::addHook("vocal_command", "mouvment_vocal_command");
?>