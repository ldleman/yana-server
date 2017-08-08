<?php 
global $myUser;
if(!$myUser->can('user','configure')) throw new Exception("Permissions insuffisantes");
?>
<div class="row">
	<div class="col-md-12">
		<h3>Utilisateurs</h3>

		<div id="userForm" class="row" data-action="save_user">
			<div class="col-md-6">
				<label for="login">Identifiant</label>
				<input id="login" class="form-control" placeholder="Identifiant" type="text">
				<label for="password">Mot de passe</label>
				<input id="password" class="form-control" placeholder="Mot de passe" type="password">
				<label for="firstname">Prénom</label>
				<input id="firstname" class="form-control" placeholder="Prénom" type="text">
			</div>
			<div class="col-md-6">
				<label for="mail">Mail</label>
				<input id="mail" class="form-control" placeholder="Mail" type="text">
				<label for="password2">Mot de passe (confirmation)</label>
				<input id="password2" class="form-control" placeholder="Mot de passe (confirmation)" type="password">
				<label for="name">Nom</label>
				<input id="name" class="form-control" placeholder="Nom" type="text">
			</div>
			<div class="col-md-12">
				<label for="rank">Rang</label>
				<select id="rank" class="form-control" placeholder="Rang">
					<?php foreach(Rank::loadAll() as $rank):  ?>
						<option value="<?php echo $rank->id; ?>"><?php echo $rank->label; ?></option>
					<?php endforeach; ?>
				</select><br/>
				<div onclick="save_user();" class="btn btn-info"><i class="fa fa-check"></i> Enregistrer</div>
			</div>
		</div>
		<br/>
		<div class="panel panel-default">
			<div class="panel-heading">Utilisateurs existants</div>
			<table id="users" class="table">
				<thead>
					<tr>
						<th>#</th>
						<th></th>
						<th>Nom</th>
						<th>Mail</th>
						<th>Identifiant</th>
						<th>Rang</th>
					</tr>
				</thead>
				<tbody>
					<tr data-id="{{id}}" class="noDisplay">
						<td>{{id}}</td>
						<td><img class="avatar" data-src="{{avatar}}"/></td>
						<td>{{firstname}} {{name}}</td>
						<td>{{mail}}</td>
						<td>{{login}}</td>
						<td>{{rank.label}} {{#superadmin}}<span class="label label-info">Super Admin</span>{{/superadmin}}</td>
						<td>
							<div onclick="edit_user(this)" class="btn btn-info btn-mini"><i class="fa fa-pencil"></i></div>
							<div onclick="delete_user(this)" class="btn btn-danger btn-mini"><i class="fa fa-times"></i></div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

