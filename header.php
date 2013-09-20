<?php 

require_once('common.php');

$menuItems = array();
Plugin::callHook("menubar_pre_home", array(&$menuItems));
uasort ($menuItems , function($a,$b){return $a['sort']>$b['sort']?1:-1;});
$start=microtime(true);
// $notifications = array();


// $ctx=stream_context_create(array('http'=>
//     array(
//         'timeout' => 1
//     )
// ));

// $json = @file_get_contents(UPDATE_URL,false,$ctx);

// if($json!=false)$json = json_decode( $json ,true);
// $notificationUrl = '#';
// 	if(isset ($json['maj']['yana-server']['version']) && $json['maj']['yana-server']['version']!=PROGRAM_VERSION){	
// 		$notifications[] = 'Version '.$json['maj']['yana-server']['version'].' disponible.';
// 		if(isset($json['maj']['yana-server']['link']))$notificationUrl = $json['maj']['yana-server']['link'];
		
// 	}




// $notificationsCount = count($notifications);
// $tpl->assign('notificationsCount',$notificationsCount);
// $tpl->assign('notifications',implode(',',$notifications));
// $tpl->assign('notificationUrl',$notificationUrl);
$tpl->assign('menuItems',$menuItems);
?>

