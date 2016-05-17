$(document).ready(function(){

});

function change_gpio_state(pin,element){
	
	state = $(element).hasClass('gpio_state_on')? 0:1;
	$(element).attr('class','gpio_state_waiting');
	$.ajax({
		  url: "action.php",
		  type: "POST",
		  data: {action:'CHANGE_GPIO_STATE',pin:pin,state:state},
		  success:function(response){
		  	if(state){
				$(element).attr('class','gpio_state_on');
		  	}else{
				$(element).attr('class','gpio_state_off');
		  	}

		  }
		});
}