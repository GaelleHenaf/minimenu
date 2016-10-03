<div class="jumbotron col-xs-12">
	<form class="form-horizontal col-xs-12 col-md-6 col-md-offset-3"  action="index.php" method="post" id="sign_in">
		<fieldset>
			<h3 class="pacifico">Se Connecter</h3>
			<hr>
			<div class="form-group ">
				<label for="inputEmail" class="col-md-4 control-label">Email</label>
				<div class="col-md-8">
					<input type="email" class="form-control" id="inputEmail" name="emailsign" placeholder="Email">
				</div>
			</div>
			<div class="form-group">
				<label for="inputPassword" class="col-md-4 control-label">Password</label>
				<div class="col-md-8">
					<input type="password" class="form-control" id="inputPassword" name="passwordsign" placeholder="Password">
				</div>
			</div>
			<div class="form-group text-center">
				<input type="checkbox" name="remember" value="remember" id="inputCookie">
				<label for="inputCookie" class="control-label">Se souvenir de moi </label>
			</div>
			<div class="form-group text-center">
				<a href="index.php?page=mail-oublie">Mot de passe oublié ? </a>
			</div>
			<div class="form-group text-center">
				<a href="index.php?page=home"><button type="reset" class="btn btn-default">Annuler</button></a>
				<button type="submit" class="btn btn-primary">Envoyer</button>
			</div>
		</fieldset>
	</form>

	<div class="text-center col-xs-12 col-md-6 col-md-offset-3">
		<h3 class="col-md-12 pacifico">Pas encore de compte sur MiniMenu ?</h3>
		<hr><a href="index.php?page=register"><button class="btn btn-primary">Créer un compte maintenant</button></a>
	</div>
</div>

