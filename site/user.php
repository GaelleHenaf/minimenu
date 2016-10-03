<div class="jumbotron">
	<div class="container">

		<?php



			// Affichage
			$membres = $bdd->prepare('SELECT pseudo, avatar, prenom, sexe, date_naissance, date_last_connexion, date_creation, presentation FROM mm_user WHERE id = :id');
			$membres -> execute(array('id' => (int)$_GET['mb']));

			$membre = $membres->fetch();
	


				// Affichage
			
				echo '<div class="col-xs-12">
					<h1 class="pacifico col-xs-7 col-sm-10">'.$membre->pseudo.'</h1>
					<div class="col-xs-5 col-sm-2 text-left boutonTitreCompte">';
						
					echo '</div>
				</div>
				<hr>
				<div class="col-sm-5 col-xs-12 text-center">
					<h2 class="col-xs-12 pacifico text-center">Informations</h2>
					<div class="col-xs-12  espaceTitreCompte text-center">
						<img src="img/rond.png" alt="rond dégradé de couleur" id="rondDegrade">';
						if(isset($membre->avatar) && $membre->avatar!= ''){
							echo '<img src="avatar/'.$membre->avatar.'" alt="avatar" id="avatarCompte" class="img-circle avatar relative">';
						}
						else {
							echo '<img class="img-circle avatar relative" src="avatar/avatar_defaut.png" alt="avatar" id="avatarCompte">';
						}
						echo '</div>';
					
					echo '
					<div class="col-xs-12 text-left">
						<div class="col-xs-12 ligneInfoCompte">
							<strong class="col-xs-5 col-xs-offset-1 text-left">Prénom</strong>';
							
								echo '<div class="col-xs-5 text-left"> ';
									if($membre->prenom!=''){echo $membre->prenom; }
									else{echo '&nbsp';}
								echo '</div>';
							
						echo '</div>
				
						
						<div class="col-xs-12 ligneInfoCompte">
							<strong class="col-xs-5 col-xs-offset-1 text-left">Sexe</strong>';
					
								echo '<div class="col-xs-5 text-left"> ';
									if($membre->sexe==1) { echo 'Homme'; }
									elseif ($membre->sexe==2) { echo 'Femme'; }
									else{ echo '&nbsp'; }
								echo '</div>';
						echo '</div>
						<div class="col-xs-12 ligneInfoCompte">
							<strong class="col-xs-5 col-xs-offset-1 text-left">Age</strong>
							<div class="col-xs-5 text-left"> ';
								if($membre->date_naissance!='0000-00-00 00:00:00') {
									$dateUs = preg_replace('#^([0-3][0-9])/([0-1][0-9])/([1-2][0-9]{3})#', '$3-$2-$1', $membre->date_naissance);
									$age = (time() - strtotime($dateUs)) / 3600 / 24 / 365; echo floor($age).' ans';
								}
								else{ echo '&nbsp'; }
							echo '</div>
						</div>
						<div class="col-xs-12 ligneInfoCompte">
							<strong class="col-xs-5 col-xs-offset-1 text-left">Date de naissance</strong>';
							
								echo '<div class="col-xs-5 text-left"> ';
									if($membre->date_naissance!='0000-00-00 00:00:00') { $date = date_create($membre->date_naissance); echo date_format($date,'d/m/Y');}
									else{ echo '&nbsp'; }
								echo '</div>';
						
						echo '</div>
					</div>
				</div>

				<div class="col-sm-6 col-sm-offset-1">
					<h2 class="col-sm-12 pacifico text-center espaceTitreCompte">Compte MiniMenu</h2>
				';
						
				
						
					echo '<div class="col-sm-12 ligneInfoCompte">
						<strong class="col-sm-offset-1 col-sm-5 text-left">Date de dernière connexion</strong>
						<div class="col-sm-5 text-left"> ';
							if($membre->date_last_connexion!='0000-00-00 00:00:00') {  $date = date_create($membre->date_last_connexion); echo date_format($date,'d/m/Y'); }
							else{ echo '&nbsp'; }
						echo '</div>
					</div>
					<div class="col-sm-12 ligneInfoCompte">
						<strong class="col-sm-offset-1 col-sm-5 text-left">Date de création du compte</strong>
							<div class="col-sm-5 text-left"> ';
								if($membre->date_creation!='0000-00-00 00:00:00') {  $date = date_create($membre->date_creation); echo date_format($date,'d/m/Y'); }
								else{ echo '&nbsp'; }
						echo '</div>
					</div>

				</div>

				<div class="col-xs-12">
					<h2 class="col-xs-12 pacifico text-center espaceTitreCompte">Présentation</h2>';
				
					echo '<div class="col-xs-12 text-center espaceTitreCompte">';
						if(isset($membre->presentation) && $membre->presentation!='') {  echo $membre->presentation; }
						else{ echo 'Votre présentation n\'a pas encore été renseignée'; }
					echo '</div>';
				
				echo '</div>';

			

		?>
	</div>
</div>

