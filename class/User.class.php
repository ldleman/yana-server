<?php

/**
 * Define an application user.
 *
 * @author valentin carruesco
 *
 * @category Core
 *
 * @license copyright
 */
class User extends Entity
{
    public $id,$login,$password,$name,$firstname,$mail,$state,$rank,$rights,$superadmin,$token;
    protected $fields =
    array(
        'id' => 'key',
        'login' => 'string',
        'password' => 'string',
        'name' => 'string',
        'firstname' => 'string',
        'token' => 'string',
        'mail' => 'string',
        'rank' => 'int',
        'state' => 'int',
		'superadmin' => 'int'
    );

    public static function getAll(){
        $users = self::loadAll();
        Plugin::callHook('user_load',array(&$user));
        return $users;
    }
    public function __sleep()
    {
        return array_merge(array('rights'),array_keys($this->toArray()));
    }

    public function can($section,$right){
		if($this->superadmin) return true;
        return !isset($this->rights[$section][$right]) ? false: $this->rights[$section][$right]==1;
    }

    public function loadRights(){
        $this->rights = array();
        foreach(Right::loadAll(array('rank'=>$this->rank)) as $right):
            $this->rights[$right->section] = array('read'=>$right->read,'edit'=>$right->edit,'delete'=>$right->delete,'configure'=>$right->configure);
        endforeach;
    }

	public function getAvatar(){
		$avatar = 'img/avatar.png';
		$files = glob(__ROOT__.AVATAR_PATH.$this->login.'.{jpg,png}',GLOB_BRACE);

		if(count($files)>0) $avatar = str_replace(__ROOT__,'',$files[0]);
		return $avatar;
	}
	
    public static function check($login, $password)
    {
        $user = self::load(array('login' => $login, 'password' => self::password_encrypt($password)));
		
        return is_object($user) ? $user : new self();
    }


    public function fullName()
    {
        $fullName = ucfirst($this->firstname).' '.strtoupper($this->name);

        return trim($fullName) != '' ? $fullName : $this->login;
    }

    public static function password_encrypt($password)
    {
        return sha1(md5($password));
    }

    public function connected()
    {
        return $this->id != 0;
    }
}
