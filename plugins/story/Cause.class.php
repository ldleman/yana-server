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
					'template' => ''
					),
			'listen' => array(
					'icon' => 'fa-microphone',
					'label' => 'Phrase',
					'template' => '<select data-field="operator" class="operator"><option>=</option><option>!=</option></select> <input type="text" style="max-width:50%;width:50%;" data-field="value" placeholder="Ma phrase.." value="{value}">'
					),
			'captor' => array(
					'icon' => 'fa-tachometer',
					'label' => 'Capteur',
					'template' => '<select  class="plugin_selector"></select> Capteur <select data-field="captor" class="captor_selector"></select> Champ <select data-field="field" class="captor_field_selector"></select> <select data-field="operator" class="operator"><option>=</option><option>!=</option><option><</option><option>></option></select> <input data-field="value" type="text" placeholder="valeur" value="{value}">'
					),
			'readvar' => array(
					'icon' => 'fa-dollar',
					'label' => 'Variable',
					'template' => '<input type="text" data-field="var" placeholder="Ma variable" value="{var}"> <select data-field="operator" class="operator"><option value="=">=</option><option value="!=">!=</option><option value="<"><</option><option value=">">></option></select> <input type="text" data-field="value" placeholder="Ma valeur" value="{value}">'
					),
		);
		
			$types['time']['template'] = '<select class="operator"><option>=</option><option>!=</option></select> 
					<select style="width:100px;" data-field="minut">
					<option value="*">Toutes les minutes</option>';
				for($i=0;$i<60;$i++)
					$types['time']['template'] .= '<option  value="'.$i.'">'.$i.'</option>';
				$types['time']['template'] .= '</select> <select data-field="hour"><option value="*">Toutes les heures</option>';
				for($i=0;$i<24;$i++)
					$types['time']['template'] .=  '<option  value="'.$i.'">'.$i.'</option>';
				$types['time']['template'] .= '</select>  <select data-field="day"><option value="*">Tous les mois</option>';
				for($i=1;$i<13;$i++)
					$types['time']['template'] .=  '<option  value="'.$i.'">'.$i.'</option>';
				$types['time']['template'] .= '</select> <select data-field="month"><option value="*">Toutes les jours</option>';
				for($i=1;$i<32;$i++)
					$types['time']['template'] .=  '<option  value="'.$i.'">'.$i.'</option>';
				$types['time']['template'] .= '</select> <select style="width:100px;" data-field="year">
				<option value="*">Tous les ans</option>';
				for($i=2000;$i<2200;$i++)
					$types['time']['template'] .=  '<option  value="'.$i.'">'.$i.'</option>';
				$types['time']['template'] .= '</select>';

		return $types;
	}

	function setValues($values){
		$this->values = json_encode($values);
	}
	
	function getValues(){
		return json_decode($this->values);
	}
	
}

?>