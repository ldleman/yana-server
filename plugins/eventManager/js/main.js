$(document).ready(function(){
	setActionTypeList($('select[name="eventType"]').attr('value'));
});


function setActionTypeList(type){
	if($('select[name="eventTarget"]').val()=='server'){

		$('select[name="eventType"]').html(
		'<option '+(type=='command'?'selected="selected"':'')+'value="command">Executer une commande</option>'+
		'<option '+(type=='gpio'?'selected="selected"':'')+'value="gpio">Changer un etat GPIO</option>'
		);
	}else{
		$('select[name="eventType"]').html(
		'<option '+(type=='talk'?'selected="selected"':'')+'value="talk">Parler</option>'+
		'<option '+(type=='command'?'selected="selected"':'')+'value="command">Executer une commande</option>'+
		'<option '+(type=='sound'?'selected="selected"':'')+'value="sound">Jouer un son</option>'
		);
	}
}