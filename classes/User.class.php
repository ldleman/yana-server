<?php

/*
 @nom: User
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Classe de gestion des utilisateurs
 */

class User extends SQLiteEntity{

	protected $id,$login,$password,$name,$firstname,$mail,$state,$groups,$rank,$rights,$phone,$token,$cookie;
	protected $TABLE_NAME = 'user';
	protected $CLASS_NAME = 'User';
	protected $object_fields = 
	array(
		'id'=>'key',
		'login'=>'string',
		'password'=>'string',
		'name'=>'string',
		'firstname'=>'string',
		'mail'=>'string',
		'rank'=>'int',
		'token'=>'string',
		'state'=>'int',
		'cookie'=>'string'
	);

	function __construct(){
		parent::__construct();
	}

	function setId($id){
		$this->id = $id;
	}

	//Teste la validité d'un compte à l'identification
	function exist($login,$password){
		$userManager = new User();
	    $newUser = false;
	    $newUser = $userManager->load(array('login'=>$login,'password'=>sha1(md5($password))));
	    Plugin::callHook("action_pre_login", array(&$newUser));
	   	if(is_object($newUser)) $newUser->loadRight();
		return $newUser;
	}

	//Récupère les droits en CRUD de l'utilisateur courant et les charge dans son tableau de droits interne
	function loadRight(){
		$rightManager = new Right();

		$rights = $rightManager->loadAll(array('rank'=>$this->getRank()));

		$sectionManager= new Section();
		foreach($rights as $right){
			$section = $sectionManager->getById($right->getSection());
			if(is_object($section)){
				$this->rights[$section->getLabel()]['c'] = ($right->getCreate()=='1'?true:false);
				$this->rights[$section->getLabel()]['r'] = ($right->getRead()=='1'?true:false);
				$this->rights[$section->getLabel()]['u'] = ($right->getUpdate()=='1'?true:false);
				$this->rights[$section->getLabel()]['d'] = ($right->getDelete()=='1'?true:false);
			}else{
				$rightManager->delete(array('section'=>$right->getSection()));
			}
		}
	}


	static function getByLogin($login){
		$returnedUser = new User();
		$users = User::getAllUsers();
		foreach($users as $user){
			if($user->getLogin()==$login) $returnedUser = $user;
		}
		return $returnedUser;
	}

	//Retourne une liste d'objets contenant tout les utilisateurs habilités à se connecter au programme
	//@return Liste d'objets User
	static function getAllUsers(){
		$userManager = new User();
		$users = $userManager->populate();
		Plugin::callHook("user_get_all", array(&$users));
		usort($users, "User::userorder");
		return $users;
	}

	static function userorder($a, $b)
	{
	    return strcmp($a->getName(), $b->getName());
	}


	function getGravatar($size = 100){
		$gravatar = AVATAR_FOLDER.'/'.$this->getMail().'-'.$size.'.jpg';
		if(!file_exists($gravatar)){
			if (!file_exists(AVATAR_FOLDER)) mkdir(AVATAR_FOLDER);
			file_put_contents($gravatar, file_get_contents("http://www.gravatar.com/avatar/" . md5( strtolower( trim( $this->getMail() ) ) ) . "?&s=".$size));
		}
		return $gravatar;
	}
	function getGravatarImg($size = 100){
		return "<img class='avatar avatar-".$size."' src='".$this->getGravatar($size)."' />" ;
	}

	function can($section,$selectedRight){

		return (!isset($this->rights[$section])?false:$this->rights[$section][$selectedRight]);
	}

	function haveGroup($group){
		return in_array($group,$this->getGroups());
	}
	
	function getId(){
		return $this->id;
	}

	function getLogin(){
		return $this->login;
	}

	function setLogin($login){
		$this->login = $login;
	}

	function getFullName(){
		$fn = ucfirst($this->firstname).' '.strtoupper($this->name);
		return trim($fn)==''?'Anonymous Guy':$fn;
	}

	function getName(){
		return $this->name;
	}

	function getFirstName(){
		return $this->firstname;
	}

	function getMail(){
		return $this->mail;
	}

	function getState(){
		return $this->state;
	}

	function setName($name){
		$this->name = $name;
	}

	function setFirstName($firstname){
		$this->firstname = $firstname;
	}

	function setMail($mail){
		$this->mail = $mail;
	}

	function setState($state){
		$this->state = $state;
	}

	function setGroups($groups){
		$this->groups = $groups;
	}

	function getGroups(){
		return (is_array($this->groups)?$this->groups:array());
	}

	function getRank(){
		return $this->rank;
	}
	function setRank($rank){
		$this->rank = $rank;
		$this->loadRight();
	}

	function setPassword($password){
		$this->password = User::cryptPassword($password);
	}

	public static function cryptPassword($string){
		return sha1(md5($string));
	}

	function setPhone($phone){
		$this->phone = $phone;
	}

	function getPhone(){
		return $this->phone;
	}
	function setToken($token){
		$this->token = $token;
	}

	function getToken(){
		return $this->token;
	}

	function setCookie($cookie){
		$this->cookie = $cookie;
	}

	function getCookie(){
		return $this->cookie;
	}


}

?>