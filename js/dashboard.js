
		//handle target
		var target = false;
		
		$(document).ready(function(){

			$.action({
				action : 'search_widget',
				dashboard : $('#view li[data-selected]').attr('data-id')
			},function(r){
				for(var i in r.rows){
					var widget = r.rows[i];
					addWidget(widget);
					loadWidget(widget);
				}
				setInterval(function(){
					$.action({
						action : 'refresh_widget',
						dashboard : $('#view li[data-selected]').attr('data-id')
					},function(r){
						for(var i in r.rows){
							var widget = r.rows[i];
							updateWidget(widget);
						}
					});
				},3000);
			});

		});
			
		//Chargement du contenu php du widget
		function loadWidget(widget){

			$.getJSON(widget.load,$.extend(widget,{content:''}),function(r){
				updateWidget(r);
				var data = $.extend($('.widget[data-id="'+widget.id+'"]').data('data'), r.widget);
				var init = 'widget_'+widget.model+'_init';
				if(window[init]!=null) window[init]();
			});
		}
		
		//Ajout d'un widget
		function addWidget(data){
			var tpl = $('#dashboard .widget:hidden').get(0).outerHTML;
			var widget = $(tpl);
			$('#dashboard').append(widget);
			if(data.js!=null){
				for(k in data.js){
					var js = data.js[k];
					if($('script[src="'+js+'"]').length!=0) continue;
					var jsFile= document.createElement('script');
					jsFile.setAttribute("type","text/javascript");
					jsFile.setAttribute("src", js);
					document.getElementsByTagName("body")[0].appendChild(jsFile);
				}
			}
				
			if(data.css!=null){
				for(k in data.css){
					var css = data.css[k];
					if($('script[src="'+css+'"]').length!=0) continue;
					var cssFile= document.createElement('link');
					cssFile.setAttribute("rel","stylesheet");
					cssFile.setAttribute("type","text/css");
					cssFile.setAttribute("href", css);
					document.getElementsByTagName("body")[0].appendChild(cssFile);
				}
			}

			data.options.push({label : '',icon : 'fa-times', function : 'delete_widget(this);'});
			renderWidget(widget,data);
		}
		
		//Modification d'un widget existant
		function updateWidget(data){
			var widget = $('.widget[data-id="'+data.id+'"]');
			var data = $.extend(widget.data('data'), data);
			renderWidget(widget,data);
		}
		
		//Enregistrement de toutes les positions de widget
		function saveWidgetPosition(){
			var positions = [];
			$('.widget:visible').each(function(i,element){
				positions.push({id:$(element).attr('data-id'),position:$(element).index()});
			});
			console.log('MOVE_WIDGETS',positions);
		}
		
		//Mise à jour des infos d'un élement widget à partir d'un object data
		function renderWidget(widget,data){
			widget.attr('data-id',data.id);
			widget.removeClass (function (index, css) {
				return (css.match (/(^|\s)col-md-\S+/g) || []).join(' ');
			});
			widget.attr('data-id',data.id)
			.addClass('col-md-'+data.width)
			.find('.widget_header')
			.css('background',data.background)
			.find('i:eq(0)').attr('class','fa '+data.icon);
				
			widget.find('.widget_header span:eq(0)').html(data.title);
			widget.find('.widget_content').html(data.content);
				
			var options = '';

			
			for(var k in data.options){
				var option = data.options[k];
				options+='<li onclick="'+option.function+'"><i class="fa '+option.icon+'"></i> '+option.label+'</li>';
			}
				
			widget.find('.widget_options').html(options);
				
			widget.data('data',data);
			widget.show();
			return widget;
		}
		
		//Récuperation de l'élement du widget cliqué pour le drag (gestion du handle / bar de titre)
		function mouseDown(ev){
			target = ev.target;
		}
		
		//Ajout d'une classe au survol d'un emplacement de drop
		function dragOver(ev) {
			ev.preventDefault();
			$(ev.target).addClass('dragover');
		}
		
		//Supression d'une classe a la fin survol d'un emplacement de drop
		function dragOut(ev) {
			ev.preventDefault();
			$(ev.target).removeClass('dragover');
		}
		
		//Départ de déplacement du widget
		function dragStart(ev) {
			if (!$(ev.target).find('.widget_header').get(0).contains(target)) {
				ev.preventDefault();
				return;
			}
			ev.dataTransfer.setData("from", $(ev.target).attr('data-id'));
		}
		
		//Déplacement du widget (drop après drag)
		function drop(ev) {
			ev.preventDefault();
			$('.widget_dropper').removeClass('dragover');
			var from =$('.widget[data-id="'+ev.dataTransfer.getData("from")+'"]');
			var to = $(ev.target).closest('.widget');
			if(to.attr('data-id') == from.attr('data-id')) return;
			if($(ev.target).attr('data-side')=='left'){
				to.before($(from).detach());
			}else{
				to.after($(from).detach());
			}
			
			saveWidgetPosition();
			var data = from.data('data');
			if(data.move != null){
				$.getJSON(data.move,$.extend(data,{content:''}));
			}
		}
		//Supression du widget
		function delete_widget(element){
			var element = $(element).closest('.widget');
			var data = element.data('data');
			
			element.remove();
			if(data.delete != null){
				$.getJSON(data.delete,$.extend(data,{content:''}));
			}
			console.log('DELETE_WIDGET',data.id);
		}