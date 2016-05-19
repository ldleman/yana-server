<?php
	$story = new Story();
	$stories = $story->populate();
?>

	<div class="span12">

		<h1>Gestion des scénarios</h1>
		<form action="action.php?action=plugin_story_import" method="POST" enctype="multipart/form-data">
		<a class="btn" href="index.php?module=story&action=edit">Ajouter un scenario</a> OU Importer un scénario <input type="file" class="btn" onchange="$(this).parent().submit()" name="import">
		</form>
		
		<h2>Scénarios existants</h2>
		
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
		
		<div class="alert">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>Important!</strong> Consultez <a href="#doc.install">la rubrique installation</a> avant de créer des scénarios, certaines manipulations sont obligatoires
			pour le bon fonctionnement du plugin.
		</div>
		
		<?php 
			$nerve = dirname(dirname(__DIR__)).'\nerve';
			if(!file_exists($nerve)):
		?>
		<div class="alert alert-error">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>Attention!</strong> Le fichier <?php echo $nerve; ?> doit être présent et lancé pour que les causes types 'gpio' fonctionnent.
		</div>
		<?php endif; ?>
		
		<hr/>
		<h1>Documentation</h1>
		<h3 id="doc.install">Installation</h3>
		Pour profiter pleinement de ce plugin, vous devez ajouter (si ce n'est pas déja fait) une tâche planifiée sur le raspberry PI.<br/>Pour cela tapez : 
		<br/><code>sudo crontab -e</code> 
		<br/>Puis ajoutez la ligne <br/><code>*/1 * * * * wget http://localhost/yana-server/action.php?action=crontab -O /dev/null 2>&1</code><br/>
		<br/>Puis ajoutez la ligne <br/><code>@reboot <?php echo $nerve; ?> /var/www/yana-server/action.php -O /dev/null 2>&1</code><br/>Puis sauvegardez (ctrl+x puis O puis Entrée)<br/>
		<br/>Executez la commande<br/><code>sudo chmod +x <?php echo $nerve; ?></code>
		<br/><br/>
		
		<h3>Variables</h3>
		
		Des variables peuvent être définies, testées ou consultées dans les scénarios.<br/>
		Les points suivants sont à noter
		<ul>
			<li>Pour définir une variable et sa valeur, il faut créer un effet "variable"</li>
			<li>Pour utiliser une variable existante en tant que cause il faut créer une cause "variable" en reprenant le nom de la variable créée</li>
			<li>Pour utiliser la valeur d'une variable dans un autre effet (liste de commande, url etc...) vous pouvez placer la variable entre accolades.<small> ex : pour utiliser une variable <code>toto</code> dans une ligne de commande, créez un effet commande et placez dans le texte <code>ma-commande {toto}</code> </small></li>
			<li>Les effet de type <code>commande</code> envoient automatiquement leurs résultat de sortie dans la variable <code>cmd_result</code></li>
			<li>Les effet de type <code>url</code> envoient automatiquement leurs résultat de requette dans la variable <code>url_result</code></li>
			<li>Certaines variables "communes" sont définies par défaut (voir ci dessous)</li>
		</ul>
		
		Les variables par défaut sont les suivantes
		<ul>
		<?php foreach(Story::keywords() as $key=>$value): ?>
		<li><code><?php echo $key; ?></code> : <?php echo $value; ?></li>
		<?php endforeach; ?>
		</ul>
	</div>
