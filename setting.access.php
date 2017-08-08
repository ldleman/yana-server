
<div class="row">
	<div class="col-md-12">
		<h1>Plugins</h1>
		<ul id="plugins" class="list-group">
			<li class="list-group-item noDisplay" data-folder="{{folder.name}}">
				<h4 class="list-group-item-heading">{{name}} <span class="label label-info">v{{version}}</span></h4>
				<p class="list-group-item-text">{{description}}</p>
				
				<a class="pointer" onclick="$(this).next('ul').slideToggle(200);"><i class="fa fa-search"></i> + d'Infos</a>
				<ul class="noDisplay">
					<li>ID : {{id}}</li>
					<li><span class="label label label-default">{{author.name}}</span></li>
					<li>Licence: {{#licence.url}}<a href="{{licence.url}}">{{/licence.url}}{{licence.name}}{{#licence.url}}</a>{{/licence.url}}</li>
					<li>Version: <code>{{version}}</code></li>
					<li>Site web: <a href="{{url}}">{{url}}</a></li>
					<li>Dossier: {{folder.path}}</li>
					<li>Pré-requis : <ul>{{#require}}<li>{{id}} - <span class="label label-info">{{version}}</span></li>{{/require}}</ul></li>
				</ul>
				<label class="activator">
					<small>On/Off</small>
					<label class="toggle">
						<input {{#state}}checked=""{{##state}} type="checkbox">
						<span class="handle"></span>
					</label>
				</label>
				
			</li>
		</ul>
	</div>
</div>

