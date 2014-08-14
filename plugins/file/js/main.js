$(document).ready(function(){
	Dropzone.autoDiscover = false;

	$.fn.extend({
		upload: function(options) {
            var data = {
				label : 'Faites glisser le(s) fichier(s) à envoyer sur cette zone ou cliquez dessus.',
				url : 'action.php?action=component_file_upload',
				preview : '<div class="dz-preview dz-file-preview"> \
					  <div class="dz-details"> \
					    <div class="dz-filename"></div> - \
					    <div class="dz-size"></div> \
					    <div class="dz-options"></div> \
					    <div class="dz-tags"></div> \
					  </div> \
					  <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div> \
					</div>'
			}
		
			data = $.extend(data, options);
			$(this).dropzone({
				dictDefaultMessage : data.label,
				url : data.url,
				previewTemplate : data.preview,
				init: function() {
				    this.on("success", function(file, json) {
				       if(data.success != null) data.success(file, json);
				    });
			  	}
			});
        },
    });


});

