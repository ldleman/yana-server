$(document).ready(function(){



});


function debug_plugin_send(button){
	button = $(button);
	button.html('En cours...').prop('disabled',true);
	$.ajax({
		url : 'action.php?action=plugin_debug_send',
		data : {debug:$('#debug_selector').val()},
		success: function(r){
			$('#debug_monitor').val(r);
		},
		complete : function(){
			button.html('Envoyé').prop('disabled',false);
		}
	});

}