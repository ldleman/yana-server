<?php

global $_,$myUser,$conf;

require_once('Data.class.php');
require_once('Sensor.class.php');

$sensors = Sensor::loadAll();
$rooms = Room::loadAll(array('state'=>'0'));
$selected = isset($_['id']) ? Sensor::getById($_['id']) : new Sensor();
			
?>
	<div class="row">
		<div class="col-md-12">
			<h3>Sondes</h3>

			<div id="propiseForm" class="row form-inline" data-action="propise_save">
				<div class="col-md-12">
					<label for="label">Nom de la sonde</label>
					<input id="label" class="form-control" placeholder="Salon,sonde 1..." type="text">&nbsp;
					<label for="location">Pièce de la maison</label>
		    		<select id="location" class="form-control">
		    		<?php foreach($rooms as $room): ?>
		    			<option <?php echo $selected->location == $room->id ? 'selected="selected"':''; ?> value="<?php echo $room->id; ?>"><?php echo $room->label; ?></option>
		    		<?php endforeach; ?>
					</select>&nbsp;
				
					<div onclick="propise_save();" class="btn btn-info"><i class="fa fa-check"></i> Enregistrer</div>
				</div>
			</div>
			<br/>
			<div class="panel panel-default">
	              <div class="panel-heading">Sondes existantes</div>
	              <table id="rooms" class="table">
	                <thead>
	                  <tr>
	                    <th>#</th>
	                    <th>Nom</th>
	                    <th>Pièce</th>
	                    <th></th>
	                  </tr>
	                </thead>
	                <tbody>
	                  <tr data-id="{{id}}" class="noDisplay">
	                    <td>{{id}}</td>
	                    <td>{{label}}</td>
	                    <td>{{location.label}}</td>
	                    <td>
	                    	<a onclick="$(this).next().toggle()" class="btn"><i class="fa fa-search-plus"></i> Guide Installation</a>
	                    	<ul style="display:none;">
				    		<li>Démarrer la sonde en appuyant sur le boutton jusqu'a ce que la lumière s'allume en bleu</li>
				    		<li>Se connecter au wifi de la sonde <code>PROPISE-XX</code> avec le mot de passe <code>bananeflambee</code></li>
				    		<li>Ouvrir l'interface de la sonde sur <a href="http://192.168.4.1">http://192.168.4.1</a></li>
				    		<li>Remplir les identifiant WIFI de votre réseau</li>
				    		<li>Dans le dernier champs, mettre le lien suivant :
						    <a href="{{link}}">{{link}}</a></li>
					</ul>
	                    </td>
	                    <td>
	                    	<div onclick="propise_edit(this)" class="btn btn-info btn-mini"><i class="fa fa-pencil"></i></div>
	                    	<div onclick="propise_delete(this)" class="btn btn-danger btn-mini"><i class="fa fa-times"></i></div>
	                    </td>
	                  </tr>
	                </tbody>
	              </table>
	            </div>
		</div>
	</div>