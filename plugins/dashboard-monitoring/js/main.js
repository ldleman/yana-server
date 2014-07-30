$(document).ready(function(){

});

function change_gpio_state(pin,element){
	state = $(element).text()=='on'?0:1;
	$.ajax({
		  url: "action.php",
		  type: "POST",
		  data: {action:'CHANGE_GPIO_STATE',pin:pin,state:state},
		  success:function(response){
		  	if(state){
		  		$(element).removeClass('label-info');
		  		$(element).addClass('label-warning');
		  		$(element).html('on');
		  	}else{
		  		$(element).addClass('label-info');
		  		$(element).removeClass('label-warning');
		  		$(element).html('off');
		  	}

		  }
		});
}