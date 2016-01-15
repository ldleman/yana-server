<?php

/*
 @nom: Effect
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Représente un effet de scénario (une conséquence produite par une ou plusieurs causes)
 */

class Effect extends SQLiteEntity{

	public $id,$story,$sort,$type,$values,$operator,$union;
	protected $TABLE_NAME = 'plugin_story_effect';
	protected $CLASS_NAME = 'Effect';
	protected $object_fields = 
	array(
		'id'=>'key',
		'story'=>'int',
		'sort'=>'int',
		'type'=>'string',
		'union'=>'string',
		'values'=>'longstring'
	);

	function __construct(){
		parent::__construct();
	}
	
	public static function types(){
		$types = array(
			'command' => array(
					'icon' => 'fa-terminal',
					'label' => 'Commande',
					'template' => '<select data-value="{target}" data-field="target"><option value="server">Serveur</option><option value="client">Client</option></select> = <input data-field="value" style="max-width:50%;width:50%;" type="text" placeholder="valeur" value="{value}">'
					),
			'gpio' => array(
					'icon' => 'fa-dot-circle-o',
					'label' => 'GPIO',
					'template' => 'numéro <input type="text" data-field="gpio" placeholder="1,2,3,4..." value="{gpio}"> en état <select data-value="{value}" data-field="value"><option value="1">Actif</option><option value="0">Inactif</option></select>'
					),
			'talk' => array(
					'icon' => 'fa-volume-up',
					'label' => 'Phrase',
					'template' => '= <input type="text" style="max-width:50%;width:50%;" data-field="value" placeholder="Ma phrase.." value="{value}">'
					),
			'var' => array(
					'icon' => 'fa-dollar',
					'label' => 'Variable',
					'template' => '<input type="text" data-field="var" placeholder="Ma variable" value="{var}"> <span data-field="operator" class="operator">=</span> <input data-field="value" type="text" placeholder="Ma valeur" value="{value}">'
					),
			'sleep' => array(
					'icon' => 'fa-coffee',
					'label' => 'Pause',
					'template' => '= <input type="text" placeholder="durée(secondes)" data-field="value" value="{value}"> seconde(s)'
					),
			'story' => array(
					'icon' => 'fa-caret-square-o-right',
					'label' => 'Scénario',
					'template' => ''
					),
			'url' => array(
					'icon' => 'fa-globe',
					'label' => 'Url',
					'template' => '<input style="max-width:50%;width:50%;" data-field="value" value="{value}" type="text">'
					),
		);
		
	$types['story']['template'] = '<select data-value="{value}"  data-field="value" class="story">';
		require_once('Story.class.php');
		$stories = new Story();
		$stories = $stories->populate();
		foreach($stories as $story):
			$types['story']['template'] .= '<option value="'.$story->id.'">'.$story->label.'</option>';
		endforeach;
		$types['story']['template'] .= '</select>';
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