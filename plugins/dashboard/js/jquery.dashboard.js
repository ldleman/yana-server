/*
	Jquery.dashboard V1.0
	@author V.carruesco aka idleman
	@mail idleman@idleman.fr
	@licence CC-by-nc-sa
*/




	var dashboard_handle = null;
	var dashboard_bloc = null;
	var clickpoint = null;
	var dashboard_handle_x = null;
	var dashboard_handle_y = null;

	(function($){

		$.dashboard =  {
			addBloc : function(o) {
				//On créé le bloc avec un modèle par défaut
				
				var bloc = $('<div class="dashboard_bloc '+(o.minified==1?'dashboard_bloc_minified"':'')+'"> \
					<div class="dashboard_bloc_head">\
						<label> \
							<i class="fa fa-caret-right"></i> \
							<span>'+o.title+'</span> \
						</label> \
						<ul> \
							<li class="dashboard_minimize_button"> \
								<i class="fa fa-sort"></i> \
							</li> \
							<li class="dashboard_delete_button"> \
								<i class="fa fa-times"></i> \
							</li> \
						</ul> \
					</div> \
					<div class="dashboard_bloc_content">'+o.content+'</div> \
					</div>');

				var placement = $(o.placement).parent();
				
				if(placement.length==0) return;
			
					var column = placement.attr('id').split('_');
					column = column[column.length-1];
					var cell = bloc.index();
				

				
				if(o.widget!=null)bloc.attr('data-uid',o.widget.model);
				bloc.data('widget',{column: column,cell:cell});

				//on place le bloc
				if(o.placement!=null){
					$(o.placement).before(bloc);
					//Si le model est définit, on l'applique au bloc
					if(o.model!=null){
						$.dashboard.setBlocModel(bloc,o.model);
						//si on charge un module existant
						if(o.model.onLoad!=null) $.dashboard.loadBlocData(bloc,o.model.onLoad,o);
					}
				}
        	},
        	setBlocModel : function(bloc,model) {
				bloc.data('widget',model);
				$.dashboard.setBlocData(bloc,model);
				if(model.addToHead != null) bloc.find('.dashboard_bloc_head ul').prepend(model.addToHead);
				if(model.onEdit != null) bloc.find('.dashboard_bloc_head ul').prepend('<li class="dashboard_setting_button"><i class="fa fa-wrench"></i></li>');
        	},
        	loadBlocData : function(bloc,url,data){
        		//Affichage du chargement
        		$.dashboard.setBlocData(bloc,{title:"Chargement",content:"<div class='dashboard_loading'></div>"});
        		
        		//Chargement des donnees du bloc
        		$.ajax({
					url: url,
					data : data,
					id : data.id,
					success:function(response){
						response.id =  data.id;
						$.dashboard.setBlocData(bloc,response);
					},
					error:function(response){
						//Si la requete ne repond pas correctement, on affiche le message d'erreur
						$.dashboard.setBlocData(bloc,{title:"ERREUR",content:response.responseText});
					}
				});
        	},
        	setBlocData : function(bloc,data){
        		var widget = bloc.data('widget');
        		widget.id = data.id;
        		bloc.data('widget',widget);

        		if(data.id!=null)               bloc.attr('id','dashboard_bloc_'+data.id);
				if(data.title!=null)            bloc.find('.dashboard_bloc_head label span').html(data.title);
				if(data.content!=null)          bloc.find('.dashboard_bloc_content').html(data.content);
				if(data.background!=null)       bloc.find('.dashboard_bloc_head').css('background-color',data.background);
				if(data.color!=null)            bloc.find('.dashboard_bloc_head').css('color',data.color);
        		if(data.icon!=null)             bloc.find('.dashboard_bloc_head label i').removeAttr('class').addClass(data.icon);
        		            
				
        		if(data.minified!=null){
        			if(data.minified==0){
        				bloc.addClass('dashboard_bloc_minified');
        			}else{
        				bloc.removeClass('dashboard_bloc_minified');
        			}
        		}
			}
    	};

	
        $.fn.extend({

        dashboard: function (options){
        	var defaults = {
                column: 3,
                models: [],
            	data : []
            }
                    
            var o = $.extend(defaults, options);
				

            return this.each(function() {
					

					var obj = $(this);
					var columnWidth = (100/o.column) -2;
			

					obj.html('');
					$('.dashboard_placement').off( "click" );
					$(document).off('mousedown');
					$(document).off('mousemove');
					$(document).off('change');
					$(document).off('mousedown');
					$(document).off('mouseup');
					$('.dashboard_widget_picker').off('change');
					$('.dashboard_delete_button,.dashboard_bloc_head,.dashboard_setting_cancel_button,.dashboard_setting_save_button,.dashboard_setting_button,.dashboard_minimize_button').off('mousedown');
					

					/********************/
					/** INIT STRUCTURE **/
					/********************/

					for(i=0;i<o.column;i++){
						obj.append('<div style="min-width:300px;width:'+columnWidth+'%;" id="dashboard_column_'+i+'" class="dashboard_column"><div data-column="'+i+'" data-cell="0"  class="dashboard_placement"></div></div>');
					}

					/***************/
					/** INIT DATA **/
					/***************/

					for(var key in o.data){
						var widget =  o.data[key];
						
						var model = '';
						for(var key in o.models){
							if(o.models[key].uid == widget.model) model = o.models[key];
						}
				

						$.dashboard.addBloc({
							id : widget.id,
							minified : widget.minified,
							placement : '#dashboard_column_'+widget.column+' .dashboard_placement:eq(0)',
							model : model,
							widget : widget
						});
						if(o.onLoad!=null)
							o.onLoad(model,widget);
						
					}

					/*****************/
					/** CREATE BLOC **/
					/*****************/

					//Etape 1 : choix du modele de widget
					$('.dashboard_placement').on('click',function(){
						var content = '<select class="dashboard_widget_picker" id="dashboard_widget_picker"><option value=""> - </option>';
						for(var key in o.models){
							var widget = o.models[key];
							content += '<option value="'+key+'">'+widget.label+'</option>';
						}
						content += '</select>';
						$.dashboard.addBloc({
							title :'Choisissez un widget',
							content : content,
							placement : this
						});
					});

					//Etape 2 : creation du widget
					$(document).on('change','.dashboard_widget_picker',function(){
						var bloc = $(this).parent().parent();
						var widget = bloc.data('widget');
						var model = o.models[$(this).val()];
						$.dashboard.setBlocModel(bloc,model);
						
						if(o.onCreate!=null)
							o.onCreate(model,bloc,widget.column,widget.cell);
						
					});

					

					/*****************/
					/** DRAG & DROP **/
					/*****************/
					
					

					
					$( ".dashboard_column" ).sortable({
						connectWith: ".dashboard_column",
						handle: ".dashboard_bloc_head",
						placeholder: "dashboard_place_holder",
						update: function( event, ui ){
							
							var sort = {};
							sort.column = $('.dashboard_column').index(this);
							sort.cell = ui.item.index();
							var cells = [];
							
							$('.dashboard_column').each(function(i,column){
								$('.dashboard_bloc',column).each(function(i,bloc){
									var id = $(bloc).attr('id').replace('dashboard_bloc_','');
									cells[id] = {cell : $(bloc).index(),column: $('.dashboard_column').index(column)}
								});
							});
							sort.cells = cells;
							if(o.onMove!=null && sort.column!=null) o.onMove(ui.item.data('widget'),sort);
						}
					});
							
					/*************/
					/** Setting **/
					/*************/

					$(document).on('mousedown','.dashboard_setting_button',function(e){
						var bloc = $(this).parent().parent().parent();
						var widget = bloc.data('widget');
						widget.id = bloc.attr('id').replace('dashboard_bloc_','');
						bloc.data('widget',widget);


						bloc.find('.dashboard_bloc_content').load(widget.onEdit,{id:widget.id},function(){
							
							bloc.find('.dashboard_bloc_content').append('<div class="dashboard_setting_form_options"></div>');

							var options = bloc.find('.dashboard_bloc_content .dashboard_setting_form_options');
						
							if(widget.onSave!=null)
								options.append('<button class="dashboard_setting_save_button">Enregistrer</button> ');
							
							options.append('<button class="dashboard_setting_cancel_button">Annuler</button>');
							
							
						});
						e.preventDefault();
						e.stopPropagation();
					});
					$(document).on('mousedown','.dashboard_setting_save_button',function(e){
						var bloc = $(this).parent().parent().parent();
						var widget = bloc.data('widget');

						data = {id : widget.id};
						bloc.find('.dashboard_bloc_content').find('input,select,textarea').each(function(i,input){
							if(input.id!=null){
								var inp = $(input);
								if(inp.attr('type') == 'checkbox'){
									data[input.id] = inp.is(':checked')?1:0;
								}else{
									data[input.id] = $(input).val();
								}
							}
						});

						$.ajax({
							url : widget.onSave,
							data : data,
							complete : function(){
								$.dashboard.loadBlocData(bloc,widget.onLoad,{id:widget.id});
							}
						});
						e.preventDefault();
						e.stopPropagation();
					});
					
					$(document).on('mousedown','.dashboard_setting_cancel_button',function(e){
						var bloc = $(this).parent().parent().parent();
						var widget = bloc.data('widget');
						$.dashboard.loadBlocData(bloc,widget.onLoad,{id:widget.id});
						e.preventDefault();
						e.stopPropagation();
					});
					$(document).on('mousedown','.dashboard_delete_button',function(e){
						var bloc = $(this).parent().parent().parent();
						var widget = bloc.data('widget');
						bloc.remove();
						if(o.onDelete!=null) o.onDelete(widget,bloc);
						e.preventDefault();
						e.stopPropagation();
					});

					$(document).on('mousedown','.dashboard_minimize_button',function(e){
						var bloc = $(this).parent().parent().parent();
						var widget = bloc.data('widget');
						var content = bloc.find('.dashboard_bloc_content');
						if(content.is(':visible')){
							bloc.find('.dashboard_bloc_content').slideUp(150);
							bloc.addClass('dashboard_bloc_minified');
							if(o.onMinimize!=null) o.onMinimize(widget);
						}else{
							bloc.find('.dashboard_bloc_content').slideDown(150);
							bloc.removeClass('dashboard_bloc_minified');
							if(o.onMaximize!=null) o.onMaximize(widget);
						}
						e.preventDefault();
						e.stopPropagation();
					});
				
            });
		},
		
        });
        
    })(jQuery);
	

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



