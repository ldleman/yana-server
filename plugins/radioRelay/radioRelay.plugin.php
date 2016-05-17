<?php
/*
@name Radio Relay
@author Valentin CARRUESCO <idleman@idleman.fr>
@link Http://blog.idleman.fr
@licence Cc -by-nc-sa
@version 1.1
@description Plugin de gestion des relais radio 433mhz au protocole home easy (compatible chacon et prises "maison" du blog.idleman.fr)
*/

//On appelle les entités de base de données
require_once(dirname(__FILE__).'/RadioRelay.class.php');

//Cette fonction ajoute une commande vocale
function radiorelay_plugin_vocal_command(&$response,$actionUrl){
	global $conf;

	$radioRelayManager = new RadioRelay();

	$radioRelays = $radioRelayManager->populate();
	foreach($radioRelays as $radioRelay){
		if(!empty($radioRelay->onCommand))
		$response['commands'][] = array('command'=>$conf->get('VOCAL_ENTITY_NAME').', '.$radioRelay->onCommand,'url'=>$actionUrl.'?action=radioRelay_vocal_change_state&engine='.$radioRelay->id.'&state=1','confidence'=>('0.90'+$conf->get('VOCAL_SENSITIVITY')));
		if(!empty($radioRelay->offCommand))
		$response['commands'][] = array('command'=>$conf->get('VOCAL_ENTITY_NAME').', '.$radioRelay->offCommand,'url'=>$actionUrl.'?action=radioRelay_vocal_change_state&engine='.$radioRelay->id.'&state=0','confidence'=>('0.90'+$conf->get('VOCAL_SENSITIVITY')));
	}
}

//cette fonction comprends toutes les actions du plugin qui ne nécessitent pas de vue html
function radiorelay_plugin_action(){
	global $_,$conf,$myUser;

	//Action de réponse à la commande vocale "Yana, commande de test"
	switch($_['action']){

		case 'radioRelay_save_radioRelay':
			Action::write(
				function($_,&$response){
					$radioRelayManager = new RadioRelay();

					if(empty($_['nameRadioRelay'])) throw new Exception("Le nom est obligatoire");
					if(!is_numeric($_['radioCodeRadioRelay']))  throw new Exception("Le code radio est obligatoire et doit être numerique");

					$radioRelay = !empty($_['id']) ? $radioRelayManager->getById($_['id']): new RadioRelay();
					$radioRelay->name = $_['nameRadioRelay'];
					$radioRelay->description = $_['descriptionRadioRelay'];
					$radioRelay->room = $_['roomRadioRelay'];
					$radioRelay->pulse = $_['pulseRadioRelay'];
					$radioRelay->onCommand = $_['onRadioRelay'];
					$radioRelay->offCommand = $_['offRadioRelay'];
					$radioRelay->icon = $_['iconRadioRelay'];
					$radioRelay->radiocode = $_['radioCodeRadioRelay'];
					$radioRelay->save();
					$response['message'] = 'Relais enregistré avec succès';
				},
				array('plugin_radiorelay'=>'c')
			);
		break;

		case 'radioRelay_delete_radioRelay':
			Action::write(
				function($_,$response){
					$radioRelayManager = new RadioRelay();
					$radioRelayManager->delete(array('id'=>$_['id']));
				},
				array('plugin_radiorelay'=>'d') 
			);
		break;



		case 'radioRelay_plugin_setting':
			Action::write(
				function($_,&$response){	
					global $conf;
					$conf->put('plugin_radioRelay_emitter_pin',$_['emiterPin']);
					$conf->put('plugin_radioRelay_emitter_code',$_['emiterCode']);
					$response['message'] = 'Configuration enregistrée';
				},
				array('plugin_radiorelay'=>'c') 
			);
		break;

		case 'radioRelay_manual_change_state':
			Action::write(
				function($_,&$response){	
					radiorelay_plugin_change_state($_['engine'],$_['state']);
				},
				array('plugin_radiorelay'=>'c') 
			);
		break;

		case 'radioRelay_vocal_change_state':
			global $_,$myUser;
			try{
				$response['responses'][0]['type'] = 'talk';
				if(!$myUser->can('plugin_radiorelay','u')) throw new Exception ('Je ne vous connais pas, ou alors vous n\'avez pas le droit, je refuse de faire ça!');
				radiorelay_plugin_change_state($_['engine'],$_['state']);
				$response['responses'][0]['sentence'] = Personality::response('ORDER_CONFIRMATION');
			}catch(Exception $e){
				$response['responses'][0]['sentence'] = Personality::response('WORRY_EMOTION').'! '.$e->getMessage();
			}
			$json = json_encode($response);
			echo ($json=='[]'?'{}':$json);
		break;

		case 'radioRelay_plugin_setting':
			Action::write(
				function($_,&$response){	
					global $conf;
					$conf->put('plugin_radioRelay_emitter_pin',$_['emiterPin']);
					$conf->put('plugin_radioRelay_emitter_code',$_['emiterCode']);
					$response['message'] = 'Configuration modifiée avec succès';
				},
				array('plugin_radiorelay'=>'u') 
			);
		break;

		case 'radioRelay_load_widget':

			require_once(dirname(__FILE__).'/../dashboard/Widget.class.php');
			
			Action::write(
				function($_,&$response){	
					
					$widget = new Widget();
					$widget = $widget->getById($_['id']);
					$data = $widget->data();

					$content = '';

					if(empty($data['relay'])){
						$content = 'Choisissez un relais en cliquant sur l \'icone <i class="fa fa-wrench"></i> de la barre du widget';
					}else{


						$radioPermission = fileperms(Plugin::path().'radioEmission')!='36333';
						if(($radioPermission !='36333') && ($radioPermission !='35913'))
						$content .= '<div style="margin:0px;" class="flatBloc pink-color">Attention, les droits vers le fichier <br/> radioEmission sont mal réglés.<br/> Référez vous à <span style="cursor:pointer;text-decoration:underline;" onclick="window.location.href=\'https://github.com/ldleman/yana-server#installation\';">la doc</span> pour les régler</div>';
						


						$relay = new RadioRelay();
						$relay = $relay->getById($data['relay']);
						
						$response['title'] = $relay->name;

						

						$content .= '
						<!-- CSS -->
						<style>
							
							.radiorelay_relay_pane {
							    background: none repeat scroll 0 0 #50597b;
							    list-style-type: none;
							    margin: 0;
							    cursor:default;
							    width: 100%;
							}
							.radiorelay_relay_pane li {
							    background: none repeat scroll 0 0 #50597b;
							    display: inline-block;
							    margin: 0 1px 0 0;
							    padding: 10px;
							    cursor:default;
							    vertical-align: top;
							}
							.radiorelay_relay_pane li h2 {
							    color: #ffffff;
							    font-size: 16px;
							    margin: 0 0 5px;
							    padding: 0;
							    cursor:default;
							}
							.radiorelay_relay_pane li h1 {
							    color: #B6BED9;
							    font-size: 14px;
							    margin: 0 0 10px;
							    padding: 0;
							    cursor:default;
							}

							.radiorelay_relay_pane li.radiorelay-case{
								background-color:  #373f59;
								width: 55px;
								cursor:pointer;
							}
							.radiorelay-case i{
								color:#8b95b8;
								font-size:50px;
								transition: all 0.2s ease-in-out;
							}
							.radiorelay-case.active i{
								color:#ffffff;
								text-shadow: 0 0 10px #ffffff;
							}

							.radiorelay-case.active i.fa-lightbulb-o{
								color:#FFED00;
								text-shadow: 0 0 10px #ffdc00;
							}
							.radiorelay-case.active i.fa-power-off{
								color:#BDFF00;
								text-shadow: 0 0 10px #4fff00;
							}

							.radiorelay-case.active i.fa-flash{
								color:#FFFFFF;
								text-shadow: 0 0 10px #00FFD9;
							}

							.radiorelay-case.active i.fa-gears{
								color:#FFFFFF;
								text-shadow: 0 0 10px #FF00E4;
							}

						</style>
						
						<!-- CSS -->
						<ul class="radiorelay_relay_pane">
								<li class="radiorelay-case '.($relay->state?'active':'').'" onclick="plugin_radiorelay_change(this,'.$relay->id.');" style="text-align:center;">
									<i title="On/Off" class="'.$relay->icon.'"></i>
								</li>
								<li>
									<h2>'.$relay->description.'</h2>
									<h1>CODE '.$relay->radiocode.($relay->pulse!=0?' - Pulse '.$relay->pulse.'µs':'').'</h1>
								</li>
							</ul>

						<!-- JS -->
						<script type="text/javascript">
							function plugin_radiorelay_change(element,id){
								var state = $(element).hasClass(\'active\') ? 0 : 1 ;

								$.action(
									{
										action : \'radioRelay_manual_change_state\', 
										engine: id,
										state: state
									},
									function(response){
										$(element).toggleClass("active");
									}
								);

							}
						</script>
						';
					}
					$response['content'] = $content;
				}
			);
		break;

		case 'radioRelay_edit_widget':
			require_once(dirname(__FILE__).'/../dashboard/Widget.class.php');
			$widget = new Widget();
			$widget = $widget->getById($_['id']);
			$data = $widget->data();
		
			$relayManager = new RadioRelay();
			$relays = $relayManager->populate();

			$content = '<h3>Relais ciblé</h3>';

			if(count($relays) == 0){
				$content = 'Aucun relais existant dans yana, <a href="setting.php?section=radioRelay">Créer un relais ?</a>';
			}else{
				$content .= '<select id="relay">';
				$content .= '<option value="">-</option>';
				foreach ($relays as $relay) {
					$content .= '<option value="'.$relay->id.'">'.$relay->name.'</option>';
				}
				$content .= '</select>';
			}
			echo $content;
		break;

		case 'radioRelay_save_widget':
			require_once(dirname(__FILE__).'/../dashboard/Widget.class.php');
			$widget = new Widget();
			$widget = $widget->getById($_['id']);
			$data = $widget->data();
			
			$data['relay'] = $_['relay'];
			$widget->data($data);
			$widget->save();
			echo $content;
		break;
	}
}


function radiorelay_plugin_change_state($engine,$state){
	global $conf;
       
        

	$radioRelay = new RadioRelay();
	$radioRelay = $radioRelay->getById($engine);

	$cmd = dirname(__FILE__).'/radioEmission '.$conf->get('plugin_radioRelay_emitter_pin').' '.$conf->get('plugin_radioRelay_emitter_code').' '.$radioRelay->radiocode.' ';
	$cmd .= $radioRelay->pulse ==0 ? ($state==1?'on':'off') : 'pulse '.$radioRelay->pulse;
					
	$radioRelay->state = $state;
	Functions::log('Launch system command : '.$cmd);
	system($cmd,$out);
	$radioRelay->save();

}




function radioRelay_plugin_setting_page(){
	global $_,$myUser,$conf;
	if(isset($_['section']) && $_['section']=='radioRelay' ){

		if(!$myUser) throw new Exception('Vous devez être connecté pour effectuer cette action');
		$radioRelayManager = new RadioRelay();
		$radioRelays = $radioRelayManager->populate();
		$roomManager = new Room();
		$rooms = $roomManager->loadAll(array('state'=>'0'));
		$selected =  new RadioRelay();
		$selected->pulse = 0;
		$selected->icon = 'fa fa-flash';

		//Si on est en mode modification
		if (isset($_['id']))
			$selected = $radioRelayManager->getById($_['id']);
			

		$icons = array(
			'fa fa-lightbulb-o',
			'fa fa-power-off',
			'fa fa-flash',
			'fa fa-gears',
			'fa fa-align-justify',
			'fa fa-adjust',
			'fa fa-arrow-circle-o-right',
			'fa fa-desktop',
			'fa fa-music',
			'fa fa-bell-o',
			'fa fa-beer',
			'fa fa-bullseye',
			'fa fa-automobile',
			'fa fa-book',
			'fa fa-bomb',
			'fa fa-clock-o',
			'fa fa-cutlery',
			'fa fa-microphone',
			'fa fa-tint'
			);
		?>

		<div class="span9 userBloc">

			<h1>Relais</h1>
			<p>Gestion des relais radios</p>  

			<fieldset>
			    <legend>Ajouter/Modifier un relais radio</legend>

			    <div class="left">

				    <label for="nameRadioRelay">Nom</label>
				    <input type="hidden" id="id" value="<?php echo $selected->id; ?>">
				    <input type="text" id="nameRadioRelay" value="<?php echo $selected->name; ?>" placeholder="Lumiere Canapé…"/>
				    
				    <label for="descriptionRadioRelay">Description</label>
				    <input type="text"  value="<?php echo $selected->description; ?>" id="descriptionRadioRelay" placeholder="Relais sous le canapé…" />

					<label for="iconRadioRelay">Icone</label>
				    <input type="hidden"  value="<?php echo $selected->icon; ?>" id="iconRadioRelay"  />
					
					<div>
						<div style='margin:5px;'>
						<?php foreach($icons as $i=>$icon){
							if($i%6==0) echo '</div><div style="margin:5px;">';
							?>
							<i style="width:25px;" onclick="plugin_radiorelay_set_icon(this,'<?php echo $icon; ?>');" class="<?php echo $icon; ?> btn <?php echo $selected->icon==$icon?'btn-success':''; ?>"></i>
						<?php } ?> 
						</div>
					</div>

					<label for="radioCodeRadioRelay">Code radio</label>
					<input type="text" value="<?php echo $selected->radiocode; ?>" name="radioCodeRadioRelay" id="radioCodeRadioRelay" placeholder="1234,1111,0022..." />

				    <label for="onRadioRelay">Commande vocale "ON" associée</label>
				    <?php echo $conf->get('VOCAL_ENTITY_NAME') ?>, <input type="text" id="onRadioRelay" value="<?php echo $selected->onCommand; ?>" placeholder="Allume la lumière, Ouvre le volet…"/>
				   
				    
				    <label for="offRadioRelay">Commande vocale "OFF" associée</label>
				    <?php echo $conf->get('VOCAL_ENTITY_NAME') ?>, <input type="text" id="offRadioRelay" value="<?php echo $selected->offCommand; ?>" placeholder="Eteinds la lumière, Ferme le volet…"/>
				    
				    
				    <label for="roomRadioRelay">Pièce de la maison</label>
				    <select id="roomRadioRelay">
				    	<?php foreach($rooms as $room){ ?>
				    	<option <?php if ($selected->room == $room->getId()){echo "selected";} ?> value="<?php echo $room->getId(); ?>"><?php echo $room->getName(); ?></option>
				    	<?php } ?>
				    </select>
				   <label for="pinRadioRelay">Mode impulsion (micro secondes)</label>
				   <input type="number" value="<?php echo $selected->pulse; ?>" id="pulseRadioRelay" placeholder="0" />
				   <small>laisser à zéro pour désactiver le mode impulsion</small>
				</div>

	  			<div class="clear"></div>
			    <br/><button onclick="plugin_radiorelay_save(this)" class="btn">Enregistrer</button>
		  	</fieldset>
			<br/>


			<fieldset>
				<legend>Consulter les relais radios existants</legend>
				<table class="table table-striped table-bordered table-hover">
				    <thead>
					    <tr>
					    	<th>Nom</th>
						    <th>Description</th>
						    <th>Code</th>
						    
						    <th>Pièce</th>
						    <th colspan="2">Impulsion</th>
						    
					    </tr>
				    </thead>
			    
			    	<?php foreach($radioRelays as $radioRelay){ 
			    		$room = $roomManager->load(array('id'=>$radioRelay->room)); 
			    	?>
					<tr>
				    	<td><?php echo $radioRelay->name; ?></td>
					    <td><?php echo $radioRelay->description; ?></td>
					    <td><?php echo $radioRelay->radiocode; ?></td>
					    
					    <td><?php echo $room->getName(); ?></td>
					    <td><?php echo $radioRelay->pulse; ?></td>
					    <td>
					    	<a class="btn" href="setting.php?section=radioRelay&id=<?php echo $radioRelay->id; ?>"><i class="fa fa-pencil"></i></a>
					    	<div class="btn" onclick="plugin_radiorelay_delete(<?php echo $radioRelay->id; ?>,this);"><i class="fa fa-times"></i></div>
					    </td>
					    </td>
			    	</tr>
			    <?php } ?>
			    </table>
			</fieldset>
		</div>

<?php
	}
}

function radioRelay_plugin_setting_menu(){
	global $_;
	echo '<li '.(isset($_['section']) && $_['section']=='radioRelay'?'class="active"':'').'><a href="setting.php?section=radioRelay"><i class="fa fa-angle-right"></i> Relais radio</a></li>';
}


function radioRelay_plugin_widget(&$widgets){
		$widgets[] = array(
		    'uid'      => 'dash_radiorelay',
		    'icon'     => 'fa fa-bullseye',
		    'label'    => 'Relais Radio',
		    'background' => '#50597b', 
		    'color' => '#fffffff',
		    'onLoad'   => 'action.php?action=radioRelay_load_widget',
		    'onEdit'   => 'action.php?action=radioRelay_edit_widget',
		    'onSave'   => 'action.php?action=radioRelay_save_widget',
		);
}




function radioRelay_plugin_preference_menu(){
	global $_;
	echo '<li '.(@$_['block']=='radioRelay'?'class="active"':'').'><a  href="setting.php?section=preference&block=radioRelay"><i class="fa fa-angle-right"></i> Radio Relais</a></li>';
}
function radioRelay_plugin_preference_page(){
	global $myUser,$_,$conf;
	if((isset($_['section']) && $_['section']=='preference' && @$_['block']=='radioRelay' )  ){
		if($myUser!=false){
			?>

			<div class="span9 userBloc">

					<div>
						<label>Pin du raspberry PI branché à l'émetteur radio: </label>
						<input type="text" class="input-large" id="emiterPin" value="<?php echo $conf->get('plugin_radioRelay_emitter_pin');?>" placeholder="Pin wiring PI...">

						<label>Code de la télécommande pris par le raspberry pi: </label>
						<input type="text" class="input-large" id="emiterCode" value="<?php echo $conf->get('plugin_radioRelay_emitter_code');?>" placeholder="par exemple 8217034...">

						<button onclick="plugin_radiorelay_save_settings(this);" class="btn">Enregistrer</button>
					</div>
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



Plugin::addCss("/css/main.css"); 
Plugin::addJs("/js/main.js",true); 

//Lie radioRelay_plugin_preference_menu au menu de réglages
Plugin::addHook("preference_menu", "radioRelay_plugin_preference_menu"); 
//Lie radioRelay_plugin_preference_page a la page  de réglages
Plugin::addHook("preference_content", "radioRelay_plugin_preference_page"); 
//Lie radioRelay_plugin_setting_page a la zone réglages
Plugin::addHook("setting_bloc", "radioRelay_plugin_setting_page");
//Lie radioRelay_plugin_setting_menu au menu de réglages
Plugin::addHook("setting_menu", "radioRelay_plugin_setting_menu"); 
//Lie radiorelay_plugin_action a la page d'action qui perme d'effecuer des actionx ajax ou ne demdnant pas de retour visuels
Plugin::addHook("action_post_case", "radiorelay_plugin_action");    
//Lie radiorelay_plugin_vocal_command a la gestion de commandes vocales proposées par yana
Plugin::addHook("vocal_command", "radiorelay_plugin_vocal_command");
//Lie radioRelay_plugin_widget aux widgets de la dashboard
Plugin::addHook("widgets", "radioRelay_plugin_widget");

?>
