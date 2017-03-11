
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