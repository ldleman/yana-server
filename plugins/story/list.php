<?php
	$story = new Story();
	$stories = $story->populate();
?>

	<div class="span12">

		<h1>Gestion des scénarios</h1>
		<a class="btn" href="index.php?module=story&action=edit">Ajouter un scenario</a>
		
		<h2>Scénarios existants</h2>
		<form action="action.php?action=plugin_story_import" method="POST" enctype="multipart/form-data">
		Importer un scénario <input type="file" onchange="$(this).parent().submit()" name="import">
		</form>
	    <table class="table table-striped table-bordered table-hover">
	    <thead>
	    <tr>
	    <th colspan="2">Titre</th>
	    </tr>
		
	    </thead>
		<?php 
			foreach($stories as $story){
				echo '<tr data-id="'.$story->id.'">
						<td><a style="display:block;" href="index.php?module=story&action=edit&story='.$story->id.'">'.$story->label.'</a></td>
						<td class="story_loader" class="pointer" title="Executer manuellement le scénario" onclick="story_launch('.$story->id.',this);"><i class="fa"> <span>Chargement...</span></td>
						<td style="width:15px;" class="pointer" title="Voir le dernier log executé" onclick="story_log(\''.$story->id.'\')"><i class="fa fa-align-justify"></i></td>
						<td style="width:15px;" class="pointer" title="Activer/Désactiver" onclick="story_change_state(\''.$story->id.'\',this)"><i class="fa '.($story->state?'fa-check-square-o':'fa-square-o').'"></i></td>
						<td style="width:15px;" class="pointer" title="Exporter" onclick="window.location=\'action.php?action=plugin_story_export&id='.$story->id.'\'"><i class="fa fa-external-link"></i></td>
						<td style="width:15px;" class="pointer" onclick="story_delete(\''.$story->id.'\',this)"><i class="fa fa-times"></i></td>
					</tr>';
				echo '<tr style="display:none" data-log="'.$story->id.'"><td colspan="3"><pre>'.$story->log.'</pre></td></tr>';
			}
		?>
	    </table>
		
		
		<strong>Important:</strong> Pour profiter pleinement de ce plugin, vous devez ajouter (si ce n'est pas déja fait) une tâche planifiée sur le raspberry PI.<br/>Pour cela tapez : 

		<br/><code>sudo crontab -e</code> 
		<br/>Puis ajoutez la ligne <br/><code>*/1 * * * * wget http://localhost/yana-server/action.php?action=crontab -O /dev/null 2>&1</code><br/>
		<br/>Puis ajoutez la ligne <br/><code>@REBOOT ./nerve /var/www/yana-server/action.php -O /dev/null 2>&1</code><br/>Puis sauvegardez (ctrl+x puis O puis Entrée)<br/>
		<br/>Enfin executez la commande<br/><code>sudo chmod +x /var/www/yana-server/nerve</code><br/><br/>
	</div>
