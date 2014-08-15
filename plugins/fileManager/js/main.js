$(document).ready(function(){
	init();
});

function init(){

	$('.dropzone').upload({
		url:'action.php?action=upload',
		success : plugin_filemanager_upload_response
	});

	plugin_filemanager_search();
	$('#keyword').enter(function(){
		plugin_filemanager_search();
	});
}


function plugin_filemanager_upload_response(file,json){
	 var tpl = $(file.previewTemplate);
			      if (json.error.length == 0){
				      tpl
				      .find('.dz-filename')
				      .html('<a href="'+json.file.url+'"><i class="fa fa-file-text-o"></i> '+json.file.name+'</a>');
				      tpl
				      .find('.dz-options').html('<ul class="dz-options"><li title="Envoyer par email" onclick="plugin_filemanager_send_mail('+json.file.id+');"><i class="fa fa-paper-plane-o"></i></li><li onclick="plugin_filemanager_set_permission("'+json.file.id+'");" title="Récuperer le lien direct"><i class="fa fa-link"></i></li><li><i class="fa fa-times"></i></li></ul>');
				      tpl
				      .find('.dz-tags')
				      .html('<span class="dz-tag label">Tags</span> <span class="dz-tag label label-inverse">'+json.file.tags.join('</span> <span class="dz-tag label label-inverse">')+'</span>');    
				 		tpl
				      .find('.dz-size')
				      .html('<strong>'+json.file.size+'</strong>');    
				  }else{
				  	  tpl
				      .find('.dz-details').html(json.error.join('<br/>'));
				      tpl
				      .find('.dz-upload').attr('style','width:100%;background: none repeat scroll 0 0 #e72121;box-shadow: 0 0 5px 1px #e40404;');
				  }
}



function plugin_filemanager_search(){
	$('#plugin_filemanager_list').load('action.php?action=plugin_filemanager_search&keyword='+$('#keyword').val());
}

function plugin_filemanager_set_permission_form(id){
		$.ajax({
		url : 'action.php',
		method : 'POST',
		data : {'action' : 'plugin_filemanager_set_permission_form' , 'id' : id},
		success: function(result){
			modal('Permissions du fichier',result,function(){
				$('#recipient').focus();
			},580,'<div onclick="plugin_filemanager_set_permission('+id+')" class="btn btn-primary">Enregistrer</div>');
		}
	});

}


function plugin_filemanager_set_permission(id){
		$.ajax({
			url : 'action.php',
			method : 'POST',
			data : {
				action : 'plugin_filemanager_set_permission' ,
				id : id, 
				allow_internal : $('#allow_internal').is(':checked'),
				allow_all : $('#allow_all').is(':checked'),
				allow_user : $('#allow_user').val()
			},
			success: function(result){
				
				if(result.success)
					$('#modalWindow').modal('hide');
			}
		});
}

function plugin_filemanager_send_mail_form(id){
	$.ajax({
		url : 'action.php',
		method : 'POST',
		data : {'action' : 'plugin_filemanager_send_email_form' , 'id' : id},
		success: function(result){
			modal('Envoyer le fichier par e-mail',result,function(){
				$('#recipient').focus();
			},580,'<div onclick="plugin_filemanager_send_mail('+id+')" class="btn btn-primary">Envoyer</div>');
		}
	});
}

function plugin_filemanager_send_mail(id){
		$.ajax({
			url : 'action.php',
			method : 'POST',
			data : {
				action : 'plugin_filemanager_send_mail' ,
				id : id, 
				recipient : $('#recipient').val(),
				message : $('#message').val()
			},
			success: function(result){
				if(result.success)
					$('#modalWindow').modal('hide');
			}
		});
}

function plugin_filemanager_delete(id){
	if(confirm('Veuillez confirmer la supression.')){
		$.ajax({
			url : 'action.php',
			method : 'POST',
			data : {'action' : 'plugin_filemanager_delete' , 'id' : id},
			success: function(result){
				if(result.success)
					$('#file_'+id).fadeOut();
			}
		});
	}
}