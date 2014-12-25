<?php

/*
 @nom: Gpio
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Classe de gestion des gpio via wiring PI
 */

class Gpio{

	const GPIO_DEFAULT_PATH = '/usr/local/bin/gpio';
	
	private static function system($cmd){
		Functions::log('Launch system command : '.$cmd);
		return system($cmd);
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
		return self::system(self::GPIO_DEFAULT_PATH.' read '.$pin);
	}
	public static function pulse($pin,$miliseconds,$state){
		Gpio::write($pin,$state);
		usleep($miliseconds);
		$state = $state == 1 ? 0 : 1;
		Gpio::write($pin,$state);
	}
}
?>