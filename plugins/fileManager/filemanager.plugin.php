<?php
/*
@name Manager Fichier
@author Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@description Gestion des fichiers et medias
*/


function plugin_filemanager_plugin_menu(&$menuItems){
	global $_,$myUser;

		$menuItems[] = array('sort'=>10,'content'=>'<a href="index.php?module=filemanager&title=Fichiers"><i class="fa fa-upload"></i> Fichiers</a>');
	
}


function plugin_filemanager_plugin_actions(){
	global $myUser,$_,$conf;
	switch($_['action']){

	case 'plugin_filemanager_get':
		if(!$myUser->can('file','r')) exit('permission denied');
			$files = array();
			echo json_encode($files);
	break;

	case 'plugin_filemanager_edit':
		if(!$myUser->can('file','u')) exit('permission denied');
		
		require_once('plugins/file/FileUploaded.class.php');
		$current = new FileUploaded();
		$current->date = time();
		$current->user = $myUser->getLogin();
		$current = isset($_['id']) && $_['id']!='' ? $current->getById($_['id']) : $current ;
		$current->save();

	break;




	case "plugin_filemanager_plugin_setting":
            if($myUser->can('plugin_filemanager_validation','u')){
               // $conf->put("plugin_filemanager_plugin_recipients",$_['mail']); // on enregistre le contenu des champs (adresses mails de destination) dans la bdd 
               // $conf->put("plugin_filemanager_plugin_users",json_encode($_['users']));
                echo 'Enregistré.';
            }else{
             echo 'Permissions insuffisantes.';
            }
    break;
	case 'plugin_filemanager_delete':
		header('Content-type: application/json');
		if($myUser->can('file','d')){
			require_once('plugins/file/FileUploaded.class.php');
			$response = array();
			try{
			$current = new FileUploaded();
			$current = $current->getById($_['id']);
			@unlink($current->path);
			if($_['id']!=0)$current->delete(array('id'=>$_['id']));
			$response['success'] = true;
			}catch(Exception $e){
				$response['message'] = 'Erreur : '.$e->getMessage();
			}
			echo json_encode($response);
		}
	break;
	case 'plugin_filemanager_send_email_form':
		require_once('plugins/file/FileUploaded.class.php');

		$file =  new FileUploaded();
		if(isset($_['id'])){
			$file = $file->getById($_['id']);

			if($file->name !=''){

				if($file->checkPermission($myUser)){

					echo '<div class="label label-important">Attention, les permissions de de fichier seront modifiées en "acces public".</div><br/><br/>Destinaire : <input type="text" id="recipient" value=""><br/>

						  Fichier : <a target="_blank" href="'.$file->getUrl().'">'.$file->name.'</a><br/>
						  <br/>Message<br/><textarea style="width:520px;" id="message">Veuillez trouver en pièce jointe le fichier : '.$file->name.'</textarea><br/>';
				}
			}
		}
	break;

	case 'plugin_filemanager_set_permission_form':
		require_once('plugins/file/FileUploaded.class.php');

		$file =  new FileUploaded();
		if(isset($_['id'])){
			$file = $file->getById($_['id']);

			if($file->name !=''){

				if($file->checkPermission($myUser)){
				
					$permissions = $file->getPermissions();

				echo "
					<label class='checkbox'><input id='allow_internal' ".(in_array('$', $permissions)?'checked=checked':'')." type='checkbox'> Autoriser tous les utilisateurs ERP</label>
					<label class='checkbox'><input id='allow_all' ".(in_array('*', $permissions)?'checked=checked':'')." type='checkbox'> Autoriser tous le monde (même exterieur a l'erp)</label>
					<label>Autoriser les utilisateurs suivants (séparés par saut de ligne) </label>

					<textarea  style='width:510px;' placeholder='valentin.carruesco...' id='allow_user'>";

					foreach ($permissions as $permission) {
						if($permission != '*' && $permission!='$'){
							echo $permission."\n";
						}
					};
					
					echo "</textarea>";
				}
			}
		}
	break;


	case 'plugin_filemanager_set_permission':
		header('Content-type: application/json');
		require_once('plugins/file/FileUploaded.class.php');
		$response = array();
		$file =  new FileUploaded();
		if(isset($_['id'])){
			$file = $file->getById($_['id']);

			if($file->name !=''){
				$permissions = explode("\n",$_['allow_user']);
				if($_['allow_all']=='true') $permissions[] = '*';
				if($_['allow_internal']=='true') $permissions[] = '$';
				$file->setPermissions($permissions); 
				$file->save();
				$response['success'] = true;
			}
		}
		echo json_encode($response);
	break;

	case 'plugin_filemanager_send_mail':
	header('Content-type: application/json');
	require_once('plugins/file/FileUploaded.class.php');
		$response = array();
		$file =  new FileUploaded();
		if(isset($_['id']) && isset($_['recipient'])){
			$file = $file->getById($_['id']);

			if($file->name !=''){
				if($file->checkPermission($myUser)){
					$file->addPermission('*'); 
					$file->save();

					$mail = new Mail();

					$mail->disableBorder();
					$mail->setExpeditor('"'.$myUser->getFullName().'" <'.$myUser->getMail().'>');
					$mail->setReply('"'.$myUser->getFullName().'" <'.$myUser->getMail().'>');
					$mail->addRecipient($_['recipient']);
					$mail->addAttachment($file->name.'|'.$file->mime,file_get_contents($file->path));
					$mail->setTitle("Pièce jointe - ".$file->name,false);
					$mail->setMessage($_['message']);
					$mail->send();
					$response['success'] = true;
				}
			}
		}
		echo json_encode($response);
	break;

	case 'plugin_filemanager_search':
		require_once('plugins/file/FileUploaded.class.php');
		$fileManager =  new FileUploaded();

		$keyword = isset($_['keyword']) && $_['keyword']!='' ? $_['keyword'] : '';
		$files = $fileManager->search($keyword);

		foreach($files as $file){
			if($file->checkPermission($myUser)){
			?>
			<div id="file_<?php echo $file->id; ?>" class="dz-preview dz-file-preview"> 
			  <div class="dz-details"> 
			    <div class="dz-filename"><a href="<?php echo $file->getUrl(); ?>"><i class="fa fa-file-text-o"></i> <?php echo $file->name; ?></a></div> - 
			    <div class="dz-size" data-dz-size><strong><?php echo $file->getSize(); ?></strong></div> 
			    <div class="dz-options"><ul class="dz-options"><li title="Envoyer par email" onclick="plugin_filemanager_send_mail_form(<?php echo $file->id; ?>);"><i class="fa fa-paper-plane-o"></i></li><li onclick="plugin_filemanager_set_permission_form('<?php echo $file->id; ?>');" title="Régler les permissions"><i class="fa fa-check-square-o"></i></li><li title="Supprimer" onclick="plugin_filemanager_delete(<?php echo $file->id; ?>)"><i class="fa fa-times"></i></li></ul></div> 
			    <div class="dz-tags"><span class="dz-tag label">Tags</span> <span class="dz-tag label label-inverse"><?php echo implode('</span> <span class="dz-tag label label-inverse">',$file->getTags()); ?></span></div> 
			  </div> 
			</div>
			<?php
			}
		}
	break;

	case 'open_file':
		require_once('plugins/file/FileUploaded.class.php');
		$file =  new FileUploaded();
		if(isset($_['file'])){
			$file = $file->getById($_['file']);
			if($file->name !=''){

				if($file->checkPermission($myUser)){
					header('Content-type: '. $file->mime);
		    		header('Content-Transfer-Encoding: binary');
		    		header('Expires: 0');
		   	 		header('Cache-Control: must-revalidate');
		    		header('Pragma: public');;
		    		ob_clean();
		    		flush();
					echo file_get_contents( $file->path);
				}else{
					echo 'Acces interdit';
				}
			}
		}
	break;



	case 'upload':
		require_once('plugins/file/FileUploaded.class.php');
		header('Content-type: application/json');
		$response = array('error'=>array());

		try{
			$max_size = 419430400;
			$allowed_ext = array('jpg','png','bmp','psd','doc','docx','xls','xlsx','mp3','mp4','ppt','txt','sql','pptx');
			$file = new FileUploaded($_FILES['file']);
			
			if(!$file->check('size',$max_size)) throw new Exception('Taille maximum dépassée, (autorisé : '.$max_size.' octets max) ');
			if(!$file->check('extension',$allowed_ext))  throw new Exception('Extension non permise (autorisé : '.implode(',',$allowed_ext).')');
			
			$file->user = $myUser->getLogin();
			$file->permissions = '*';

			if(!$file->upload()) throw new Exception('Erreur lors de l\'envoi, merci de contacter un administrateur');
		
			$response['file'] = array(
			    	'id' => $file->id,
			    	'url' => $file->url,
			    	'name' => $file->name,
			    	'ext' => $file->ext,
			    	'tags' => $file->getTags(),
			    	'size' => $file->getSize()
			    	);

		}catch(Exception $e){
			$response['error'][] = $e->getMessage();
		}
		echo json_encode($response);
	break;


	}
}


function plugin_filemanager_plugin_setting_menu(){ // partie congé dans le plugin configuration
    global $_;
    global $myUser;
    if($myUser->can('plugin_filemanager_validation','r')){
        echo '<li '.(@$_['block']=='file'?'class="active"':'').'><a href="setting.php?section=preference&block=file"><i class="icon-chevron-right"></i> file</a></li>';
    }
}

function plugin_filemanager_plugin_setting_page() // partie congé dans le plugin configuration
{
    global $_,$conf;
    global $myUser;
    
    if((isset($_['section']) && $_['section']=='preference' && @$_['block']=='file' ))
    {
        if($myUser->can('plugin_filemanager_validation','r')){
        ?>
            <!-- début du formulaire pour entrer les adresses mails de réception des mails pour le plugin congé-->
            <div class="span9 userBloc">
                <div class="form-inline">

                </div>
            </div>
            <!-- fin du formulaire-->
        <?php   
        }
    }
} 





function plugin_filemanager_plugin_page($_){

	if(isset($_['module']) && $_['module'] == 'filemanager' ){
		global $myUser,$conf;
		if(!$myUser->can('file','r')) exit('permission denied');
	?>

		<h4 style="display:inline;">Fichiers</h4>
		<div class="dropzone"></div>

		<label for="keyword">Rechercher un fichier</label> 
		<div class="input-append">
	    	<input class="span2" id="keyword" placeholder="ma recherche ici..." type="text">
	    	<button onclick="plugin_filemanager_search();" class="btn" type="button"><i class="fa fa-search"></i></button>
	    </div>

		<div id="plugin_filemanager_list"></div>
		<?php 
	}
}

function plugin_filemanager_plugin_dashboard(){
	global $_,$myUser;
	if($myUser->can('file','r')){
		
	?>
    <li style="cursor:pointer;" onclick="window.location='index.php?module=filemanager';" class="span2">
	    <div class="thumbnail">
		    <img src="./plugins/file/img/file.png">
		    <h4>Module fichier</h4>
		    <p>Gestion des fichiers et de médias.</p>
	    </div>
    </li>
	<?php
	}
}




Plugin::addJs("/js/main.js"); 

Plugin::addHook("menubar_pre_home", "plugin_filemanager_plugin_menu");  
Plugin::addHook("home", "plugin_filemanager_plugin_page");  
Plugin::addHook("action_post_case", "plugin_filemanager_plugin_actions");
Plugin::addHook("preference_content", "plugin_filemanager_plugin_setting_page");
Plugin::addHook("preference_menu", "plugin_filemanager_plugin_setting_menu"); 
Plugin::addHook("dashboard", "plugin_filemanager_plugin_dashboard");
?>