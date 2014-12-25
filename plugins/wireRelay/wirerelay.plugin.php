<?php
/*
@name WireRelay
@author Valentin CARRUESCO <idleman@idleman.fr>
@link Http://blog.idleman.fr
@licence Cc -by-nc-sa
@version 1.0
@description Prise relais filaire
*/

//On appelle les entités de base de données
include('WireRelay.class.php');


//Cette fonction ajoute une commande vocale
function wirerelay_plugin_vocal_command(&$response,$actionUrl){
	global $conf;

	$wireRelayManager = new WireRelay();

	$wireRelays = $wireRelayManager->populate();
	foreach($wireRelays as $wireRelay){
		$response['commands'][] = array('command'=>$conf->get('VOCAL_ENTITY_NAME').', '.$wireRelay->onCommand,'url'=>$actionUrl.'?action=wireRelay_vocal_change_state&engine='.$wireRelay->id.'&state=1','confidence'=>('0.90'+$conf->get('VOCAL_SENSITIVITY')));
		$response['commands'][] = array('command'=>$conf->get('VOCAL_ENTITY_NAME').', '.$wireRelay->offCommand,'url'=>$actionUrl.'?action=wireRelay_vocal_change_state&engine='.$wireRelay->id.'&state=0','confidence'=>('0.90'+$conf->get('VOCAL_SENSITIVITY')));
	}
}

//cette fonction comprends toutes les actions du plugin qui ne nécessitent pas de vue html
function wirerelay_plugin_action(){
	global $_,$conf,$myUser;

	//Action de réponse à la commande vocale "Yana, commande de test"
	switch($_['action']){

		case 'wireRelay_save_wireRelay':
			Action::write(
				function($_,&$response){
					$wireRelayManager = new WireRelay();

					if(empty($_['nameWireRelay'])) throw new Exception("Le nom est obligatoire");
					if(!is_numeric($_['pinWireRelay']))  throw new Exception("Le PIN GPIO est obligatoire et doit être numerique");

					$wireRelay = !empty($_['id']) ? $wireRelayManager->getById($_['id']): new WireRelay();
					$wireRelay->name = $_['nameWireRelay'];
					$wireRelay->description = $_['descriptionWireRelay'];
					$wireRelay->pin = $_['pinWireRelay'];
					$wireRelay->room = $_['roomWireRelay'];
					$wireRelay->pulse = $_['pulseWireRelay'];
					$wireRelay->onCommand = $_['onWireRelay'];
					$wireRelay->offCommand = $_['offWireRelay'];
					$wireRelay->icon = $_['iconWireRelay'];
					$wireRelay->save();
					$response['message'] = 'Relais enregistré avec succès';
				},
				array('plugin_wirerelay'=>'c')
			);
		break;

		case 'wireRelay_delete_wireRelay':
			Action::write(
				function($_,$response){
					$wireRelayManager = new WireRelay();
					$wireRelayManager->delete(array('id'=>$_['id']));
				},
				array('plugin_wirerelay'=>'d') 
			);
		break;



		case 'wireRelay_plugin_setting':
			Action::write(
				function($_,$response){	
					$conf->put('plugin_wireRelay_emitter_pin',$_['emiterPin']);
					$conf->put('plugin_wireRelay_emitter_code',$_['emiterCode']);
				},
				array('plugin_wirerelay'=>'c') 
			);
		break;

		case 'wireRelay_manual_change_state':
			Action::write(
				function($_,&$response){	
					wirerelay_plugin_change_state($_['engine'],$_['state']);
				},
				array('plugin_wirerelay'=>'c') 
			);
		break;

		case 'wireRelay_vocal_change_state':
			global $_,$myUser;
			try{
				$response['responses'][0]['type'] = 'talk';
				if(!$myUser->can('relais filaire','u')) throw new Exception ('Je ne vous connais pas, ou alors vous n\'avez pas le droit, je refuse de faire ça!');
				wirerelay_plugin_change_state($_['engine'],$_['state']);
				$response['responses'][0]['sentence'] = Personality::response('ORDER_CONFIRMATION');
			}catch(Exception $e){
				$response['responses'][0]['sentence'] = Personality::response('WORRY_EMOTION').'! '.$e->getMessage();
			}
			$json = json_encode($response);
			echo ($json=='[]'?'{}':$json);
		break;

		case 'wireRelay_load_widget':

			require_once(dirname(__FILE__).'/../dashboard/Widget.class.php');
			
			Action::write(
				function($_,&$response){	

					$widget = new Widget();
					$widget = $widget->getById($_['id']);
					$data = $widget->data();



					if(empty($data['relay'])){
						$content = 'Choisissez un relais en cliquant sur l \'icone <i class="fa fa-wrench"></i> de la barre du widget';
					}else{
						$relay = new WireRelay();
						$relay = $relay->getById($data['relay']);
						
						$response['title'] = $relay->name;

						

						$content = '
						<!-- CSS -->
						<style>
							
							.relay_pane {
							    background: none repeat scroll 0 0 #50597b;
							    list-style-type: none;
							    margin: 0;
							    cursor:default;
							    width: 100%;
							}
							.relay_pane li {
							    background: none repeat scroll 0 0 #50597b;
							    display: inline-block;
							    margin: 0 1px 0 0;
							    padding: 10px;
							    cursor:default;
							    vertical-align: top;
							}
							.relay_pane li h2 {
							    color: #ffffff;
							    font-size: 16px;
							    margin: 0 0 5px;
							    padding: 0;
							    cursor:default;
							}
							.relay_pane li h1 {
							    color: #B6BED9;
							    font-size: 14px;
							    margin: 0 0 10px;
							    padding: 0;
							    cursor:default;
							}

							.relay_pane li.wirerelay-case{
								background-color:  #373f59;
								cursor:pointer;
							}
							.wirerelay-case i{
								color:#8b95b8;
								font-size:50px;
								transition: all 0.2s ease-in-out;
							}
							.wirerelay-case.active i{
								color:#ffffff;
								text-shadow: 0 0 10px #ffffff;
							}

							.wirerelay-case.active i.fa-lightbulb-o{
								color:#FFED00;
								text-shadow: 0 0 10px #ffdc00;
							}
							.wirerelay-case.active i.fa-power-off{
								color:#BDFF00;
								text-shadow: 0 0 10px #4fff00;
							}

							.wirerelay-case.active i.fa-flash{
								color:#FFFFFF;
								text-shadow: 0 0 10px #00FFD9;
							}

							.wirerelay-case.active i.fa-gears{
								color:#FFFFFF;
								text-shadow: 0 0 10px #FF00E4;
							}

						</style>
						
						<!-- CSS -->
						<ul class="relay_pane">
								<li class="wirerelay-case '.(Gpio::read($relay->pin,true)?'active':'').'" onclick="plugin_wirerelay_change(this);" style="text-align:center;">
									<i title="On/Off" class="'.$relay->icon.'"></i>
								</li>
								<li>
									<h2>'.$relay->description.'</h2>
									<h1>PIN '.$relay->pin.($relay->pulse!=0?' - Pulse '.$relay->pulse.'µs':'').'</h1>
								</li>
							</ul>

						<!-- JS -->
						<script type="text/javascript">
							function plugin_wirerelay_change(element){
								var state = $(element).hasClass(\'active\') ? 0 : 1 ;

								$.action(
									{
										action : \'wireRelay_manual_change_state\', 
										engine: '.$relay->id.',
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

		case 'wireRelay_edit_widget':
			require_once(dirname(__FILE__).'/../dashboard/Widget.class.php');
			$widget = new Widget();
			$widget = $widget->getById($_['id']);
			$data = $widget->data();
		
			$relayManager = new WireRelay();
			$relays = $relayManager->populate();

			$content = '<h3>Relais ciblé</h3>';

			if(count($relays) == 0){
				$content = 'Aucun relais existant dans yana, <a href="setting.php?section=wireRelay">Créer un relais ?</a>';
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

		case 'wireRelay_save_widget':
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


function wirerelay_plugin_change_state($engine,$state){
	$wireRelay = new WireRelay();
	$wireRelay = $wireRelay->getById($engine);
	Gpio::mode($wireRelay->pin,'out');
	if($wireRelay->pulse==0){
		Gpio::write($wireRelay->pin,$state);
	}else{
		Gpio::pulse($wireRelay->pulse,1);
	}
}




function wireRelay_plugin_setting_page(){
	global $_,$myUser,$conf;
	if(isset($_['section']) && $_['section']=='wireRelay' ){

		if(!$myUser) throw new Exception('Vous devez être connecté pour effectuer cette action');
		$wireRelayManager = new WireRelay();
		$wireRelays = $wireRelayManager->populate();
		$roomManager = new Room();
		$rooms = $roomManager->populate();
		$selected =  new WireRelay();
		$selected->pulse = 0;
		$selected->icon = 'fa fa-flash';

		//Si on est en mode modification
		if (isset($_['id']))
			$selected = $wireRelayManager->getById($_['id']);
			

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
			<p>Gestion des relais filaires</p>  

			<fieldset>
			    <legend>Ajouter/Modifier un relais filaire</legend>

			    <div class="left">

				    <label for="nameWireRelay">Nom</label>
				    <input type="hidden" id="id" value="<?php echo $selected->id; ?>">
				    <input type="text" id="nameWireRelay" value="<?php echo $selected->name; ?>" placeholder="Lumiere Canapé…"/>
				    
				    <label for="descriptionWireRelay">Description</label>
				    <input type="text"  value="<?php echo $selected->description; ?>" id="descriptionWireRelay" placeholder="Relais sous le canapé…" />

					<label for="iconWireRelay">Icone</label>
				    <input type="hidden"  value="<?php echo $selected->icon; ?>" id="iconWireRelay"  />
					
					<div>
						<div style='margin:5px;'>
						<?php foreach($icons as $i=>$icon){
							if($i%6==0) echo '</div><div style="margin:5px;">';
							?>
							<i style="width:25px;" onclick="plugin_wirerelay_set_icon(this,'<?php echo $icon; ?>');" class="<?php echo $icon; ?> btn <?php echo $selected->icon==$icon?'btn-success':''; ?>"></i>
						<?php } ?> 
						</div>
					</div>

				    <label for="onWireRelay">Commande vocale "ON" associée</label>
				    <?php echo $conf->get('VOCAL_ENTITY_NAME') ?>, <input type="text" id="onWireRelay" value="<?php echo $selected->onCommand; ?>" placeholder="Allume la lumière, Ouvre le volet…"/>
				   
				    
				    <label for="offWireRelay">Commande vocale "OFF" associée</label>
				    <?php echo $conf->get('VOCAL_ENTITY_NAME') ?>, <input type="text" id="offWireRelay" value="<?php echo $selected->offCommand; ?>" placeholder="Eteinds la lumière, Ferme le volet…"/>
				    
				    
				    <label for="pinWireRelay">Pin GPIO (Numéro Wiring PI) relié au relais</label>
				    <input type="number" value="<?php echo $selected->pin; ?>" id="pinWireRelay" placeholder="0,1,2…" />
				    
				    <label for="roomWireRelay">Pièce de la maison</label>
				    <select id="roomWireRelay">
				    	<?php foreach($rooms as $room){ ?>
				    	<option <?php if ($selected->room == $room->getId()){echo "selected";} ?> value="<?php echo $room->getId(); ?>"><?php echo $room->getName(); ?></option>
				    	<?php } ?>
				    </select>
				   <label for="pinWireRelay">Mode impulsion (micro secondes)</label>
				   <input type="number" value="<?php echo $selected->pulse; ?>" id="pulseWireRelay" placeholder="0" />
				   <small>laisser à zéro pour désactiver le mode impulsion</small>
				</div>

	  			<div class="clear"></div>
			    <br/><button onclick="plugin_wirerelay_save(this)" class="btn">Enregistrer</button>
		  	</fieldset>
			<br/>


			<fieldset>
				<legend>Consulter les relais filaires existants</legend>
				<table class="table table-striped table-bordered table-hover">
				    <thead>
					    <tr>
					    	<th>Nom</th>
						    <th>Description</th>
						    <th>Pin GPIO</th>
						    <th>Pièce</th>
						    <th colspan="2">Impulsion</th>
						    
					    </tr>
				    </thead>
			    
			    	<?php foreach($wireRelays as $wireRelay){ 
			    		$room = $roomManager->load(array('id'=>$wireRelay->room)); 
			    	?>
					<tr>
				    	<td><?php echo $wireRelay->name; ?></td>
					    <td><?php echo $wireRelay->description; ?></td>
					    <td><?php echo $wireRelay->pin; ?></td>
					    <td><?php echo $room->getName(); ?></td>
					    <td><?php echo $wireRelay->pulse; ?></td>
					    <td>
					    	<a class="btn" href="setting.php?section=wireRelay&id=<?php echo $wireRelay->id; ?>"><i class="fa fa-pencil"></i></a>
					    	<div class="btn" onclick="plugin_wirerelay_delete(<?php echo $wireRelay->id; ?>,this);"><i class="fa fa-times"></i></div>
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

function wireRelay_plugin_setting_menu(){
	global $_;
	echo '<li '.(isset($_['section']) && $_['section']=='wireRelay'?'class="active"':'').'><a href="setting.php?section=wireRelay"><i class="fa fa-angle-right"></i> Relais filaires</a></li>';
}


function wireRelay_plugin_widget(&$widgets){
		$widgets[] = array(
		    'uid'      => 'dash_wirerelay',
		    'icon'     => 'fa fa-flash',
		    'label'    => 'Relais',
		    'background' => '#50597b', 
		    'color' => '#fffffff',
		    'onLoad'   => 'action.php?action=wireRelay_load_widget',
		    'onEdit'   => 'action.php?action=wireRelay_edit_widget',
		    'onSave'   => 'action.php?action=wireRelay_save_widget',
		);
}



Plugin::addCss("/css/main.css"); 
Plugin::addJs("/js/main.js"); 


//Lie wireRelay_plugin_setting_page a la zone réglages
Plugin::addHook("setting_bloc", "wireRelay_plugin_setting_page");
//Lie wireRelay_plugin_setting_menu au menu de réglages
Plugin::addHook("setting_menu", "wireRelay_plugin_setting_menu"); 
//Lie wirerelay_plugin_action a la page d'action qui perme d'effecuer des actionx ajax ou ne demdnant pas de retour visuels
Plugin::addHook("action_post_case", "wirerelay_plugin_action");    
//Lie wirerelay_plugin_vocal_command a la gestion de commandes vocales proposées par yana
Plugin::addHook("vocal_command", "wirerelay_plugin_vocal_command");
//Lie wireRelay_plugin_widget aux widgets de la dashboard
Plugin::addHook("widgets", "wireRelay_plugin_widget");

?>