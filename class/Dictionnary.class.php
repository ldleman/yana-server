<?php
/**
* Manage application and plugins lists with key/value pair.
* 
* @author valentin carruesco
*
* @category Core
*
* @license copyright
*/
class Dictionnary extends Entity
{
    public $id,$slug,$label,$parent,$state;
    protected $fields =
    array(
        'id' => 'key',
        'slug' => 'string',
        'label' => 'longstring',
        'parent' => 'int',
        'state' => 'int',
    );

    public function __construct()
    {
        parent::__construct();
    }

    public static function childs($slug, $sort = 'label ASC')
    {
        $obj = new self();
        $childs = array();
        $parent = $obj->load(array('slug' => $slug));
        if (!$parent) {
            return $childs;
        }
        foreach ($obj->loadAll(array('parent' => $parent->id)) as $child) {
            $childs[$child->id] = $child;
        }

        return $childs;
    }

    public static function table($slug)
    {
        $obj = new self();
        $parent = $obj->load(array('slug' => $slug));
        echo '<div class="table_list_'.$slug.'" data-list="'.$parent->id.'">
					<label for="config_application_table"></label>
					<table id="" class="table table-striped table-bordered table-hover">
						<thead>
						<tr>
							<th colspan="2">'.$parent->label.'</th>
						</tr>
						<tr>
							<th>Ajouter : <input style="margin:0;width:80%;height:inherit;box-sizing:border-box;padding:5px;" type="text"></th>
							<th style="width:50px;text-align:center;"><div class="btn btn-mini btn-success"><i class="fa fa-plus"></i></div></th>
						</tr>
						</thead>
						<tbody>
						
						<tr style="display:none" data-id="{{id}}">
							<td>{{label}}</td>
							<td>
								<div class="btn btn-mini btnEdit"><i class="fa fa-pencil"></i></div>
								<div class="btn btn-mini btn-danger"><i class="fa fa-times"></i></div>
							</td>
						</tr>
					</tbody></table>
				</div>';
    }
}
