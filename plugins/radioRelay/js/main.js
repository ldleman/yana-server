$(document).ready(function(){



});




function plugin_radiorelay_set_icon(element,icon){
	$(element).parent().parent().find('i').removeClass('btn-success');
	$('#iconRadioRelay').val(icon);
	$(element).addClass('btn-success');
}


function plugin_radiorelay_save_settings(element){
	var form = $(element).parent();
 	var data = form.toData();
 	data.action = 'radioRelay_plugin_setting'
	$.action(data,
		function(response){
			alert(response.message);
			location.reload();
		}
	);
}

//Ajout / Modification
function plugin_radiorelay_save(element){
	var form = $(element).closest('fieldset');
 	var data = form.toData();
 	data.action = 'radioRelay_save_radioRelay'
	$.action(data,
		function(response){
			alert(response.message);
			form.find('input').val('');
			location.reload();
		}
	);
}

//Supression
function plugin_radiorelay_delete(id,element){

	if(!confirm('Êtes vous sûr de vouloir faire ça ?')) return;
	$.action(
		{
			action : 'radioRelay_delete_radioRelay', 
			id: id
		},
		function(response){
			$(element).closest('tr').fadeOut();
		}
	);

}