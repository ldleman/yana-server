<?php

global $_,$myUser,$conf;

require_once('Data.class.php');
require_once('Sensor.class.php');



$sensors = Sensor::loadAll();
$rooms = Room::loadAll(array('state'=>'0'));
$selected = isset($_['id']) ? Sensor::getById($_['id']) : new Sensor();
			



?>

<div class="span9 userBloc">

	<h1>Sondes Propise</h1>
	<p>Gestion des multi-sondes</p>  

	
	    <h4>Ajouter/Modifier une sonde</h4>
		    <label for="label">Nom</label>
		    <input type="text"  class="form-control" id="label" value="<?php echo $selected->label; ?>" placeholder="Sonde du salon"/>
		    <label for="location">Pièce de la maison</label>
		    <select id="location" class="form-control">
		    	<?php foreach($rooms as $room): ?>
		    	<option <?php echo $selected->location == $room->id ? 'selected="selected"':''; ?> value="<?php echo $room->id; ?>"><?php echo $room->label; ?></option>
		    	<?php endforeach; ?>
		    </select>
		   
	    <br/><button onclick="plugin_propise_save(this)" class="btn">Enregistrer</button>
 
	    <hr/>



		<h4>Consulter les sondes existants</h4>
		<table class="table table-striped table-bordered table-hover">
		    <thead>
			    <tr>
			    	<th>Nom</th>
				    <th>UID</th>
				    <th>Pièce</th>
					<th>A copier dans la sonde</th>
				    <th colspan="2"></th>
				    
			    </tr>
		    </thead>
	    
	    	<?php foreach($sensors as $sensor): 
	    		$room = Room::getById($sensor->location); 
	    	?>
			<tr>
		    	<td><?php echo $sensor->label; ?></td>
			    <td><?php echo $sensor->uid; ?></td>
			     <td><?php echo $room->label; ?></td>
			    <td>
			    	<a onclick="$(this).next().toggle()" class="btn">Guide Installation</a>
			    	<ul style="display:none;">
			    		<li>Démarrer la sonde en appuyant sur le boutton jusqu'a ce que la lumière s'allume en bleu</li>
			    		<li>Se connecter au wifi de la sonde (PROPISE-XX) avec le mot de passe bananeflambee</li>
			    		<li>Ouvrir l'interface de la sonde sur <a href="http://192.168.4.1">http://192.168.4.1</a></li>
			    		<li>Remplir les identifiant WIFI de votre réseau</li>
			    		<li>Dans le dernier champs, mettre le lien suivant :
			    <?php 
			    $url = YANA_URL.'/action.php?action=propise_add_data&id='.$sensor->id.'&light={{LIGHT}}&humidity={{HUMIDITY}}&temperature={{TEMPERATURE}}&mouvment={{MOUVMENT}}';
			    echo '<a href="'.$url.'">'.$url.'</a>'; ?>
					</li>
					</ul>
			   	</td>
			    <td>
			    	<a class="btn" href="setting.php?section=propise&id=<?php echo $sensor->id; ?>"><i class="fa fa-pencil"></i></a>
			    	<div class="btn" onclick="plugin_propise_delete(<?php echo $sensor->id; ?>,this);"><i class="fa fa-times"></i></div>
			    </td>
			    </td>
	    	</tr>
	    <?php endforeach; ?>
	    </table>
	</fieldset>
</div>