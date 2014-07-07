var handle = null;
var mode = 'CAUSE';


$(document).ready(function(){
	init();
});

function init(){
	
	for(var key in story){
		addLine(story[key]);
	}
	
	$(document).mousemove(function(e){

		if(handle !=null){
			handle.css('top',e.clientY-(handle.height()/2)+'px').css('left',(e.clientX-handle.width()/2)+'px');
			$('.place').each(function(i,elem){
				if(collision($(elem),handle)){
					$(elem).addClass('dragover');
				}else{
					$(elem).removeClass('dragover');
				}
			})
		}
		e.preventDefault();
		e.stopPropagation();
	});

	$('.toolbar li').mousedown(function(e){
		var item = $(this).clone();
		item.addClass('dragged');
		$('body').append(item);
		e.preventDefault();
		handle = item;
		item.mouseup(function(){
			if($('.dragover').length!=0){
				addLine({type:item.data('type'),place:$('.dragover'),panel:mode});
			}
			$('.dragover').removeClass('dragover');
			item.remove();
			handle = null;
		});
	});
}


function story_delete(id,element){
	if(confirm('Etes vous sûr de vouloir supprimer cette ligne ?')){
		$.ajax({
				method: 'POST',
				url : 'action.php',
				data : {action:'DELETE_STORY',id:id},
				success: function(response){
					if($.trim(response)==''){
						$(element).parent().fadeOut();
					}else{
						alert(response);
					}
				}
			});
	}
}

function switchCauseEffect(mde){
	$('#causePanelButton,#effectPanelButton').removeClass('active');
	$('#effectPanel,#causePanel').hide();
	mode = mde;
	switch(mode){
		case 'CAUSE':
			$('#causePanel').fadeIn(200);
			$('#causePanelButton').addClass('active');
		break;
		case 'EFFECT':
			$('#effectPanel').fadeIn(200);
			$('#effectPanelButton').addClass('active');
		break;
	}
}

function addLine(options){

	options.data = options.data == null ? {value:'',target:'',operator:'',union:''} : options.data ;
	
	if(options.place==null){
		if(options.panel=='CAUSE') options.place = $('#causePanel .place').last();
		if(options.panel=='EFFECT') options.place = $('#effectPanel .place').last();
	}
	
	var line = '';

	if($(options.place).attr('id')!='place-0' && $(options.place).attr('id')!='place-effect-0'){
		line +='<li class="union"><i class="fa fa-arrow-circle-o-right"></i> <select><option '+(options.data.union=='ET'?' selected="selected" ':'')+' >ET</option>';

		if(options.panel=='CAUSE') line +='<option '+(options.data.union=='OU'?' selected="selected" ':'')+'>OU</option></select></li>';
		line +='</select></li>';
	}
	line += '<li class="line" data-type="'+options.type+'"><div class="target">';
	switch(options.type){
		case 'time':
			line += '<i class="fa fa-clock-o"></i> Date / Heure</div> <select class="operator"><option>=</option><option>!=</option></select>  <div class="value"> ';
				
				values = ['*','*','*','*','*'];
				if(options.data.value.length !=0) values = options.data.value.split('-');
				line += '<select style="width:100px;" id="minut">';
				line +='<option value="*">Toutes les minutes</option>';
				for(var i=0;i<60;i++)
					line += '<option '+(values[0]==i?' selected="selected" ':'')+' value="'+i+'">'+i+'</option>';
				line +='</select>';
				line += '<select style="width:100px;" id="hour">';
				line +='<option value="*">Toutes les heures</option>';
				for(var i=0;i<24;i++)
					line += '<option '+(values[1]==i?' selected="selected" ':'')+' value="'+i+'">'+i+'</option>';
				line +='</select> ';
				line += '<select style="width:100px;" id="day">';
				line +='<option value="*">Tous les mois</option>';
				for(var i=1;i<13;i++)
					line += '<option '+(values[2]==i?' selected="selected" ':'')+' value="'+i+'">'+i+'</option>';
				line +='</select>';
				line += '<select style="width:100px;" id="month">';
				line +='<option value="*">Toutes les jours</option>';
				for(var i=1;i<32;i++)
					line += '<option value="'+i+'">'+i+'</option>';
				line +='</select>';
				line += '<select style="width:100px;" id="year">';
				line +='<option value="*">Tous les ans</option>';
				for(var i=2000;i<2200;i++)
					line += '<option '+(values[3]==i?' selected="selected" ':'')+' value="'+i+'">'+i+'</option>';
				line +='</select>';

		break;
		case 'listen':
			line += '<i class="fa fa-microphone"></i> Phrase</div> <select class="operator"><option>=</option><option>!=</option></select> <div class="value"><input type="text" placeholder="Ma phrase.." value="'+options.data.value+'">';
		break;
		case 'event':
			line += '<i class="fa fa-tachometer"></i> Evenement <select id="event"><option>Capteur 1</option><option>Capteur 2</option></select> <select id="eventField"><option>Champ 1</option></select></div> <select class="operator"><option>=</option><option>!=</option><option><</option><option>></option></select> <div class="value"><input type="text" placeholder="valeur" value="'+options.data.value+'">';
		break;
		case 'command':
			line += '<i class="fa fa-terminal"></i> Commande <select><option>Serveur</option><option>Client</option></select></div> <div class="operator">=</div> <div class="value"><input type="text" placeholder="valeur" value="'+options.data.value+'">';
		break;
		case 'talk':
			line += '<i class="fa fa-volume-up"></i> Phrase</div> <div class="operator">=</div> <div class="value"><input type="text" placeholder="Ma phrase.." value="'+options.data.value+'">';
		break;
		case 'sleep':
			line += '<i class="fa fa-coffee"></i> Pause</div> <div class="operator">=</div> <div class="value"><input type="text" placeholder="durée(secondes)" value="'+options.data.value+'">';
		break;
		case 'actuator':
			line += '<i class="fa fa-cogs"></i> Action</div> <div class="operator">=</div> <div class="value"><input type="text" placeholder="MON_ACTION" value="'+options.data.value+'">';
		break;
		case 'var':
			line += '<i class="fa dollar"></i> Variable <input type="text" placeholder="Ma variable" value=""></div> <div class="operator">=</div> <div class="value"><input type="text" placeholder="Ma valeur" value="'+options.data.value+'">';
		break;
		case 'readvar':
			line += '<i class="fa dollar"></i> Variable <input type="text" placeholder="Ma variable" value=""></div> <select class="operator"><option>=</option><option>!=</option><option><</option><option>></option></select> <div class="value"><input type="text" placeholder="Ma valeur" value="'+options.data.value+'">';
		break;
	}
	line += '<div class="delbutton" onclick="deleteLine(this);"><i class="fa fa-times"></i></div></div><div class="clear"></div></li><li class="place">...</li>';
	
	$(options.place).after(line);
	
}

function deleteLine(elem){
	if(confirm('Sûr?')){
		var line = $(elem).parent().parent();
		if(line.prev().attr('id')!='place-0' && line.prev().attr('id')!='place-effect-0')line.prev().remove();
		line.next().remove();
		line.remove();
	}
}

function saveScenario(){
	story = {};
	if($('.story #story').val()!='')story.id = $('.story #story').val();
	story.label = $('.story h1 input').val();
	story.cause = [];
	story.effect = [];
	
	lastunion ='';
	$('.story div#causePanel ul.workspace li').each(function(i,elem){
		
		switch($(elem).attr('class')){
			case 'line':
				var line = {
					type  : $(elem).data('type'),
					value : $(elem).find('.value').find('input').val()
					}
				switch(line.type){
					
					case 'listen':
						line.target = line.type ;
					break;
					case 'time':
						var valBloc = $(elem).find('.value');
						line.target = line.type ;
						
						line.value =  $('#minut',valBloc).val()+'-'+ $('#hour',valBloc).val()+'-'+ $('#day',valBloc).val()+'-'+ $('#month',valBloc).val()+'-'+ $('#year',valBloc).val();
					break;
					case 'readvar':
						line.target = {target:$(elem).find('.target').find('input').val()} ;
					break;
					case 'event':
						line.target = {event:$(elem).find('.target').find('#event').val(),field:$(elem).find('.target').find('#eventField').val()} ;
					break;
				}
				line.operator = $(elem).find('.operator').val();
				line.union = lastunion;
				story.cause.push(line);
			break;
			case 'union':
				if($(elem).find('select').length!=0) lastunion = $(elem).find('select').val();
			break;
		}
		
		
	});
	lastunion ='';
	$('.story div#effectPanel ul.workspace li').each(function(i,elem){
		
		switch($(elem).attr('class')){
			case 'line':
				var line = {
					type  : $(elem).data('type'),
					value : $(elem).find('.value').find('input').val()
					}
				switch(line.type){
					case 'command':
						line.target = {target:$(elem).find('.target').find('select').val()} ;
					break;
					case 'var':
						line.target = {target:$(elem).find('.target').find('input').val()} ;
					break;
				}
				line.operator = $(elem).find('.operator').val();
				line.union = lastunion;
				story.effect.push(line);
			break;
			case 'union':
				if($(elem).find('select').length!=0)  lastunion = $(elem).find('select').val();
			break;
			
		}
		
		
	});
	if(story.effect.length != 0 && story.cause.length != 0){
		if(story.label != ''){
			$.ajax({
				method: 'POST',
				url : 'action.php',
				data : {action:'SAVE_STORY',story:story},
				success: function(response){
					if($.trim(response)=='') response = 'Scénario enregistré :)';
					alert(response);
				}
			});
		}else{
			$('.story h1 input').css('color','red')
			alert('Merci de bien vouloir nommer votre scénario');
		}
	}else{
		alert('Pour valider le scénario, vous devez remplir au moins une cause et au moins un effet');
	}
	
}



 function collision(div1, div2) {
      var x1 = div1.offset().left;
      var y1 = div1.offset().top;
      var h1 = div1.outerHeight(true);
      var w1 = div1.outerWidth(true);
      var b1 = y1 + h1;
      var r1 = x1 + w1;
      var x2 = div2.offset().left;
      var y2 = div2.offset().top;
      var h2 = div2.outerHeight(true);
      var w2 = div2.outerWidth(true);
      var b2 = y2 + h2;
      var r2 = x2 + w2;

      if (b1 < y2 || y1 > b2 || r1 < x2 || x1 > r2) return false;
      return true;
    }