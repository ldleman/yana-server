<?php
/*
@name Speech commands
@author Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@description Permet l'ajout de phrases à yana et la définition des conséquences de ces phrases
*/


function speechcommands_vocal_command(&$response,$actionUrl){
	global $conf;
	require_once('SpeechCommand.class.php');
	$command = new SpeechCommand();
	$commands = $command->populate();
	
	foreach($commands as $command){
		
		if($command->state !=1) continue;
		$response['commands'][] = array(
		'command'=>$conf->get('VOCAL_ENTITY_NAME').' '.$command->command,
		'url'=>$actionUrl.'?action=speechcommands_execute&command='.$command->id,'confidence'=>($command->confidence+$conf->get('VOCAL_SENSITIVITY'))
		);
	}
}

function speechcommands_action(){
	global $_,$conf,$myUser;

	switch($_['action']){
	
		case 'plugin_speechcommands_save':
			if(!$myUser->can('speech_command','c')) exit('Permissions insufisantes');
			require_once('SpeechCommand.class.php');
			$command = new SpeechCommand();
			$command = !empty($_['id']) ? $command->getById($_['id']): new SpeechCommand();
			$command->command= $_['command'];
			$command->action = $_['type'];
			$command->parameter = $_['parameter'];
			$command->confidence = $_['confidence'];
			$command->state = $_['state']=='on'?1:0;
			$command->save();
			header('location: setting.php?section=speechcommands');
		break;
	
		case 'plugin_speechcommands_delete' :
			if(!$myUser->can('speech_command','d')) exit('Permissions insufisantes');
			require_once('SpeechCommand.class.php');
			$command = new SpeechCommand();
			$command->delete(array('id'=>$_['id']));
			header('location: setting.php?section=speechcommands');
		break;
		case 'speechcommands_execute':
			global $_;
			require_once('SpeechCommand.class.php');
			$command = new SpeechCommand();
			$command = $command->getById($_['command']);
			
			set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
				// error was suppressed with the @-operator
				if (0 === error_reporting()) {
					return false;
				}

				throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
			});
			try{
			
				
					switch($command->action){
						case 'talk':
							$response = array(
												'responses'=>array(
																	array('type'=>'talk',
																		  'sentence'=>$command->parameter
																		)
												)
											);
							$json = json_encode($response);
							echo ($json=='[]'?'{}':$json);
						break;
						case 'gpio':
							list($pin,$value) = explode(',',$command->parameter);
							Gpio::write($pin,$value,true);
						break;
						case 'server_command':
							System::commandSilent(html_entity_decode($command->parameter));
							echo '{}';
						break;	
						case 'client_command':
							$response = array(
												'responses'=>array(
																	array('type'=>'command',
																		  'program'=>$command->parameter
																		)
												)
											);
							$json = json_encode($response);
							echo ($json=='[]'?'{}':$json);
						break;
						case 'sound':
							$response = array(
												'responses'=>array(
																	array('type'=>'sound',
																		  'file'=>$command->parameter
																		)
												)
											);
							$json = json_encode($response);
							echo ($json=='[]'?'{}':$json);
							
						break;
						case 'url':
							$content = file_get_contents($command->parameter);
							$response = array(
												'responses'=>array(
																	array('type'=>'talk',
																		  'sentence'=>$content
																		)
												)
											);
							$json = json_encode($response);
							echo ($json=='[]'?'{}':$json);
							
						break;
						default:
							throw new Exception('Aucun action n\'est spécifiée');								
						break;
					}
				}catch(Exception $e){
					$response = array(
												'responses'=>array(
																	array('type'=>'talk',
																		  'sentence'=>Personality::response('WORRY_EMOTION').', le problème viens de : '.$e->getMessage()
																		)
												)
											);
							$json = json_encode($response);
							echo ($json=='[]'?'{}':$json);
				}
			
			break;
	}

}

function speechcommands_plugin_preference_menu(){
	global $_;
	echo '<li '.(@$_['section']=='speechcommands'?'class="active"':'').'><a  href="setting.php?section=speechcommands"><i class="fa fa-angle-right"></i> Commandes Vocales</a></li>';
}


function speechcommands_plugin_preference_page(){
	global $myUser,$_,$conf;
	if((isset($_['section']) && $_['section']=='speechcommands'  )  ){
		if($myUser!=false){
	
	require_once('SpeechCommand.class.php');
	$command = new SpeechCommand();
	$commands = $command->populate();
	$command->state = 1;
	$command->confidence = '0.8';
	$command = isset($_['id'])?$command->getById($_['id']):$command;
	?>

		<div class="span9 userBloc">
		<legend>Commandes</legend>
		<form action="action.php?action=plugin_speechcommands_save" method="POST">
		<input type="hidden" value="<?php echo $command->id; ?>" name="id"/>
	<table class="table table-striped table-bordered">
		<tr>
			
			<th>Commande</th>
			<th>Confidence</th>
			<th>Action</th>
			<th>Parametre</th>
			<th>Etat</th>
			<th></th>
		</tr>
		<tr class="command">
				
				<td><?php echo $conf->get('VOCAL_ENTITY_NAME').', <input type="text" class="input-medium" value="'.$command->command.'" placeholder="ma phrase ici" name="command">' ?></td>
				<td><input  type="number" min="0" max="1" step=".01" class="input-mini" name="confidence" value="<?php echo $command->confidence; ?>"/></td>
				<td>
					<select name="type" class="type input-small">
						<option <?php echo $command->action=='gpio'?'selected="selected"':''; ?> value="gpio">Changer un GPIO (sur le serveur)</option>
						<option <?php echo $command->action=='server_command'?'selected="selected"':''; ?> value="server_command">Executer une commande (sur le serveur)</option>
						<option <?php echo $command->action=='url'?'selected="selected"':''; ?> value="url">Executer une adresse web (sur le serveur)</option>
						<option <?php echo $command->action=='client_command'?'selected="selected"':''; ?> value="client_command">Executer une commande (sur le client)</option>
						<option <?php echo $command->action=='talk'?'selected="selected"':''; ?> value="talk">Parler (sur le client)</option>
						<option <?php echo $command->action=='sound'?'selected="selected"':''; ?> value="sound">Son (sur le client)</option>
					</select>
				</td>
				<td><input type="text" name="parameter" class="input-medium" value="<?php echo $command->parameter; ?>"/></td>
				<td><input type="checkbox" name="state" <?php echo $command->state=='1'?'checked=""checked""':''; ?> /></td>
				<td><input class="btn" type="submit" value="Enregistrer"/></td>
			</tr>
	<?php	foreach($commands as $command){ ?>
			<tr class="command">
			
				<td><?php echo $conf->get('VOCAL_ENTITY_NAME').', '.$command->command; ?></td>
				<td><?php echo $command->confidence; ?></td>
				<td><?php echo $command->action; ?></td>
				<td><?php echo $command->parameter; ?></td>
				<td><?php echo $command->state=='1'?'Actif':'Inactif'; ?></td>
				<td>
					<a class="btn" title="modifier" href="setting.php?section=speechcommands&id=<?php echo $command->id; ?>"><i class="fa fa-edit"></i></a>
					<a class="btn" title="supprimer" href="action.php?action=plugin_speechcommands_delete&id=<?php echo $command->id; ?>"><i class="fa fa-times"></i></a>
				</td>
			</tr>
	<?php	}  ?>
	
	</table>
		</form>
		
		<h2>
			<i class="fa fa-book"></i> Explications</h2>
			<p>Ce plugin permet d'ajouter des actions en fonction de certaines phrases prononcées que vous pouvez choisir et ajouter à l\'infini.</p>
			<p>Notez bien que ce plugin est optimisé pour yana android et qu'en fonction de l'action souhaitée, celle ci s'execute sur le serveur ou sur le client (
			Ceci est spéficié dans l'action à choisir).
			</p>
			<ul>
				<li>Le champ <strong>Commande</strong> représente la phrase a enoncer pour lancer l'action spécifiée</li>
				<li>Le champ <strong>Confidence</strong> représente la sensibilité de reconnaissance de la phrase (chiffre entre 0 et 1) plus cette valeur est basse, plus la phrase sera reconnue facilement</li>
				<li>Le champ <strong>Action</strong> représente le type d'action a effectuer (lancer une url, parler, etc..)</li>
				<li>Le champ <strong>Parametre</strong> représente la valeur de cette action (ex : si l'action est "parler" parametre sera la phrase qui doit être dite). <br><strong>NB :</strong> Pour l'action "gpio" le paramêtre doit être au format : "n°gpio,etat" par exemple pour mettre le gpio 2 à 1 : "2,1".  </li>
				<li>Le champ <strong>Etat</strong> active ou desactive cette commande, vous pouvez ainsi la désactiver temporairement sans la supprimer définitivement</li>
			</ul>
			<h4>Examples</h4>
			<img src="<?php echo Plugin::path(); ?>/img/sample.png">
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


Plugin::addHook("setting_menu", "speechcommands_plugin_preference_menu"); 
Plugin::addHook("setting_bloc", "speechcommands_plugin_preference_page");   
Plugin::addHook("action_post_case", "speechcommands_action");    
Plugin::addHook("vocal_command", "speechcommands_vocal_command");
?>
