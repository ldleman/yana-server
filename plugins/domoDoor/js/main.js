
$(document).ready(function(){
	refresh_domodoor('#dash_domodoor');
		setInterval(function(){
			refresh_domodoor('#dash_domodoor');
		},120000);
});


function refresh_domodoor(elem){
	$(elem).load('action.php?action=domodoor_get_history');
}