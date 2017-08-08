<?php 
global $myUser;
if(!$myUser->can('dashboard','configure')) throw new Exception("Permissions insuffisantes");
?>
<div class="row">
	<div class="col-md-12"> 
		<h3>Dashboard</h3>

		<div id="dashboardForm" class="row" data-action="save_dashboard">
			<div class="col-md-12">
				<label for="label">Nom de la page de dashboard</label>
				<input id="label" class="form-control" placeholder="Salon,Cuisine..." type="text">
				<label for="icon">Iconela page de dashboard</label>
				<input type="hidden" id="icon" class="form-control" >
				<div class="iconSet">
					<?php foreach(availableIcon() as $icon=>$code):?>
						<i data-value="<?php echo $icon; ?>" class="fa <?php echo $icon; ?>"></i>
					<?php endforeach; ?>
				</div>
			</div>
			
			<div class="col-md-12"><br/>
				<div onclick="save_dashboard();" class="btn btn-info"><i class="fa fa-check"></i> Enregistrer</div>
			</div>
		</div>
		<br/>
		<div class="panel panel-default">
			<div class="panel-heading">Page de dashboard existantes</div>
			<table id="dashboards" class="table">
				<thead>
					<tr>
						<th>#</th>
						<th>Nom</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<tr data-id="{{id}}" class="noDisplay">
						<td>{{id}}</td>
						<td><i class="fa {{icon}}"></i> {{label}}</td>
						<td>
							<div onclick="edit_dashboard(this)" class="btn btn-info btn-mini"><i class="fa fa-pencil"></i></div>
							<div onclick="delete_dashboard(this)" class="btn btn-danger btn-mini"><i class="fa fa-times"></i></div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

