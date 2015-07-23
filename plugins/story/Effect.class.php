<?php

/*
 @nom: Effect
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Représente un effet de scénario (une conséquence produite par une ou plusieurs causes)
 */

class Effect extends SQLiteEntity{

	public $id,$story,$sort,$type,$value,$target,$operator,$union;
	protected $TABLE_NAME = 'plugin_story_effect';
	protected $CLASS_NAME = 'Effect';
	protected $object_fields = 
	array(
		'id'=>'key',
		'story'=>'int',
		'sort'=>'int',
		'type'=>'string',
		'union'=>'string',
		'value'=>'longstring',
		'target'=>'longstring'
	);

	function __construct(){
		parent::__construct();
	}
	
	public static function types(){
		return array(
			'command' => array(
					'icon' => 'fa-terminal',
					'label' => 'Commande',
					'template' => '<select data-field="target"><option value="server">Serveur</option><option value="client">Client</option></select> = <input data-field="command" type="text" placeholder="valeur" value="{value}">'
					),
			'talk' => array(
					'icon' => 'fa-volume-up',
					'label' => 'Phrase',
					'template' => '= <input type="text" data-field="sentence" placeholder="Ma phrase.." value="{value}">'
					),
			'var' => array(
					'icon' => 'fa-dollar',
					'label' => 'Variable',
					'template' => '<input type="text" data-field="var" placeholder="Ma variable" value=""> <span data-field="operator" class="operator">=</span> <input data-field="value" type="text" placeholder="Ma valeur" value="{value}">'
					),
			'sleep' => array(
					'icon' => 'fa-coffee',
					'label' => 'Pause',
					'template' => '= <input type="text" placeholder="durée(secondes)" data-field="seconds" value="{value}"> seconde(s)'
					),
			'story' => array(
					'icon' => 'fa-caret-square-o-right',
					'label' => 'Scénario',
					'template' => '<select data-field="story" class="story"></select>'
					),
			'url' => array(
					'icon' => 'fa-globe',
					'label' => 'Url',
					'template' => '<input data-field="url" type="text">'
					),
		);
	}
}

?>