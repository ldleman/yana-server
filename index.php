<?php require_once __DIR__.DIRECTORY_SEPARATOR.'header.php'; ?>

<?php 

Plugin::callHook('page'); 

?>
<!-- Modal -->
<div id="editSketch" class="modal fade" role="dialog" data-action="create_sketch">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Edition Sketch</h4>
			</div>
			<div class="modal-body">
				<label for="label">Comment vas t-on appeller ?a ? :D</label>
				<input class="form-control" type="text" id="label"/>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" onclick="create_sketch();" data-dismiss="modal">Enregistrer</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
		
	</div>
</div>


<?php require_once __ROOT__.'footer.php' ?>
