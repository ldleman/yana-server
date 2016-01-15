<?php
	$story = new Story();
	$stories = $story->populate();
?>

	<div class="span12">

		<h1>Gestion des scénarios</h1>
		<a class="btn" href="index.php?module=story&action=edit">Ajouter un scenario</a>
		
		<h2>Scénarios existants</h2>
	    <table class="table table-striped table-bordered table-hover">
	    <thead>
	    <tr>
	    <th colspan="2">Titre</th>
	    </tr>
		
	    </thead>
		<?php 
			foreach($stories as $story){
				echo '<tr><td><a style="display:block;" href="index.php?module=story&action=edit&story='.$story->id.'">'.$story->label.'</a></td><td style="width:15px;" class="pointer" onclick="story_delete(\''.$story->id.'\',this)"><i class="fa fa-times"></i></td></tr>';
			}
		?>
	    </table>
		
		
		<strong>Important:</strong> Pour profiter pleinement de ce plugin, vous devez ajouter (si ce n'est pas déja fait) une tâche planifiée sur le raspberry PI.<br/>Pour cela tapez : <br/><code>sudo crontab -e</code> <br/>Puis ajoutez la ligne <br/><code>*/1 * * * * wget http://localhost/yana-server/action.php?action=crontab -O /dev/null 2>&1</code><br/>Puis sauvegardez (ctrl+x puis O puis Entrée)<br/><br/><br/>
		
	</div>
