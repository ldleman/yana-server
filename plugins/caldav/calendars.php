<?php

require_once(__DIR__.'/../../common.php');
require_once(__DIR__.'/constant.php');
require_once __ROOT__.'/lib/sabre/autoload.php';
	
global $conf;
	
$pdo = new PDO('sqlite:'.__ROOT__.'/db/.caldav.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Backends
$authBackend      = new Sabre\DAV\Auth\Backend\PDO($pdo);
$principalBackend = new Sabre\DAVACL\PrincipalBackend\PDO($pdo);
$calendarBackend = new Sabre\CalDAV\Backend\PDO($pdo);

$authBackend->setRealm('sabre');

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