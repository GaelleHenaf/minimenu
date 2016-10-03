<div class="jumbotron">
		<div class="col-xs-12">
			<h1 class="pacifico col-md-9 col-xs-7">Mes repas préférés</h1>


			<?php
$fetch=0;
$recettes = $bdd -> prepare('SELECT r.* ,u.id AS id_user, u.pseudo, tp.nom_type_plat, tc.nom_type_cuisine
									FROM mm_recette r
										LEFT JOIN mm_user u
											ON r.id_user = u.id
										LEFT JOIN mm_type_plat tp
											ON r.id_type_plat = tp.id
										LEFT JOIN mm_type_cuisine tc
											ON r.id_type_cuisine = tc.id
										LEFT JOIN mm_repertoire f
											ON r.id = f.id_recette
												WHERE  f.id_user = :id_user
													');
	$recettes->bindValue('id_user', intval($_SESSION['id']), PDO::PARAM_INT);
	$recettes -> execute();
	while ($recipe = $recettes->fetch()) {
		$fetch++;
	}


			if ($fetch>0) {
				echo '
				<p class="text-right col-md-2 col-xs-5" id="delAll">
					<a href="index.php?page=repertoire&del=all">
						<button class="btn btn-success"><i class="fa fa-trash"></i> Vider tout</button>
					</a>
				</p>';
			}
		echo '
		</div>
		<hr>';



$nbsRepas = $bdd -> prepare('SELECT COUNT(*) FROM mm_repertoire where id_user = :id_user');
$nbsRepas -> execute(array(	'id_user' => (int)$_SESSION['id'] ));
$nbRepas = $nbsRepas -> fetchColumn();
$repasParPage = 12;
$nbPage = ceil($nbRepas/$repasParPage);

if(isset($_GET['numPage'])) // Si la variable $_GET['page'] existe...
{
     $pageActuelle=intval($_GET['numPage']);

     if($pageActuelle>$nbPage) // Si la valeur de $pageActuelle (le numéro de la page) est plus grande que $nombreDePages...
     {
          $pageActuelle=$nbPage;
     }
}
else // Sinon
{
     $pageActuelle=1; // La page actuelle est la n°1
}

$premiereEntree=($pageActuelle-1)*$repasParPage; // On calcul la première entrée à lire


	// Ajout de la recette au répertoire



	if (isset($_GET['ajout']) && $_GET['ajout']==o && isset($_SESSION['id']) && $_SESSION['id']!='' && isset($_GET['recette']) && $_GET['recette']!=''){
		$repertoire = $bdd -> prepare('SELECT * FROM mm_repertoire WHERE id_recette = :id_recette && id_user = :id_user');
		$repertoire -> execute(array( 	'id_recette' => (int)$_GET['recette'],
											'id_user' => (int)$_SESSION['id'] ));

		if ($repertoire->fetch() == false){
			$addsRepertoire = $bdd->prepare('INSERT INTO mm_repertoire(id_recette, id_user) VALUES (:id_recette, :id_user)');
			$addsRepertoire -> execute(array( 	'id_recette' => (int)$_GET['recette'],
												'id_user' => (int)$_SESSION['id'] ));
		}
	}
	elseif (isset($_GET['del']) && $_GET['del']==all) {
		$repertoire = $bdd -> prepare('DELETE FROM mm_repertoire	WHERE id_user = :id_user');
		$repertoire -> execute(array(	'id_user' => (int)$_SESSION['id'] ));
	}



	// Affichage des recettes

	$recettes = $bdd -> prepare('SELECT r.* ,u.id AS id_user, u.pseudo, tp.nom_type_plat, tc.nom_type_cuisine
									FROM mm_recette r
										LEFT JOIN mm_user u
											ON r.id_user = u.id
										LEFT JOIN mm_type_plat tp
											ON r.id_type_plat = tp.id
										LEFT JOIN mm_type_cuisine tc
											ON r.id_type_cuisine = tc.id
										LEFT JOIN mm_repertoire f
											ON r.id = f.id_recette
												WHERE  f.id_user = :id_user
													ORDER BY r.id DESC
														LIMIT :premiereEntree,:nbRepas');
	$recettes->bindValue('id_user', intval($_SESSION['id']), PDO::PARAM_INT);
	$recettes->bindValue('premiereEntree', intval($premiereEntree), PDO::PARAM_INT);
	$recettes->bindValue('nbRepas', intval($repasParPage), PDO::PARAM_INT);
	$recettes -> execute();
	echo '<div class="col-lg-12">';
	while ($recette = $recettes->fetch()) {
		$fetch++;
echo '<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
							<div class="panel panel-default">';


								// La photo de la recette
								if (isset($recette->nom_photo) && $recette->nom_photo!= '') {
									echo '<img class="center-block" src="'.$recette->nom_photo.'" alt="photo de la recette" onerror="imgError(this);" id="imgListeRecettes">';
								}
								else {
									echo '<img class="center-block" src="photo-recette/recette_par_default.jpg" alt="photo de la recette" id="imgListeRecettes">';
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


echo '</div>';
if ($fetch==0) {echo '
	<div class="col-xs-12"><h2 class="col-lg-8 col-xs-12 pacifico">Oups !Il n\'y a pas de repas dans votre liste</h2><br>
	<p class="col-xs-12 col-lg-8">Ajouter en directement depuis le repas ou depuis la liste de repas en cliquant sur <i class="fa fa-bookmark-o"></i></p> 
	<img class="center-block" src="img/filleoups.png" alt="avatar fille oups">
	</div>';}
elseif ($nbRepas>12) {
		echo '<nav class="text-center">
		<ul class="pagination pacifico">
			<li>
				<a href="#" aria-label="Previous">
						<span aria-hidden="true">&laquo;</span>
				</a>
				</li>';
			for ($i=1; $i<=$nbPage; $i++) {

				echo '<li';
				if (isset($_GET['numPage']) && $_GET['numPage']==$i) { echo ' class="active"';}
			else{ if ($i==1) { echo ' class="active"';}}
				echo '><a href="index.php?page=repertoire&numPage='.$i.'">'.$i.'</a></li>';
			}
			echo '<li>
				<a href="#" aria-label="Next">
						<span aria-hidden="true">&raquo;</span>
					</a>
			</li>
		</ul>
		</nav>';
}
?>
</div></div>


<script type="text/javascript">
	function supprRepasRepertoire(id_recette) {
		$.ajax( {
			type: "POST",
			url: "ajax/supprRepasRepertoire.php",
			data: {id_recette : id_recette},
			success: function(data) {
				// console.log(data);
				$('#RepasRepertoire_'+id_recette).remove();
				var notification = alertify.notify('Le repas "'+data+'" a bien été supprimé de votre liste', 'custom', 5, function(){  console.log('dismissed'); });
			}
		});
	}
</script>
