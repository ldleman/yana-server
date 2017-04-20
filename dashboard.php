<?php global $myUser; ?>
<div class="container-fluid">
	<?php if($myUser->connected()): ?>
	<ul id="dashboardView">
	<?php foreach(Dashboard::loadAll() as $dashboard): ?>
		<li <?php echo $dashboard->default?'data-selected="1" class="active"':''; ?> data-id="<?php echo $dashboard->id; ?>"><i class="fa <?php echo $dashboard->icon; ?>"></i> <?php echo $dashboard->label; ?></li>
	<?php endforeach; ?>
		<li class="right"><div data-toggle="modal" data-target="#addWidgetModal" title="Ajouter un widget"><i class="fa fa-plus-square-o"></i></div></li>
	</ul>
	<?php endif; ?>

	
	<!-- Add wiget modal -->
	<div class="modal fade" id="addWidgetModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel">Ajout d'un widget</h4>
		  </div>
		  <div class="modal-body">
			<label for="widgetList">Sélectionnez le widget</label>
			<select id="widgetList">
			<?php $models = array();
			Plugin::callHook('widget',array(&$models));
			foreach($models as $model): 
			
			
			
			?>
				<option value="<?php echo $model->model; ?>"><?php echo $model->title; ?></option>
			<?php endforeach; ?>
			</select>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
			<button type="button" class="btn btn-primary" onclick="addWidget();">Ajouter</button>
		  </div>
		</div>
	  </div>
	</div>

	<div class="row" id="dashboard">
		<!-- MODEL WIDGET -->
		<div class="col-md-4 widget" data-id="" style="display:none" draggable="true" ondragstart="dragStart(event)" onmousedown="mouseDown(event)">
			<div class="widget_dropper" data-side="left" ondrop="drop(event)" ondragover="dragOver(event)" ondragleave="dragOut(event);"></div>
			<div class="widget_window">
				<div class="widget_header">
					<i class="fa fa-caret"></i> <span></span>
					<ul class="widget_options"></ul>
				</div>
				<div class="widget_content"></div>
				<div class="widget_footer"></div>
			</div>
			<div class="widget_dropper"  data-side="right" ondrop="drop(event)" ondragover="dragOver(event)" ondragleave="dragOut(event);"></div>
		</div>

	</div>
	
</div> 