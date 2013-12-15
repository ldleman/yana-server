<?php
/*
@name Event manager
@author Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@description Permet la programmation d'évenements yana client (parole,commande,son...) selon un horaire fixé
*/




function eventmanager_action(){
	
	global $_,$conf,$myUser;
	switch($_['action']){
	

		case 'eventmanager_save_event':
		
			if($myUser->can('event','c') || $myUser->can('event','u')){
				$event = new Event();
				$event =($_['eventId']!=''?$event->getById($_['eventId']):$event);
				$event->setName($_['eventName']);


				$event->setYear($_['eventYear']);
				$event->setMonth($_['eventMonth']);
				$event->setDay($_['eventDay']);
				$event->setHour($_['eventHour']);
				$event->setMinut($_['eventMinut']);
				$event->setRepeat('0');

				$content = array();
				//Todo, prendre en compte le multi action ([1],[2]...)
				$content[0]['type'] = $_['eventType'];

				switch($content[0]['type']){
					case 'talk':
						$content[0]['sentence'] = $_['eventContent'];
					break;
					case 'sound':
						$content[0]['file'] = $_['eventContent'];
					break;
					case 'command':
						$content[0]['program'] = $_['eventContent'];
					break;
				}

				$event->setContent($content[0]);
				$event->setRecipients('all'); //@TODO

				$event->save();
				header('location:index.php?module=eventmanager');
			}else{
				header('location:index.php?module=eventmanager&error=Vous n\'avez pas le droit de faire ça!');
			}
		break;


		case 'eventmanager_delete_event':
		
			if($myUser->can('event','d')){
				$event = new Event();
				$event->delete(array('id'=>$_['id']));
				header('location:index.php?module=eventmanager');
			}else{
				header('location:index.php?module=eventmanager&error=Vous n\'avez pas le droit de faire ça!');
			}
		break;

	}

}



function eventmanager_plugin_menu(&$menuItems){
	global $_;
	$menuItems[] = array('sort'=>1,'content'=>'<a href="index.php?module=eventmanager"><i class="icon-time"></i> Evenements</a>');
}


function eventmanager_plugin_page($_){
	if(isset($_['module']) && $_['module']=='eventmanager'){
		$eventManager = new Event();
		$currentEvent = new Event();
	
		$currentEvent->setYear('*');
		$currentEvent->setMonth('*');
		$currentEvent->setDay('*');
		$currentEvent->setHour('*');
		$currentEvent->setMinut('*');

		$currentEvent = (isset($_['id'])?$eventManager->getById($_['id']):$currentEvent);

		?>


			<div class="span12">


				<h1>Evenements</h1>
				
				<form action="action.php?action=eventmanager_save_event" method="POST">
				<fieldset>
				    <legend>Gestion des evenements</legend>
				    <p>Ce module vous permet de créer un évenement en fonction d'une date que le client (yana windows ou yana for android) pourra retranscrire.
				    	<br/> L'évenement peut être une action parole (le client yana prononce une phrase), commande (une commande est lancée sur
				    	le poste qui execute yana client), ou encore  un son à joeur (le son doit être un .wav situé dans le repertoire son de yana-windows)</p>
				    <span class="row">
				    	<span class="span6">
						 
							    <label for="eventName">Nom</label>
							    <input class="input-xxlarge" type="text" id="eventName" value="<?php echo $currentEvent->getName(); ?>"  name="eventName" placeholder="ex : Signale un anniversaire"/>
						
						</span>
						<span class="span2">
						<?php 
								$content = $currentEvent->getContent();
								$action = $content;
							?>
						    <label for="eventType">Action</label>
						    <select class="input-medium" name="eventType">
						    	<option <?php echo ($action['type']=='talk'?'selected="selected"':''); ?> value="talk">Parler</option>
						    	<option <?php echo ($action['type']=='command'?'selected="selected"':''); ?> value="command">Executer une commande</option>
						    	<option <?php echo ($action['type']=='sound'?'selected="selected"':''); ?> value="sound">Jouer un son</option>
						    </select>
						</span>
		
						</span>
						<span class="row">
						<span class="span2">
							<label for="eventMinut">Minute</label>
						    <select class="input-medium" name="eventMinut" id="eventMinut">
						    		<option <?php  if($currentEvent->getMinut()=='*') echo 'selected="selected"';  ?> value="*">Toutes</option>
						    	<?php for($i=0;$i<60;$i++){ ?>
						    		<option <?php  if($currentEvent->getMinut()==$i) echo 'selected="selected"';  ?>><?php echo $i; ?></option>
						    	<?php } ?>
						    </select>
						</span>

						<span class="span2">
							<label for="eventHour">Heure</label>

						    <select class="input-medium" name="eventHour" id="eventHour">
						    		<option <?php  if($currentEvent->getHour()=='*'){  echo 'selected="selected"'; } ?> value="*">Toutes</option>
						    	<?php for($i=0;$i<24;$i++){ ?>
						    		<option <?php  if($currentEvent->getHour()==$i) echo 'selected="selected"';  ?>><?php echo $i; ?></option>
						    	<?php } ?>
						    </select>
						</span>

						<span class="span2">
							<label for="eventDay">Jour</label>
						    <select class="input-medium" name="eventDay" id="eventDay">
						    		<option <?php  if($currentEvent->getDay()=='*') echo 'selected="selected"';  ?> value="*">Tous</option>
						    	<?php for($i=1;$i<32;$i++){ ?>
						    		<option <?php  if($currentEvent->getDay()==$i) echo 'selected="selected"';  ?>><?php echo $i; ?></option>
						    	<?php } ?>
						    </select>
						</span>

						<span class="span2">
							<label for="eventMonth">Mois</label>
						    <select class="input-medium" name="eventMonth" id="eventMonth">
						    		<option <?php  if($currentEvent->getMonth()=='*') echo 'selected="selected"';  ?> value="*">Tous</option>
						    	<?php for($i=1;$i<13;$i++){ ?>
						    		<option <?php  if($currentEvent->getMonth()==$i) echo 'selected="selected"';  ?>><?php echo $i; ?></option>
						    	<?php } ?>
						    </select>
						</span>

						<span class="span3">
							<label for="eventYear">Année (taper * pour toutes)</label>
						    <input class="input-medium" type="text" value="<?php echo $currentEvent->getYear(); ?>" name="eventYear" id="eventYear" placeholder="1988" />
						</span>
						
						
					</span>
					<span class="row">
						<span class="span12">
							
						    <label for="eventContent">Contenu</label>
						    <textarea  class="span10" name="eventContent"  id="eventContent"><?php 
						   	switch($action['type']){
						   		case 'talk':
						    		echo $action['sentence'];
						    	break;
						    	case 'sound':
						    		echo $action['file'];
						    	break;
						    	case 'command':
						    		echo $action['program'];
						    	break;
						    }
						    ?></textarea>
							
							<input  type="hidden" name="eventId" value="<?php echo $currentEvent->getId(); ?>" >
						</span>
					</span>

		  			<div class="clear"></div>
				    <br/><button type="submit" class="btn">Enregistrer</button>
			  	</fieldset>
				</form>

				<table class="table table-striped table-bordered table-hover">
			    <thead>
			    <tr>
			    	<th>Nom</th>
				    <th>Heure Date</th>
				    <th>Type</th>
				    <th>Contenu</th>
				    <th>Dernier lancement</th>
				    <th>Destinataires</th>
				    <th></th>
			    </tr>
			    </thead>
			    
			    <?php 
			    	$eventManager = new Event();
			    	$events = $eventManager->populate();
			    	foreach($events as $event){ 
			    		$action = $event->getContent();
			    		//$action = $action[0];
			    	?>
			    <tr>
			    	<td><?php echo $event->getName(); ?></td>
			    	<td><?php echo $event->getHour().':'.$event->getMinut().' '.$event->getDay().'/'.$event->getMonth().'/'.$event->getYear(); ?></td>
				    <td><?php echo $action['type']; ?></td>
				    <td><?php switch($action['type']){
						   		case 'talk':
						    		echo $action['sentence'];
						    	break;
						    	case 'sound':
						    		echo $action['file'];
						    	break;
						    	case 'command':
						    		echo $action['program'];
						    	break;
						    }; ?></td>
				    <td><?php echo $event->getRepeat(); ?></td>
				    <td><?php echo $event->getRecipients(); ?></td>
				    <td>
	<a class="btn" href="index.php?module=eventmanager&id=<?php echo $event->getId(); ?>"><i class="icon-edit"></i></a>
			   
				    	<a class="btn" href="action.php?action=eventmanager_delete_event&id=<?php echo $event->getId(); ?>"><i class="icon-remove"></i></a></td>
			   	 </tr>
			    <?php } ?>
			    </table>
			</div>

		<?php
	}
}




Plugin::addHook("action_post_case", "eventmanager_action");    
Plugin::addHook("menubar_pre_home", "eventmanager_plugin_menu");  
Plugin::addHook("home", "eventmanager_plugin_page");  
?>