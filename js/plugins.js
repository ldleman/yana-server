// Avoid `console` errors in browsers that lack a console.
(function() {
    var method;
    var noop = function () {};
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeline', 'timelineEnd', 'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());


	


	//Affiche un message 'message' de type 'type' pendant 'timeout' secondes
    $.message = function (type,message,timeout){
        $.toast({ type: type, content: message, timeout: timeout });
    }
	
	

	//Permet  les notifications types toast sans dépendance de librairie/css/html 
	$.toast = function (options) {
	    var defaults = {
	        title: null,
	        content: '',
	        type: 'info',
	        timeout: 3000
	    };
	    var o = $.extend(defaults, options);
	    var css = "color:#ffffff;display:none;border-radius:1px;margin-bottom:10px;text-align:center;width:100%;box-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);position:relative;";
	    var types = {
	        error: { css: "background:#DC5026;", icon: 'exclamation-triangle' },
	        info: { css: "background:#2AA7EA;", icon: 'info-circle' },
	        success: { css: "background:#B2CE16;", icon: 'check' },
	    }
	    css += types[o.type].css;
	    o.icon = types[o.type].icon;

	    if ($('.toastContainer').length == 0)
	        $('body').append('<div class="toastContainer" style="z-index:100;position:absolute;right:10px;top:60px;width:350px;"></div>');

	    var popupContainer = $('.toastContainer');

	    var popup = $('<div style="' + css + '"><i class="toastRemove fa fa-times" style="position:absolute;top:5px;right:5px;display:none;cursor:pointer;" onclick="$(this).parent().remove();"; style="position:absolute;top:5px;right:5px;cursor:pointer;"></i><h1 class="toastTitle" style="margin:0;padding:10px;"></h1><i class="toastIcon" style="display:inline-block;vertical-align:top;width:30px;font-size:20px;margin:10px 0;"></i><p style="display: inline-block;margin-top: 10px;padding:0 10px 0 10px;width:300px;" class="toastContent"></p><div style="clear:both;"></div></div>');
	    $(popupContainer).append(popup);
	    if (o.title) $('.toastTitle', popup).html(o.title);
	    $('.toastIcon', popup).attr('class', 'fa fa-' + o.icon);

	    if (!o.title) $('.toastTitle', popup).remove();

	    $('.toastContent', popup).html(o.content);
	    popup.fadeIn();
	    
	    window.scrollTo(0, popup.offset().top);
	    if (o.timeout != 0) {
	        setTimeout(function () { popup.fadeOut(); }, o.timeout);
	    } else {
	        popup.find('.toastRemove').show();
	    }
	}

	
	$.page = function(element){
        	
		var path = window.location.href.split('/') ;
		path = path[path.length-1];
		path = path.split('.php');
		path = path[0];
        return path;
    }

	$.getForm= function(element){
        	var o = {};
			var obj = $(element);
			for(var key in obj.data()){
				if(key!="action" &&  key != "id") continue;
				o[key] = obj.attr('data-'+key);
			}
			
			$('input,select,textarea',obj).each(function(i,element){
					 if(element.id!=null && element.id!=""){
						if($(element).attr("type")=='checkbox' || $(element).attr("type")=='radio'){
							o[element.id] = ($(element).is(':checked')?1:0);
						}else{
							o[element.id] = $(element).val();
						}
					 }
			});

           return o;
    }
	
	$.setForm= function(element,data){
        	var o = {};
			var obj = $(element);
			$('input,select,textarea',obj).each(function(i,element){
					
					 if(element.id!=null && element.id!=""){
						 
						if(data[element.id]!=null){
							if($(element).attr("type")=='checkbox' || $(element).attr("type")=='radio'){
								$(element).prop("checked",data[element.id]==1 || data[element.id]=='true' ?true:false);
							}else{
								
								$(element).val(data[element.id]);
							}
						}

					 }
			});
           return o;
    }
	
	
	$.action =  function(data,success,error) {
			$.ajax({
				dataType : 'json',
				method : 'POST',
				url : 'action.php',
				data : data,
				success: function(response){
					
					if(response && !response.error){
						if(success!=null)success(response);
					}else{
						$.message('error','ERREUR : '+"\n"+response.error);
						if(error!=null) error(response);
					}
				},
				error : function(response){
					
					if(response.status == 200 && $.localhost() ){
						$('body').append('<div class="debugFrame" style="box-shadow:0px 0px 3px rgba(0,0,0,0.8);z-index:10000;padding:5px;position:absolute;left:0;top:0;width:40%;min-height:100%;border-right:5px solid #DDDDDD;background:#DDEBF9"><h4>Action debug <i onclick="$(this).parent().parent().remove()" class="fa fa-times pointer"></i></h4>'+response.responseText+'</div>');
					}else{
						if(error!=null){ 
							error(response); 
						}else{
							$.message('error','Erreur indefinie, merci de contacter un administrateur');
						}
					}
	


				}
			});
	}
	
	$.localhost = function(){
    		return (document.location.hostname=='127.0.0.1' || document.location.hostname=='localhost');
    	}
	
	
	$.hashData = function(name){

			var page = window.location.hash.substring(1);

			page += "&"+window.location.search.substring(1);

			data = {};
			if(page!='' && page!= null){
				options = page.split('&');
				var data = {};
				for(var key in options){
					infos = options[key].split('=');
					data[infos[0]] = infos[1];
				}
			}
			if(name == null) return data;
			if(typeof name === "object"){
				data = name;
				hashstring = '';
				for(var key in data)
					hashstring+= "&"+key+"="+data[key];
				hashstring = hashstring.substring(1);
				window.location.hash = hashstring;
				return;
			}

			return typeof data[name] == "undefined" ? '':data[name];
	}


	$.urlParam = function (name,value) {
	    var parameters = window.location.href.match(/[\\?&]([^&#]*)=([^&#]*)/g);
	    var data = {};
	    for (var key in parameters) {
	        var couple = parameters[key].substring(1, parameters[key].length).split('=');
	        data[couple[0]] = couple[1];
	    }
		if(name == null) return data;
	    if (value == null) 
	        return data[name] ? data[name] : null;
	    if (value != false) data[name] = value;
	    var url = '?';
	    for (var key in data) {
	        if (value == false && key == name) continue;
	        url += key + '=' + data[key]+'&';
	    }
	    window.history.pushState('', document.title, url.substring(0, url.length-1));
	}



	$.upzoneTransfert = function(e,o){
		var list = [];
		if(e.dataTransfer) {
			filelist = e.dataTransfer.files;
		} else if(e.target) {
			filelist = e.target.files;
		}

		if (!filelist || !filelist.length ) return;

		        totalSize = 0;
		        totalProgress = 0;
		

		        for (var i = 0; i < filelist.length && i < 5; i++) {
		            list.push(filelist[i]);
		            totalSize += filelist[i].size;
		        }

		        if (list.length) {
		           

		            var nextFile = list.shift();
		            //if (nextFile.size >= 262144) { // 256 kb
		         
				        var xhr = new XMLHttpRequest();
				        xhr.open('POST', o.url);
				        xhr.onload = function() {
				           // result.innerHTML += this.responseText;
				           // handleComplete(file.size);
				           if(o.success!=null)o.success(this.responseText);
				        };
				        xhr.onerror = function() {
				           // result.textContent = this.responseText;
				          //  handleComplete(file.size);
				          console.log(this.responseText,'error');
				          if(o.error!=null)o.error(this.responseText);
				        };
				        xhr.upload.onprogress = function(event) {
				           // handleProgress(event);
				           //console.log(event);
				        }
				        xhr.upload.onloadstart = function(event) {
				        }

				        // création de l'objet FormData
				        var formData = new FormData();
				        for(var k in o)
				        	formData.append(k, o[k]);
				        
				      
				        formData.append('file', nextFile);
				        xhr.send(formData);
		           
		 }

	}

	
$.fn.extend({
	
	upzone : function (option){
		var defaults = {
			url : '',
			success : function(){},
			error : function(){}
		}
		
		var o = $.extend(defaults,option);
		return this.each(function() {
			var obj = $(this);

			obj.before('<input type="file" id="test" style="display:none">');
			
			$('#test').change(function(e) {
				
				$.upzoneTransfert(e,o);

     	 	});
			obj.click(function(){
				$('#test').trigger('click');
			});

			obj.get(0).addEventListener('drop', function(event) {
		        event.stopPropagation();
		        event.preventDefault();

		        $.upzoneTransfert(event,o);

		      	//var filelist = event.dataTransfer.files;
		  
		        



    		}, false);

        	obj.get(0).addEventListener('dragover', function handleDragOver(event) {
		        event.stopPropagation();
		        event.preventDefault();

		      
		    }, false);

		});
	},
	
	clear: function (){
        return this.each(function() {
        	var obj = $(this);
        	obj.find('input,select,textarea').val();
    	});
	},

	fill: function (option,callback){
		
            return this.each(function() {
				
				var obj = $(this);
				var model = null;
				var container = null;
				
				if(obj.prop("tagName") == 'UL'){
					container = obj;
					model = container.find('li:first-child');
					container.find('li:visible').remove();
				}else if(obj.prop("tagName") == 'TABLE'){
					container = obj.find('tbody');
					model = container.find('tr:first-child');
					container.find('tr:visible').remove();
				}else{
					container = obj;
					childName = container.children().get(0).nodeName;
					model = container.find(childName+':first-child');
					container.find(childName+':visible:not(.nofill)').remove();
				}
				var tpl = model.get(0).outerHTML;
				
				//fix jquery backslahes break
				tpl = tpl.replace(/{{##/g,'{{/').replace(/{{\/(.*)}}=""/g,'{{/$1}}');
			
				//fix images url not found on template
				tpl = tpl.replace(/(<img\s[^>]*\s)(data-src)/g,'$1src');
				$.action(option,function(r){
					for(var key in r.rows){
						var line = $(Mustache.render(tpl,r.rows[key]));
						container.append(line);
						line.show();
					}
					
					if(callback!=null)callback(r);
				});
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
					monthNames: ["Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","Décembre"],
					firstDay: 1,
					changeMonth: true,
					yearRange: "-100:+0",
					changeYear: true,
					onSelect: function(dateText, inst) { $( this).trigger( "blur" ); }
				});

            });
	}
});