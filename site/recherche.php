<div class="jumbotron">
	<div class="container">

		<?php

		if (isset($_POST['recherche']) && $_POST['recherche']!='' ) {
			$requete = addslashes(htmlspecialchars($_POST['recherche']));
			$query = $bdd->query("SELECT * FROM mm_recette r
				LEFT JOIN mm_user u
				ON r.id_user = u.id
				LEFT JOIN mm_type_plat tp
				ON r.id_type_plat = tp.id
				LEFT JOIN mm_type_cuisine tc
				ON r.id_type_cuisine = tc.id
				WHERE r.diffusion = 2 AND r.nom_recette  LIKE '%$requete%' ");
			if($query->rowCount() >0)
			{
				echo '
				<h1 class="pacifico">Résultats de votre recherche</h1><hr>
				<p>Nous avons trouvé '.$query->rowCount();
					if($query->rowCount() > 1) { echo ' résultats'; } else { echo ' résultat'; } //
					echo '
					. Voici les repas que nous avons trouvés :<br/>
					</p>';
					
					while($recette = $query->fetch())
					{
						echo '<div class="col-lg-4 col-sm-6 col-xs-12">
						<div class="panel panel-default">';


						// La photo de la recette
							if (isset($recette->nom_photo) && $recette->nom_photo!= '') {
								echo '<img class="center-block" src="'.$recette->nom_photo.'" alt="photo de la recette" onerror="imgError(this);" id="imgListeRecettes">';
							}
							else {
								echo '<img class="center-block" src="photo-recette/recette_par_default.jpg" alt="photo de la recette" id="imgListeRecettes">';
							}

							if(isset($_SESSION['authentify']) && $_SESSION['authentify']<3){

							// Test déjà au répertoire
								$repertoire = $bdd->prepare('SELECT * FROM mm_repertoire WHERE id_recette = :id_recette && id_user = :id_user');
								$repertoire -> execute(array( 	'id_recette' => $recette->id,
									'id_user' => (int)$_SESSION['id'] ));
								if ($repertoire->fetch()){
									echo '<button class="btn btn-default btnBookmark"><i class="fa fa-bookmark" data-toggle="tooltip" onclick="suppr_repertoire(\''.$recette->id.'\');" id="notInList_'.$recette->id.'" data-placement="top" title="Supprimer de votre liste de repas"></i></button>';
								}
								else{
									echo '<button class="btn btn-default btnBookmark"><i class="fa fa-bookmark-o" onclick="ajout_repertoire(\''.$recette->id.'\');" data-toggle="tooltip" data-placement="top" title="Ajouter à votre liste de repas" id="notInList_'.$recette->id.'"></i></button>';
								}

							// Test déjà au menu
								$menu = $bdd->prepare('SELECT * FROM mm_menu_recette mr
									LEFT JOIN mm_menu m
									ON m.id = mr.id_menu
									WHERE mr.id_recette = :id_recette AND m.id_user = :id_user AND m.etat = 1');
								$menu -> execute(array( 	'id_recette' => $recette->id,
									'id_user' => (int)$_SESSION['id'] ));
								if ($menu->fetch()){
									echo '<button class="btn btn-primary btnCalendar"><i class="fa fa-calendar"   data-toggle="tooltip" onclick="suppr_menu('.$recette->id.');" id="notInMenu_'.$recette->id.'"  data-placement="top" title="Supprimer du menu"></i></button';
								}
								else{
									echo '<button class="btn btn-primary btnCalendar"><i class="fa fa-calendar-plus-o" data-toggle="tooltip" onclick="ajout_menu(\''.$recette->id.'\');" data-placement="top" title="Ajouter à vôtre menu" id="notInMenu_'.$recette->id.'"></i></button>';
								}
							}

							// Les infos de la recette
							echo '
							<a href="index.php?page=recette&id='.$recette->id.'"><h3 class="pacifico titreRecetteLR">'.$recette->nom_recette.'</h3></a>
							<hr class="hrListeRecette">


							<div class="row text-center">
								<div class="col-xs-6 infoRecette">';
									if($recette->moyenne!=''){
										for ($i=0 ; $i < floor($recette->moyenne); $i++) echo '<i class="fa fa-star"></i>';
											for ($i=$recette->moyenne ; $i <5; $i++) echo '<i class="fa fa-star-o"></i>';
										}
									else{
										echo '&nbsp';
									}
									echo '
								</div>
								<div class="col-xs-6 infoRecette">';
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
							</div>

							<div class="row text-center">
								<div class="unLR col-xs-4  infoRecette">
									<i class="fa fa-eur"></i>  ';
									if(isset($recette->prixRepas) && $recette->prixRepas!=0 || $recette->prixRepas!='' ){
										echo round($recette->prixRepas,1);
									}
									else{
										echo '&nbsp';
									}
									echo '
								</div>

								<div class="deuxLR col-xs-4  infoRecette">
									<i class="fa fa-clock-o"></i>  ';
									if($recette->temps_preparation!=''  && $recette->temps_cuisson!=''){
										echo $recette->temps_preparation + $recette->temps_cuisson;
									}
									else{
										echo '&nbsp';
									}
									echo ' m
								</div>

								<div class="troisLR col-xs-4  infoRecette">
									<i class="fa fa-pie-chart"></i> '.$recette->part.'
								</div>
							</div>
						</div>
					</div>';

				}

			}
			else
			{
				echo '
				<h3 class="pacifico">Pas de résultats</h3><hr>
				<p>Nous n\'avons trouvé aucun résultat pour votre requête "'.$_POST['recherche'].'". Réessayez avec autre chose.</p>
				';
			}
		}
	
		?>
	</div>
</div>


