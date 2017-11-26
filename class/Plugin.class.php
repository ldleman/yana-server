<?php

/**
* Manage plugin système trhought php hooks
* @author valentin carruesco
* @category Core
* @license copyright
*/

class Plugin{
	public $id,$name,$author,$link,$licence,$folder,$description,$version,$state,$type,$require;

	
	function __construct(){
		$this->state = false;
	}
	public static function exist($id){
		foreach(self::getAll() as $plugin)
			if($plugin->id==$id) return true;

		return false;
	}
	public static function addHook($hookName, $functionName){
		$GLOBALS['hooks'][$hookName][] = $functionName;  
	}
	public static function callHook($hookName, $hookArguments = array()) {
		if(!isset($GLOBALS['hooks'][$hookName])) return;
		foreach($GLOBALS['hooks'][$hookName] as $functionName)
			call_user_func_array($functionName, $hookArguments);  
	}
	
	public static function includeAll(){
		foreach(self::getAll() as $plugin):
			if(!$plugin->state) continue;
		$main = $plugin->path().SLASH.$plugin->folder.'.plugin.php';
		if(file_exists($main))
			require_once($main);
		endforeach;
	}
	
	public static function getAll(){
		$enabled = self::states();
		$plugins = array();
		foreach(glob(__ROOT__.PLUGIN_PATH.'*'.SLASH.'app.json') as $file)
			$plugins[] = self::parseManifest($file);

		usort($plugins, function($a, $b){
			if ($a->name == $b->name) 
				$result = 0;

			if($a->name < $b->name){
				$result = -1;
			} else{
				$result = 1;
			}
			return  $result;
		});
		return $plugins;
	}
	
	public static function getById($id){
		$plugin = false;
		foreach(self::getAll() as $onePlugin)
			if($onePlugin->id==$id) $plugin = $onePlugin;
		return $plugin;
	}
	
	public static function parseManifest($file){
		$enabled = self::states();
		$plugin = new self();
		$manifest = json_decode(file_get_contents($file),true);
		if(!$manifest) return $plugin;
		if(!isset($manifest['id']) || !isset($manifest['name']) ) return;
		$plugin->name = $manifest['name'];
		$plugin->id = $manifest['id'];
		$plugin->folder = basename(dirname($file));
		if(in_array($plugin->id,$enabled)) $plugin->state = true;
		if(isset($manifest['author'])) $plugin->author = $manifest['author'];
		if(isset($manifest['url'])) $plugin->url = $manifest['url'];
		if(isset($manifest['licence'])) $plugin->licence = $manifest['licence'];
		if(isset($manifest['description'])) $plugin->description = $manifest['description'];
		if(isset($manifest['version'])) $plugin->version = $manifest['version'];
		if(isset($manifest['core'])) $plugin->core = $manifest['core'];
		if(isset($manifest['require'])) $plugin->require = $manifest['require'];
		return $plugin;
	}
	
	public static function state($id,$state){
		$enabled = self::states();
		
		$plugin = self::getById($id);
		
		$main = $plugin->path().SLASH.$plugin->folder.'.plugin.php';
		if(file_exists($main))
			require_once($main);
		
		
		if($state==0){
			$key = array_search($plugin->id, $enabled);
			
			if($key  !== false)
				unset($enabled[$key]);
			
			plugin::callHook('uninstall',array($plugin->id));
		}else{
			if(!in_array($plugin->id,$enabled))
				$enabled[] = $plugin->id;
			plugin::callHook('install',array($plugin->id));
		}
		
		self::states($enabled);
	}
	
	public static  function states($states = false){
		$enabledFile = __ROOT__.PLUGIN_PATH.'enabled.json';
		if(!file_exists($enabledFile)) touch($enabledFile);
		if(!is_array($states)){
			$enabled = json_decode(file_get_contents($enabledFile),true);
			return !is_array($enabled)?array():$enabled;
		}
		file_put_contents($enabledFile,json_encode($states));
	}
	
	public function path(){
		return __ROOT__.PLUGIN_PATH.$this->folder;
	}
	

	public static function url(){
		$bt =  debug_backtrace();
		return ROOT_URL.SLASH.PLUGIN_PATH.basename(dirname($bt[0]['file']));
	}


	public static function addCss($css) {  
		$bt =  debug_backtrace();
		$GLOBALS['hooks']['css_files'][] = str_replace(__ROOT__,'',dirname($bt[0]['file'])).$css;  
	}

	public static function callCss(){
		if(!isset($GLOBALS['hooks']['css_files'])) return '';
		$stream = '';
		foreach($GLOBALS['hooks']['css_files'] as $css_file) 
			$stream .='<link rel="stylesheet" href="'.$css_file.'">'."\n";
		return $stream;
	}
	
	public static function addJs($js){  
		global $_;
		$bt =  debug_backtrace();
		$GLOBALS['hooks']['js_files'][] = str_replace(__ROOT__,'',dirname($bt[0]['file'])).$js;  
	}

	public static function callJs(){
		if(!isset($GLOBALS['hooks']['js_files'])) return '';
		$stream = '';
		foreach($GLOBALS['hooks']['js_files'] as $js_file)
			$stream .='<script type="text/javascript" src="'.$js_file.'"></script>'."\n";
		return $stream;
	}
	
}

?>