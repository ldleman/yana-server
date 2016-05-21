
var mode = 'cause';

$(document).ready(function(){
	init();
});

function initDragAndDrop(){
		$( ".toolbar li" ).draggable({
			helper: "clone"
		});
		$( ".workspace li" ).draggable({
			helper: "clone",
			cursorAt: { top: 15 }
		});
		$( ".place" ).droppable({
			hoverClass :  "dragover" ,
			drop: function( event, ui ) {
				var element = $(ui.draggable[0]);
				if(element.hasClass('typeButton')){
					addLine({type:element.attr('data-type'),place:$(this),panel:mode});
				}else{
					moveLine({line:element,place:$(this),panel:mode});
				}
			}
		});
}

function init(){ 
	//Add lines from database
	$.action({
		action : 'plugin_story_get_causes_effects',
		id:$('#story').data('id')
	},function(r){
		
		if(r.results['causes'].length==0)
			addLine({type:'time',panel:'cause'});

		if(r.results['effects'].length==0)
			addLine({type:'talk',panel:'effect'});

		for(var key in r.results['causes'])
			addLine(r.results['causes'][key]);
		
		for(var key in r.results['effects'])
			addLine(r.results['effects'][key]);
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


function story_launch(id,elem){
	$(elem).addClass('loading');
	$('tr[data-log="'+id+'"] td pre').html('En cours d\'execution...');
	$.action({
		action:'plugin_story_launch_story',id:id},
		function(response){
			$('tr[data-log="'+id+'"] td pre').html(response.log);
			$('tr[data-log="'+id+'"]').slideDown();
			$(elem).removeClass('loading');
		}
	);
}


function story_change_state(id,elem){
	var icon = $(elem).find('i');
	var state = icon.hasClass('fa-check-square-o') ? 0 : 1;
	
	$.action({
		action:'plugin_story_change_state',id:id,state:state},
		function(){
			icon.removeClass('fa-square-o').removeClass('fa-check-square-o');
			icon.addClass((state == 1 ? 'fa-check-square-o' : 'fa-square-o'));
		}
	);
}

//Log story
function story_log(id){
	$('tr[data-log="'+id+'"]').slideToggle();
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

	$('#story').attr('data-mode',mode);
	switch(mode){
		case 'cause':
			$('#causePanel').fadeIn(200);
			$('#causePanelButton').addClass('active');
		break;
		case 'effect':
			$('#effectPanel').fadeIn(200);
			$('#effectPanelButton').addClass('active');

		break;
	}
}

function moveLine(options){
	var line = options.line;
	var number = $('.workspace li').index(line);
	options.place.parent().before(line.detach());
}

//Add line to board
function addLine(options){
	
	options.data = options.data == null ? {value:'',target:'',operator:'',union:''} : options.data ;

	$.action({
		action : 'plugin_story_get_type_template',
		data : options.data,
		type : options.type,
		async:false
	},function(r){
	
			var line = '<li>';
			line += '<div data-element="place" class="place"></div>';
			line +='<div data-element="union" class="union">ET</div>';
			line += r.html;
			line += '</li>';
			
			
			if(options.place==null){
				$('.workspace-'+(options.panel=='cause'?'cause':'effect')).append(line);
			}else{
				$(options.place).parent().before(line);
			}
			
			//Fill select by database values
			$('.workspace select[data-value]').each(function(i,elem){
				$(elem).find('option[value="'+$(elem).attr('data-value')+'"]').attr("selected", "selected");
			});
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


	if($('.workspace-'+$('#story').attr('data-mode')+' li').length == 1){
		alert('Vous ne pouvez pas supprimer la dernière ligne');
		return;
	}

	if(!confirm('Sûr?')) return;
	var line = $(elem).closest('li');
	line.remove();
}

//Save story
function saveStory(){
	story = {};
	if($('#story').attr('data-id')!='')story.id = $('#story').attr('data-id');
	story.label = $('.story h1 input').val();
	story.causes = [];
	story.effects = [];
	
	//causes
	lastunion ='';
	$('.story div#causePanel ul.workspace li div').each(function(i,elem){
		switch($(elem).attr('data-element')){
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
	
	//effectS
	lastunion ='';
	$('.story div#effectPanel ul.workspace li div').each(function(i,elem){
		
		switch($(elem).attr('data-element')){
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
			$('#story').attr('data-id',response.id);
			alert('Scénario enregistré :)');
		}
	);
		
}



