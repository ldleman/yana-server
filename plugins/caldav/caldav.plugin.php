<?php
/*
@name Caldav
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
	global $myUser;
	
	
	

	try{
		
	$dbmanager = new Configuration();
	$calendarsQuery = $dbmanager->customQuery("SELECT * FROM ".ENTITY_PREFIX."plugin_caldav_calendars WHERE principaluri ='principals/".$myUser->getLogin()."'");
	$calendars =array();
	while($calendar = $calendarsQuery->fetchArray()):
		$calendars[] = $calendar;
	endwhile;
	
	if(count($calendars)==0) throw new Exception('Aucun calendrier créé pour votre compte, veuillez <a href="setting.php?section=caldav">créer un calendrier</a> avant de le consulter.');
		
	
	$_['calendar'] = isset($_['calendar']) ? $_['calendar']: $calendars[0]['uri'];
	
	$url = YANA_URL.'/plugins/caldav/calendars.php/calendars/'.$myUser->getLogin() .'/'.$_['calendar'];
			
	
	?>
	<div style="width:300px;margin:20px auto;" >
	Calendrier : <select id="calendarSelect" onchange="window.location='index.php?module=caldav&calendar='+$(this).val();">
	<?php foreach($calendars as $calendar): ?>
	<option <?php echo $_['calendar'] ==$calendar['uri'] ?'selected="selected"':'';  ?> value="<?php echo $calendar['uri']; ?>"><?php echo  $calendar['displayname']; ?></option>
	<?php endforeach; ?>
	</select>
	</div>
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
	}catch(Exception $e){
		?><div class="row"><div class="span12">
		<div class="alert alert-error fade in">
    <button type="button" class="close" data-dismiss="alert">×</button>
		<?php echo $e->getMessage(); ?></div>
		</div></div><?php
	}
}



function caldav_action(){
	global $_,$myUser;
	switch($_['action']){
		
		case 'caldav_get_events':
			Action::write(function($_,&$response){
				global $myUser;
				require_once('CalDavClient.class.php');
				$client = new CaldavClient();
				$client->host = YANA_URL.'/plugins/caldav/calendars.php/calendars';
				
	
				$client->login = $myUser->getLogin();
				$client->password = $myUser->getToken();
				
				$client->user = $myUser->getLogin();
				$client->calendar = $_['calendar'];
				
			
				
				$events = $client->get_events($_['calendar'],strtotime($_['start']),strtotime($_['end']));
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
				global $myUser;
				require_once('CalDavClient.class.php');
				$client = new CaldavClient();
				$client->host = YANA_URL.'/plugins/caldav/calendars.php/calendars';
				
				$client->login = $myUser->getLogin();
				$client->password = $myUser->getToken();
				
				$client->user = $myUser->getLogin();
				$client->calendar = $_['calendar'];
				
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
				global $myUser;
				require_once('CalDavClient.class.php');
				$client = new CaldavClient();
				$client->host = YANA_URL.'/plugins/caldav/calendars.php/calendars';
				
				$client->login = $myUser->getLogin();
				$client->password = $myUser->getToken();
				
				$client->user = $myUser->getLogin();
				$client->calendar = $_['calendar'];
				foreach($_['events'] as $ics){
					$ics = $client->delete_event($ics);
				}
			});
		break;
		case 'caldav_add_calendar':
			global $myUser;
			$dbmanager = new Configuration();
			$slug = Functions::slugify($_['label']);
			
			$principalQuery = $dbmanager->customQuery("SELECT * FROM ".ENTITY_PREFIX."plugin_caldav_principals WHERE uri='principalsr/".$myUser->getLogin()."'  LIMIT 1");
			$principal = $principalQuery->fetchArray();
			
			
			if(!$principal){
				$query = "
				INSERT INTO ".ENTITY_PREFIX."plugin_caldav_principals (uri,email,displayname) VALUES ('principals/".$myUser->getLogin()."','". $myUser->getMail()."','".$myUser->getLogin()."');
				INSERT INTO ".ENTITY_PREFIX."plugin_caldav_principals (uri,email,displayname) VALUES ('principals/".$myUser->getLogin()."/calendar-proxy-read', null, null);
				INSERT INTO ".ENTITY_PREFIX."plugin_caldav_principals (uri,email,displayname) VALUES ('principals/".$myUser->getLogin()."/calendar-proxy-write', null, null);";
				$dbmanager->customExecute($query);
			}
			
			
			$query = "INSERT INTO ".ENTITY_PREFIX."plugin_caldav_calendars (principaluri, displayname, uri, synctoken, description, calendarorder, calendarcolor, timezone, components, transparent) VALUES ('principals/".$myUser->getLogin()."','".	$_['label']."','".	$slug."',	1,	NULL,	NULL,	NULL,	NULL,	'VEVENT,VTODO',	'0');";
			$dbmanager->customExecute($query);
			header('location: setting.php?section=caldav');
		break;
	}
}

function caldav_setting_menu(){
	global $_;
	echo '<li '.(isset($_['section']) && $_['section']=='propise'?'class="active"':'').'><a href="setting.php?section=caldav"><i class="fa fa-angle-right"></i> Calendriers</a></li>';
}


function caldav_setting_page(){
	global $_,$myUser,$conf;
	if(isset($_['section']) && $_['section']=='caldav' ){
		
		if(!$myUser) throw new Exception('Vous devez être connecté pour effectuer cette action');
		$dbmanager = new Configuration();
		$calendars = $dbmanager->customQuery("SELECT * FROM ".ENTITY_PREFIX."plugin_caldav_calendars WHERE principaluri ='principals/".$myUser->getLogin()."'");
		
		?>

		<div class="span9">

			<h1>Calendriers</h1>
			<p>Gestion des calendriers</p>  

			<form action="action.php" method="POST">
			<input type="hidden" value="caldav_add_calendar" name="action">
			<h4>Créer un nouveau calendrier</h4>
			<label>Nom</label>
			<input type="text" name="label" id="label">
			<input type="submit" class="btn" value="Créer">
			</form>
			
			<hr/>
			<h4>Calendrier créés</h4>
			<table class="table">
			<thead>
			<tr>
				<th>Nom</th>
				<th>Adresse du synchronisation</th>
			</tr>
			</thead>
			<tbody>
			<?php while($calendar = $calendars->fetchArray()): 
			$url = YANA_URL.'/plugins/caldav/calendars.php/calendars/'.str_replace('principals/','',$calendar['principaluri']) .'/'.$calendar['uri'];
			?>
			<tr>
			<td>
				<?php echo $calendar['displayname']; ?>
			</td>
			<td>
				<a href="<?php echo $url;?>"><?php echo $url ?></a>
			</td>
			</tr>
			<?php endwhile; ?>
			</tbody>
			</table>
		</div>

<?php
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
Plugin::addHook("setting_menu", "caldav_setting_menu"); 
Plugin::addHook("setting_bloc", "caldav_setting_page"); 
?>