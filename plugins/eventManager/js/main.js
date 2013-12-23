$(document).ready(function(){
	setActionTypeList();
});


function setActionTypeList(){
	if($('select[name="eventTarget"]').val()=='server'){
		$('select[name="eventType"] option[value="talk"],select[name="eventType"] option[value="sound"]').hide();
	}else{
		$('select[name="eventType"] option[value="talk"],select[name="eventType"] option').show();
	}
}