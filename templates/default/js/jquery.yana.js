
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
			
		menu: function(options) {
                var defaults = {
                    margin_x: 0,
					margin_y: 20
                }
                    
                var options = $.extend(defaults, options);
				
				
            return this.each(function() {
                    var o = options;
					var obj = $(this);
					
					
				$('li',obj).hover(
				function(){
					var position  = $(this).position();
					$('ul',this).css("left",position.left+o.margin_x+"px");
					$('ul',this).css("top",position.top+o.margin_y+"px");
					$('ul',this).fadeIn(100);
				},
				function(){
					$('ul',this).fadeOut(100);
				}
				);
				

				
            });
        },
			
		
		page: function(options) {
                var defaults = {
                    url: function(){},
					success: $.callBack,
					data: {}
                }
                    
                var options = $.extend(defaults, options);
				
				
            return this.each(function() {
                    var o = options;
					var obj = $(this);
					
					
				obj.html('<div class="preloader">Chargement en cours...</div>');
				$.ajax({
						url: o.url,
						type:"POST",
						data:o.data,
						success: function(response) {
							obj.html(response);
							o.success();
						}
					});

				
            });
        },
		
            send: function(options) {
                var defaults = {
                    url: function(){},
					success: $.callBack,
					mandatory : 'mandatory',
					data: {}
                }
                    
                var options = $.extend(defaults, options);
				
				
            return this.each(function() {
                    var o = options;
					var obj = $(this);
					var mandatoryFields = new Array();
					
					$('input,select,textarea',obj).each(function(i,element){
					 if(element.id!=null && element.id!=""){
						if($(element).attr("type")=='checkbox' || $(element).attr("type")=='radio'){
							eval('o.'+element.id+'="'+($(element).is(':checked')?1:0)+'";');
						}else{
							eval('o.'+element.id+'="'+$(element).val().replace("'","’")+'";');
						}
						if($(element).hasClass(o.mandatory) && ($(element).val() =="" || $(element).val() =="0" || $(element).val() == null))
							mandatoryFields.push($(element).attr("title"));
					 }
					});
					
					
					if(mandatoryFields.length != 0){
						alert('Veuillez renseigner le(s) champ(s) obligatoires!\n ('+mandatoryFields.join(',')+')');
					}else{
						$.method(o);
					}
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
	   
	   
		mandatory: function (options){
			
			var defaults = {
                    mandatory: 'mandatory',
					warning: 'warning'
                }
                    
            var options = $.extend(defaults, options);
			
            return this.each(function() {
				var obj = $(this);
				var o = options;
				
				 $('.'+o.mandatory,obj).each(function(){
					
					if($.trim($(this).val()).length==0){
						$(this).addClass(o.warning);
					}else{
						$(this).removeClass(o.warning);
					}
				 
					
					$(this).keyup(function(){
						if($.trim($(this).val()).length==0){
							$(this).addClass(o.warning);
						}else{
							$(this).removeClass(o.warning);
						}
					});
					$(this).change(function(){
						if($.trim($(this).val()).length==0){
							$(this).addClass(o.warning);
						}else{
							$(this).removeClass(o.warning);
						}
					});
				});
				
            });
	   },
	   readonly: function (options){
			
			var defaults = {
                    mandatory: 'readonly'
                }
                    
            var options = $.extend(defaults, options);
			
            return this.each(function() {
				var obj = $(this);
				var o = options;
				
				 $('.'+o.mandatory,obj).each(function(){
						$(this)
						$(this).attr('readonly','readonly').addClass(o.mandatory).attr('disabled','true').attr('title','Champ en lecture seule').attr('style','cursor:help;');
				});
				
            });
	   }
		
		
        });
        
    })(jQuery);
	
