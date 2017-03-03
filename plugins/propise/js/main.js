
$(document).ready(function(){
Chart.defaults.global.responsive = true;

	$('canvas[id^="chart_"]').each(function(i,element){

		var graphic = new Chart($(element)[0].getContext("2d"));
		var conf = {
							labels : $(element).data('hours'),
							datasets:[
							{
								label : $(element).data('label'),
								fillColor : "rgba(220,220,220,0.2)",
								strokeColor : "#FCB150",
								data : $(element).data('data')
							}
							]
						};
		graphic.Line(conf);
	});
	

		
	


		
})

//Ajout / Modification
function plugin_propise_save(element){
	var form = $(element).closest('fieldset');
 	var data = form.toData();
 	data.action = 'propise_save_sensor'
	$.action(data,
		function(response){
			alert(response.message);
			form.find('input').val('');
			location.reload();
		}
	);
}

//Supression
function plugin_propise_delete(id,element){

	if(!confirm('Êtes vous sûr de vouloir faire ça ?')) return;
	$.action(
		{
			action : 'propise_delete_sensor', 
			id: id
		},
		function(response){
			$(element).closest('tr').fadeOut();
		}
	);

}