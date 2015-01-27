<?php
class System{

	public static function getInfos(){
		$return = self::system('cat /proc/cpuinfo');
		$lines = preg_split("/\\r\\n|\\r|\\n/", $return);
		$infos = array();
		foreach($lines as $line){
			if(strpos($line,':') === false ) continue;
			list($key,$value)  = explode(':',$line);
			$infos[trim($key)] = trim($value);
		}
		return $infos;
	}
	
	public static function getModel(){
		$infos = self::getInfos();
		$deductionArray = array(
			'0002' => array('ram'=>'256','version'=>'1.0','type'=>'b'),
			'0003' => array('ram'=>'256','version'=>'1.0+ecn0001','type'=>'b'),
			'0004' => array('ram'=>'256','version'=>'2.0','type'=>'b'),
			'0005' => array('ram'=>'256','version'=>'2.0','type'=>'b'),
			'0006' => array('ram'=>'256','version'=>'2.0','type'=>'b'),
			'0007' => array('ram'=>'256','version'=>'1.0','type'=>'a'),
			'0008' => array('ram'=>'256','version'=>'1.0','type'=>'a'),
			'0009' => array('ram'=>'256','version'=>'1.0','type'=>'a'),
			'0010' => array('ram'=>'512','version'=>'1.0','type'=>'b+'),
			'0011' => array('ram'=>'512','version'=>'1.0','type'=>'compute'),
			'0012' => array('ram'=>'256','version'=>'1.0','type'=>'a+'),
			'000d' => array('ram'=>'512','version'=>'2.0','type'=>'b'),
			'000e' => array('ram'=>'512','version'=>'2.0','type'=>'b'),
			'000f' => array('ram'=>'512','version'=>'2.0','type'=>'b'),
		);
		return isset($deductionArray[$infos['Revision']]) ? $deductionArray[$infos['Revision']] :'unknown';
	}
	

	private static function command($cmd){
		Functions::log('Launch system command : '.$cmd);
		return system($cmd);
	}
	
	public static function gpio() {
		$infos = self::getInfos();
		$maxPins = $infos['type'] == 'b+' ? 39 : 25 ;
		$gpios = array();
		for($i=0;$i<$maxPins;$i++){
		  $gpios[$i] = exec("/usr/local/bin/gpio read ".$i, $out);
		}
		return $gpios;
  }
	
}
?>