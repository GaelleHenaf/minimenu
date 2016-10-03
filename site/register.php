<form class="form-horizontal col-md-8 col-md-offset-2 jumbotron josefin" action= "index.php"  method="post" id="register" name="form" onSubmit="return verif(this.password, this.passwordconfirm, this.email, this.email_confirm)">
	<fieldset>
	    <h2 class="pacifico">Inscription</h2>
	    <hr>
	    <div class="form-group">
	      	<label for="pseudo" class="col-md-4 control-label">Pseudo*</label>
	      	<div class="col-md-8">
	        	<input type="text" class="form-control" id="pseudo" name="pseudo" placeholder="Pseudo" value ="<?php 
	        	if(isset($_POST['pseudo']) && $_POST['pseudo']!='') {
	        		echo $_POST['pseudo'];
	        	}
	        	 ?>">
	      	</div>
	    </div>
	    <div class="form-group">
	      	<label for="prenom" class="col-md-4 control-label">Prénom</label>
	      	<div class="col-md-8">
	        	<input type="text" class="form-control" id="prenom" name="prenom" placeholder="Prenom" value="<?php 
	        	if(isset($_POST['prenom']) && $_POST['prenom']!='') {
	        		echo $_POST['prenom'];
	        	}
	        	 ?>">
	      	</div>
	    </div>
	    <div class="form-group">
	      	<label for="nom" class="col-md-4 control-label">Nom</label>
	      	<div class="col-md-8">
	        	<input type="text" class="form-control" id="nom" name="nom" placeholder="Nom" value="<?php 
	        	if(isset($_POST['nom']) && $_POST['nom']!='') {
	        		echo $_POST['nom'];
	        	}
	        	?>">
	      	</div>
	    </div>
	    <div class="form-group">
	      	<label for="password" class="col-md-4 control-label">Mot de Passe*</label>
	      	<div class="col-md-8">
	        	<input type="password" class="form-control" id="password" name="password" placeholder="Mot de passe" required>
	        </div>

	        <div class="alert_form col-md-8 col-md-offset-4 text-center">6 caractères dont une lettre et un chiffre minimum. <br>
	        Les caractères spéciaux ne sont pas autorisés. </div>
	    </div>
	    <div class="form-group">
	      	<label for="password_confirm" class="col-md-4 control-label">Confirmation du mot de passe*</label>
	      	<div class="col-md-8">
	        	<input type="password" class="form-control" id="password_confirm" name="passwordconfirm" placeholder="Retapez votre mot de passe ici" required>
	        </div>
	    </div>
	    <div class="form-group">
	      	<label for="email" class="col-md-4 control-label">Email*</label>
	      	<div class="col-md-8">
	        	<input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php 
	        	if(isset($_POST['email']) && $_POST['email']!='') {
	        		echo $_POST['email'];
	        	}
	        	 ?>" required>
	      	</div>
	    </div>
	    <div class="form-group">
	      	<label for="email_confirm" class="col-md-4 control-label">Confirmation de l'Email*</label>
	      	<div class="col-md-8">
	        	<input type="email" class="form-control" id="email_confirm" name="email_confirm" placeholder="Retapez votre email ici" required>
	      	</div>
	    </div>
	    
     	<div class="form-group">
	      	<label for="date_naissance" class="col-md-4 control-label">Date de naissance*</label>
	      	<div class="col-md-8">
	        	<input type="date" class="form-control" id="date_naissance" name="date_naissance" placeholder="" value="<?php 
	        	if(isset($_POST['date_naissance']) && $_POST['date_naissance']!='') {
	        		echo $_POST['date_naissance'];
	        	}
	        	 ?>" infobulle="format jj/mm/aaaa" max=<?php echo '"'.date('Y-m-d').'"'; ?> required>
	      	</div>
	    </div>
	    <div class="form-group">
	    	<label for="sexe" class="col-md-4 control-label">Sexe :</label>
	    	<div class="col-md-8">
		    	<div class="radio-inline" id="sexe">
					<label for="homme">
						<input type="radio" name="sexe" id="homme" value="1" <?php if ($_POST['sexe']==1) { echo 'checked';} ?>>Homme
					</label> 
				</div>
				<div class="radio-inline">
					<label for="femme">
					    <input type="radio" name="sexe" id="femme" value="2" <?php if ($_POST['sexe']!=1) { echo 'checked';} ?>>Femme
					</label>
				</div>
			</div>
	    </div>
	   
	    <div class="form-group text-center">
				<a href="index.php">
					<button type="reset" class="btn btn-default">Annuler</button>
				</a>
				<button type="submit" class="btn btn-primary">Envoyer</button>
		</div>
	</fieldset>
</form>

<script src="verif.js" type="text/javascript"></script>