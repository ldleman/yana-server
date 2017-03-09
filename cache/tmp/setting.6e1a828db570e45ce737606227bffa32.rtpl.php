<?php if(!class_exists('raintpl')){exit;}?><?php $tpl = new RainTPL;$tpl->assign( $this->var );$tpl->draw( "header" );?>


<div class="row">
	<div class="span3 bs-docs-sidebar">
	  <ul class="nav nav-tabs nav-stacked">
	  	<li <?php if( isset($_['section']) && $_['section']=='plugin' ){ ?>class="active"<?php } ?>><a href="setting.php?section=plugin"><i class="fa fa-angle-right"></i> Plugins</a></li>
	  	<li <?php if( isset($_['section']) && $_['section']=='user' ){ ?>class="active"<?php } ?>><a href="setting.php?section=user"><i class="fa fa-angle-right"></i> Utilisateurs</a></li>
	  	<li <?php if( isset($_['section']) && $_['section']=='access' ){ ?>class="active"<?php } ?>><a href="setting.php?section=access"><i class="fa fa-angle-right"></i> Accès</a></li>
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
				    <legend><?php echo $description;?></legend>

					    <label for="labelRank">Libellé</label>
					    <?php if( isset($id_modrank) ){ ?><input type="hidden" name="id" value="<?php echo $id_modrank;?>"><?php } ?>
					    <input <?php if( isset($label_rank) ){ ?>value="<?php echo $label_rank;?>"<?php } ?> type="text" id="labelRank" name="labelRank" placeholder="Libellé du rang…"/>
					    <label for="descriptionRank">Description</label>
					    <textarea name="descriptionRank" id="descriptionRank" placeholder="Description courte du rang…" ><?php if( isset($description_rank) ){ ?><?php echo $description_rank;?><?php } ?></textarea>
		  				<div class="clear"></div>
				    	<br/><button type="submit" class="btn"><?php echo $button;?></button>
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
				    <td><a class="btn btn-warning" href="setting.php?section=right&id=<?php echo $value1->getId();?>"><i class="icon-white fa fa-check-circle-o"></i></a>
				    	<a class="btn btn-danger" href="setting.php?section=access&id_rank=<?php echo $value1->getId();?>"><i class="fa fa-pencil icon-white"></i></a>
				    	<a class="btn btn-danger" href="action.php?action=access_delete_rank&id=<?php echo $value1->getId();?>"><i class="fa fa-times icon-white"></i></a></td>
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
		    <th>Consultation</th>
		    <th>Modification</th>  
		    <th>Suppression</th>
		    
		    
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
		    	<a class="btn btn-danger" href="action.php?action=access_delete_right&id=<?php echo $value1->getId();?>"><i class="fa fa-times icon-white"></i></a>
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
			<ul class="nav nav-tabs">
					<li  <?php if( @$_['block']=='actif' || !isset($_['block']) ){ ?>class="active"<?php } ?>><a  href="setting.php?section=plugin&block=actif"><i class="fa fa-angle-right"></i> Actif</a></li>
					<li <?php if( @$_['block']=='inactif' ){ ?>class="active"<?php } ?>><a  href="setting.php?section=plugin&block=inactif"><i class="fa fa-angle-right"></i> Inactif</a></li>
					<li <?php if( @$_['block']=='market' ){ ?>class="active"<?php } ?>><a  href="setting.php?section=plugin&block=market"><i class="fa fa-angle-right"></i> Market</a></li>
			</ul>
				

			<?php if( @$_['block']=='market' ){ ?>
				<br/><p>Recherchez et ajoutez les plugins proposés par la communauté<br/>en utilisant le formulaire ci dessous ! (<a href="http://market.idleman.fr/index.php?software=yana-server&amp;page=submit">Proposer un nouveau plugin</a>)</p>
				<form class="form-inline">
				<input type="text" id="keywordPlugin"> <button class="btn" id="btnSearchPlugin" onclick="searchPlugin($('#keywordPlugin').val());return false;">Rechercher</button>
				</form>
				<ul id="resultsPlugin"></ul>
				<br/><br/>
				
			<?php }else{ ?>
				<ul class="pluginList">
					<?php if( count($plugins)==0 ){ ?>
						Aucun plugin n'est installé pour le moment.
					<?php }else{ ?>
						 <?php $counter1=-1; if( isset($plugins) && is_array($plugins) && sizeof($plugins) ) foreach( $plugins as $key1 => $value1 ){ $counter1++; ?>
									<?php if( $value1->getType()!='component' && $value1->getState() == (@$_['block']=='inactif' ?0:1) ){ ?>
									<li>
										<ul>
											<li><h4>Nom: </h4><?php echo $value1->getName();?></li>
											<li><h4>Auteur: </h4><a href="mailto:<?php echo $value1->getMail();?>"><?php echo $value1->getAuthor();?></a></li>
											<li><h4>Licence: </h4><?php echo $value1->getLicence();?></li>
											<li><h4>Version: </h4><code><?php echo $value1->getVersion();?></code></li>
											<li><h4>Site web: </h4><a href="<?php echo $value1->getLink();?>"><?php echo $value1->getLink();?></a></li>
											<li><?php echo $value1->getDescription();?></li>
											<li><a href="action.php?action=changePluginState&plugin=<?php echo $value1->getUid();?>&state=<?php echo $value1->getState();?>&amp;block=<?php echo $_['block'];?>" class="button"><?php echo $value1->getState()=="0"?"Activer":"Désactiver";?></a></li>
										</ul>
									</li>
								  <?php } ?>
						<?php } ?>
					<?php } ?>
				</ul>
			<?php } ?>

		</div>
	<?php } ?>

	<!-- SECTION USER -->
	<?php if( @$_['section']=='user' ){ ?>
		<div class="span9 userBloc">
		<h1>Utilisateurs</h1>
		<p>Gestion des utilisateurs du programme</p>  
		<form action="action.php?action=user_add_user" method="POST">
		<fieldset>
		    <legend><?php echo $description;?></legend>
		    <div class="left">
		   		 <?php if( isset($id_modusers) ){ ?><input type="hidden" name="id" value="<?php echo $id_modusers;?>"><?php } ?>
			    <label for="loginUser">Identifiant</label>
			    <input <?php if( isset($login) ){ ?>value="<?php echo $login;?>"<?php } ?> type="text" id="loginUser" name="loginUser"  placeholder="Identifiant utilisateur…"/>
			    <label for="passwordUser">Mot de passe</label>
			    <input type="password" name="passwordUser" id="passwordUser" />

			</div>
		    
		    <div class="left marginLeftMedium">
			    <label for="nameUser">Nom</label>
			    <input <?php if( isset($lastname) ){ ?>value="<?php echo $lastname;?>"<?php } ?> type="text" id="nameUser" name="nameUser"  placeholder="Nom"/>
			    <label for="firstNameUser">Prenom</label>
			    <input <?php if( isset($firstname) ){ ?>value="<?php echo $firstname;?>"<?php } ?> type="text" id="firstNameUser" name="firstNameUser"  placeholder="Prenom"/>
  			</div>

  			 <div class="left marginLeftMedium">
  			 	<label for="mailUser">E-mail</label>
			    <input <?php if( isset($email) ){ ?>value="<?php echo $email;?>"<?php } ?> type="text" id="mailUser" name="mailUser"  placeholder="Email"/>
			    <label for="rankUser">Rang</label>
			    <select type="text" id="rankUser" name="rankUser">
			    	<?php $counter1=-1; if( isset($ranks) && is_array($ranks) && sizeof($ranks) ) foreach( $ranks as $key1 => $value1 ){ $counter1++; ?>
			    	<option <?php if( ($userrank==$value1->getId()) ){ ?>selected<?php } ?> value="<?php echo $value1->getId();?>"><?php echo $value1->getLabel();?></option>
			    	<?php } ?>
			    </select>
  			</div>

  			<div class="clear"></div>
		    <br/><button type="submit" class="btn"><?php echo $button;?></button>
	  	</fieldset>
		<br/>
	</form>
		<table class="table table-striped table-bordered table-hover">
	    <thead>
	    <tr>
	    	<th>Login</th>
	    	<th>Utilisateur</th>
		    <th>Email</th>
		    <th>Rang</th>
		    <td></td>
	    </tr>
	    </thead>
	    
	    <?php $counter1=-1; if( isset($users) && is_array($users) && sizeof($users) ) foreach( $users as $key1 => $value1 ){ $counter1++; ?>
	    <tr>
	    	<td><?php echo $value1->getLogin();?></td>
	    	<td style="width:100px"><?php echo Functions::truncate($value1->getFullName(),20); ?></td>
		    <td><a href="mailto:<?php echo $value1->getMail();?>"><?php echo $value1->getMail();?></a></td>
		    <td><?php echo $ranksLabel[$value1->getRank()];?></td>
		    <td>
		    	<?php if( $value1->getId()!='' && $myUser->can('user','d') ){ ?>
					<a href="action.php?action=delete_user&amp;id=<?php echo $value1->getId();?>" class="btn btn-danger"><i class="fa fa-times icon-white"></i></a>
					<a class="btn btn-danger" href="setting.php?section=user&id_user=<?php echo $value1->getId();?>"><i class="fa fa-pencil icon-white"></i></a></td>
		    	<?php } ?>
		    </td>
	    </tr>
	    <?php } ?>
	    </table>
		</div>
	<?php } ?>


	<!-- SECTION DEBUG --> 
	<?php if( @$_['section']=='debug' ){ ?>

				<div class="span9 accessBloc">

				

				<h1>Débug vocal</h1>
				<p>Test de débugages vocaux, si le client est bien connecté, yana à du monologuer</p>  

				

	<?php } ?>





	<?php echo Plugin::callHook("setting_bloc", array()); ?>
	</div>
</div>
<?php $tpl = new RainTPL;$tpl->assign( $this->var );$tpl->draw( "footer" );?>
   
