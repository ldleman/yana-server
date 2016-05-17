<?php
/**
* Tool library wich provide many common functions to ease your life :)
* 
* @author Idleman
* @category Tools
* @license cc by nc sa
*/

class Functions
{
	private $id;
	public $debug=0;

	/**
	* Secure client var
	* #### Example
	* ```php
	* Functions::secure($_GET['nonThrustedInput']);
	* ```
	* @param mixed var to secure
	* @return mixed secured var
	*/

	public static function secure($var){
		$response = '';
		if(is_array($var)){
			foreach($var as $key=>$value)
				$response[Functions::secure($key)] = Functions::secure($value);
		}else{
			$response = addslashes(htmlspecialchars($var, ENT_QUOTES, "UTF-8"));
		}
		return $response;
	}

	/**
	* Get client IP
	* #### Example
	* ```php
	* Functions::getIP();
	* ```
	* @return string client ip
	*/
	public static function getIP(){
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];}
			elseif(isset($_SERVER['HTTP_CLIENT_IP'])){
				$ip = $_SERVER['HTTP_CLIENT_IP'];}
				else{ $ip = $_SERVER['REMOTE_ADDR'];}
				return $ip;
			}

	/**
	* Truncate string after 'x' characters and add '...'
	* #### Example
	* ```php
	* echo Functions::truncate('This is incredibly long !!',5);
	* ```
	* @param string String to truncate
	* @param int Max length before truncate
	* @return string truncated string
	*/
	public static function truncate($msg,$limit){
		if(mb_strlen($msg)>$limit){
			$fin='…' ;
			$nb=$limit-mb_strlen($fin) ;
		}else{
			$nb=mb_strlen($msg);
			$fin='';
		}
		return mb_substr($msg, 0, $nb).$fin;
	}

	/**
	* Get script base url (require calling file path in parameter)
	* #### Example
	* ```php
	* echo Functions::getBaseUrl('action.php');
	* ```
	* @param string calling file path
	* @return string base url
	*/
	public static function getBaseUrl($from){

		$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$split = explode('/'.$from,$_SERVER['REQUEST_URI']);
		return $protocol.$_SERVER['HTTP_HOST'].$split[0];
	}

	/**
	 * Definis si la chaine fournie est existante dans la reference fournie ou non
	 * @TODO delete that after verifying that is not used by plugin or core! 
	 * @param unknown_type $string
	 * @param unknown_type $reference
	 * @return false si aucune occurence du string, true dans le cas contraire
	 */
	public static function contain($string,$reference){
		$return = true;
		$pos = strpos($reference,$string);
		if ($pos === false) {
			$return = false;
		}
		return strtolower($return);
	}

	/**
	 * @TODO delete that after verifying that is not used by plugin or core! 
	 * Définis si la chaine passée en parametre est une url ou non
	 */
	public static function isUrl($url){
		$return =false;
		if (preg_match('/^(http|https|ftp)://([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/?/i', $url)) {
			$return =true;
		}
		return $return;
	}

	/**
	 * @TODO delete that after verifying that is not used by plugin or core! 
	 * Définis si la chaine passée en parametre est une couleur héxadécimale ou non
	 */
	public static function isColor($color){
		$return =false;
		if (preg_match('/^#(?:(?:[a-fd]{3}){1,2})$/i', $color)) {
			$return =true;
		}
		return $return;
	}

	/**
	 * @TODO delete that after verifying that is not used by plugin or core! 
	 * Définis si la chaine passée en parametre est un mail ou non
	 */
	public static function isMail($mail){
		$return =false;
		if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
			$return =true;
		}
		return $return;
	}

	/**
	 * @TODO delete that after verifying that is not used by plugin or core! 
	 * Définis si la chaine passée en parametre est une IP ou non
	 */
	public static function isIp($ip){
		$return =false;
		if (preg_match('^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$',$ip)) {
			$return =true;
		}
		return $return;
	}

	public static function makeCookie($name, $value,$expire) {
		setcookie($name,$value,$expire,'/');
	}

	public static function destroyCookie($name){
		setcookie(COOKIE_NAME, "", time()-3600,"/");
	}

	public static function convertFileSize($bytes)
	{
		if($bytes<1024){
			return round(($bytes / 1024), 2).' o';
		}elseif(1024<$bytes && $bytes<1048576){
			return round(($bytes / 1024), 2).' ko';
		}elseif(1048576<$bytes && $bytes<1073741824){
			return round(($bytes / 1024)/1024, 2).' Mo';
		}elseif(1073741824<$bytes){
			return round(($bytes / 1024)/1024/1024, 2).' Go';
		}
	}

	//Calcul une adresse relative en fonction de deux adresse absolues
	public static function relativePath($from, $to, $ps = '/') {
		$arFrom = explode($ps, rtrim($from, $ps));
		$arTo = explode($ps, rtrim($to, $ps));
		while(count($arFrom) && count($arTo) && ($arFrom[0] == $arTo[0])) {
			array_shift($arFrom);
			array_shift($arTo);
		}
		return str_pad("", count($arFrom) * 3, '..'.$ps).implode($ps, $arTo);
	}

	//Transforme une date en timestamp
	
	public static function totimestamp($date,$delimiter='/')
	{
		$explode=explode($delimiter,$date);
		return strtotime($explode[1].'/'.$explode[0].'/'.$explode[2]);


	}

	public static function goback($page,$section="",$param="")
	{
		if ($section == "")
		{
			header('location:'.$page.'.php '.$param);
		}
		else
		{
			header('location:'.$page.'.php?section='.$section.$param);
		}
		
	}

	public static function rmFullDir($path){
		$files = array_diff(scandir($path), array('.','..'));
	    foreach ($files as $file) {
	      (is_dir("$path/$file")) ? Functions::rmFullDir("$path/$file") : unlink("$path/$file");
	    }
	    return rmdir($path); 
	}
	
	public static function log($message,$type = 'notice'){
	$message = date('d-m-Y H:i:s').' - ['.$type.'] :'.$message.PHP_EOL;
	if(!file_exists(LOG_FILE)) touch(LOG_FILE);
		$linecount = 0;
		$handle = fopen(LOG_FILE, "r");
		while(fgets($handle)!=false){
		  $linecount++;
		}
		fclose($handle);
		if($linecount>1000) unlink(LOG_FILE);
		file_put_contents(LOG_FILE,$message,FILE_APPEND);
	}

	public static function alterBase($versions,$current){
		$manager = new User();
		foreach($versions as $version){
			if($version['version'] <= $current) continue;
			set_error_handler('Functions::alterBaseError');
			foreach($version['sql'] as $command){
				$sql = str_replace(array('{PREFIX}'), array(MYSQL_PREFIX), $command);
				Functions::log('Execute alter base query: '.$sql);
				$manager->customQuery($sql);
			}
		 	restore_error_handler();
		}
	}

	public static function alterBaseError($errno, $errstr, $errfile, $errline){
		self::log("Erreur update sql :  [$errno] $errstr L$errline dans le fichier $errfile");
	}

	public static function array_rand($array){
		return $array[array_rand($array)];
	}
	

	public static function tail($filepath, $lines = 1, $adaptive = true) {
		$f = @fopen($filepath, "rb");
		if ($f === false) return false;
		if (!$adaptive) $buffer = 4096;
		else $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));
		fseek($f, -1, SEEK_END);
		if (fread($f, 1) != "\n") $lines -= 1;
		$output = '';
		$chunk = '';
		while (ftell($f) > 0 && $lines >= 0) {
		$seek = min(ftell($f), $buffer);
		fseek($f, -$seek, SEEK_CUR);
		$output = ($chunk = fread($f, $seek)) . $output;
		fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);
		$lines -= substr_count($chunk, "\n");
		}
		while ($lines++ < 0) {
		$output = substr($output, strpos($output, "\n") + 1);
		}
		fclose($f);
		return trim($output);
	} 



}
?>
