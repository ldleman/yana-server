
function ajax_snapshot(){
	$("#camera_btn").prop('disabled', true);
	$.ajax({
	  type: "POST",
	  url: "action.php",
	  data: { action: "camera_refresh"},
	  success:function(data){
	  	if(data){
	  	alert(data);
	  }
	  else
	  {
	  	$('#cameraPI').attr('src','plugins/camera/view.jpg?'+Math.random());
	  }
	  $("#camera_btn").prop('disabled', false);
	  }
	  });
}