<?php 
global $myUser;
if(!$myUser->can('log','read')) throw new Exception("Permissions insuffisantes");
?>
<div class="row">
	<div class="col-md-12">
		<h3>Logs</h3>

		<label for="rank">Rechercher</label>
		<div class="input-group">
			<input id="keyword" type="text" class="form-control">
			<span class="input-group-btn">
			<div onclick="search_log();" class="btn btn-info"><i class="fa fa-search"></i> Chercher</div>
			</span>
		</div>
	
		<br/>
		<div class="panel panel-default">
			<div class="panel-heading">Logs</div>
			<table id="logs" class="table">
				<thead>
					<tr>
						<th>Date</th>
						<th>Libellé</th>
						<th>Utilisateur</th>
						<th>Ip</th>
					</tr>
				</thead>
				<tbody>
					<tr data-id="{{id}}" class="noDisplay">
						<td>{{date}}</td>
						<td>{{{label}}}</td>
						<td>{{user}}</td>
						<td>{{ip}}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

