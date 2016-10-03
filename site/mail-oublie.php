<?php


ini_set('SMTP', 'smtp.minimenu.fr');
ini_set('smtp_port', '587');
//  ini_set('SMTP', '127.0.0.1');

// echo '<pre>';
// print_r(ini_get_all());
// echo '</pre>';


if (isset($_POST['email']) && $_POST['email']!='') {

  $addressUsers = $bdd -> prepare('SELECT * FROM mm_user WHERE email = :email');
  $addressUsers -> execute(array('email' => $_POST['email']));
  if ( $addressUser = $addressUsers->fetch() ) {
    echo $_POST['email'];
    $to = $_POST['email'];
    $subject = "MiniMenu - Récupération de Mot de Passe";
    $message="boo";
    mail($to, $subject, $message);
  }
  else {
    echo '<div class="alert text-center alert-danger alert-dismissible col-lg-12" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Attention !</strong> Ancun compte MiniMenu n\'a été crée avec cet e-mail </div>';
  }
}

echo '
<form class="form-horizontal josefin jumbotron col-md-6 col-md-offset-3" action="" method="post" id="sign_in">
	<fieldset>
		<h3 class="col-lg-12 pacifico">Mot de passe oublié ? </h3>
		<hr>
		<div class="form-group ">
			<label for="inputEmail" class="col-lg-4 control-label">Entrer ici votre Email*</label>
			<div class="col-lg-8">
				<input type="email" class="form-control" id="inputEmail" name="email" placeholder="Email" required>
			</div>
		</div>
		<div class="form-group">
			<div id="bouton_form_sign_in" class=" col-lg-8 col-lg-offset-4 ">
				<a href="index.php?page=home"><button type="button" class="btn marron_clair ">Annuler</button></a>
				<button type="submit" class="btn rose ">Envoyer</button>
			</div>
		</div>
	</fieldset>
</form>
';

 ?>
