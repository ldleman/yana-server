

$(document).ready(function(){



	function refreshDoor(){
	$('div[id^="state"]').each(function(e,elem){
		var id = $(elem).attr('id').replace('state','');
		$.ajax({
		  type: "GET",
		  url: "action.php?action=door_get_state&engine="+id
		}).done(function( msg ) {
		  $('img',elem).attr('src','plugins/door/img/'+(msg==1?'open':'close')+'.png');
		});
	});
	}
	refreshDoor();

	setInterval(function(){

	refreshDoor();
	},2000);

});


