$(document).ready(function(){
	$('.date').date();
	$('.infobulle').tooltip({placement:'bottom'});

	if($.urlParam('init')=='1'){
		$.getJSON($("#UPDATE_URL").html(),function(data){});
	}

	var page = window.location.href.split('/');
	page = page[page.length-1];

	if(page=='index.php' && $.trim($('.container .row:eq(0)').html()) == ''){
			$('.container .row:eq(0)').html('<div class="alert alert-warning">Aucune plugin n\'utilise la home actuellement, souhaitez vous <a href="action.php?action=ENABLE_DASHBOARD">activer la dashboard?</a></div>');
	}

	$('#btnSearchPlugin').trigger("click");

});




function maj(data){
 	server = data.maj["yana-server"];
    	
		if(server.version!=null && server.version!=$("#PROGRAM_VERSION").html()){
			$('#notification').html('1');
			$('#notification').css('visibility','visible');
			$('#notification').attr('title','Version '+server.version+' disponible.');
			if(server.link != null) $('#notification').attr('onclick','window.location="'+server.link+'";');
		}
}


function setRankAccess(rank,section,access,elem){
	var data = {
				action : 'set_rank_access',
				rank : rank,
				section : section,
				access : access,
				state : 0
			}

	if($(elem).is(':checked')) data.state = 1;

	$.ajax({
		  url: "action.php",
		  type: "POST",
		  data: data
		});


}


function searchPlugin(keyword){
	$('#resultsPlugin').html('Chargement en cours...');
	var baseUrl = (location.protocol == 'https:'?"https://market.idleman.fr:666":"http://127.0.0.1/mark31")
	$.getJSON(baseUrl+"/action.php?action=api&s=yana-server&m=search&k="+keyword+"&callback=?");
}
function jsonp(data){
	
	switch(data.method){
		case 'search':
			$('#resultsPlugin').html('');
			if(data.results!=null && data.results.length>0){
				for(var key in data.results){
					var plugin = data.results[key];
					tpl = 
					'<li>\
						<ul>\
							<li><h4>Nom: </h4>'+plugin.name+'</li>\
							<li><h4>Auteur: </h4><a href="mailto:'+plugin.mail+'">'+plugin.author+'</a></li>\
							<li><h4>Licence: </h4><a href="http://google.fr/#q='+plugin.licence+'">'+plugin.licence+'</a></li>\
							<li><h4>Version: </h4><code>'+plugin.version+'</code></li>\
							<li><h4>Site web: </h4><a href="'+plugin.link+'">'+plugin.link+'</a></li>\
							<li>'+plugin.description+'</li>\
							<li><button class="btn" onclick="installPlugin(\''+plugin.dll+'\');">Installer</button></li>\
						</ul>\
					</li>';
					$('#resultsPlugin').append(tpl);
				}
			}else{
				$('#resultsPlugin').append('<li>Aucun résultats pour cette recherche.</li>');
			}	
		break;
		case 'get':
		
		break;
	}
}

function installPlugin(url){
	$('#resultsPlugin').load('action.php?action=installPlugin&zip='+encodeURIComponent(url));
}
