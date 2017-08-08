<?php

/**
 * Execute an action (request which no need html view response: ajax,json etc...) and manage automatically
 * access rights, exceptions and json response.
 *
 * @author valentin carruesco
 *
 * @category Core
 *
 * @license cc by nc sa
 */
class Action
{
    /**
     * Execute an action 
     * #### Example
     * ```php
     * Action::write(function($_,&$response){ 
     *	$response['custom'] = 'hello world!'; 
     * },array('user'=>'u','plugin'=>'d')); //User must have user update right and delete plugin right to perform this action
     * ```.
     *
     * @param function action/code to execute
     * @param array Array wich contain right to execute action
     *
     * @return print json response
     */
    public static function write($f, $p = array())
    {
        global $myUser,$_,$conf;
        header('content-type:application/json');
        $response = array();
        set_error_handler(function ($level, $error, $file, $line) { throw new Exception($error." \r\n\r\n  ".$file.' - L '.$line.'');});
        try {
            foreach ($p as $section => $right) {
                if (!$myUser->can($section, $right)) {
                    throw new Exception('Vous ne disposez pas des droits suffisants pour effectuer cette action');
                }
            }
            $f($_, $response);
            
        } catch (Exception $e) {
            $response['error'] = $e->getMessage();
            $response['trace'] = $e->getTraceAsString();
        }
        echo json_encode($response);
        restore_error_handler();
    }
}
