<?php

/*
 @nom: Gpio
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Classe de gestion des gpio via wiring PI
 */

class Gpio{

	const GPIO_DEFAULT_PATH = '/usr/bin/gpio';
	
	public $name,$role,$wiringPiNumber,$bcmNumber,$physicalNumber;
	
	function __construct($name,$role,$wiringPiNumber,$bcmNumber,$physicalNumber){
		$this->name = $name;
		$this->role = $role;
		$this->wiringPiNumber = $wiringPiNumber;
		$this->bcmNumber = $bcmNumber;
		$this->physicalNumber = $physicalNumber;
	}
	
	private static function system($cmd){
		// For compatibily with plugins wich call that method from GPIO instead of System.
		System::command($cmd);
	}
	
	public static function mode($pin,$mode = 'out'){
		return self::system(self::GPIO_DEFAULT_PATH.' mode '.$pin.' '.$mode);
	}
	public static function write($pin,$value = 0,$automode = false){
		if($automode) self::mode($pin,'out');
		return self::system(self::GPIO_DEFAULT_PATH.' write '.$pin.' '.$value);
	}
	public static function read($pin,$automode = false){
		if($automode) self::mode($pin,'in');
		return System::commandSilent(self::GPIO_DEFAULT_PATH.' read '.$pin);
	}
	public static function pulse($pin,$miliseconds,$state){
		Gpio::write($pin,$state);
		usleep($miliseconds);
		$state = $state == 1 ? 0 : 1;
		Gpio::write($pin,$state);
	}

	public static function emit($gpio, $state){
		
		if(isset($GLOBALS['gpio'][$gpio])) {
		    foreach($GLOBALS['gpio'][$gpio] as $functionName) {  
		        call_user_func_array($functionName, array($gpio,$state));  
		    }  
		} 
		if(isset($GLOBALS['gpio']['all'])) {
	
		    foreach($GLOBALS['gpio']['all'] as $functionName) {  
		        call_user_func_array($functionName, array($gpio,$state));  
		    }  
		} 
	}

	public static function listen($gpio,$functionName){
		$GLOBALS['gpio'][$gpio][] = $functionName;  
	}
}
?>