<?php if(!class_exists('raintpl')){exit;}?><?php $tpl = new RainTPL;$tpl->assign( $this->var );$tpl->draw( "header" );?>


<div class="row">
    <?php echo Plugin::callHook("home", array($_)); ?>
</div>

<?php $tpl = new RainTPL;$tpl->assign( $this->var );$tpl->draw( "footer" );?>
   