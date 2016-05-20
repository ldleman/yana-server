
	(function($){
	
	    $.callBack =  function(options) {
	        if(options!=null){
                if(options.message!=null)alert(options.message);
                if(options.fonction!=null && options.redirection==null)eval(options.fonction);
                
                if(options.redirection!=null){
                $.ajax({
                      url: options.redirection,
                      type:"GET",
                      success: function(response) {
                          $((options.section==null?'#content':options.section)).html(response);
                          if(options.fonction!=null)eval(options.fonction);
                      }
					});
			    }
                
            }
        }

         $.action =  function(data,success,error) {
			
			$.ajax({
				dataType : 'json',
				method : 'POST',
				url : 'action.php',
				data : data,
				async:(data.async!=null?data.async:true),
				success: function(response){
					if(response.errors == null ) response.errors.push('Erreur indefinie, merci de contacter un administrateur');
					if(response.errors.length ==0 ){
						if(success!=null)success(response);
					}else{
						alert('ERREUR : '+"\n"+response.errors.join("\n"));
						if(error!=null) error(response);
					}
				},
				error : function(){
					alert('Erreur indefinie, merci de contacter un administrateur');
				}
			});
        }

		$.urlParam = function(name){
			var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(window.location.href);
			if (results==null){
			return null;
			}else{
				return results[1] || 0;
			}
		}
		
	
		
		$.method =  function(options) {
                var defaults = {
                    url: function(){},
					success: $.callBack
					
                }
                    
                var options = $.extend(defaults, options);
            
                    var o = options;
                   
					$.ajax({
                      url: o.url,
                      type:"POST",
                      contentType: 'application/json',
                      dataType: 'json',
                      data:$.toJSON(o),
                      success: function(response) {
                          o.success(eval(response.d));
                      }
					});
            
        }
	
	
	
        $.fn.extend({
			

		chart: function(options) {
                var defaults = {
                    type: 'bar',
					success: $.callBack,
					label : ["January","February","March","April","May","June","July"],
					data:  [0,1,2,3,4,5,6],
					options : {responsive : true},
					backgroundColor : ["rgba(220,220,220,0.5)"],
					borderColor : ["rgba(220,220,220,0.8)"],
					backgroundColorHover: ["rgba(220,220,220,0.75)"],
					borderColorHover: ["rgba(220,220,220,1)"],
					segmentShowStroke:false
                }
                    
            var options = $.extend(defaults, options);
				
				
            return this.each(function() {
                    var o = options;
					var obj = $(this);
				
				var graphic = new Chart(obj[0].getContext("2d"));
				
				var conf = {
							labels : o.label,
							datasets:[{
								fillColor : o.backgroundColor[0],
								strokeColor : o.borderColor[0],
								highlightFill: o.backgroundColorHover[0],
								highlightStroke: o.borderColorHover[0],
								data : o.data
							}]
						};
							
				
				switch(o.type){
					case 'line':
						graphic.Line(conf,o.options);
					break;		

					case 'pie':
						var conf = [];
						for(var key in o.data){
							var backgroundColor = o.backgroundColor[key] == null ? '#cecece': o.backgroundColor[key];
							var backgroundColorHover = o.backgroundColorHover[key] == null ? '#dedede': o.backgroundColorHover[key];
							conf.push({ value : o.data[key],highlight : backgroundColorHover,color : backgroundColor,label : o.label[key]  });
						}
						
						graphic.Pie(conf,o.options);
					break;
					
					case 'doughnut':
						var conf = [];
						for(var key in o.data){
							var backgroundColor = o.backgroundColor[key] == null ? '#cecece': o.backgroundColor[key];
							var backgroundColorHover = o.backgroundColorHover[key] == null ? '#dedede': o.backgroundColorHover[key];
							conf.push({ value : o.data[key],highlight : backgroundColorHover,color : backgroundColor,label : o.label[key] });
						}

						o.options.segmentShowStroke = false;
						o.options.percentageInnerCutout = 60;
						
						var myGraphic = graphic.Doughnut(conf,o.options);
						

					break;

					case 'bar':
					default :
						graphic.Bar(conf,o.options);
					break;
				}
				
            });
        },


        toData: function() {
        	var data = {};

			$('input,select,textarea',this).each(function(i,element){
				if(element.id!=null && element.id!=""){
					if($(element).attr("type")=='checkbox' || $(element).attr("type")=='radio'){
						data[element.id] = $(element).is(':checked')?1:0;
					}else{
						data[element.id] = $(element).val();
					}
				 }
			});	
          
            return data;
        },


		
        send: function(options) {
			console.warn('Function $(form).send is deprecated, please replace by $(form).toData + ajax request');
			var defaults = {
				url: function(){},
				success: $.callBack,
				data: {}
            }
                    
           var options = $.extend(defaults, options);

			return this.each(function() {
				var o = $.extend( $(this).toData(), o );
				$.method(o);
			});
        },
		
 
	   enter: function (option){
            return this.each(function() {
				var obj = $(this);
				obj.keydown(function(event){
				if(event.keyCode == 13){
					option();
					return false;
				}
				});
            });
	   },

		date: function (){
            return this.each(function() {
				var obj = $(this);
				obj.datepicker({
					dateFormat: "dd/mm/yy",
					dayNames: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"],
					dayNamesMin: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"],
					dayNamesShort: ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"],
					monthNames: ["Janvier","Février","Mars","Avril","Mai","Juin","Jullet","Aout","Septembre","Octobre","Novembre","Décembre"],
					firstDay: 1
				});
            });
	   },

	   autocomplete: function (options){
	   		var defaults = {
                    source: []
                }
            var options = $.extend(defaults, options);
            return this.each(function() {
            	var o = options;
				var obj = $(this);
				obj.typeahead(o);
            });
	   },
	   

      });
        
    })(jQuery);
	
