<?php
class System{

	public static function getInfos(){
		$return = self::commandSilent('cat /proc/cpuinfo');
		$lines = preg_split("/\\r\\n|\\r|\\n/", $return);
		$infos = array();
		foreach($lines as $line){
			if(strpos($line,':') === false ) continue;
			list($key,$value)  = explode(':',$line);
			$infos[trim($key)] = trim($value);
		}
		return $infos;
	}
	
	public static function getPinForModel($model,$version){
		$model = $model.$version;
		
		
		//@TODO re-check pins mapping for revisions / types (it's fucking nightmare)
		//remi : It would be easier to use the output of gpio readall
		$pins = array();
		
		// rev 1.0 type B
		$pins['b1.0'] = array(
							array(
								//name,function,wiringPiNumber,bcmNumber,physicalNumber
								new Gpio('3.3V','Alimentation',-1,-1,1),
								new Gpio('SDA0','I2C',8,0,3),
								new Gpio('SCL0','I2C',9,1,5),
								new Gpio('GPIO 7','',7,4,7),
								new Gpio('DNC','Masse (GND)',-1,-1,9),
								new Gpio('GPIO 0','',0,17,11),
								new Gpio('GPIO 2','',2,21,13),
								new Gpio('GPIO 3','',3,22,15),
								new Gpio('DNC','Masse (GND)',-1,-1,17),
								new Gpio('MOSI','SPI',12,10,19),
								new Gpio('MISO','SPI',13,9,21),
								new Gpio('SCLK','SPI',14,11,23),
								new Gpio('DNC','',-1,-1,25),
							),
							array(
								new Gpio('5V','Alimentation',-1,-1,2),
								new Gpio('DNC','Masse (GND)',-1,-1,4),
								new Gpio('0V','Masse (GND)',-1,-1,6),
								new Gpio('TxD','UART (Transmission)',14,14,8),
								new Gpio('RxD','UART (Réception)',15,15,10),
								new Gpio('GPIO 1','',1,18,12),
								new Gpio('DNC','Masse (GND)',-1,-1,14),
								new Gpio('GPIO 4','',4,23,16),
								new Gpio('GPIO 5','',5,24,18),
								new Gpio('DNC','Masse (GND)',-1,-1,20),
								new Gpio('GPIO 6','',6,25,22),
								new Gpio('CE 0','SPI',10,8,24),
								new Gpio('CE 1','SPI',11,7,26),
							)
						);
		$pins['a1.0'] = $pins['b1.0'];
		// rev 2.0 type B
		$pins['b2.0'] = array(
							array(
								//name,function,wiringPiNumber,bcmNumber,physicalNumber
								new Gpio('3.3V','Alimentation',-1,-1,1),
								new Gpio('SDA0','I2C',8,2,3),
								new Gpio('SCL0','I2C',9,3,5),
								new Gpio('GPIO 7','',7,4,7),
								new Gpio('DNC','Masse (GND)',-1,-1,9),
								new Gpio('GPIO 0','',0,17,11),
								new Gpio('GPIO 2','',2,27,13),
								new Gpio('GPIO 3','',3,22,15),
								new Gpio('DNC','Masse (GND)',-1,-1,17),
								new Gpio('MOSI','SPI',12,10,19),
								new Gpio('MISO','SPI',13,9,21),
								new Gpio('SCLK','SPI',14,11,23),
								new Gpio('DNC','',-1,-1,25),
							),
							array(
								new Gpio('5V','Alimentation',-1,-1,2),
								new Gpio('DNC','Masse (GND)',-1,-1,4),
								new Gpio('0V','Masse (GND)',-1,-1,6),
								new Gpio('TxD','UART (Transmission)',14,14,8),
								new Gpio('RxD','UART (Réception)',15,15,10),
								new Gpio('GPIO 1','',1,18,12),
								new Gpio('DNC','Masse (GND)',-1,-1,14),
								new Gpio('GPIO 4','',4,23,16),
								new Gpio('GPIO 5','',5,24,18),
								new Gpio('DNC','Masse (GND)',-1,-1,20),
								new Gpio('GPIO 6','',6,25,22),
								new Gpio('CE 0','SPI',10,8,24),
								new Gpio('CE 1','SPI',11,7,26),
							)
						);
		$pins['a2.0'] = $pins['b2.0'];
		
		// type B+
		// @maditnerd I fix the gpio numbering , some were wrong.
		$pins['b+1.0'] = array(
							array(
								//name,function,wiringPiNumber,bcmNumber,physicalNumber
								new Gpio('3.3V','Alimentation',-1,-1,1),
								new Gpio('SDA1','I2C',8,2,3),
								new Gpio('SCL1','I2C',9,3,5),
								new Gpio('GPIO 7','',7,4,7),
								new Gpio('DNC','Masse (GND)',-1,-1,9),
								new Gpio('GPIO 0','',0,17,11),
								new Gpio('GPIO 2','',2,27,13),
								new Gpio('GPIO 3','',3,22,15),
								new Gpio('3.3V','Alimentation',-1,-1,1),
								new Gpio('MOSI','SPI',12,10,19),
								new Gpio('MISO','SPI',13,9,21),
								new Gpio('SCLK','SPI',14,11,23),
								new Gpio('DNC','Masse (GND)',-1,-1,25),
								new Gpio('SDA0','I2C',30,0,27),
								new Gpio('GPIO 21','',21,5,29),
								new Gpio('GPIO 22','',22,6,31),
								new Gpio('GPIO 23','',23,13,33),
								new Gpio('GPIO 24','',24,19,35),
								new Gpio('GPIO 25','',25,26,37),
								new Gpio('0V','Masse (GND)',-1,-1,39),
							),
							array(
								new Gpio('5V','Alimentation',-1,-1,2),
								new Gpio('5V','Alimentation',-1,-1,4),
								new Gpio('0V','Masse',-1,-1,6),
								new Gpio('TxD','UART (Transmission)',15,15,8),
								new Gpio('RxD','UART (Réception)',16,16,10),
								new Gpio('GPIO 1','',1,18,12),
								new Gpio('0V','Masse (GND)',-1,-1,14),
								new Gpio('GPIO 4','',4,23,16),
								new Gpio('GPIO 5','',5,24,18),
								new Gpio('0V','Masse (GND)',-1,-1,20),
								new Gpio('GPIO 6','',6,25,22),
								new Gpio('CE 0','SPI',10,8,24),
								new Gpio('CE 1','SPI',11,7,26),
								new Gpio('SCL 0','I2C ID EEPROM',-1,-1,28),
								new Gpio('0V','Masse (GND)',-1,-1,30),
								new Gpio('GPIO 26','PWM0',26,12,32),
								new Gpio('0V','Masse (GND)',-1,-1,34),
								new Gpio('GPIO 27','',27,16,36),
								new Gpio('GPIO 28','',28,20,38),
								new Gpio('GPIO 29','',29,21,40),
								
							)
						);

		//type B2
		$pins['b21.0'] = $pins['b+1.0'];
		//A3
		$pins['a3.0'] = $pins['b21.0'];
		//Zero
		$pins['zero1.0'] = $pins['a3.0'];

		 // Banana PI M1
		$pins['M11.0'] = array(
			array(
				new Gpio('3.3V','Alimentation',-1,-1,1),
				new Gpio('SDA0','I2C',8,0,3),
				new Gpio('SCL0','I2C',9,1,5),
				new Gpio('GPIO 7','',7,4,7),
				new Gpio('0V','Masse (GND)',-1,-1,9),
				new Gpio('GPIO 0','',0,17,11),
				new Gpio('GPIO 2','',2,21,13),
				new Gpio('GPIO 3','',3,22,15),
				new Gpio('3.3V','Alimentation',-1,-1,17),
				new Gpio('MOSI','SPI',12,10,19),
				new Gpio('MISO','SPI',13,9,21),
				new Gpio('SCLK','SPI',14,11,23),
				new Gpio('0V','Masse (GND)',-1,-1,25),
	            new Gpio('',' ',-1,-1,-1),
	            new Gpio('5V','Alimentation',-1,-1,1),
	            new Gpio('GPIO 8','',17,28,3),
	            new Gpio('GPIO 10','',19,30,5),
	            new Gpio('0V','Masse (GND)',-1,-1,7),
            ),
            array(
				new Gpio('5V','Alimentation',-1,-1,2),
				new Gpio('5V','Alimentation',-1,-1,4),
				new Gpio('0V','Masse (GND)',-1,-1,6),
				new Gpio('TxD','UART (Transmission)',14,14,8),
				new Gpio('RxD','UART (Réception)',15,15,10),
				new Gpio('GPIO 1','',1,18,12),
				new Gpio('0V','Masse (GND)',-1,-1,14),
				new Gpio('GPIO 4','',4,23,16),
				new Gpio('GPIO 5','',5,24,18),
				new Gpio('0V','Masse (GND)',-1,-1,20),
				new Gpio('GPIO 6','',6,25,22),
				new Gpio('CE 0','SPI',10,8,24),
				new Gpio('CE 1','SPI',11,7,26),
				new Gpio('',' ',-1,-1,-1),
				new Gpio('3.3V','Alimentation',-1,-1,2),
				new Gpio('Gpio 9 Tx','UART (Transmission)',18,29,4),
				new Gpio('Gpio 11 Rx','UART (Réception)',20,31,6),
				new Gpio('0V','Masse (GND)',-1,-1,8),
            )
        );



		return isset($pins[$model])?$pins[$model]:$pins['b1.0'];
	}
	
	public static function getModel(){
		$infos = self::getInfos();
		$deductionArray = array(
			'0002' => array('ram'=>'256','version'=>'1.0','type'=>'b','revision'=>'0002'),
			'0003' => array('ram'=>'256','version'=>'1.0+ecn0001','type'=>'b','revision'=>'0003'),
			'0004' => array('ram'=>'256','version'=>'2.0','type'=>'b','revision'=>'0004'),
			'0005' => array('ram'=>'256','version'=>'2.0','type'=>'b','revision'=>'0005'),
			'0006' => array('ram'=>'256','version'=>'2.0','type'=>'b','revision'=>'0006'),
			'0007' => array('ram'=>'256','version'=>'1.0','type'=>'a','revision'=>'0007'),
			'0008' => array('ram'=>'256','version'=>'1.0','type'=>'a','revision'=>'0008'),
			'0009' => array('ram'=>'256','version'=>'1.0','type'=>'a','revision'=>'0009'),
			'0010' => array('ram'=>'512','version'=>'1.0','type'=>'b+','revision'=>'0010'),
			'0011' => array('ram'=>'512','version'=>'1.0','type'=>'compute','revision'=>'0011'),
			'0012' => array('ram'=>'256','version'=>'1.0','type'=>'a+','revision'=>'0012'),
			'0013' => array('ram'=>'512','version'=>'1.0','type'=>'b+','revision'=>'0013'),
			'000d' => array('ram'=>'512','version'=>'2.0','type'=>'b','revision'=>'000d'),
			'000e' => array('ram'=>'512','version'=>'2.0','type'=>'b','revision'=>'000e'),
			'000f' => array('ram'=>'512','version'=>'2.0','type'=>'b','revision'=>'000f'),
			'a01041' => array('ram'=>'1024','version'=>'1.0','type'=>'b2','revision'=>'a01041'),
			'1a01041' => array('ram'=>'1024','version'=>'1.0','type'=>'b2','revision'=>'1a01041'),
			'a21041' => array('ram'=>'1024','version'=>'1.0','type'=>'b2','revision'=>'a21041'),
			'2a01041' => array('ram'=>'1024','version'=>'1.0','type'=>'b2','revision'=>'2a01041'),
			'a02082' => array('ram'=>'1024','version'=>'3.0','type'=>'a','revision'=>'a02082'),
			'a22082' => array('ram'=>'1024','version'=>'3.0','type'=>'a','revision'=>'a22082'), 
			'900092' => array('ram'=>'512','version'=>'1.0','type'=>'zero','revision'=>'900092'),
			'0000' => array('ram'=>'1024','version'=>'1.0','type'=>'M1','revision'=>'0000')
		);
		if(PHP_OS=='WINNT') $infos['Revision'] = 'a01041';//for dev mode on windows only
		return isset($deductionArray[$infos['Revision']]) ? $deductionArray[$infos['Revision']] :array('ram'=>'0','version'=>'0','type'=>'unknown','revision'=>$infos['Revision']);
	}

	public static function commandSilent($cmd){
		Functions::log('Launch system command (without output): '.$cmd);
		return shell_exec($cmd);
	}
	

	public static function command($cmd){
		Functions::log('Launch system command : '.$cmd);
		return system($cmd);
	}
	
	public static function gpio() {
		$model = self::getModel();
		$pinsRange = self::getPinForModel($model['type'],$model['version']);
		$gpios = array();
		foreach($pinsRange as $range){
				foreach($range as $pin){
					if(PHP_OS=='WINNT'){
						$gpios[$pin->wiringPiNumber] = rand(0,1);
						continue;
					}
					if($pin->wiringPiNumber<0) continue;
					$gpios[$pin->wiringPiNumber] = exec("/usr/local/bin/gpio read ".$pin->wiringPiNumber, $out);				
				}
		}
		return $gpios;
  }


	
}
?>
