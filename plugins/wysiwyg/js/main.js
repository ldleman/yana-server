(function($){


$.trumbowyg.upload.serverPath = 'action.php?action=upload';

//plugin alias wrapper
$.fn.extend({
        wysiwyg : function(o1,o2){
        	return this.each(function() {
	        	$(this).trumbowyg(o1,o2);
        	});
        }
 });
})(jQuery);