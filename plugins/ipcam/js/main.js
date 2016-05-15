


//Ajout / Modification
function plugin_ipcam_save(element){
	var form = $(element).closest('fieldset');
 	var data = form.toData();
 	data.action = 'ipcam_save_camera'
	$.action(data,
		function(response){
			alert(response.message);
			form.find('input').val('');
			location.reload();
		}
	);
}

//Supression
function plugin_ipcam_delete(id,element){

	if(!confirm('Êtes vous sûr de vouloir faire ça ?')) return;
	$.action(
		{
			action : 'ipcam_delete_camera', 
			id: id
		},
		function(response){
			$(element).closest('tr').fadeOut();
		}
	);

}

function plugin_ipcam_brand(element){
	$('#patternCamera').val($(element).val());
}