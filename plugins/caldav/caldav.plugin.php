<?php
/*
@name Caldav (Construction)
@author Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@description Plugin intégrant un serveur caldav et un calendrier mois/semaine/jour
*/
function caldav_menu(&$menuItems){
	global $_;
	$menuItems[] = array('sort'=>10,'content'=>'<a href="index.php?module=caldav"><i class="fa fa-calendar"></i> Calendrier</a>');
}


function caldav_home($_){
	if(!isset($_['module']) || $_['module']!='caldav') return;
?>
	<div id='calendar'></div>
	

    
    <!-- Modal -->
    <div id="eventModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Evenement</h3>
      </div>
      <div class="modal-body">
        
	  <label>Libellé</label>
	  <input type="text" id="label">
		
		<label label="startDay">Début</label>
		<input type="text" class="input-small date" id="startDay">
		<input type="text" class="input-mini" id="startHour">:
		<input type="text" class="input-mini" id="startMinut">

		<label label="endDay">Fin</label>
		<input type="text" class="input-small date" id="endDay">
		<input type="text" class="input-mini" id="endHour">
		<input type="text" class="input-mini" id="endMinut">
		<hr/>
		<label label="location">Lieu</label>
		<input type="text" class="input-large" id="location">
		<hr/>
		<div class="form-inline">
			<input id="alert" type="checkbox" id=""> Alerter <input id="alertNumber" type="text" class="input-mini"> 
			<select id="alertUnity" class="input-small">
				<option value="m">Minute(s)</option>
				<option value="h">Heure(s)</option>
				<option value="d">Jour(s)</option>
			</select> avant l'évenement.
		</div>
      </div>
      <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Fermer</button>
        <button class="btn btn-primary" onclick="caldav_save_event();">Enregistrer</button>
      </div>
    </div>
	
	
	
	
<?php
}

function caldav_action(){
	global $_,$myUser;
	switch($_['action']){
		
		case 'caldav_get_events':
			Action::write(function($_,&$response){
				require_once('CalDavClient.class.php');
				$client = new CaldavClient();
				$client->host = YANA_URL.'/plugins/caldav/calendars.php/calendars';
				$client->login = 'admin';
				$client->password = 'admin';
				$client->user = 'admin';
				$client->calendar = 'global';
				
				$events = $client->get_events('global');
				$response = array();
				foreach($events as $event):
					$response[] = array(
					'id' => $event->ics,
					'title' => $event->title,
					'start' => date('Y-m-d\TH:i:s',$event->start),
					'end' => date('Y-m-d\TH:i:s',$event->end),
					'backgroundColor'=> '#94c655',
					'borderColor' => '#78ab24',
					'textColor' => '#ffffff'
					);
				endforeach;
			});
		break;
		case 'caldav_save_event':
			Action::write(function($_,&$response){
				require_once('CalDavClient.class.php');
				$client = new CaldavClient();
				$client->host = YANA_URL.'/plugins/caldav/calendars.php/calendars';
				$client->login = 'admin';
				$client->password = 'admin';
				$client->user = 'admin';
				$client->calendar = 'global';
				
				list($startDay,$startMonth,$startYear) = explode('/',$_['startDay']);
				list($endDay,$endMonth,$endYear) = explode('/',$_['endDay']);
				$start = mktime ($_['startHour'], $_['startMinut'], 0, $startMonth,$startDay, $startYear);
				$end = mktime ($_['endHour'], $_['endMinut'], 0, $endMonth,$endDay, $endYear);
				
				$event = new IcalEvent();
				$event->title = $_['label'];
				$event->description = $_['label'];
				$event->start = $start;
				$event->end = $end ;
				$event->categories =  array('évenement');
				if($_['alert']=='1')
					$event->alarms = array($_['alertNumber'].$_['alertUnity']);
				if($_['location']!='')
					$event->location = $_['location'];

				$ics = isset($_['ics']) && !empty($_['ics']) ? $_['ics'] : null;
				$ics = $client->create_event($event,$ics);

				$event->id = $ics;
				$event->start = date('Y-m-d\TH:i:s',$start);
				$event->end = date('Y-m-d\TH:i:s',$end);
				$response['event'] = $event;
			});
		break;
		case 'caldav_delete_event':
			Action::write(function($_,&$response){
				require_once('CalDavClient.class.php');
				$client = new CaldavClient();
				$client->host = YANA_URL.'/plugins/caldav/calendars.php/calendars';
				$client->login = 'admin';
				$client->password = 'admin';
				$client->user = 'admin';
				$client->calendar = 'global';
				foreach($_['events'] as $ics){
					$ics = $client->delete_event($ics);
				}
			});
		break;
	}
}


Plugin::addJs('/js/moment.min.js');
Plugin::addJs('/js/fullcalendar.min.js');
Plugin::addJs('/js/locale-all.js');
Plugin::addJs('/js/main.js');
Plugin::addCss('/css/main.css',true);
Plugin::addCss('/css/fullcalendar.min.css');

Plugin::addHook("menubar_pre_home", "caldav_menu");  
Plugin::addHook("home", "caldav_home");
Plugin::addHook("action_post_case", "caldav_action");
//Plugin::addHook("preference_menu", "dashboard_plugin_preference_menu"); 
//Plugin::addHook("preference_content", "dashboard_plugin_preference_page"); 
?>