<?php if(!class_exists('raintpl')){exit;}?><?php $tpl = new RainTPL;$tpl->assign( $this->var );$tpl->draw( "header" );?>



<div class="row">
    <?php echo Plugin::callHook("home", array($_)); ?>

    
    <?php if( !isset($_['module']) || $_['module']=='home' ){ ?>

    <div class="span9">
    <h1>Bienvenue</h1>
	    <ul>
	    	<li><strong>Identité :</strong> <?php echo $myUser->getFullName();?></li>
	    	<li><strong>Mail :</strong> <a href="mailto:<?php echo $myUser->getMail();?>"><?php echo $myUser->getMail();?></a></li>
	    	<li><strong>Token :</strong> <code><?php echo $myUser->getToken();?></code></li>
	    </ul>
	</div>
	<?php } ?>


</div>

<?php $tpl = new RainTPL;$tpl->assign( $this->var );$tpl->draw( "footer" );?>

   