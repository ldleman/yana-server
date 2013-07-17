
function snapshot(){
	$.ajax({
	  type: "POST",
	  url: "action.php",
	  data: { action: "camera_refresh"},
	  success:function(){

	  	 setTimeout(function(){$('#cameraPI').attr('src','plugins/camera/view.jpg?'+Math.random())},1000);
	  	
	  }
	});
}