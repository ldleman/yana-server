$(document).ready(function(){



});




function plugin_wirerelay_set_icon(element,icon){
	$(element).parent().find('i').removeClass('btn-success');
	$('#iconWireRelay').val(icon);
	$(element).addClass('btn-success');
}

//Ajout / Modification
function plugin_wirerelay_save(element){
	var form = $(element).closest('fieldset');
 	var data = form.toData();
 	data.action = 'wireRelay_save_wireRelay'
	$.action(data,
		function(response){
			alert(response.message);
			form.find('input').val('');
			location.reload();
		}
	);
}

//Supression
function plugin_wirerelay_delete(id,element){

	if(!confirm('Êtes vous sûr de vouloir faire ça ?')) return;
	$.action(
		{
			action : 'wireRelay_delete_wireRelay', 
			id: id
		},
		function(response){
			$(element).closest('tr').fadeOut();
		}
	);

}