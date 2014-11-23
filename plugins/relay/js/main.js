
function plugin_radiorelay_state(id,element){
	var state = $(element).hasClass('btn-warning') ? 0:1; 
	$.action(
		{
			'action':'radioRelay_change_state',
			'id' : id,
			'state' : state
		}
		,function(r){
			$(element).toggleClass('btn-warning');
		});
}