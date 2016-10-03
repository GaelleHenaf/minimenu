<!-- propre -->
<?php

// Suppression d'une recette

if (isset($_GET['suppr']) && $_GET['suppr']=='suppr' && isset($_GET['id']) && $_GET['id']!='') {
	$RecetteSuppr = $bdd->prepare('DELETE FROM mm_recette WHERE id = :id_recette AND id_user = :id_user');
	$RecetteSuppr -> execute(array('id_recette' => $_GET['id'], 'id_user' =>  $_SESSION['id']));
}

// requete selon tri
$where ='';
if (isset($_GET['d'])) {
	$d = $_GET['d'];
	$where = $where.' AND r.difficulte = '.$d;
	$urlD='&d='.$d;
}
else {
	$urlD='';
}
if (isset($_GET['tc'])) {
	$tc = $_GET['tc'];
	$where = $where.' AND r.id_type_cuisine= '.$tc;
	$urlTC='&tc='.$tc;
}
else {
	$urlTC='';
}

if (isset($_GET['tp'])) {
	$tp = $_GET['tp'];
	$where= $where.' AND tp.id = '.$tp;
	$urlTp='&tp='.$tp;
}
else {
	$urlTp='';
}
if (isset($_GET['n'])) {
	$n = $_GET['n'];
	$where= $where.' AND r.note > '.$n;
	$urlN='&n='.$n;
}
else {
	$urlN='';
}
if (isset($_GET['t0']) AND isset($_GET['t1'])) {
	$t0 = $_GET['t0'];
	$t1 = $_GET['t1'];
	$where = $where.' AND r.temps_preparation+r.temps_cuisson BETWEEN '.$t0.' AND '.$t1;
	$urlT='&t0='.$t0.'&t1='.$t1;
}
else {
	$urlT='';
}
$requeteNbRepas = 'SELECT COUNT(distinct r.id)
FROM mm_recette r
LEFT JOIN mm_type_plat tp
ON r.id_type_plat = tp.id
LEFT JOIN mm_recette_ingredient ri
ON ri.id_recette = r.id
LEFT JOIN mm_ingredient i
ON ri.id_ingredient = i.id
WHERE r.diffusion = 2'.$where;
if (isset($_GET['p0']) AND isset($_GET['p1'])) {
	$p0 = $_GET['p0'];
	$p1 = $_GET['p1'];
	$requeteNbRepas = 'SELECT COUNT(*) FROM (SELECT r.id FROM mm_recette r LEFT JOIN mm_type_plat tp ON r.id_type_plat = tp.id LEFT JOIN mm_recette_ingredient ri ON ri.id_recette = r.id LEFT JOIN mm_ingredient i ON ri.id_ingredient = i.id WHERE r.diffusion = 2 '.$where.' GROUP BY r.id HAVING SUM(prix_ingredient) > '.$p0.' AND SUM(prix_ingredient) < '.$p1.') AS c';
	$urlP='&p0='.$p0.'&p1='.$p1;
}
else {
	$urlP='';
}

if (isset($_GET['tri'])) {
	$urlTri = '&tri='.$_GET['tri'];
	if($_GET['tri']=='duree') {
		$orderby =' ORDER BY r.temps_preparation+r.temps_cuisson';
	}
	elseif($_GET['tri']=='prix') {
		$orderby =' ORDER BY r.prixRepas';
	}
	elseif($_GET['tri']=='note') {
		$orderby =' ORDER BY ';
	}
	else {
		$orderby = ' ORDER BY r.nom_recette';
	}
}
else {
	$orderby = ' ORDER BY r.nom_recette';
}

if (isset($_GET['D'])) {
	$orderby=$orderby.' DESC ';
}





$nbsRepas = $bdd -> query($requeteNbRepas);
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



$limit = ' LIMIT :premiereEntree,:nbRepas';
$query = 'SELECT r.* ,u.id AS id_user, u.pseudo, tp.nom_type_plat, tc.nom_type_cuisine
FROM mm_recette r
LEFT JOIN mm_user u
ON r.id_user = u.id
LEFT JOIN mm_type_plat tp
ON r.id_type_plat = tp.id
LEFT JOIN mm_type_cuisine tc
ON r.id_type_cuisine = tc.id
WHERE r.diffusion = 2';
$requete = $query.$where.$orderby.$limit;
if (isset($_GET['p0']) AND isset($_GET['p1'])) {

	$requete = 'SELECT r.id,r.nom_recette,r.part, r.temps_cuisson, r.temps_preparation, r.difficulte FROM mm_recette r LEFT JOIN mm_type_plat tp ON r.id_type_plat = tp.id LEFT JOIN mm_recette_ingredient ri ON ri.id_recette = r.id LEFT JOIN mm_ingredient i ON ri.id_ingredient = i.id WHERE r.diffusion = 2 '.$where.' GROUP BY r.id HAVING SUM(prix_ingredient) > '.$p0.' AND SUM(prix_ingredient) < '.$p1.$orderby.$limit;
}



	// Affichage de toutes les recettes

$recettes = $bdd->prepare($requete);
$recettes->bindValue('premiereEntree', intval($premiereEntree), PDO::PARAM_INT);
$recettes->bindValue('nbRepas', intval($repasParPage), PDO::PARAM_INT);
$recettes -> execute();


echo '
<div class="jumbotron">
	<div class="row">
		<h1 class="pacifico col-md-9 col-xs-12 col-sm-9">Tous les repas</h1>
		<form class="form col-md-2 col-xs-6 col-sm-2" id="triPar" action="" method="post" accept-charset="utf-8">
			<div class="form-group">
				<label for="tri" class="text-center col-xs-12" id ="trierPar" >Trier par</label>
				<select class="form-control center-block col-xs-6" name="tri" id="tri" onchange="triPar(\''.$urlT.'\',\''.$urlP.'\',\''.$urlTp.'\',\''.$urlN.'\',\''.$urlD.'\');" >
					<option value="nom">Nom</option>
					<option value ="prix" ';
					if (isset($_GET['tri']) && $_GET['tri']=='prix') { echo 'selected';  }
					echo '>Prix</option>
					<option value="duree" ';
					if (isset($_GET['tri']) && $_GET['tri']=='duree') { echo 'selected';  }
					echo '>Durée</option>
					<option value="note" ';
					if (isset($_GET['tri']) && $_GET['tri']=='note') { echo 'selected';  }
					echo '>Note</option>
				</select>
			</div>
		</form>
		<div class="col-md-1 col-xs-6 col-sm-1" id="fleche">
			<a href="index.php?page=liste-recettes'.$urlN.$urlP.$urlT.$urlTp.$urlN.$urlTC.$urlTri.'&D"><i class="fa fa-arrow-down" data-toggle="tooltip" data-placement="top" title="Décroissant"></i></a>
			<a href="index.php?page=liste-recettes'.$urlN.$urlP.$urlT.$urlTp.$urlN.$urlTC.$urlTri.'"><i class="fa fa-arrow-up" data-toggle="tooltip" data-placement="top" title="Croissant"></i></a>
		</div>

		<div class="col-xs-12">
			<hr>
		</div>';

		echo '
		<div class="col-md-3">
			<div class="panel panel-primary hidden-xs hidden-sm">
				<div class="panel-heading">
					<h3 class="panel-title"> Filtres </h3>
				</div>
				<div class="panel-body">
					<h4 class="pacifico text-center">Type de Plat :</h3>
						<hr>
						<div class="row">
							<a href="index.php?page=liste-recettes'.$urlT.$urlD.$urlP.$urlTC.$urlN.'" class="col-md-12 ligneTri">';
								if (isset($_GET['tp'])) {echo '<i class="fa fa-square-o"></i> ';}
								else { echo '<i class="fa fa-check-square-o"></i> '; }
								echo 'Tous les types
							</a>';
							$plats = $bdd->query('SELECT * FROM mm_type_plat');
							while($plat = $plats->fetch()){
								echo '
								<a href="index.php?page=liste-recettes&tp='.$plat->id.$urlT.$urlD.$urlTC.$urlP.$urlN.'" class="col-md-12 ligneTri">';

									if (isset($_GET['tp']) && $_GET['tp']==$plat->id) {echo '<i class="fa fa-check-square-o"></i> ';}
									else { echo '<i class="fa fa-square-o"></i> '; }
									echo '	'.$plat->nom_type_plat.'
								</a>';
							}
							echo '
						</div>

						<h4 class="pacifico text-center">Difficulté :</h4>
						<hr>
						<div class="row">
							<a href="index.php?page=liste-recettes'.$urlTp.$urlT.$urlP.$urlTC.$urlN.'" class="col-md-12 ligneTri">';
								if (isset($_GET['d'])) {echo '<i class="fa fa-square-o"></i> ';}
								else { echo '<i class="fa fa-check-square-o"></i> '; }
								echo ' Toutes les difficultés
							</a>
							<a href="index.php?page=liste-recettes&d=1'.$urlTp.$urlT.$urlP.$urlTC.$urlN.'" class="col-md-12 ligneTri">';
								if (isset($_GET['d']) && $_GET['d']==1) {echo '<i class="fa fa-check-square-o"></i> ';}
								else { echo '<i class="fa fa-square-o"></i> '; }
								echo ' Facile 
							</a>
							<a href="index.php?page=liste-recettes&d=2'.$urlTp.$urlT.$urlP.$urlTC.$urlN.'" class="col-md-12 ligneTri">';
								if (isset($_GET['d']) && $_GET['d']==2) {echo '<i class="fa fa-check-square-o"></i> ';}
								else { echo '<i class="fa fa-square-o"></i> '; }
								echo ' Moyenne 
							</a>
							<a href="index.php?page=liste-recettes&d=3'.$urlTp.$urlT.$urlP.$urlTC.$urlN.'" class="col-md-12 ligneTri"> ';
								if (isset($_GET['d']) && $_GET['d']==3) {echo '<i class="fa fa-check-square-o"></i> ';}
								else { echo '<i class="fa fa-square-o"></i> '; }
								echo ' Difficile 
							</a>
						</div>

						<h4 class="pacifico text-center">Durée :</h4>
						<hr>
						<div class="row">
							<a href="index.php?page=liste-recettes'.$urlTp.$urlD.$urlP.$urlTC.$urlN.'" class="col-md-12 ligneTri">';
								if (isset($_GET['t0'])) {echo '<i class="fa fa-square-o"></i> ';}
								else { echo '<i class="fa fa-check-square-o"></i> '; }
								echo ' Toutes les durées
							</a>
							<a href="index.php?page=liste-recettes&t0=0&t1=15'.$urlTp.$urlD.$urlP.$urlTC.$urlN.'" class="col-md-12 ligneTri">';
								if (isset($_GET['t0']) && $_GET['t0']==0 && isset($_GET['t1']) && $_GET['t1']==15) {echo '<i class="fa fa-check-square-o"></i> ';}
								else { echo '<i class="fa fa-square-o"></i> '; }
								echo ' 0-15 minutes 
							</a>
							<a href="index.php?page=liste-recettes&t0=16&t1=30'.$urlTp.$urlD.$urlP.$urlTC.$urlN.'" class="col-md-12 ligneTri">';
								if (isset($_GET['t0']) && $_GET['t0']==16 && isset($_GET['t1']) && $_GET['t1']==30) {echo '<i class="fa fa-check-square-o"></i> ';}
								else { echo '<i class="fa fa-square-o"></i> '; }
								echo  ' 16-30 minutes 
							</a>
							<a href="index.php?page=liste-recettes&t0=31&t1=45'.$urlTp.$urlD.$urlP.$urlTC.$urlN.'" class="col-md-12 ligneTri">';
								if (isset($_GET['t0']) && $_GET['t0']==31 && isset($_GET['t1']) && $_GET['t1']==45) {echo '<i class="fa fa-check-square-o"></i> ';}
								else { echo '<i class="fa fa-square-o"></i> '; }
								echo ' 31-45 minutes 
							</a>
							<a href="index.php?page=liste-recettes&t0=46&t1=99999'.$urlTp.$urlD.$urlP.$urlTC.$urlN.'" class="col-md-12 ligneTri">';
								if (isset($_GET['t0']) && $_GET['t0']==31 && isset($_GET['t1']) && $_GET['t1']==999999) {echo '<i class="fa fa-check-square-o"></i> ';}
								else { echo '<i class="fa fa-square-o"></i> '; }
								echo ' + de 45 minutes 
							</a>
						</div>

						<h4 class="pacifico text-center">Prix :</h4>
						<hr>
						<div class="row">
							<a href="index.php?page=liste-recettes'.$urlTp.$urlD.$urlT.$urlTC.$urlN.'" class="col-md-12 ligneTri">';
								if (isset($_GET['p0'])) {echo '<i class="fa fa-square-o"></i> ';}
								else { echo '<i class="fa fa-check-square-o"></i> '; }
								echo 'Tous les prix 
							</a>
							<a href="index.php?page=liste-recettes&p0=0&p1=2'.$urlTp.$urlD.$urlT.$urlTC.$urlN.'" class="col-md-12 ligneTri"> ';
								if (isset($_GET['p0']) && $_GET['p0']==0 && isset($_GET['p1']) && $_GET['p1']==2) {echo '<i class="fa fa-check-square-o"></i> ';}
								else { echo '<i class="fa fa-square-o"></i> '; }
								echo ' - de 2€ par personne 
							</a>
							<a href="index.php?page=liste-recettes&p0=2&p1=5'.$urlTp.$urlD.$urlT.$urlTC.$urlN.'" class="col-md-12 ligneTri">';
								if (isset($_GET['p0']) && $_GET['p0']==2 && isset($_GET['p1']) && $_GET['p1']==5) {echo '<i class="fa fa-check-square-o"></i> ';}
								else { echo '<i class="fa fa-square-o"></i> '; }
								echo ' de 2€ à 5€ par personne 
							</a>
							<a href="index.php?page=liste-recettes&p0=5&p1=10'.$urlTp.$urlD.$urlT.$urlTC.$urlN.'" class="col-md-12 ligneTri">';
								if (isset($_GET['p0']) && $_GET['p0']==5 && isset($_GET['p1']) && $_GET['p1']==10) {echo '<i class="fa fa-check-square-o"></i> ';}
								else { echo '<i class="fa fa-square-o"></i> '; }
								echo ' de 5€ à 10€ par personne 
							</a>
							<a href="index.php?page=liste-recettes&p0=10&p1=99999'.$urlTp.$urlD.$urlT.$urlTC.$urlN.'" class="col-md-12 ligneTri"> ';
								if (isset($_GET['p0']) && $_GET['p0']==10 && isset($_GET['p1']) && $_GET['p1']==99999) {echo '<i class="fa fa-check-square-o"></i> ';}
								else { echo '<i class="fa fa-square-o"></i> '; }
								echo ' + de 10€ par personne 
							</a>
						</div>

						<h4 class="pacifico text-center">Note :</h4>
						<hr>
						<div class="row">
							<a href="index.php?page=liste-recettes'.$urlTp.$urlD.$urlT.$urlTC.$urlP.'" class="col-md-12 ligneTri">';
								if (isset($_GET['n'])) {echo '<i class="fa fa-square-o"></i> ';}
								else { echo '<i class="fa fa-check-square-o"></i> '; }
								echo 'Toutes les notes 
							</a>
							<a href="index.php?page=liste-recettes&n=0'.$urlTp.$urlD.$urlT.$urlTC.$urlP.'" class="col-md-12 ligneTri"> ';
								if (isset($_GET['n']) && $_GET['n']==0) {echo '<i class="fa fa-check-square-o"></i> ';}
								else { echo '<i class="fa fa-square-o"></i> '; }
								echo ' > 0/5 
							</a>
							<a href="index.php?page=liste-recettes&n=1'.$urlTp.$urlD.$urlT.$urlTC.$urlP.'" class="col-md-12 ligneTri">';
								if (isset($_GET['n']) && $_GET['n']==1) {echo '<i class="fa fa-check-square-o"></i> ';}
								else { echo '<i class="fa fa-square-o"></i> '; }
								echo ' > 1/5 
							</a>
							<a href="index.php?page=liste-recettes&n=2'.$urlTp.$urlD.$urlT.$urlTC.$urlP.'" class="col-md-12 ligneTri">';
								if (isset($_GET['n']) && $_GET['n']==2) {echo '<i class="fa fa-check-square-o"></i> ';}
								else { echo '<i class="fa fa-square-o"></i> '; }
								echo ' > 2/5 
							</a>
							<a href="index.php?page=liste-recettes&n=3'.$urlTp.$urlD.$urlT.$urlTC.$urlP.'" class="col-md-12 ligneTri">';
								if (isset($_GET['n']) && $_GET['n']==3) {echo '<i class="fa fa-check-square-o"></i> ';}
								else { echo '<i class="fa fa-square-o"></i> '; }
								echo ' > 3/5 
							</a>
							<a href="index.php?page=liste-recettes&n=4'.$urlTp.$urlD.$urlT.$urlTC.$urlP.'" class="col-md-12 ligneTri">';
								if (isset($_GET['n']) && $_GET['n']==4) {echo '<i class="fa fa-check-square-o"></i> ';}
								else { echo '<i class="fa fa-square-o"></i> '; }
								echo ' > 4/5 
							</a>
						</div>

						<h4 class="pacifico text-center">Type de Cuisine :</h4>
						<hr>
						<div class="row">
							<a href="index.php?page=liste-recettes'.$urlT.$urlD.$urlP.$urlTC.$urlN.'" class="col-md-12 ligneTri">';
								if (isset($_GET['tc'])) {echo '<i class="fa fa-square-o"></i> ';}
								else { echo '<i class="fa fa-check-square-o"></i> '; }
								echo 'Toutes les Cuisines</a>';
								$cuisines = $bdd->query('SELECT * FROM mm_type_cuisine');
								while($cuisine = $cuisines->fetch()){
									echo '<a href="index.php?page=liste-recettes&tc='.$cuisine->id.$urlT.$urlD.$urlTC.$urlP.$urlN.'" class="col-md-12 ligneTri">';

									if (isset($_GET['tc']) && $_GET['tc']==$cuisine->id) {echo '<i class="fa fa-check-square-o"></i> ';}
									else { echo '<i class="fa fa-square-o"></i> '; }
									echo '	'.$cuisine->nom_type_cuisine.'
								</a>';
							}

							echo '			        
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-9">
				<div class="col-md-12">';
					$fetch=0;
					while ($recette = $recettes->fetch()) {
						$fetch++;

						echo '
						<div class="col-lg-4 col-sm-6 col-xs-12">
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
								<a href="index.php?page=recette&id='.$recette->id.'"><h3 class="titreRecetteLR">'.$recette->nom_recette.'</h3></a>
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
					echo '
				</div>
			</div>
		
	';

	if ($fetch==0 ) {echo '<div class="col-xs-8 text-center"><h2 class="pacifico">Oups !</h2><p>Il n\'y a pas de repas correspondants à vos critères</p></div></div>';}
	elseif ($nbRepas>12) { echo '</div>
		<div class="row">
			<nav class="text-center">
				<ul class="pagination pacifico">
					<li>
						<a href="#" aria-label="Previous">
							<span aria-hidden="true">&laquo;</span>
						</a>
					</li>';
					for ($i=1; $i<=$nbPage; $i++) {

						echo '<li';
						if (isset($_GET['numPage']) && $_GET['numPage']==$i) { echo ' class="active"';}
						elseif (!isset($_GET['numPage'])) { if($i==1) { echo ' class="active"';}}
						echo '><a href="index.php?page=liste-recettes'.$urlT.$urlTp.$urlD.'&numPage='.$i.'">'.$i.'</a></li>';
					}
					echo '
					<li>
						<a href="#" aria-label="Next">
							<span aria-hidden="true">&raquo;</span>
						</a>
					</li>
				</ul>
			</nav>
		</div>';}
		echo '
	</div>
</div>
</div>';

?>


<script src="js/imgError.js"></script>

<script type="text/javascript">
	function suppr_repertoire(id_recette) {
		var id_recette = parseInt(id_recette);
		$.ajax( {
			type: "POST",
			url: "ajax/suppr-repertoire.php",
			data: {id_recette : id_recette},
			success : function(data){
				$('#notInList_'+id_recette).attr('data-original-title', 'Ajouter à votre liste de repas').attr('onclick', 'ajout_repertoire('+id_recette+');' ).removeClass('fa-bookmark').addClass('fa-bookmark-o');
			}
		});
	}

	function ajout_repertoire(id_recette) {
		var id_recette = parseInt(id_recette);
		$.ajax( {
			type: "POST",
			url: "ajax/ajout-repertoire.php",
			data: {id_recette : id_recette},
			success : function(data){
				$('#notInList_'+id_recette).attr('data-original-title', 'Supprimer de votre liste de repas').attr('onclick', 'suppr_repertoire('+id_recette+');' ).removeClass('fa-bookmark-o').addClass('fa-bookmark');
			}
		});
	}

	function suppr_menu(id_recette) {
		var id_recette = parseInt(id_recette);
		$.ajax( {
			type: "POST",
			url: "ajax/suppr-menu.php",
			data: {id_recette : id_recette},
			success : function(data){
				console.log(data);
				$('#notInMenu_'+id_recette).attr('data-original-title', 'Ajouter au menu').attr('onclick', 'ajout_menu('+id_recette+');' ).removeClass('fa-calendar').addClass('fa-calendar-plus-o');
			}
		});
	}

	function ajout_menu(id_recette) {
		var id_recette = parseInt(id_recette);
		$.ajax( {
			type: "POST",
			url: "ajax/ajout-menu.php",
			data: {id_recette : id_recette},
			success : function(data){
				$('#notInMenu_'+id_recette).attr('data-original-title', 'Supprimer du menu').attr('onclick', 'suppr_menu('+id_recette+');' ).removeClass('fa-calendar-plus-o').addClass('fa-calendar');
			}
		});
	}

	function triPar(t,p,tp,n,d) {
		var tri = document.getElementById("tri").value;
		window.location = "index.php?page=liste-recettes&tri="+tri+t+p+tp+n+d;
		console.log("caca");
	}
</script>
