<div class="jumbotron">
	<div class="row">
		<?php

		echo '
		<div class="row">
			<div class="col-xs-12">
				<div class=" panel panel-success">';
					require_once('message-home.php');
					echo '
				</div>
			</div>
		</div>

		<div class="col-md-8 hidden-xs hidden-sm">';
			$recettes = $bdd->prepare('	SELECT r.* ,u.id AS id_user, u.pseudo, tp.nom_type_plat, tc.nom_type_cuisine
				FROM mm_recette r
				LEFT JOIN mm_user u
				ON r.id_user = u.id
				LEFT JOIN mm_type_plat tp
				ON r.id_type_plat = tp.id
				LEFT JOIN mm_type_cuisine tc
				ON r.id_type_cuisine = tc.id
				WHERE r.diffusion = 2
				ORDER BY r.id DESC
				LIMIT 4');
			$recettes -> execute();
			echo '
			<h2 class="pacifico">Derniers Repas ajoutés à MiniMenu</h2><hr>';
			while ($recette = $recettes->fetch()) {

				$dateR = date_create($recette->date_creation_recette);

				echo '
				<div class="panel panel-default col-xs-6 col-md-12 blocRecetteHome">
					<div class="col-md-4">';
						if(isset($recette->nom_photo) && $recette->nom_photo!= ''){
							echo '<img class="center-block photoRecetteHome" src="'.$recette->nom_photo.'" alt="photo de la recette"  onerror="imgError(this);"  >';
						}
						else{
							echo '<img class="center-block photoRecetteHome" src="photo-recette/recette_par_default.jpg" alt="photo de la recette" >';
						}
						echo '	
					</div>

					<a href="index.php?page=recette&id='.$recette->id.'">
						<h3 class="titreRecetteHome">'.$recette->nom_recette.'</h3>
					</a>
					<div class="col-md-8 text-center"><hr>
						<div class="col-lg-4 col-sm-4">
							<i class="fa fa-user"></i> '.$recette->pseudo.'
						</div>
						<div class="col-lg-4 col-sm-2">
							<div>
								<i class="fa fa-pie-chart"></i> '.$recette->part.'
							</div>
						</div>
						<div class="col-lg-4 col-sm-6">
							<i class="fa fa-calendar"></i> '.date_format($dateR, 'd/m/Y').'
						</div>
					</div>
					<div>
						<div class="col-md-2 text-center unHome">
							<i class="fa fa-eur"></i>  ';
							if(isset($recette->prixRepas) && $recette->prixRepas!=0 || $recette->prixRepas!='' ){
								echo round($recette->prixRepas,1);
							}
							else{
								echo '&nbsp';
							}
							echo '
						</div>
						<div class="col-md-2 text-center deuxHome">';
							if($recette->difficulte==1){
								echo 'Facile';
							}
							elseif($recette->difficulte==2){
								echo 'Moyen';
							}
							elseif($recette->difficulte==3){
								echo 'Difficile';
							}
							else{
								echo '&nbsp';
							}
							echo '
						</div>
						<div class="col-md-2 text-center troisHome">';
							if($recette->moyenne!=''){
								for ($i=0 ; $i < floor($recette->moyenne); $i++) echo '<i class="fa fa-star"></i>';
									for ($i=$recette->moyenne ; $i < 5; $i++) echo '<i class="fa fa-star-o"></i>';
								}
							else{
								echo '&nbsp';
							}
							echo '
						</div>
						<div class="col-md-2 text-center quatreHome">
							<i class="fa fa-clock-o"></i>  ';
							if($recette->temps_preparation!=''  && $recette->temps_cuisson!=''){
								echo $recette->temps_preparation + $recette->temps_cuisson;
							}
							else{
								echo '&nbsp';
							}
							echo 'm
						</div>';
						echo '
					</div>
				</div>

				';
			}


			echo '
		</div>

		<div class="col-md-4">
			';

			if (isset($_SESSION['authentify']) && $_SESSION['authentify']<3) {
				echo '
				<div class="panel panel-primary hidden-xs hidden-sm">
					<div class="panel panel-body">
						<h3 class="text-center pacifico">Mon Menu de la Semaine</h3>
						<hr>
						<div class="row col-xs-12">';
							$recettes = $bdd->prepare('	SELECT r.* ,u.id AS id_user, u.pseudo, tp.nom_type_plat, tc.nom_type_cuisine , m.id AS id_menu
								FROM mm_recette r
								LEFT JOIN mm_user u
								ON r.id_user = u.id
								LEFT JOIN mm_type_plat tp
								ON r.id_type_plat = tp.id
								LEFT JOIN mm_type_cuisine tc
								ON r.id_type_cuisine = tc.id
								LEFT JOIN mm_menu_recette mr
								ON r.id = mr.id_recette
								LEFT JOIN mm_menu m
								ON mr.id_menu = m.id
								WHERE  m.id_user = :id_user AND m.etat = 1
								ORDER BY r.id DESC LIMIT 4');
							$recettes -> execute(array('id_user' => (int)$_SESSION['id']));
							$fetch=0;
							while ($recette = $recettes->fetch()) {
								$fetch++;

								echo '

								<a href="index.php?page=recette&id='.$recette->id.'">
										<div class="col-xs-3 ';
										switch($fetch) {
											case 1: echo 'un'; break;
											case 2: echo 'deux'; break;
											case 3: echo 'trois'; break;
											case 4: echo 'quatre'; break;
											default: echo '&nbsp';
										}
										echo '">';
										if(isset($recette->nom_photo) && $recette->nom_photo!= ''){
											echo '<img class="center-block photoRecetteMenuHome" src="'.$recette->nom_photo.'" alt="photo de la recette">';
										}
										else{
											echo '<img class="center-block photoRecetteMenuHome" src="photo-recette/recette_par_default.jpg" alt="photo de la recette" >';
										}

										echo '
									</div>
									<p class="pacifico col-xs-7 titreRecetteMenuHome">'.$recette->nom_recette.'</p>
								</a>	';

							}
							echo '
						</div>';
						if ($fetch>3) {
							echo '
							<div class="col-md-12 text-center">
								<a href="index.php?page=menu">
									<button class="btn btn-primary">Voir le menu complet</button>
								</a>
							</div>';
						}
						elseif ($fetch==0) {
							echo '
							<div class="col-md-12 text-center">
								<h4 class="pacifico">Vous n\'avez pas de menu en cours, ajouter une recette pour en générer un nouveau</h4>
								<p class="">Pour ajouter une recette, cliquer sur le calendrier dans la liste des recettes ou cliquer sur ajouter au menu sur la page de la recette souhaitée</p>
							</div>';
						}
						echo '
					</div>
				</div>';
			}
			else {
				echo '
				<div class="panel panel-primary">
					<div class="panel panel-body">
						<form class="form-horizontal panel" action="" method="post" id="sign_in">
							<h3 class="text-center pacifico">Connexion</h3>
							<hr>
							<div class="form-group connexion">
								<div class="col-md-10 col-md-offset-1">
									<input type="email" class="form-control text-center" id="inputEmail" name="emailsign" placeholder="Email">
								</div>
							</div>
							<div class="form-group connexion">
								<div class="col-md-10 col-md-offset-1">
									<input type="password" class="form-control text-center" id="inputPassword" name="passwordsign" placeholder="Password">
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-12 text-center">
									<input type="checkbox" name="remember" value="remember" id="inputCookie">
									<label for="inputCookie" class="control-label">Se souvenir de moi </label>
								</div>
							</div>
							<div class="form-group text-center">
								<a href="index.php?page=mail-oublie">Mot de passe oublié ? </a>
							</div>
							<div class="form-group text-center">
								<div class="col-md-12">
									<button type="reset" class="btn btn-default">Annuler</button>
									<button type="submit" class="btn btn-primary">Envoyer</button>
								</div>
							</div>
							<!-- <a href="index.php?page=mail-oublie" class="col-md-12 text-center">Mot de passe oublié ?</a> -->
						</form>

						<div class=" text-center josefin col-md-12">
							<h3 class="col-md-12 pacifico ">Pas encore de compte sur MiniMenu ?</h3>
							<hr>
							<a href="index.php?page=register"><button class="btn btn-primary">Créer un compte maintenant</button></a>
						</div>
					</div>
				</div>
			</div>
		</div>';
	}
	echo '
	</div>
	</div>';
	?>
</div>
</div>
