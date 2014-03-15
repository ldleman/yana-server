<?php

/*
Classe basée sur le code de Raspcontrol [https://github.com/imjacobclark/Raspcontrol/]
*/

class Monitoring {
 
  
  public static function cpu() {
	// $loads[0] > 1 == 'danger'
	$loads = @sys_getloadavg();
    return array
	(
		'current_frequency' => round(file_get_contents("/sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq") / 1000), //Mhz
		'minimum_frequency' => round(file_get_contents("/sys/devices/system/cpu/cpu0/cpufreq/scaling_min_freq") / 1000), //Mhz
		'maximum_frequency' => round(file_get_contents("/sys/devices/system/cpu/cpu0/cpufreq/scaling_max_freq") / 1000), //Mhz
		'governor'		    => substr(file_get_contents("/sys/devices/system/cpu/cpu0/cpufreq/scaling_governor"), 0, -1),
		'loads'			    => $loads[0],
		'loads5'		    => $loads[1],
		'loads15'		    => $loads[2],
	);
  }

  public static function heat() {
  	$result = array(
		'degrees' => round(file_get_contents("/sys/class/thermal/thermal_zone0/temp") / 1000)
    );
    return round($result['degrees'] / 85 * 100);
  }
  
  public static function disks() {
		$result = array();
		exec('lsblk --pairs', $disksArray);
		for ($i = 0; $i < count($disksArray); $i++) { 
			parse_str(str_replace(array('"',' '), array("","&"), $disksArray[$i]), $output);		
			$result[$i]['name'] = $output["NAME"];     
			$result[$i]['maj:min'] = $output["MAJ:MIN"];     
			$result[$i]['rm'] = $output["RM"];     
			$result[$i]['size'] = $output["SIZE"];     
			$result[$i]['ro'] = $output["RO"];     
			$result[$i]['type'] = $output["TYPE"];     
			$result[$i]['mountpoint'] = $output["MOUNTPOINT"];     
		}
		return $result;
	}  
	
	
  public static function ram() {
		//$result['percentage'] >= '80'  = 'danger'
		exec('free -mo', $out);
		preg_match_all('/\s+([0-9]+)/', $out[1], $matches);
		list($total, $used, $free, $shared, $buffers, $cached) = $matches[1];
		return array(
			'free' => $free + $buffers + $cached,
			'percentage' => round(($used - $buffers - $cached) / $total * 100),
			'total'  => $total,
			'used' => $used - $buffers - $cached,
			'detail' => shell_exec('ps -e -o pmem,user,args --sort=-pmem | sed "/^ 0.0 /d" | head -5')
		);
  }

  public static function swap() {
		//$result['percentage'] >= '80' = danger
		exec('free -mo', $out);
		preg_match_all('/\s+([0-9]+)/', $out[2], $matches);
		list($total, $used, $free) = $matches[1]; 
		
		return array(
			'percentage' => round($used / $total * 100),
			'free' => $free,
			'used' => $used,
			'total' => $total
		);
  }

  public static function gpio() {
    $gpios = array();
    for($i=0;$i<25;$i++){
      $gpios[$i] = exec("/usr/local/bin/gpio read ".$i, $out);
 
    }
    return $gpios;
  }

  public static function connections() {
		//$connections >= 50 = 'warning'
		$connections = shell_exec("netstat -nta --inet | wc -l");
		$connections--;
		return substr($connections, 0, -1);
		  
  }

  public static function ethernet() {
	  $data = str_ireplace(array("TX bytes:","RX bytes:"), "",shell_exec("/sbin/ifconfig eth0 | grep RX\ bytes"));
	  $data =  explode(" ", trim($data));
	  return array(
		'up' => round($data[4] / 1024 / 1024,2),
		'down' => round($data[0] / 1024 / 1024,2)
		);
  }
  
   public static function distribution() {
    $distroTypeRaw = exec("cat /etc/*-release | grep PRETTY_NAME=", $out);
    return str_ireplace(array('PRETTY_NAME="','"'), '', $distroTypeRaw);
  }

  public static function kernel() {
    return exec("uname -mrs");
  }

  public static function firmware() {
    return exec("uname -v");
  }

  public static function hostname($full = false) {
    return $full ? exec("hostname -f") : gethostname();
  }

  public static function internalIp() {
    return $_SERVER['SERVER_ADDR'];
  }

  public static function externalIp() {
      $ip = self::loadUrl('http://whatismyip.akamai.com');
      if(filter_var($ip, FILTER_VALIDATE_IP) === false)
          $ip = self::loadUrl('http://ipecho.net/plain');
      if(filter_var($ip, FILTER_VALIDATE_IP) === false)
          return 'Unavailable';
      return $ip;
  }

  public static function webServer() {
    return $_SERVER['SERVER_SOFTWARE'];
  }
  
  public static function services() {
    $result = array();
    exec('/usr/sbin/service --status-all', $servicesArray);
    for ($i = 0; $i < count($servicesArray); $i++) {
		$servicesArray[$i] = preg_replace('!\s+!', ' ', $servicesArray[$i]);
		preg_match_all('/\S+/', $servicesArray[$i], $serviceDetails);
		list($bracket1, $result[$i]['status'], $bracket2, $result[$i]['name']) = $serviceDetails[0];
    $result[$i]['status'] = ($result[$i]['status']=='+'?true:false);
    }
    return $result;
  }
  
  public static function hdd() {
	//$result[$i]['percentage'] > '80' = danger
    $result = array();
    exec('df -T | grep -vE "tmpfs|rootfs|Filesystem"', $drivesarray);
    for ($i=0; $i<count($drivesarray); $i++) {
      $drivesarray[$i] = preg_replace('!\s+!', ' ', $drivesarray[$i]);
      preg_match_all('/\S+/', $drivesarray[$i], $drivedetails);
      list($fs, $type, $size, $used, $available, $percentage, $mounted) = $drivedetails[0];
      $result[$i] = array(
		'name' => $mounted,
		'total' => self::kConv($size),
		'free' => self::kConv($available),
		'used' => self::kConv($size - $available),
		'format' => $type,
		'percentage' => rtrim($percentage, '%')
	  ); 
    }
    return $result;
  }
  
    public static function temperature() {
    $temp_file = "/sys/bus/w1/devices/28-000004e8a0f3/w1_slave";
    if (file_exists($temp_file)) {
       $lines = file($temp_file);
       $currenttemp = round(substr($lines[1], strpos($lines[1], "t=")+2) / 1000 , 1) . "°C" ;
    } else {
       $currenttemp = "N/A";
    }
    return  $currenttemp;
  }
  
  public static function uptime() {
    $uptime = shell_exec("cat /proc/uptime");
    $uptime = explode(" ", $uptime); 
    return self::readbleTime($uptime[0]);
  }

   public static function users() {
    $result = array();
    $dataRaw = shell_exec("who --ips");
    $dataRawDNS = shell_exec("who --lookup");
    //patch for arch linux - the "who" binary doesnt support the --ips flag
    if (empty($dataRaw)) $dataRaw = shell_exec("who");
    foreach (explode ("\n", $dataRawDNS) as $line) {
      $line = preg_replace("/ +/", " ", $line);
      if (strlen($line)>0) {
        $line = explode(" ", $line);
     
        $temp[] = @$line[5];
      }
    }

    $i = 0;
    foreach (explode ("\n", $dataRaw) as $line) {
      $line = preg_replace("/ +/", " ", $line);

      if (strlen($line)>0) {
        $line = explode(" ", $line);
        $result[] = array(
          'user' => $line[0],
          'ip' => @$line[5],
          'dns' => $temp[$i],
          'date' => $line[2] .' '. $line[3],
          'hour' => $line[4]
          );
      }
      $i++;
    }

    return $result;
  }
  
  //TOOLS
  
  protected static function readbleTime($seconds) {
    $y = floor($seconds / 60/60/24/365);
    $d = floor($seconds / 60/60/24) % 365;
    $h = floor(($seconds / 3600) % 24);
    $m = floor(($seconds / 60) % 60);
    $s = $seconds % 60;

    $string = '';

    if ($y > 0) {
      $yw = $y > 1 ? ' years ' : ' year ';
      $string .= $y . $yw;
    }

    if ($d > 0) {
      $dw = $d > 1 ? ' days ' : ' day ';
      $string .= $d . $dw;
    }

    if ($h > 0) {
      $hw = $h > 1 ? ' hours ' : ' hour ';
      $string .= $h . $hw;
    }

    if ($m > 0) {
      $mw = $m > 1 ? ' minutes ' : ' minute ';
      $string .= $m . $mw;
    }

    if ($s > 0) {
     $sw = $s > 1 ? ' seconds ' : ' second ';
     $string .= $s . $sw;
    }

    return preg_replace('/\s+/', ' ', $string);
  }
   public static function kConv($kSize){
    $unit = array('K', 'M', 'G', 'T');
    $i = 0;
    $size = $kSize;
    while($i < 3 && $size > 1024){
      $i++;
      $size = $size / 1024;
    }
    return round($size, 2).$unit[$i];
  }
    protected static function loadUrl($url){
      if(function_exists('curl_init')){
          $curl = curl_init();
          curl_setopt($curl, CURLOPT_URL, $url);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
          $content = curl_exec($curl);
          curl_close($curl);
          return trim($content);
      }elseif(function_exists('file_get_contents')){
          return trim(file_get_contents($url));
      }else{
          return false;
      }
  }
     

}

?>
