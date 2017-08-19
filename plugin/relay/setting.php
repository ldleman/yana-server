<?php

global $_,$myUser,$conf;

require_once('Relay.class.php');

$rooms = Room::loadAll(array('state'=>'0'));
$types = Relay::types();

?>
	<div class="row">
		<div class="col-md-12">
			<h3>Relais</h3>

			<div id="relayForm" data-action="relay_save">
				
				<div class="row">
					<div class="col-md-12 form-inline">
						<label for="type">Type</label>
						<select id="type" class="form-control" onchange="relay_change_type(this)">
						<option value=""> - </option>
						<?php foreach ($types as $uid => $type): ?>
							<option value="<?php echo $uid  ?>"><?php echo $type['label'];  ?></option>
				    	<?php endforeach; ?>
						</select>
						<label for="label">Nom</label>
						<input id="label" class="form-control" placeholder="Salon,relais 1..." type="text">&nbsp;
						<label for="description">Description</label>
						<input id="description" class="form-control" placeholder="" type="text">&nbsp;
					</div>
				</div>
				<!-- Les formulaires custom de types de relais sont affichés ici -->
				<div class="row" id="type_form"></div>
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
	                    <td>{{type.label}}</td>
	                    <td>{{location.label}}</td>
	                    <td>{{description}}
	                    <ul>
	                    {{#meta}}
	                    	<li>{{key}} : <code>{{value}}</code></li>
	                    {{/meta}}
	                    </ul>
	                    </td>
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