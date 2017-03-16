<?php

require_once(__DIR__.'/../../common.php');
require_once(__DIR__.'/constant.php');
require_once __ROOT__.'/lib/sabre/autoload.php';
	
global $conf;
	
$pdo = new PDO('sqlite:'.__ROOT__.'/'.DB_NAME);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Backends
$authBackend = new Sabre\DAV\Auth\Backend\BasicCallBack(function($userName, $password) {
   
  // global $myUser;
   //if($myUser!=false && $myUser->getLogin()!='') return true;
   
   $myUser = User::exist($userName, $password,false,false);
   
   if(file_exists(__DIR__.'/sessions/'.$password)){
	   unlink(__DIR__.'/sessions/'.$password);
	   return true;
   }
   
   return $myUser!=false;
});

$principalBackend = new Sabre\DAVACL\PrincipalBackend\PDO($pdo);
$principalBackend->tableName = ENTITY_PREFIX.'plugin_caldav_principals';
$principalBackend->groupMembersTableName = ENTITY_PREFIX.'plugin_caldav_groupmembers';

$calendarBackend = new Sabre\CalDAV\Backend\PDO($pdo);
$calendarBackend->calendarTableName = ENTITY_PREFIX.'plugin_caldav_calendars';
$calendarBackend->calendarObjectTableName = ENTITY_PREFIX.'plugin_caldav_calendarobjects';
$calendarBackend->calendarChangesTableName = ENTITY_PREFIX.'plugin_caldav_calendarchanges';
$calendarBackend->schedulingObjectTableName = ENTITY_PREFIX.'plugin_caldav_schedulingobjects';
$calendarBackend->calendarSubscriptionsTableName = ENTITY_PREFIX.'plugin_caldav_calendarsubscriptions';



// Directory structure
$tree = [
    new Sabre\CalDAV\Principal\Collection($principalBackend),
    new Sabre\CalDAV\CalendarRoot($principalBackend, $calendarBackend),
];
// The object tree needs in turn to be passed to the server class
$server = new Sabre\DAV\Server($tree);
$server->setBaseUri($conf->get('WEBDAV_CALENDAR_URL'));
// Plugins
$server->addPlugin(new Sabre\DAV\Auth\Plugin($authBackend));
$server->addPlugin(new Sabre\DAVACL\Plugin());
/* CalDAV support */
$caldavPlugin = new Sabre\CalDAV\Plugin();
$server->addPlugin($caldavPlugin);
/* Calendar subscription support */
$server->addPlugin(new Sabre\CalDAV\Subscriptions\Plugin());
/* Calendar scheduling support */
$server->addPlugin(new Sabre\CalDAV\Schedule\Plugin());
/* WebDAV-Sync plugin */
$server->addPlugin(new Sabre\DAV\Sync\Plugin());
// Support for html frontend
$server->addPlugin( new Sabre\DAV\Browser\Plugin());

$server->exec();


?>