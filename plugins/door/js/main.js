

$(document).ready(function(){




	$('div[id^="state"]').each(function(e,elem){
		var id = $(elem).attr('id').replace('state','');
		$.ajax({
		  type: "GET",
		  url: "action.php?action=door_get_state&engine="+id
		}).done(function( msg ) {
		  $(elem).html('<img style="width:150px;height:150px;" src="plugins/door/img/'+(msg==0?'open':'close')+'.png">');
		});
	});

	

	setInterval(function(){

	$('div[id^="state"]').each(function(e,elem){
		var id = $(elem).attr('id').replace('state','');
		$.ajax({
		  type: "GET",
		  url: "action.php?action=door_get_state&engine="+id
		}).done(function( msg ) {
		  $(elem).html('<img style="width:150px;height:150px;" src="plugins/door/img/'+(msg==0?'open':'close')+'.png">');
		});
	});

	},2000);

});


