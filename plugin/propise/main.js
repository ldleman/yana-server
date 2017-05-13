
function init_setting_propise(){
	propise_search();
}

// SEARCH
function propise_search(callback){
	$('#rooms').fill({action:'propise_search'},function(){
		if(callback!=null) callback();
	});
}

// SAVE
function propise_save(account){
	var data = $.getForm('#propiseForm');

	$.action(data,function(r){
		$.message('info','Sonde enregistrée');
		propise_search();
		$('#propiseForm input').val('');
		$('#propiseForm').attr('data-id','');
		search();
	});
}

// EDIT
function propise_edit(element){
	var line = $(element).closest('tr');
	$.action({action:'propise_edit',id:line.attr('data-id')},function(r){
		$.setForm('#propiseForm',r);
		$('#propiseForm').attr('data-id',r.id);
	});
}

// DELETE
function propise_delete(element){
	if(!confirm('Êtes vous sûr de vouloir supprimer cet item?')) return;
	var line = $(element).closest('tr');
	$.action({action : 'propise_delete',id : line.attr('data-id')},function(r){
		line.remove();
	});
}


function propise_menu(element,global){
	var line = $(element);
	var container = line.closest(".propise_widget");
	var view = $(element).attr("data-view");
	var widget = $(element).closest('.dashboard_bloc').attr('data-id');
	
	$(container).attr("data-view",view);
	$.action({
		action:"propise_select_widget_menu",
		id:widget,
		menu: view
	});

	propise_show(container,view);
};

function propise_show(container,view){
	
	$(container).attr("data-selected",view);
	
	if(view==''){
		$(".propise_view ul li",container).fadeIn();
		$(".propise_view",container).removeClass("propise_detail_view")
		return;
	}
	
	$(".propise_view",container).addClass("propise_detail_view")
	$(".propise_view ul li",container).hide();
	$(".propise_view").css("background-color",$(".propise_view ul li[data-view='"+view+"'] .widget_content",container).css("border-top-color"));
	$(".propise_view ul li[data-type='"+view+"']",container).fadeIn();
}


function propise_refresh(widget,data){
	widget.find('li[data-type="light"] span').text(data.light);
	widget.find('li[data-type="humidity"] span').text(data.humidity);
	widget.find('li[data-type="temperature"] span').text(data.temperature);
	widget.find('li[data-type="mouvment"] span').text(data.mouvment);
}