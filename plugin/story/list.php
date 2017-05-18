<?php
$nerve = __ROOT__.'nerve';
?>
<div class="row">
	<div class="col-md-12">

	<?php
		$story = new Story();
		$stories = $story->populate();
		
		?>
		<h3>Gestion des scénarios</h3>
		<form action="action.php?action=plugin_story_import" class="form-inline" method="POST" enctype="multipart/form-data">
			<div class="row">
				<div class="col-md-3">
					<a class="btn btn-primary" href="index.php?module=story&action=edit"><i class="fa fa-plus"></i> Ajouter un scenario</a> 
				</div>
				<div class="col-md-9">
					Importer un scénario existant <input type="file" class="form-control" onchange="$(this).parent().submit()" name="import">
				</div>
			</div>
		</form>

		<hr/>

		<div class="panel panel-default">
              <div class="panel-heading">Scénarios existants</div>
              <table id="users" class="table">
                <thead>
                  <tr>
                    <th colspan="5">Titre du scénario</th>
                  </tr>
                </thead>
                <tbody>
                <?php if(count($stories)==0): ?>
                	<tr><td colspan="5">Aucun scénario créé</td></tr>
                <?php endif; ?>
                <?php foreach($stories as $story): ?>
					<tr data-id="<?php echo $story->id ?>">
					<td>
						<a style="display:block;" href="index.php?module=story&action=edit&story=<?php echo $story->id ?>"><?php echo $story->label;?></a>
					</td>
					<td class="story_loader" class="pointer" title="Executer manuellement le scénario" onclick="story_launch(<?php echo $story->id; ?>,this);"><i class="fa"> <span>Chargement...</span></td>
					<td style="width:15px;" class="pointer" title="Voir le dernier log executé" onclick="story_log('<?php echo $story->id; ?>')"><i class="fa fa-align-justify"></i></td>
					<td style="width:15px;" class="pointer" title="Activer/Désactiver" onclick="story_change_state('<?php echo $story->id; ?>',this)">
						<i class="fa <?php echo ($story->state?'fa-check-square-o':'fa-square-o'); ?>"></i>
					</td>
					<td style="width:15px;" class="pointer" title="Exporter" onclick="window.location='action.php?action=plugin_story_export&id=<?php echo $story->id ?>"><i class="fa fa-external-link"></i></td>
					<td style="width:15px;" class="pointer" onclick="story_delete('<?php echo $story->id ?>',this)"><i class="fa fa-times"></i></td>
					</tr>
					<tr style="display:none" data-log="<?php echo $story->id ?>"><td colspan="3"><pre><?php echo $story->log ?></pre></td></tr>
				<?php endforeach; ?>

                </tbody>
              </table>
            </div>





	<div class="alert alert-warning">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<strong>Important!</strong> Consultez <a href="index.php?module=story&page=doc#doc.install">la rubrique installation</a> avant de créer des scénarios, certaines manipulations sont obligatoires
		pour le bon fonctionnement du plugin.
	</div>

	<div class="alert alert-info">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<strong>Documentation</strong> Un soucis? une question ? Voir la <a href="index.php?module=story&action=documentation">documentation</a>
	</div>

	<?php 

	if(!file_exists($nerve)):
		?>
	<div class="alert alert-error">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<strong>Attention!</strong> Le fichier <?php echo $nerve; ?> doit être présent et lancé pour que les causes types 'gpio' fonctionnent.
	</div>
<?php endif; ?>


<hr/>

</div>
</div>
