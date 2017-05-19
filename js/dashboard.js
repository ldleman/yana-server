
		//handle target
		var target = false;
		var refreshInterval = null;
		
		function init_dashboard(){

			
			loadDashBoard();
			$('#dashboardView li:not(:last-child)').click(function(){
				$('#dashboardView li').removeAttr('data-selected');
				$(this).attr('data-selected',1);
				loadDashBoard();
			});
		}
		
		function loadDashBoard(){

			$.action({
				action : 'search_widget',
				dashboard : $('#dashboardView li[data-selected]').attr('data-id')
			},function(r){
				$('#dashboard .widget:visible').remove();
				for(var i in r.rows){
					var widget = r.rows[i];
					addWidget(widget);
					loadWidget(widget);
				}
				clearInterval(refreshInterval);
				refreshInterval = setInterval(function(){
					$.action({
						action : 'refresh_widget',
						dashboard : $('#dashboardView li[data-selected]').attr('data-id')
					},function(r){

						for(var id in r.rows){
							var widget = r.rows[id];

							if(widget.widget){
								var header = $('.widget[data-id="'+id+'"]').find('.widget_header'); 
								if(widget.widget.title) header.find('span').text(widget.widget.title);
								if(widget.widget.icon) header.find('i').attr('class','fa '+widget.widget.icon);
								if(widget.widget.background) header.css('backgroundColor',widget.widget.background);
							}

							if(widget.callback){
								if(window[widget.callback]!=null) window[widget.callback]($('.widget[data-id="'+id+'"]'),widget.data);
							}
							
						}


					});
				},3000);
			});

		}
			
		//Chargement du contenu php du widget
		function loadWidget(widget){
			$.getJSON(widget.load,$.extend(widget,{content:''}),function(r){
				updateWidget(r);
				var data = $.extend($('.widget[data-id="'+widget.id+'"]').data('data'), r.widget);
				var init = 'widget_'+widget.model+'_init';
				if(window[init]!=null) window[init]();
			});
		}
		
		//Ajout (manuel par l'user) d'un widget
		function addNewWidget(){
			$.action({
				action : 'add_widget',
				dashboard : $('#dashboardView li[data-selected]').attr('data-id'),
				widget : $('#widgetList').val()
			},function(r){
				if(r.message) $.message('info',r.message);
				$('#addWidgetModal').modal('hide');
				loadDashBoard();
			});
		}

		//Configuration d'un widget
		function configure_widget(element){
		
			var element = $(element).closest('.widget');
			var data = element.data('data');
			
			$.getJSON(data.configure,{
				dashboard : $('#dashboardView li[data-selected]').attr('data-id'),
				widget : data.id
			},function(r){
				$('#configureWidgetModal .pluginContent').attr('data-id',data.id).html(r.content);
				$('#configureWidgetModal').modal('show');
			});

		}

		function saveWidgetConfiguration(){
			var data = {}
			data.widget = $('#configureWidgetModal .pluginContent').attr('data-id');
			data.action = 'configure_widget';
			data.data = $.getForm($('#configureWidgetModal .pluginContent'));

			$.getJSON('action.php',data,function(r){
				loadDashBoard();
				$('#configureWidgetModal').modal('hide');
			});
		
		}
		
		
		//Ajout (depuis le code) d'un widget
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
			//console.log('MOVE_WIDGETS',positions);


			$.action({
				action : 'move_widgets',
				dashboard : $('#dashboardView li[data-selected]').attr('data-id'),
				positions : positions,
			},function(r){
				
			});

		}
		
		//Mise à jour des infos d'un élement widget à partir d'un object data
		function renderWidget(widget,data){
			widget.attr('data-id',data.id);
			widget.removeClass (function (index, css) {
				return (css.match (/(^|\s)col-md-\S+/g) || []).join(' ');
			});
			widget.attr('data-id',data.id)
			.attr('data-load',data.load)
			.attr('data-configure',data.configure)
			.attr('data-delete',data.delete)
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

			if(data.configure) options+="<li title='Configurer' onclick='configure_widget(this);'><i class='fa fa-wrench'></i></li>";
			options+="<li title='Supprimer' onclick='delete_widget(this);'><i class='fa fa-times'></i></li>";
			

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
			
			$.action({
				action : 'delete_widget',
				dashboard : $('#dashboardView li[data-selected]').attr('data-id'),
				widget : data.id,
			},function(r){
				element.remove();
				if(r.message) $.message('info',r.message);
				if(data.delete != null){
					$.getJSON(data.delete,$.extend(data,{content:''}));
				}
			});

			
			
		}