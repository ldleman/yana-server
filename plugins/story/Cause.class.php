<?php

/*
 @nom: Cause
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Représente une cause de scénario (combinées à d'autres causes ou seule, conduit à un ou plusieurs effets)
 */

class Cause extends SQLiteEntity{

	public $id,$story,$sort,$type,$values,$operator,$union;
	protected $TABLE_NAME = 'plugin_story_cause';
	protected $CLASS_NAME = 'Cause';
	protected $object_fields = 
	array(
		'id'=>'key',
		'story'=>'int',
		'sort'=>'int',
		'type'=>'string',
		'values'=>'longstring',
		'union'=>'string',
		'operator'=>'string'
	);

	function __construct(){
		parent::__construct();
	}
	
	public static function types(){
		$types = array(
			'time' => array(
					'icon' => 'fa-clock-o',
					'label' => 'Date / Heure',
					'template' => '',
					'description' => 'Déclenche le scénario en fonction du temps sélectionné'
					),
			'listen' => array(
					'icon' => 'fa-microphone',
					'label' => 'Phrase',
					'template' => '<select data-field="operator" class="operator"><option>=</option><!--<option>!=</option>--></select> <input type="text" style="max-width:50%;width:50%;" data-field="value" placeholder="Ma phrase.." value="{value}">',
					'description' => 'Déclenche le scénario en fonction de la phrase prononcée'
					),
			'pin' => array(
					'icon' => 'fa-dot-circle-o',
					'label' => 'GPIO',
					'template' => 'numéro <select data-value="{pin}" data-field="pin"><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option></select> en état <select data-value="{value}" data-field="value"><option value="1">Actif</option><option value="0">Inactif</option></select>',
					'description' => 'Déclenche le scénario en fonction de l\'état du GPIO sélectionné en état actif (1) ou inactif(0)'
					),
			'readvar' => array(
					'icon' => 'fa-dollar',
					'label' => 'Variable',
					'template' => '<input type="text" data-field="var" placeholder="Ma variable" value="{var}"> <select data-field="operator" class="operator"><option value="=">=</option></select> <input type="text" data-field="value" placeholder="Ma valeur" value="{value}">',
					'description' => 'Déclenche le scénario si la variable déclarée existe et correpond à l\'égalité décrite, le test sur les variable est effectué toutes les minutes.'
					)
			/*'captor' => array(
					'icon' => 'fa-tachometer',
					'label' => 'Capteur',
					'template' => '<select  class="plugin_selector"></select> Capteur <select data-field="captor" class="captor_selector"></select> Champ <select data-field="field" class="captor_field_selector"></select> <select data-field="operator" class="operator"><option>=</option><option>!=</option><option><</option><option>></option></select> <input data-field="value" type="text" placeholder="valeur" value="{value}">'
					),
			,*/
		);


			$types['listen']['template'] .= 'Confidence <input type="number" step="0.01" min="0.10"  max="0.99" value="{confidence}"  data-field="confidence"/>';
		
			

			$types['time']['template'] = '<select class="operator"><option>=</option><!--<option>!=</option>--></select> 
					<select data-value="{minut}" data-field="minut">
					<option value="*">Toutes les minutes</option>';
				for($i=0;$i<60;$i++)
					$types['time']['template'] .= '<option  value="'.$i.'">'.$i.'</option>';
				$types['time']['template'] .= '</select> <select data-value="{hour}" data-field="hour"><option value="*">Toutes les heures</option>';
				for($i=0;$i<24;$i++)
					$types['time']['template'] .=  '<option  value="'.$i.'">'.$i.'</option>';
				$types['time']['template'] .= '</select>  <select data-value="{day}" data-field="day"><option value="*">Tous les jours</option>';
				for($i=1;$i<32;$i++)
					$types['time']['template'] .=  '<option  value="'.$i.'">'.$i.'</option>';
				$types['time']['template'] .= '</select> <select data-value="{month}" data-field="month"><option value="*">Toutes les mois</option>';
				for($i=1;$i<13;$i++)
					$types['time']['template'] .=  '<option  value="'.$i.'">'.$i.'</option>';
				$types['time']['template'] .= '</select> <select data-value="{year}"  data-field="year">
				<option value="*">Tous les ans</option>';
				for($i=2000;$i<2200;$i++)
					$types['time']['template'] .=  '<option  value="'.$i.'">'.$i.'</option>';
				$types['time']['template'] .= '</select>';

		return $types;
	}

	function setValues($values){
		foreach($values as $key=>$value){
			$values[$key] = htmlspecialchars_decode(stripslashes($value));
		}
		$this->values = json_encode($values);
	}
	
	function getValues(){
		return json_decode($this->values);
	}
	
}

?>