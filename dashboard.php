<div class="container-fluid">
	<div class="row">
		
		<div class="col-md-12 form-inline">
			<ul id="view">
			<?php foreach(Dashboard::loadAll() as $dashboard): ?>
				<li <?php echo $dashboard->default?'data-selected="1"':''; ?> data-id="<?php echo $dashboard->id; ?>"><i class="fa <?php echo $dashboard->icon; ?>"></i> <?php echo $dashboard->label; ?></li>
			<?php endforeach; ?>
			</ul>
			<div class="btn" onclick="add_widget();"><i class="fa fa-check"></i> Ajouter un widget</div>
		
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