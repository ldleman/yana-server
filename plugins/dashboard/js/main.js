/*
	Jquery.dashboard V1.0
	@author V.carruesco aka idleman
	@mail idleman@idleman.fr
	@licence CC-by-nc-sa
*/

$(document).ready(function(){
	var view = $('#dashboard_switch').val();
	
	plugin_dashboard_load_view(view);
	
});

function addJs(jss){
	for(var key in jss){
		if($('script[src="'+jss[key]+'"]').length==0)
			$('body').append('<script type="text/javascript" src="'+jss[key]+'"></script>');		
	}
}

function widgetLoad(widget,model){
	
	addJs(widget.js);
	
	if(model.onLoad!=null){
								$.ajax({
									url : model.onLoad,
									data : {id:widget.id},
									method : 'POST',
									success : function(response2){
										bloc = $('#dashboard_bloc_'+widget.id);
										$.dashboard.setBlocData(bloc,response2);
									}
		});
	}
	
}

function plugin_dashboard_load_view(view){
	if($.trim(view) =='') return;

	$.ajax({
		dataType: "json",
		url: 'action.php?action=GET_WIDGETS',
		data : {dashboard : view},
		success: function(response){
				
				$('#dashboard').dashboard({
				models: response.model ,
				data: response.data ,
				onCreate : function(widget,bloc,column,cell){
					
					$.ajax({
						dataType: "json",
						url: 'action.php?action=ADD_WIDGET',
						data : {view : $('#dashboard_switch').val(),model : widget['uid'],data:widget['data'] , column:column,cell:cell},
						method : 'POST',
						success : function(response){
							$.dashboard.setBlocData(bloc,response);
							widgetLoad(widget,response.model);
						}
					});

					
				},
				onLoad : function(model,widget){
					if(model.refresh>0){
						setInterval(function(){widgetLoad(widget,model);},(model.refresh*1000));
					}else{
						widgetLoad(widget,model);
					}
				},
				onDelete : function(widget,bloc){
					
					$.ajax({
						dataType: "json",
						url: 'action.php?action=DELETE_WIDGET',
						data : {id : widget.id},
						method : 'POST'
					});
					if(widget.onDelete!=null){
						$.ajax({
							url : widget.onDelete,
							data : {id:widget.id},
							method : 'POST'
						});
					}
				},

				onMinimize : function(widget){
				
					$.ajax({
						dataType: "json",
						url: 'action.php?action=MINIMIZE_WIDGET',
						data : {id : widget.id},
						method : 'POST'
					});
					if(widget.onMinimize!=null){
						$.ajax({
							url : widget.onMinimize,
							data : {id:widget.id},
							method : 'POST'
						});
					}
				},
				onMaximize : function(widget){
				
					$.ajax({
						dataType: "json",
						url: 'action.php?action=MAXIMIZE_WIDGET',
						data : {id : widget.id},
						method : 'POST'
					});
					if(widget.onMaximize!=null){
						$.ajax({
							url : id.onMaximize,
							data : {id:widget.id},
							method : 'POST'
						});
					}
				},

				onMove : function(widget,sort){
				
					$.ajax({
						dataType: "json",
						url: 'action.php?action=MOVE_WIDGET',
						data : {id : widget.id , sort : sort},
						method : 'POST'
					});
					if(widget.onMove!=null){
						$.ajax({
							url : widget.onMove,
							data : {id:widget.id,	sort : sort},
							method : 'POST'
						});
					}
				}
			});



		}
	});
	
}