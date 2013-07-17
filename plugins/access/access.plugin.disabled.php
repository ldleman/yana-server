<?php
/*
@name Acces
@author Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@type component
@description Module de gestion des droits et des rangs utilisateurs
*/



function access_plugin_menu(){
	global $_;
	echo '<li '.(isset($_['section']) && $_['section']=='access'?'class="active"':'').'><a href="index.php?module=setting&section=access"><i class="icon-chevron-right"></i> Acces</a></li>';
}


function access_plugin_page(){
	global $myUser,$_;
	
	if(isset($_['section']) && $_['section']=='access' ){
		if($myUser!=false){
			$rankManager = new Rank();
			$ranks = $rankManager->populate();
	?>

		<div class="span9 accessBloc">

		<?php Plugin::callHook("access_pre_display", array(&$accesss)); ?>

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
	    
	    <?php foreach($ranks as $rank){ ?>
	    <tr>
	    	<td><?php echo $rank->getLabel(); ?></td>
		    <td><?php echo $rank->getDescription(); ?></td>
		    <td><a class="btn" href="index.php?module=setting&section=right&id=<?php echo $rank->getId(); ?>"><i class="icon-ok-circle"></i></a>
		    	<a class="btn btn-danger" href="action.php?action=access_delete_rank&id=<?php echo $rank->getId(); ?>"><i class="icon-remove icon-white"></i></a></td>
	    </tr>
	    <?php } ?>
	    </table>
		</div>

<?php }else{ ?>

		<div id="main" class="wrapper clearfix">
			<article>
					<h3>Vous devez être connecté</h3>
			</article>
		</div>
<?php
		}
	}
}

function access_plugin_right(){
	global $myUser,$_;
	
	if(isset($_['section']) && $_['section']=='right'){
		if($myUser!=false){
			$rightManager = new Right();
			$rights = $rightManager->loadAll(array('rank'=>$_['id']));
			
	?>

		<div class="span9 accessBloc">

		<?php Plugin::callHook("access_pre_display", array(&$accesss)); ?>

		<h1>Rangs</h1>
		<p>Gestion des rangs du programme</p>  

		<form action="action.php?action=access_add_right" method="POST">
		<fieldset>
		    <legend>Ajout d'un rang</legend>
			    <label for="sectionRight">Section</label>
			    <input type="text" id="sectionRight" name="sectionRight" placeholder="Libellé de la section"/>
			    <label for="createRight" class="checkbox"><input type="checkbox" name="createRight" id="createRight"> Ajout</label>
				<label for="updateRight" class="checkbox"><input type="checkbox" name="updateRight" id="updateRight"> Modification</label>
				<label for="deleteRight" class="checkbox"><input type="checkbox" name="deleteRight" id="deleteRight"> Supression</label>
				<label for="readRight" class="checkbox"><input type="checkbox" name="readRight" id="readRight"> Consultation</label>
				<input type="hidden" id="rankRight" name="rankRight" value="<?php echo $_['id']; ?>"/>
  			<div class="clear"></div>
		    <br/><button type="submit" class="btn">Ajouter</button>
	  	</fieldset>
		<br/>
	</form>

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
	    
	    <?php foreach($rights as $right){ ?>
	    <tr>
	    	<td><?php echo $right->getSection(); ?></td>
		    <td><?php echo $right->getCreate(); ?></td>
		    <td><?php echo $right->getUpdate(); ?></td>
		    <td><?php echo $right->getDelete(); ?></td>
		    <td><?php echo $right->getRead(); ?></td>
		    <td>
		    	<a class="btn btn-danger" href="action.php?action=access_delete_right&id=<?php echo $right->getId(); ?>"><i class="icon-remove icon-white"></i></a></td>
	    </tr>
	    <?php } ?>
	    </table>
		</div>

<?php }else{ ?>

		<div id="main" class="wrapper clearfix">
			<article>
					<h3>Vous devez être connecté</h3>
			</article>
		</div>
<?php
		}
	}
}

function access_delete_rank(){
	global $_;
	if($_['action']=='access_add_rank'){
		$rankManager = new Rank();
		$rankManager->delete(array('id'=>$_['id']));
		header('location:index.php?module=setting&section=access');
	}
}
function access_add_rank(){
	global $_;
	if($_['action']=='access_add_rank'){
		$rank = new Rank();
		$rank->setLabel($_['labelRank']);
		$rank->setDescription($_['descriptionRank']);
		$rank->save();
		header('location:index.php?module=setting&section=access');
	}
}


function access_add_right(){
	global $_;
	if($_['action']=='access_add_right'){
		$right = new Right();
		$right->setSection(($_['sectionRight']));
		$right->setCreate(($_['createRight']=='on'?true:false));
		$right->setUpdate(($_['updateRight']=='on'?true:false));
		$right->setRead(($_['readRight']=='on'?true:false));
		$right->setDelete(($_['deleteRight']=='on'?true:false));
		$right->setRank($_['rankRight']);
		$right->save();
		header('location:index.php?module=setting&section=right&id='.$_['rankRight']);
	}
}

function access_delete_right (){
	global $_;
	if($_['action']=='access_delete_right'){
		$rankManager = new Right();
		$rankManager->delete(array('id'=>$_['id']));
		header('location:index.php?module=setting&section=right&id='.$_['rankRight']);
	}
}



Plugin::addHook("setting_menu", "access_plugin_menu");  
Plugin::addHook("setting_bloc", "access_plugin_page"); 
Plugin::addHook("setting_bloc", "access_plugin_right"); 
Plugin::addHook("action_post_case", "access_delete_rank"); 
Plugin::addHook("action_post_case", "access_add_rank"); 
Plugin::addHook("action_post_case", "access_add_right");
Plugin::addHook("action_post_case", "access_delete_right"); 
?>