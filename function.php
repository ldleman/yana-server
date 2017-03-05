<?php
function secondToTime($seconds) {
  $t = round($seconds);
  return sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);
}
function app_autoloader($class_name) {
		require_once('class/'.$class_name.'.class.php');
}
function errorToException( $errno, $errstr, $errfile, $errline, $errcontext)
{
	if(strpos($errstr,'disk_')!==false) return;
	throw new ErrorException($errstr, 0, $errno, $errfile, $errline);   
}

function unhandledException($ex){
	exit('<div id="message" class="alert alert-danger"><strong>Erreur</strong> <span>'.$ex->getMessage().'  -  '.$ex->getFile().' L'.$ex->getLine().'</span></div>');
}

function slugify($text)
	{ 
	  // replace non letter or digits by -
	  $text = preg_replace('~[^\\pL\.\d]+~u', '-', $text);
	  // trim
	  $text = trim($text, '-');
	  // transliterate
	  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
	  // lowercase
	  $text = strtolower($text);
	  // remove unwanted characters
	  $text = preg_replace('~[^-\.\w]+~', '', $text);

	  if (empty($text))
	    return 'n-a';
	  return $text;
}
function secure_user_vars($var){
	if(is_array($var)){
		$array = array();
		foreach($var as $key=>$value):
			$array[secure_user_vars($key)] = secure_user_vars($value);
		endforeach;
		return $array;
	}else{
		return htmlspecialchars($var, ENT_NOQUOTES, "UTF-8");
	}
}

function base64_to_image($base64_string, $output_file) {
    $ifp = fopen($output_file, "wb"); 
    $data = explode(',', $base64_string);
    fwrite($ifp, base64_decode($data[1])); 
    fclose($ifp); 
    return $output_file; 
}

function getExt($file){
	$ext = explode('.',$file);
	return strtolower(array_pop($ext));
}

function getGravatar($mail,$size = 100){
	return  file_get_contents("http://www.gravatar.com/avatar/" . md5( strtolower( trim( $mail ) ) ) . "?&s=".$size);
}

function getExtIcon($ext){
	$icon = '';
	switch($ext){
		case '7z':
		case 'rar':
		case 'gz':
		case 'zip':
			$icon = 'fa-file-archive-o';
		break;
		
		case 'php':
		case 'js':
		case 'py':
		case 'c':
		case 'cpp':
		case 'css':
		case 'h':
		case 'hpp':
		case 'html':
		case 'htm':
		case 'asp':
		case 'jsp':
			$icon = 'fa-file-code-o';
		break;
		
		case 'xls':
		case 'xlsx':
		case 'csv':
			$icon = 'fa-file-excel-o';
		break;
		
		case 'bmp':
		case 'jpg':
		case 'jpeg':
		case 'ico':
		case 'gif':
		case 'png':
		case 'svg':
			$icon = 'fa-file-image-o';
		break;
		
		case 'pdf':
			$icon = 'fa-file-pdf-o';
		break;
		case 'ppt':
		case 'pptx':
			$icon = 'fa-file-powerpoint-o';
		break;
		
		case 'txt':
		case 'htaccess':
		case 'md':
			$icon = 'fa-file-text-o';
		break;
		
		case 'doc':
		case 'docx':
		case 'word':
			$icon = 'fa-file-word-o';
		break;
		
		case 'avi':
		case 'wmv':
		case 'mov':
		case 'divx':
		case 'xvid':
		case 'mkv':
		case 'flv':
		case 'mpeg':
		case 'h264':
		case 'rmvb':
		case 'mp4':
			$icon = 'fa-file-movie-o';
		break;
		
		case 'wav':
		case 'ogg':
		case 'ogv':
		case 'ogx':
		case 'oga':
		case 'riff':
		case 'bwf':
		case 'wma':
		case 'flac':
		case 'aac':
		case 'mp3':
			$icon = 'fa-file-audio-o';
		break;
		default:
			$icon = 'fa-file-o';
		break;
	}
	return $icon;
};

function max_upload_size($limits = array()){
	$limits[]= str_replace('M','',ini_get('post_max_size')) *1048576;
	$limits[]= str_replace('M','',ini_get('upload_max_filesize')) *1048576;
	return readable_size(min($limits));
}

function readable_size($bytes)
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


function imageResize($image,$w,$h){
	$resource = imagecreatefromstring(file_get_contents($image));
	$size = getimagesize($image);
	$h = (($size[1] * (($w)/$size[0])));
	$thumbnail = imagecreatetruecolor($w , $h);
	imagecopyresampled($thumbnail ,$resource, 0,0, 0,0, $w, $h, $size[0],$size[1]);
	imagedestroy($resource);
	imagejpeg($thumbnail , $image, 100);
}





?>