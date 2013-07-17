<?php if(!class_exists('raintpl')){exit;}?><?php $tpl = new RainTPL;$tpl->assign( $this->var );$tpl->draw( "header" );?>


<?php if( $myUser!=false ){ ?>


<div class="row">
<div class="span3 bs-docs-sidebar">
  <ul class="nav nav-tabs nav-stacked">
    <li><a href="#plugins"><i class="icon-chevron-right"></i> Utilisateurs & Droits</a></li>
    <li><a href="#variables"><i class="icon-chevron-right"></i> Configurations</a></li>
    <li  class="active"><a href="#components"><i class="icon-chevron-right"></i> Plugins</a></li>
    <?php echo Plugin::callHook("setting_menu", array()); ?>
  </ul>
</div>



<?php echo Plugin::callHook("setting_bloc", array()); ?>

<div class="span9 pluginBloc">

<h1>Plugins</h1>

<p>Voici la liste des plugins installés :</p>
                    <?php if( $myUser!=false ){ ?>
                    <ul class="pluginList">
                    <?php if( count($plugins)==0 ){ ?>
                    Aucun plugin n'est installé pour le moment.
                    <?php }else{ ?>
                    <?php $counter1=-1; if( isset($plugins) && is_array($plugins) && sizeof($plugins) ) foreach( $plugins as $key1 => $value1 ){ $counter1++; ?>
                    <li>
                        <ul>
                            <li><h4>Nom: </h4><?php echo $value1->getName();?></li>
                            <li><h4>Auteur: </h4><a href="mailto:<?php echo $value1->getMail();?>"><?php echo $value1->getAuthor();?></a></li>
                            <li><h4>Licence: </h4><?php echo $value1->getLicence();?></li>
                            <li><h4>Version: </h4><code><?php echo $value1->getVersion();?></code></li>
                            <li><h4>Site web: </h4><a href="<?php echo $value1->getLink();?>"><?php echo $value1->getLink();?></a></li>
                            <li><?php echo $value1->getDescription();?></li>
                            <li><a href="action.php?action=changePluginState&plugin=<?php echo $value1->getUid();?>&state=<?php echo $value1->getState();?>" class="button"><?php echo $value1->getState()=="0"?"Activer":"Désactiver";?></a></li>
                        </ul>
                    </li>
                    <?php } ?>
                    <?php } ?>
                    </ul>

                    <?php }else{ ?>
                    <p>Vous devez être connecté pour voir les plugins.</p>
                    <?php } ?>


</div>

</div>

<?php }else{ ?>
	<div id="main" class="wrapper clearfix">
		<article>
				<h3>Vous devez être connecté</h3>
		</article>
	</div>

<?php } ?>
<?php $tpl = new RainTPL;$tpl->assign( $this->var );$tpl->draw( "footer" );?>
