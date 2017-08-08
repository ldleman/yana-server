
function init_setting_relay(){
	relay_search();
	$('.iconSet i').click(function(){
		$('.iconSet i').removeAttr('data-selected');
		$(this).attr('data-selected','true');
		$('#icon').val($(this).attr('data-value'));
	});
			
		
}



// SEARCH
function relay_search(callback){
	$('#rooms').fill({action:'relay_search'},function(){
		if(callback!=null) callback();
	});
}

// SAVE
function relay_save(account){
	var data = $.getForm('#relayForm');

	$.action(data,function(r){
		$.message('info','Relais enregistrée');
		relay_search();
		$('#relayForm input').val('');
		$('#relayForm').attr('data-id','');
		search();
	});
}

// EDIT
function relay_edit(element){
	var line = $(element).closest('tr');
	$.action({action:'relay_edit',id:line.attr('data-id')},function(r){
		$.setForm('#relayForm',r);
		$('.iconSet i[data-value="'+$('#icon').val()+'"]').attr('data-selected','true');
		$('#relayForm').attr('data-id',r.id);
	});
}

// DELETE
function relay_delete(element){
	if(!confirm('Êtes vous sûr de vouloir supprimer cet item?')) return;
	var line = $(element).closest('tr');
	$.action({action : 'relay_delete',id : line.attr('data-id')},function(r){
		line.remove();
	});
}

//CHANGE STATE
function relay_change_state(element){
	var relay = $(element).closest('.relay_widget');
	var data = relay.data();
	var state = relay.hasClass('active') ? 0 : 1 ;

	$.action(
		{
			action : 'relay_manual_change_state', 
			id: data.id,
			state: state
		},
		function(r){
			relay.toggleClass("active");
		}
	);
}


function relay_show(container,view){
	
	$(container).attr("data-selected",view);
	
	if(view==''){
		$(".relay_view ul li",container).fadeIn();
		$(".relay_view",container).removeClass("relay_detail_view")
		return;
	}
	
	$(".relay_view",container).addClass("relay_detail_view")
	$(".relay_view ul li",container).hide();
	$(".relay_view").css("background-color",$(".relay_view ul li[data-view='"+view+"'] .widget_content",container).css("border-top-color"));
	$(".relay_view ul li[data-type='"+view+"']",container).fadeIn();
}

function widget_relay_init(){

	$(".relay_widget").each(function(i,elem){
		relay_show($(elem),$(elem).attr('data-view'));
		
	});
}

