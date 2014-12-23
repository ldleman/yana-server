$(document).ready(function(){



});

//Ajout / Modification
function plugin_wirerelay_save(element){

 	var data = $(element).closest('fieldset').toData();
 	data.action = 'wireRelay_save_wireRelay'
	$.action(data,
		function(response){
			alert(response.message);
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