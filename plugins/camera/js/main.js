
function camera_refresh(){
	$.ajax({
	  type: "POST",
	  url: "action.php",
	  data: { action: "camera_refresh"},
	  success:function(){
	  		console.log('action.php?action=camera_get_stream?'+Math.random());
	  	 setTimeout(function(){$('#cameraPI').attr('src','action.php?action=camera_get_stream&t='+Math.random())},1000);
	  }
	});
}