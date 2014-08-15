<?php
	class Action{
		public static function write($f,$p){
			global $myUser,$_,$conf;
			header('content-type:application/json');
				
			$response = array('errors' => array());
			try{
				foreach ($p as $section => $right) {
					if(!$myUser->can($section,$right)) throw new Exception('permission denied');
				}
				$f($_,$response);
			}catch(Exception $e){
				$response['errors'][] = $e->getMessage();
			}
			echo json_encode($response);
		}
	}
?>