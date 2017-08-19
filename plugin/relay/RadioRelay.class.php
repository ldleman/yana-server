<?php
require_once(__DIR__.SLASH.'Relay.class.php');
/*
 @nom: WireRelay
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Classe de gestion des relais radio (prises, interrupteurs, coupe circuits ...)
 */

 class RadioRelay extends Relay{



	public static function settings(){
		?>
		<br/>
		<div class="col-md-12 form-inline">
			
			<label>Pin du récepteur radio:&nbsp;</label><input id="pin" class="form-control" placeholder="2" value="" type="text">
			<br/>
			<label>Code télécommande:</label> <input id="sender" class="form-control" placeholder="max. 67108864" value="" type="text">
			<br/>
			<label>Code relais:</label> <input id="code" class="form-control" placeholder="max. 16" value="" type="text">
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

		$descriptor .= 'Radio Pin : '.$meta['pin'].($meta['pulse']!=0?' Impulsion : '.$meta['pulse'].'µs':'');
		$descriptor .= '<br/> Code : '.$meta['sender'].' '.$meta['code'];
		

		?>
		<h2><?php echo $type['label']; ?></h2>
		<h1><?php echo $descriptor; ?></h1>
		<?php
	}

	public static function stateChange($relay,$state,&$response){
		$response['message'] = 'ok';
		$meta = json_decode($relay->meta,true);
		$cmd = dirname(__FILE__).'/radioEmission '.$meta['pin'].' '.$meta['sender'].' '.$meta['code'].' ';
		$cmd .= $meta['pulse'] ==0 ? ($state == 1 ? 'on' : 'off') : 'pulse '.$meta['pulse'];
		System::commandSilent($cmd);
	}

}

?>