<?php if(!class_exists('raintpl')){exit;}?><?php $tpl = new RainTPL;$tpl->assign( $this->var );$tpl->draw( "header" );?>

<div class="row"><?php if( !isset($_['module']) || $_['module']=='home' ){ ?><?php echo Plugin::callHook("dashboard_pre", array($_)); ?><?php } ?><?php echo Plugin::callHook("home", array($_)); ?><?php if( !isset($_['module']) || $_['module']=='home' ){ ?><?php echo Plugin::callHook("dashboard_post", array($_)); ?><?php } ?></div>


<?php $tpl = new RainTPL;$tpl->assign( $this->var );$tpl->draw( "footer" );?>
   
