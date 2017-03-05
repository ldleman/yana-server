$(document).ready(function(){

	var init = 'init_'+$.page();

	if(window[init]!=null) window[init]($.urlParam());
	if($.page()=='' ) init_index();
	
	if($.urlParam('module')!= null){
		var init = 'init_plugin_'+$.urlParam('module');
		if(window[init]!=null) window[init]($.urlParam());
	}
	
	//SHOW HTTP ERROR/NOTICE
    if ($.urlParam('error') != null) {
        $.message('error', decodeURIComponent($.urlParam('error')), 0);
        $.urlParam('error', false);
    }
    if ($.urlParam('info') != null) {
        $.message('info', decodeURIComponent($.urlParam('info')));
        $.urlParam('info', false);
    }
    if ($.urlParam('success') != null) {
        $.message('success', decodeURIComponent($.urlParam('success')));
        $.urlParam('success', false);
    }
	
	init_lists();
});

// INIT - INDEX
function init_index(){
	
}


function init_setting(parameter){
	switch(parameter.section){
		case 'plugin':
		search_plugin(function(){
			$('.toggle').change(function(){
				var input = $('input',this);
				var value = input.prop('checked');
				
				$.action({
					action : 'change_plugin_state',
					plugin : $(this).closest('li').attr('data-id'),
					state:value?1:0
				},function(r){},
				function(r){
						input.prop('checked',!value);
				});
				
			});
		});
		break;
		case 'user':
			search_user();
		break;
		case 'room':
			search_room();
		break;
		case 'rank':
			search_rank();
		break;
		case 'right':
			$('.rightColumn .toggle').change(function(){
				$.action({action:'save_right',rank:$('#rank').attr('data-rank'),section:$(this).closest('tr').attr('data-section'),right:$(this).attr('data-right'),state:$('input',this).prop('checked')});
			});
		break;
	}
}

/** RIGHT **/
function right_switch(element){
	$(element).closest('tr').find('input').trigger('click');
	
}


/** LIST **/

function init_lists(){
	$('div[data-list]').each(function(i,elem){
		refresh_list(elem);
	});

	$('div[data-list]').on('click','thead .btn-success',function(){
		var line = $(this).closest('tr');
		var list = line.closest('[data-list]');
		
		$.action({action:'save_list_table',label:line.find('input:eq(0)').val(),id:list.attr('data-id'),list:list.attr('data-list')},function(r){
			line.find('input:eq(0)').val('');
			refresh_list(list);
		});
	});

	$('div[data-list]').on('click','tbody tr .btn-danger',function(){
		if(!confirm('Êtes vous sûr de vouloir supprimer cete ligne ?')) return;
		var line = $(this).closest('tr');
		$.action({action:'delete_list_table',id:line.attr('data-id')},function(r){
			line.remove();
		});
	});

	$('div[data-list]').on('click','tbody tr .btnEdit',function(){
		var line = $(this).closest('tr');
		var list = line.closest('[data-list]');
		var input = list.find('input:eq(0)');
		$.action({action:'edit_list_table',id:line.attr('data-id')},function(r){
			console.log(r.item.label,input);
			input.val(r.item.label);
			list.attr('data-id',r.item.id);
		

		});
	});
}

function refresh_list(elem){
	var id = $(elem).attr('data-list');
	var table = $(elem).find('table:eq(0)');
	$.action({action:'fill_list_table',id:id},function(r){
		table.find('tbody tr:visible').remove();
		var tpl = table.find('tbody tr:hidden').get(0).outerHTML;
		for(var key in r.rows){
			var row = r.rows[key];
			var line = $(Mustache.render(tpl,row));
			line.show();
			table.find('tbody').append(line);
		}
	});
}

/** ROOM **/

// SEARCH
function search_room(callback){
	$('#rooms').fill({action:'search_room'},function(){
		if(callback!=null) callback();
	});
}

// SAVE
function save_room(account){
	var data = $.getForm('#roomForm');
	data.account = account == true;
	$.action(data,function(r){
		$.message('info','Pièce enregistrée');
		if(account) return;
		
		$('#roomForm input').val('');
		$('#roomForm').attr('data-id','');
		search_room();
	});
}

// EDIT
function edit_room(element){
	var line = $(element).closest('tr');
	$.action({action:'edit_room',id:line.attr('data-id')},function(r){
		$.setForm('#roomForm',r);
		$('#roomForm').attr('data-id',r.id);
	});
}

// DELETE
function delete_room(element){
	if(!confirm('Êtes vous sûr de vouloir supprimer cet item?')) return;
	var line = $(element).closest('tr');
	$.action({action : 'delete_room',id : line.attr('data-id')},function(r){
		line.remove();
	});
}

/** USER **/

// SEARCH
function search_user(callback){
	$('#users').fill({action:'search_user'},function(){
		if(callback!=null) callback();
	});
}

// SAVE
function save_user(account){
	var data = $.getForm('#userForm');
	data.account = account == true;
	$.action(data,function(r){
		$.message('info','Utilisateur enregistré');
		if(account) return;
		
		$('#userForm input').val('');
		$('#userForm').attr('data-id','');
		search_user();
	});
}

// EDIT
function edit_user(element){
	var line = $(element).closest('tr');
	$.action({action:'edit_user',id:line.attr('data-id')},function(r){
		$.setForm('#userForm',r);
		$('#userForm').attr('data-id',r.id);
	});
}

// DELETE
function delete_user(element){
	if(!confirm('Êtes vous sûr de vouloir supprimer cet item?')) return;
	var line = $(element).closest('tr');
	$.action({action : 'delete_user',id : line.attr('data-id')},function(r){
		line.remove();
	});
}


/** RANKS **/

// SEARCH
function search_rank(callback){
	$('#ranks').fill({action:'search_rank'},function(){
		if(callback!=null) callback();
	});
}

// SAVE
function save_rank(){
	var data = $.getForm('#rankForm');
	$.action(data,function(r){
		$.message('info','Rang enregistré');
		$('#rankForm input').val('');
		$('#rankForm').attr('data-id','');
		search_rank();
	});
}

// EDIT
function edit_rank(element){
	var line = $(element).closest('tr');
	$.action({action:'edit_rank',id:line.attr('data-id')},function(r){
		$.setForm('#rankForm',r);
		$('#rankForm').attr('data-id',r.id);
	});
}

// DELETE
function delete_rank(element){
	if(!confirm('Êtes vous sûr de vouloir supprimer cet item?')) return;
	var line = $(element).closest('tr');
	$.action({action : 'delete_rank',id : line.attr('data-id')},function(r){
		line.remove();
	});
}


/** PLUGINS **/

// SEARCH
function search_plugin(callback){
	$('#plugins').fill({action:'search_plugin'},function(){
		if(callback!=null) callback();
	});
}




