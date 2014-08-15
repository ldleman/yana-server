<?php

/*
 @nom: FileUploaded
 @author Valentin CARRUESCO <idleman@idleman.fr>
 @link http://blog.idleman.fr
 @licence CC by nc sa
 @description:  Classe de gestion des fichiers envoyés
 */

class FileUploaded extends SQLiteEntity{

	public $id,$name,$ext,$mime,$date,$user,$path,$permissions,$tags,$size,$postFile,$url;
	protected $TABLE_NAME = 'plugin_file_uploaded';
	protected $CLASS_NAME = 'FileUploaded';
	protected $object_fields = 
	array(
		'id'=>'key',
		'name'=>'longstring',
		'ext'=>'string',
		'date'=>'string',
		'user'=>'string',
		'path'=>'longstring',
		'permissions'=>'longstring',
		'tags'=>'longstring',
		'mime'=>'string',
		'size' => 'int'
	);

	function __construct($post=null){
		parent::__construct();
		if($post!=null){
			$this->postFile = $post;
			$this->name = basename($this->postFile['name']);
			$this->ext = self::getExtension($this->name);
			$this->size = $this->postFile['size'];
			$this->date = time();
			list($d,$m,$y) = explode('/',date('d/m/Y',$this->date));
			$tags = array($d.'/'.$m.'/'.$y);
			$tags = array_merge(preg_split("/[,. _-]/", $this->name));
			foreach ($tags as $key=>$tag) {
				if(strlen($tag)<3) unset($tags[$key]);
			}
			$this->setTags($tags);
			$this->mime = $this->postFile['type'];
			$this->url = explode('/',Functions::getRootUrl());

			array_pop($this->url);
			$this->url = implode('/',$this->url).'/'.$this->getUrl();
		}
	}


	public function check($type,$cmp){
		switch($type){
			case 'extension': return in_array($this->ext, $cmp); break;
			case 'size': return $this->size < $cmp; break;
			default: return false; break;
		}
	}


	public function upload(){
		list($d,$m,$y) = explode('-',date('d-m-Y'));
		$uploaddir = 'uploads/'.$y;
		
		if (!file_exists($uploaddir)) mkdir($uploaddir);
		$uploaddir = $uploaddir.'/'.$m;
		if (!file_exists($uploaddir)) mkdir($uploaddir);
		$uploaddir = $uploaddir.'/'.$d.'/';
		if (!file_exists($uploaddir)) mkdir($uploaddir);

		$uploadfile = $uploaddir.$this->name;
		$this->path = $uploadfile;

		if (move_uploaded_file($this->postFile['tmp_name'], $this->path)) {
				$this->save();
				
				return true;
		}else{
			return false;
		}
		
	}

	public function addTag($tag){
		$tagsArray = $this->getTags();
		$tagsArray[] = $tag;
		$this->setTags($tagsArray);
	}
	public function setTags($tagsArray){
		$this->tags = implode(',',$tagsArray);
	}
	public function getTags(){
		return explode(',',$this->tags);
	}

	public function addPermission($permission){
		$permissionsArray = $this->getPermissions();
		$permissionsArray[] = $permission;
		$this->setPermissions($permissionsArray);
	}
	public function setPermissions($permissionsArray){
		$this->permissions = implode(',',$permissionsArray);
	}
	public function getPermissions(){
		return explode(',',$this->permissions);
	}

	

	public function checkPermission($myUser){

		$permissions = $this->getPermissions();

		 if(in_array('*', $permissions)
				|| ($myUser->getLogin()!='' && in_array('$', $permissions))
				|| (isset($myUser) && $myUser->getLogin() == $this->user)
				|| (isset($myUser) && $myUser->getLogin() !='' &&  in_array($myUser->getLogin(), $permissions) )
				){
			return true;
		}else{
			return false;
		}
	}

	public static function getExtension($name){
		return strtolower(substr($name,strpos($name,'.')+1));
	}

	public function getUrl(){
		return 'action.php?action=open_file&file='.$this->id;
	}
	public function getSize(){
		return Functions::convertFileSize($this->size);
	}

}

?>