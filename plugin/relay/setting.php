<?php

global $_,$myUser,$conf;

require_once('Relay.class.php');
require_once('WireRelay.class.php');

$sensors = WireRelay::loadAll();
$rooms = Room::loadAll(array('state'=>'0'));

?>
	<div class="row">
		<div class="col-md-12">
			<h3>Relais</h3>

			<div id="relayForm" data-action="relay_save">
				
				<div class="row">
					<div class="col-md-12 form-inline">
						<label for="type">Type</label>
						<select id="type" class="form-control">
				    		<option value="wire">Filaire</option>
				    		<option value="radio">Radio</option>
						</select>
						<label for="label">Nom</label>
						<input id="label" class="form-control" placeholder="Salon,relais 1..." type="text">&nbsp;
						<label for="description">Description</label>
						<input id="description" class="form-control" placeholder="" type="text">&nbsp;
					</div>
				</div>
				<hr/>
				<div class="row">
					<div class="col-md-12">

						<label for="icon">Icone</label>
						<input id="icon" type="hidden">
						<div class="iconSet" style="font-size: 30px;">
						<?php foreach(Relay::availableIcon() as $icon=>$code):?>
							<i data-value="<?php echo $icon; ?>" class="fa <?php echo $icon; ?>"></i>
						<?php endforeach; ?>
						</div>
						<hr/>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 form-inline">
						<label for="location">Pièce de la maison</label>
			    		<select id="location" class="form-control">
			    		<?php foreach($rooms as $room): ?>
			    			<option value="<?php echo $room->id; ?>"><?php echo $room->label; ?></option>
			    		<?php endforeach; ?>
						</select>&nbsp;
					
						<div onclick="relay_save();" class="btn btn-info"><i class="fa fa-check"></i> Enregistrer</div>
				
					</div>
				</div>
			</div>
			<br/>
			<div class="panel panel-default">
	              <div class="panel-heading">Relais existantes</div>
	              <table id="rooms" class="table">
	                <thead>
	                  <tr>
	                    <th>#</th>
	                    <th>Nom</th>
	                    <th>Type</th>
	                    <th>Pièce</th>
	                    <th>Description</th>
	                    <th></th>
	                  </tr>
	                </thead>
	                <tbody>
	                  <tr data-id="{{id}}" class="noDisplay">
	                    <td>{{id}}</td>
	                    
	                    <td><i class="fa {{icon}}"></i> {{label}}</td>
	                    <td>{{type}}</td>
	                    <td>{{location.label}}</td>
	                    <td>{{description}}</td>
	                    <td>
	                    	<div onclick="relay_edit(this)" class="btn btn-info btn-mini"><i class="fa fa-pencil"></i></div>
	                    	<div onclick="relay_delete(this)" class="btn btn-danger btn-mini"><i class="fa fa-times"></i></div>
	                    </td>
	                  </tr>
	                </tbody>
	              </table>
	            </div>
		</div>
	</div>