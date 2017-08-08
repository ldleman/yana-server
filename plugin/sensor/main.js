
function init_setting_sensor(){
	sensor_search();
}



// SEARCH
function sensor_search(callback){
	$('#rooms').fill({action:'sensor_search'},function(){
		if(callback!=null) callback();
	});
}

// SAVE
function sensor_save(account){
	var data = $.getForm('#sensorForm');

	$.action(data,function(r){
		$.message('info','Sonde enregistrée');
		sensor_search();
		$('#sensorForm input').val('');
		$('#sensorForm').attr('data-id','');
		search();
	});
}

// EDIT
function sensor_edit(element){
	var line = $(element).closest('tr');
	$.action({action:'sensor_edit',id:line.attr('data-id')},function(r){
		$.setForm('#sensorForm',r);
		$('#sensorForm').attr('data-id',r.id);
	});
}

// DELETE
function sensor_delete(element){
	if(!confirm('Êtes vous sûr de vouloir supprimer cet item?')) return;
	var line = $(element).closest('tr');
	$.action({action : 'sensor_delete',id : line.attr('data-id')},function(r){
		line.remove();
	});
}


function sensor_menu(element,global){
	var line = $(element);
	var container = line.closest(".sensor_widget");
	var view = $(element).attr("data-view");
	var widget = $(element).closest('.widget').attr('data-id');
	
	$(container).attr("data-view",view);
	
	$.action({
		action:"sensor_select_widget_menu",
		id:widget,
		menu: view
	});

	sensor_show(container,view);
};

function sensor_show(container,view){
	
	$(container).attr("data-selected",view);
	
	if(view==''){
		$(".sensor_view ul li",container).fadeIn();
		$(".sensor_view",container).removeClass("sensor_detail_view")
		return;
	}
	
	$(".sensor_view",container).addClass("sensor_detail_view")
	$(".sensor_view ul li",container).hide();
	$(".sensor_view").css("background-color",$(".sensor_view ul li[data-view='"+view+"'] .widget_content",container).css("border-top-color"));
	$(".sensor_view ul li[data-type='"+view+"']",container).fadeIn();
}

function widget_sensor_init(){

	$(".sensor_widget").each(function(i,elem){
		sensor_show($(elem),$(elem).attr('data-view'));
		
	});
}

function sensor_refresh(widget,data){
	if(!data) return;
	widget.find('li[data-type="light"] span').text(data.light);
	widget.find('li[data-type="humidity"] span').text(data.humidity);
	widget.find('li[data-type="temperature"] span').text(data.temperature);
	widget.find('li[data-type="mouvment"] span').text(data.mouvment);
}