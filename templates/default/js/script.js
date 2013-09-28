$(document).ready(function(){
	$('.date').date();
	$.getJSON($("#UPDATE_URL").html(),function(data){
	});
	get_dash_infos();


	


// $('.typeahead').typeahead({
//     source: function (query, process) {
//         return $.get('/typeahead', { query: query }, function (data) {
//             return process(data.options);
//         });
//     }
// });



});

function get_dash_infos(){
	$('#dash_system,#dash_network,#dash_user,#dash_hdd,#dash_disk,#dash_services,#dash_gpio').html('Chargement...')



	$('#dash_system,#dash_network,#dash_user,#dash_hdd,#dash_disk,#dash_services,#dash_gpio').each(function(i,elem){
		refresh_dash(elem);
		setInterval(function(){
			refresh_dash(elem);
		},10000);
	});
}

function refresh_dash(elem){
	$(elem).load('action.php?action=GET_DASH_INFO&type='+$(elem).attr('id'));
}

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
