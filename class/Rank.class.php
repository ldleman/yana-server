<?php

/**
 * Define a user rank.
 * @author valentin carruesco
 * @category Core
 * @license copyright
 */
class Rank extends Entity
{
	public $id,$label,$description;
	protected $fields =
	array(
		'id' => 'key',
		'label' => 'string',
		'description' => 'longstring'
		);

	
}
