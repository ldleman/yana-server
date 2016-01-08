
var mode = 'CAUSE';

$(document).ready(function(){
	init();
});

function initDragAndDrop(){
	$( ".toolbar li" ).draggable({
			helper: "clone"
		});
		$( ".place" ).droppable({
			hoverClass :  "dragover" ,
			drop: function( event, ui ) {
				addLine({type:$(ui.draggable[0]).data('type'),place:$(this),panel:mode}); 
			}
		});
}

function init(){ 
	//Add lines from database
	$.action({
		action : 'plugin_story_get_causes_effects',
		id:$('#story').data('id')
	},function(r){
		
		for(var key in r.results)
			addLine(r.results[key]);
	});
	
	
	//Init drag and drop
	initDragAndDrop();
	
	//Captor selectors events
	$(document).on('change','.plugin_selector',function(){
		$.action({action:'plugin_story_get_captors',plugin:$(this).val()},
				function(response){
					$('.captor_selector').append('<option value="">-</option>');
					for(var key in response.devices){
						var device = response.devices[key];
						$('.captor_selector').append('<option value="'+device.id+'">'+device.label+'</option>');
					}
				}
		);
	});
	
	$(document).on('change','.captor_selector',function(){
		$.action({action:'plugin_story_get_captor_values',id:$(this).val()},
				function(response){
					$('.captor_field_selector').append('<option value="">-</option>');
					for(var key in response.values){
						$('.captor_field_selector').append('<option value="'+key+'">'+key+'</option>');
					}
				}
			);
	});
	
}

//Delete story
function story_delete(id,element){
	if(!confirm('Etes vous sûr de vouloir supprimer cette ligne ?')) return;
	$.action({
		action:'plugin_story_delete_story',id:id},
		function(response){
			$(element).parent().fadeOut();
		}
	);
}

//Switch mode (cause / effect)
function switchCauseEffect(mode){
	$('#causePanelButton,#effectPanelButton').removeClass('active');
	$('#effectPanel,#causePanel').hide();
	
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

//Add line to board
function addLine(options){
	options.data = options.data == null ? {value:'',target:'',operator:'',union:''} : options.data ;

	$.action({
		action : 'plugin_story_get_type_template',
		data : options.data,
		type : options.type
	},function(r){
			
			if(options.place==null){
				if(options.panel=='CAUSE') options.place = $('#causePanel .place').last();
				if(options.panel=='EFFECT') options.place = $('#effectPanel .place').last();
			}
			var line = '';
			if($(options.place).attr('id')!='place-0' && $(options.place).attr('id')!='place-effect-0'){
				line +='<li class="union"><select><option '+(options.data.union=='ET'?' selected="selected" ':'')+' >ET</option>';

				if(options.panel=='CAUSE') line +='<option '+(options.data.union=='OU'?' selected="selected" ':'')+'>OU</option></select></li>';
				line +='</select></li>';
			}
			
			
			
			line += r.html;
			line += '<li class="place"></li>';
			//Append line
			$(options.place).after(line);
			//For captor only
			if(options.type=='event'){
				$.action(
					{action:'plugin_story_get_captors_plugins'},
					function(response){
						$('.plugin_selector').append('<option value="">-</option>');
						for(var key in response.plugins){
							var plugin = response.plugins[key];
							$('.plugin_selector').append('<option value="'+plugin+'">'+plugin+'</option>');
						}
					}
				);
			}
			initDragAndDrop();
	});
	
}

//Delete story line
function deleteLine(elem){
	if(confirm('Sûr?')){
		var line = $(elem).parent().parent();
		if(line.prev().attr('id')!='place-0' && line.prev().attr('id')!='place-effect-0')line.prev().remove();
		line.next().remove();
		line.remove();
	}
}

//Save story
function saveStory(){
	story = {};
	if($('#story').data('id')!='')story.id = $('#story').data('id');
	story.label = $('.story h1 input').val();
	story.causes = [];
	story.effects = [];
	
	//CAUSES
	lastunion ='';
	$('.story div#causePanel ul.workspace li').each(function(i,elem){
		switch($(elem).attr('class')){
			case 'line':
				var line = { type  : $(elem).data('type') }
				$('input,select',elem).each(function(i,input){
					line[$(input).data('field')] = $(input).val();
				});
				
				line.union = lastunion;
				story.causes.push(line);
			break;
			case 'union':
				if($(elem).find('select').length!=0) lastunion = $(elem).find('select').val();
			break;
		}
	});
	
	//EFFECTS
	lastunion ='';
	$('.story div#effectPanel ul.workspace li').each(function(i,elem){
		switch($(elem).attr('class')){
			case 'line':
				var line = { type  : $(elem).data('type') }
				$('input,select',elem).each(function(i,input){
					line[$(input).data('field')] = $(input).val();
				});
				
				line.union = lastunion;
				story.effects.push(line);
			break;
			case 'union':
				if($(elem).find('select').length!=0) lastunion = $(elem).find('select').val();
			break;
		}
	});

	if(story.effects.length == 0 || story.causes.length == 0){
		alert('Pour valider le scénario, vous devez remplir au moins une cause et au moins un effet');
		return;
	}
	if(story.label == ''){
		$('.story h1 input').css('color','red')
		alert('Merci de bien vouloir nommer votre scénario');
		return;
	}

	$.action(
		{
			action:'plugin_story_save_story',
			story:story
		},
		function(response){
			alert('Scénario enregistré :)');
		}
	);
		
}



