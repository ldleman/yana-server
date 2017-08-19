<?php
require_once(__DIR__.SLASH.'Relay.class.php');
/*
 @nom: WireRelay
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Classe de gestion des relais filaires (prises, interrupteurs, coupe circuits ...)
 */

 class WireRelay extends Relay{
	


	public static function settings(){
		?>
		<br/>
		<div class="col-md-12 form-inline">
			
			<label>Appuis sur ON:&nbsp;</label> Mettre le PIN <input id="pinOn" class="form-control input-mini" placeholder="1" value="" type="text"> à 
			<select id="stateOn" class="form-control  input-mini">
				<option>0</option>
				<option>1</option>
			</select>
			OU executer la commande <input id="onCommand" class="form-control" placeholder="laisser vide si aucune commande" value="" type="text">
			<br/>
			<label>Appuis sur OFF:</label> Mettre le PIN <input id="pinOff" class="form-control input-mini" placeholder="1" value="" type="text"> à 
			<select id="stateOff" class="form-control  input-mini">
				<option>0</option>
				<option>1</option>
			</select>
			OU executer la commande <input id="offCommand" class="form-control" placeholder="laisser vide si aucune commande" value="" type="text">
			<br/>
			<i class="help">Certains appareils (ex: stores) ne nécessite qu'une brève impulsion pour changer d'état, si vous êtes dans ce cas, vous pouvez spécifier le temps d'impulsion souhaitée ci dessous :</i><br>
			<label for="pulse">Pulsations (laisser à 0 si aucune)</label>
			<input id="pulse" class="form-control input-mini" placeholder="150" value="0" type="text"> µs (micro secondes)
		</div>
		<?php
	}

	public static function widget($relay){
		$meta = json_decode($relay->meta,true);
		$descriptor = '';
		$type = Relay::types($relay->type);

		if(isset($meta['pinOn']) && !empty($meta['pinOn'])){
			$descriptor .= 'PIN '.$meta['pinOn'].($meta['pinOn']!=$meta['pinOff']?' et '.$meta['pinOff']:'').($meta['pulse']!=0?' - Impulsion '.$meta['pulse'].'µs':'');
		}
		else if(isset($meta['onCommand'])){
			$descriptor .= 'Commande système custom';
		}

		?>
		<h2><?php echo $type['label']; ?></h2>
		<h1><?php echo $descriptor; ?></h1>
		<?php
	}

	public static function stateChange($relay,$state,&$response){
		$response['message'] = 'ok';
		$meta = json_decode($relay->meta,true);

		//ON
		if($state == 1){
			// PINMODE
			if(!empty($meta['pinOn'])){
				Gpio::mode($meta['pinOn'],'out');
				if($meta['pulse']==0){
					Gpio::write($meta['pinOn'],$meta['stateOn']);
				}else{
					Gpio::pulse($meta['pinOn'],$meta['pulse'],$meta['stateOn']);
				}
			//COMMANDE MODE
			}else if(!empty($meta['onCommand'])){
				System::commandSilent($meta['onCommand']);
			}
		//OFF
		}else{
			// PINMODE
			if(!empty($meta['pinOff'])){
				Gpio::mode($meta['pinOff'],'out');
				if($meta['pulse']==0){
					Gpio::write($meta['pinOff'],$meta['stateOff']);
				}else{
					Gpio::pulse($meta['pinOff'],$meta['pulse'],$meta['stateOff']);
				}
			//COMMANDE MODE
			}else if(!empty($meta['offCommand'])){
				System::commandSilent($meta['offCommand']);
			}
		}

	}

}

?>