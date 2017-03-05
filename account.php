<?php require_once __DIR__.DIRECTORY_SEPARATOR.'header.php';  

if (!$myUser->connected()) header('location: index.php');

$user = User::getById($myUser->id);
	
?>

		<div id="userForm" class="row" data-action="save_user" data-id="<?php echo $user->id; ?>">
			<div id="userForm" class="col-md-6">
				<label for="login">Identifiant</label>
				<input id="login" class="form-control" disabled="disabled" placeholder="Identifiant" value="<?php echo $user->login; ?>" type="text">
				<label for="password">Mot de passe</label>
				<input id="password" class="form-control" placeholder="Mot de passe" type="password">
				<label for="firstname">Prénom</label>
				<input id="firstname" class="form-control" placeholder="Prénom" type="text" value="<?php echo $user->firstname; ?>">
			</div>
			<div id="userForm" class="col-md-6">
				<label for="mail">Mail</label>
				<input id="mail" class="form-control" placeholder="Mail" type="text" value="<?php echo $user->mail; ?>">
				<label for="password2">Mot de passe (confirmation)</label>
				<input id="password2" class="form-control" placeholder="Mot de passe (confirmation)" type="password">
				<label for="name">Nom</label>
				<input id="name" class="form-control" placeholder="Nom" type="text" value="<?php echo $user->name; ?>">
				<label for="rank">Rang</label>
				<input id="rank" class="form-control" disabled="disabled"  value="<?php echo $user->rank_object->label; ?>" type="text">
				
			</div>
			<div id="userForm" class="col-md-12">
				<br/>
				<div onclick="save_user(true);" class="btn btn-info"><i class="fa fa-check"></i> Enregistrer</div>
			</div>
		</div>

<?php require_once __ROOT__.'footer.php' ?>
