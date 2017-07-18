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
}
