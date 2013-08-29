<?php

class Functions
{
	private $id;
	public $debug=0;

	/**
	 * Securise la variable utilisateur entrée en parametre
	 * @author Valentin
	 * @param<String> variable a sécuriser
	 * @param<Integer> niveau de securisation
	 * @return<String> variable securisée
	 */

	public static function secure($var){
		return addslashes(htmlspecialchars($var, ENT_QUOTES, "UTF-8"));
	}

	/**
	 * Recupere l'ip de l'internaute courant
	 * @author Valentin
	 * @return<String> ip de l'utilisateur
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
	 * Retourne une version tronquée au bout de $limit caracteres de la chaine fournie
	 * @author Valentin
	 * @param<String> message a tronquer
	 * @param<Integer> limite de caracteres
	 * @return<String> chaine tronquée
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
	 * Definis si la chaine fournie est existante dans la reference fournie ou non
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
}
?>
