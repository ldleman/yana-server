<?php if(!class_exists('raintpl')){exit;}?><?php $tpl = new RainTPL;$tpl->assign( $this->var );$tpl->draw( "header" );?>

<div class="row">
	<div class="span3 bs-docs-sidebar">
	  <ul class="nav nav-tabs nav-stacked">
	  	<li <?php if( isset($_['section']) && $_['section']=='plugin' ){ ?>class="active"<?php } ?>><a href="setting.php?section=plugin"><i class="icon-chevron-right"></i> Plugins</a></li>
	  	<li <?php if( isset($_['section']) && $_['section']=='user' ){ ?>class="active"<?php } ?>><a href="setting.php?section=user"><i class="icon-chevron-right"></i> Utilisateurs</a></li>
	  	<li <?php if( isset($_['section']) && $_['section']=='access' ){ ?>class="active"<?php } ?>><a href="setting.php?section=access"><i class="icon-chevron-right"></i> Acces</a></li>
	    <?php echo Plugin::callHook("setting_menu", array()); ?>

	  </ul>
	</div>

	<div class="span9">



	<!-- SECTION RANK -->
	<?php if( @$_['section']=='access' ){ ?>


				<div class="span9 accessBloc">

				<?php echo Plugin::callHook("access_pre_display", array(&$accesss)); ?>


				<h1>Rangs</h1>
				<p>Gestion des rangs du programme</p>  

				<form action="action.php?action=access_add_rank" method="POST">
				<fieldset>
				    <legend>Ajout d'un rang</legend>

					    <label for="labelRank">Libellé</label>
					    <input type="text" id="labelRank" name="labelRank" placeholder="Libellé du rang…"/>
					    <label for="descriptionRank">Description</label>
					    <textarea name="descriptionRank" id="descriptionRank" placeholder="Description courte du rang…" ></textarea>
		  				<div class="clear"></div>
				    	<br/><button type="submit" class="btn">Ajouter</button>
			  	</fieldset>
				<br/>
			</form>

				<table class="table table-striped table-bordered table-hover">
			    <thead>
			    <tr>
			    	<th>Libellé</th>
				    <th>Description</th>
				    <th></th>
			    </tr>
			    </thead>
			    
			    <?php $counter1=-1; if( isset($ranks) && is_array($ranks) && sizeof($ranks) ) foreach( $ranks as $key1 => $value1 ){ $counter1++; ?>

			    <tr>
			    	<td><?php echo $value1->getLabel();?></td>
				    <td><?php echo $value1->getDescription();?></td>
				    <td><a class="btn" href="setting.php?section=right&id=<?php echo $value1->getId();?>"><i class="icon-ok-circle"></i></a>
				    	<a class="btn btn-danger" href="action.php?action=access_delete_rank&id=<?php echo $value1->getId();?>"><i class="icon-remove icon-white"></i></a></td>
			    </tr>
			    <?php } ?>

			    </table>
				</div>

	<?php } ?>



	<!-- SECTION ACCES -->



	<?php if( @$_['section']=='right' ){ ?>


		<div class="span9 accessBloc">
		<?php echo Plugin::callHook("access_pre_display", array(&$accesss)); ?>

		<h1>Rangs</h1>
		<p>Gestion des rangs du programme</p>  



		<table class="table table-striped table-bordered table-hover">
	    <thead>
	    <tr>
	    	<th>Section</th>
		    <th>Ajout</th>
		    <th>Modification</th>
		    <th>Supression</th>
		    <th>Consultation</th>
		    <th></th>
	    </tr>
	    </thead>
	    
	   <?php $counter1=-1; if( isset($sections) && is_array($sections) && sizeof($sections) ) foreach( $sections as $key1 => $value1 ){ $counter1++; ?>

	    <tr>
	    	<td><?php echo $value1->getLabel();?></td>
		    <td><input type="checkbox" onclick="setRankAccess(<?php echo $rank->getId();?>,<?php echo $value1->getId();?>,'c',this)" <?php if( @$rights[$value1->getId()]['c'] ){ ?>checked="checked"<?php } ?> /></td>
		    <td><input type="checkbox" onclick="setRankAccess(<?php echo $rank->getId();?>,<?php echo $value1->getId();?>,'r',this)" <?php if( @$rights[$value1->getId()]['r'] ){ ?>checked="checked"<?php } ?> /></td>
		    <td><input type="checkbox" onclick="setRankAccess(<?php echo $rank->getId();?>,<?php echo $value1->getId();?>,'u',this)" <?php if( @$rights[$value1->getId()]['u'] ){ ?>checked="checked"<?php } ?> /></td>
		    <td><input type="checkbox" onclick="setRankAccess(<?php echo $rank->getId();?>,<?php echo $value1->getId();?>,'d',this)" <?php if( @$rights[$value1->getId()]['d'] ){ ?>checked="checked"<?php } ?> /></td>
		    <td>
		    	<a class="btn btn-danger" href="action.php?action=access_delete_right&id=<?php echo $value1->getId();?>"><i class="icon-remove icon-white"></i></a>
		    </td>
	    </tr>
	    <?php } ?>

	    </table>
		</div>
	<?php } ?>



	<!-- SECTION PLUGIN -->
	<?php if( @$_['section']=='plugin' ){ ?>

		<div class="span9 pluginBloc">
			<h1>Plugins</h1>
			<p>Voici la liste des plugins installés :</p> 
			<ul class="pluginList">
				<?php if( count($plugins)==0 ){ ?>

			    	Aucun plugin n'est installé pour le moment.
			    <?php }else{ ?>

			         <?php $counter1=-1; if( isset($plugins) && is_array($plugins) && sizeof($plugins) ) foreach( $plugins as $key1 => $value1 ){ $counter1++; ?>

			                   	<?php if( $value1->getType()!='component' ){ ?>

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

			    <?php } ?>

			</ul>
		</div>
	<?php } ?>


	<!-- SECTION USER -->
	<?php if( @$_['section']=='user' ){ ?>

		<div class="span9 userBloc">
		<h1>Utilisateurs</h1>
		<p>Gestion des utilisateurs du programme</p>  
		<form action="action.php?action=user_add_user" method="POST">
		<fieldset>
		    <legend>Ajout d'un utilisateur</legend>
		    <div class="left">
			    <label for="loginUser">Identifiant</label>
			    <input type="text" id="loginUser" name="loginUser"  placeholder="Identifiant utilisateur…"/>
			    <label for="passwordUser">Mot de passe</label>
			    <input type="password" name="passwordUser" id="passwordUser" />

			</div>
		    
		    <div class="left marginLeftMedium">
			    <label for="nameUser">Nom</label>
			    <input type="text" id="nameUser" name="nameUser"  placeholder="Nom"/>
			    <label for="firstNameUser">Prenom</label>
			    <input type="text" id="firstNameUser" name="firstNameUser"  placeholder="Prenom"/>
  			</div>

  			 <div class="left marginLeftMedium">
  			 	<label for="mailUser">E-mail</label>
			    <input type="text" id="mailUser" name="mailUser"  placeholder="Email"/>
			    <label for="rankUser">Rang</label>
			    <select type="text" id="rankUser" name="rankUser">
			    	<?php $counter1=-1; if( isset($ranks) && is_array($ranks) && sizeof($ranks) ) foreach( $ranks as $key1 => $value1 ){ $counter1++; ?>

			    	<option value="<?php echo $value1->getId();?>"><?php echo $value1->getLabel();?></option>
			    	<?php } ?>

			    </select>
  			</div>

  			<div class="clear"></div>
		    <br/><button type="submit" class="btn">Ajouter</button>
	  	</fieldset>
		<br/>
	</form>
		<table class="table table-striped table-bordered table-hover">
	    <thead>
	    <tr>
	    	<th>Utilisateur</th>
		    <th>Email</th>
		    <th>Rang</th>
		    <td></td>
	    </tr>
	    </thead>
	    
	    <?php $counter1=-1; if( isset($users) && is_array($users) && sizeof($users) ) foreach( $users as $key1 => $value1 ){ $counter1++; ?>

	    <tr>
	    	<td style="width:100px"><?php echo Functions::truncate($value1->getFullName(),20); ?></td>
		    <td><a href="mailto:<?php echo $value1->getMail();?>"><?php echo $value1->getMail();?></a></td>
		    <td><?php echo $ranksLabel[$value1->getRank()];?></td>
		    <td>
		    	<?php if( $value1->getId()!='' && $myUser->can('user','d') ){ ?>

					<a href="action.php?action=delete_user&amp;id=<?php echo $value1->getId();?>" class="btn btn-danger"><i class="icon-remove icon-white"></i></a>
		    	<?php } ?>

		    </td>
	    </tr>
	    <?php } ?>

	    </table>
		</div>
	<?php } ?>


	<?php echo Plugin::callHook("setting_bloc", array()); ?>

	</div>
</div>
<?php $tpl = new RainTPL;$tpl->assign( $this->var );$tpl->draw( "footer" );?>

   