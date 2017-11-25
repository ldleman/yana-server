<?php

class Debug{
	
	public static function loadAll(){
		$debugs = array();


			$debugs['command'] = (object) array('label'=>'Test de commande serveur','execute'=>
				function(){
					
					echo 'Commande lancée avec l\'utilisateur : '.get_current_user().PHP_EOL;
		
					echo 'Test ls -l (non silencieux)'.PHP_EOL;
					echo System::command('ls -ls');

					echo 'Test ls -l (silencieux)'.PHP_EOL;
					echo System::commandSilent('ls -ls');

					echo 'Get system infos'.PHP_EOL;
					print_r(System::getInfos());
					
					
					echo 'Done';
				});


			$debugs['connect'] = (object) array('label'=>'Test de connexion','execute'=>
				function(){
					$client = new Client;
					echo 'Connexion'.PHP_EOL;
					$client->connect();
					echo 'Connexion réussie'.PHP_EOL;
					$client->disconnect();
					echo 'Déconnexion';
				});

				$debugs['talk'] = (object) array('label'=>"Test de parole",'execute'=>
				function(){
					$client = new Client;
					echo 'Connexion'.PHP_EOL;
					$client->connect();
					$text = "Bonjour, je suis YANA, omnisciente, omnipotente. Je suis l'alpha et l'omega, le Yin et le Yang, la vie et la mort.";
					echo 'Envois du texte :'.$text.PHP_EOL;
					echo  $client->talk($text).PHP_EOL;
					echo 'Parole envoyée'.PHP_EOL;
					$client->disconnect();
					echo 'Déconnexion';
				});

				$debugs['command'] = (object) array('label'=>"Test de commande",'execute'=>
				function(){
					$client = new Client;
					echo 'Connexion'.PHP_EOL;
					$client->connect();
					$command = "ls -l";
					echo 'Envois de la commande :'.$command.PHP_EOL;
					echo  $client->execute($command).PHP_EOL;
					echo 'commande envoyée'.PHP_EOL;
					$client->disconnect();
					echo 'Déconnexion';
				});

				$debugs['others'] = (object) array('label'=>'Test de son, image, execution, emotion','execute'=>
				function(){
					$client = new Client;
					echo 'Connexion'.PHP_EOL;
					$client->connect();
					
					echo 'Envois son :'.PHP_EOL;
					echo  $client->sound('C:\Users\Idleman\Music\Musique\Kalimba.mp3').PHP_EOL;
					echo 'Son envoyé'.PHP_EOL;

					echo 'Envois image :'.PHP_EOL;
					echo  $client->image('http://blog.idleman.fr/wp-content/themes/twentytwelve/idleblog%20logo.png').PHP_EOL;
					echo 'image envoyée'.PHP_EOL;

					echo 'Envois commande shell :'.PHP_EOL;
					echo  $client->execute('explorer').PHP_EOL;
					echo 'Commande envoyée'.PHP_EOL;

					echo 'Envois émotion :'.PHP_EOL;
					echo  $client->emotion('angry').PHP_EOL;
					echo 'Emotion envoyée'.PHP_EOL;

					
					$client->disconnect();
					echo 'Déconnexion';
				});

				$debugs['commands'] = (object) array('label'=>'Récupération des commandes (GET_SPEECH_COMMANDS via socket)','execute'=>
				function(){
					global $myUser;
					$client = new Client;
					echo 'Connexion'.PHP_EOL;
					$client->connect();

					$auth = array(
						"action"=>"CLIENT_INFOS",
						"version"=>"2","type"=>"ear",
						"location"=>"moon",
						"token"=>$myUser->getToken()
					);

					

					echo 'Authentification via : '.json_encode($auth).PHP_EOL;
					$client->send($auth);
					$cmds = array("action"=>"GET_SPEECH_COMMANDS");
					echo 'Ordre de récuperation des commandes via : '.json_encode($cmds).PHP_EOL;
					echo PHP_EOL.'==================================== Réponse ================================='.PHP_EOL.PHP_EOL;
					echo $client->send($cmds,true);
					$client->disconnect();
					echo 'Déconnexion';
				});

				$debugs['clients'] = (object) array('label'=>'Récupération des clients connectés (GET_CONNECTED_CLIENTS via socket)','execute'=>
				function(){
					global $myUser;
					$client = new Client;
					echo 'Connexion'.PHP_EOL;
					$client->connect();

					$auth = array(
						"action"=>"CLIENT_INFOS",
						"version"=>"2","type"=>"face",
						"location"=>"moon",
						"token"=>$myUser->getToken()
					);

					echo 'Authentification via : '.json_encode($auth).PHP_EOL;
					$client->send($auth);
					$cmds = array("action"=>"GET_CONNECTED_CLIENTS");
					echo 'Ordre de récuperation des commandes via : '.json_encode($cmds).PHP_EOL;
					echo PHP_EOL.'==================================== Réponse ================================='.PHP_EOL.PHP_EOL;
					echo $client->send($cmds,true).PHP_EOL;;
					$client->disconnect();
					echo 'Déconnexion';
				});

		return $debugs;
	}
}

?>