<?php 
	$tpl->assign('executionTime',number_format(microtime(true)-$start,3));
	if(isset($view) && $view!='') $html = $tpl->draw($view);
?>