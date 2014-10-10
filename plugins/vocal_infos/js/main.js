function plugin_vocalinfo_save(){
	var config = [];
	$('.command').each(function(i,element){
		var line = $(element);
		
	
		config.push ( {
			disabled : $('.enabled',line).is(':checked')?false:true,
			confidence : $('.confidence',line).val()
		});
	});
	$.ajax({
		url : 'action.php?action=plugin_vocalinfo_save',
		data : {config:config},
		type: "POST",
		success : function(msg){
			if(msg!='') alert(msg);
		}
	});
}