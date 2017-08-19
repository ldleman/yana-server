<?php

/**
 * Log selected action in database with ip, datetime and optional logged user.
 *
 * @author valentin carruesco
 *
 * @category Core
 *
 * @license copyright
 */
class Log extends Entity
{
    public $id,$label,$user,$date,$ip;
    protected $fields =
    array(
        'id' => 'key',
        'label' => 'longstring',
        'user' => 'string',
        'date' => 'string',
        'ip' => 'string',
        );

    public function label(){
        return preg_replace_callback('|^\[([^\]]*)\](.*)|i', function($matches){
            return '<span class="badge badge-info">'.$matches[1].'</span>'.$matches[2];
        }, $this->label);
    }

    public static function put($label)
    {
        global $myUser;
        $log = new self();
        $log->label = $label;
        if (is_object($myUser) && $myUser->login != '') {
            $log->user = $myUser->login;
        }
        $log->date = time();
        $log->ip = ip();
        $log->save();
    }

    public static function clear($delay = 1){
       $treshold = time() - ($delay * 2592000);
       self::delete(array('date:<'=>$treshold));
    }
}
