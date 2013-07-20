$(document).ready(function(){


});

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
