$(document).ready(function(){


	$('.date').date();

	$.getJSON($("#UPDATE_URL").html(),function(data){
    	
	});

	



});

function maj(data){
 	server = data.maj["yana-server"];
    	
		if(server.version!=null && server.version!=$("#PROGRAM_VERSION").html()){
			$('#notification').html('1');
			$('#notification').css('visibility','visible');
			$('#notification').attr('title','Version '+server.version+' disponible.');
			if(server.link != null) $('#notification').attr('onclick','window.location="'+server.link+'";');
		}
}


function setRankAccess(rank,section,access,elem){
	var data = {
				action : 'set_rank_access',
				rank : rank,
				section : section,
				access : access,
				state : 0
			}

	if($(elem).is(':checked')) data.state = 1;

	$.ajax({
		  url: "action.php",
		  type: "POST",
		  data: data
		});
}
