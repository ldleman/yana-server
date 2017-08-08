<?php

/**
 * Define a user right (right = crud on rank + section).
 * @author valentin carruesco
 * @category Core
 * @license copyright
 */
class Right extends Entity
{
    public $id,$rank,$section,$read,$edit,$delete,$configure;
    protected $fields =
    array(
        'id' => 'key',
        'rank' => 'string',
        'section' => 'string',
        'read' => 'bool',
        'edit' => 'bool',
        'delete' => 'bool',
        'configure' => 'bool'
        );

    
}
