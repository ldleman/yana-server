function save_settings(){
	var data = {};
	
	$('#setting_table tr').each(function(i,tr){
		if(i!=0){
			tr = $(tr);
			var key = tr.find('td').eq(0).text();
			if(key!=null && key!='' && key!='undefinded')
				data[key] = tr.find('td:eq(1) input').val();
		}
	});
	
	$.ajax({
				method: 'POST',
				url : 'action.php',
				data : {action:'SAVE_SETTINGS',data:data},
				success: function(response){
						alert(response);
				}
			});
}