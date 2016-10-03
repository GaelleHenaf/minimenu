<?php
	$menu_cours = false;
	$recettes = $bdd->prepare('	SELECT r.* ,u.id AS id_user, u.pseudo, tp.nom_type_plat, tc.nom_type_cuisine , m.id AS id_menu, mr.nb_x_recette
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
	ORDER BY r.nom_recette');
$recettes -> execute(array('id_user' => (int)$_SESSION['id']));
while ($recipe = $recettes->fetch()) {
				$menuEnCours = true;
			}
	$menus = $bdd->prepare('SELECT m.*, mr.id_recette AS id_recette
										FROM mm_menu m
										LEFT JOIN mm_menu_recette mr
											ON m.id = mr.id_menu
											WHERE  m.id = :id_menu
												GROUP BY m.id DESC');
	$menus -> execute(array( 	'id_menu' => (int)$_GET['numArch']));

	$menu = $menus->fetch();

	$dateM = date_create($menu->date_menu);
	$dateA = date_create($menu->date_archivage);

	echo '<div class="jumbotron">
		<div class="container">
			<h1 class="pacifico">Menu du '.date_format($dateM, 'd/m/Y').' au '.date_format($dateA, 'd/m/Y').'</h1><hr>
			<div class="row">
			<div class="col-lg-8">

			';

				// Affichage des recettes

				if (isset($_GET['numArch']) && $_GET['numArch']!='') {
					$recettes = $bdd->prepare('	SELECT r.* ,u.id AS id_user, u.pseudo, tp.nom_type_plat, tc.nom_type_cuisine , m.id AS id_menu, mr.nb_x_recette
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
						WHERE  m.id = :id_menu
								ORDER BY r.id DESC');
					$recettes -> execute(array('id_menu' => (int)$_GET['numArch']));


					
			while ($recette = $recettes->fetch()) {
				$m = $recette->id_menu;

				// Nb repas
				$nbsRepas = $bdd -> prepare ('SELECT sum(mr.nb_x_recette) FROM mm_menu_recette  mr
					LEFT JOIN mm_recette r
					ON mr.id_recette = r.id
					WHERE mr.id_menu = :id_menu AND r.id_type_plat =4 ');
				$nbsRepas -> execute (array('id_menu' => $m));
				$nbAperitif = $nbsRepas -> fetchColumn();
				$nbsRepas = $bdd -> prepare ('SELECT sum(mr.nb_x_recette) FROM mm_menu_recette  mr
					LEFT JOIN mm_recette r
					ON mr.id_recette = r.id
					WHERE mr.id_menu = :id_menu AND r.id_type_plat =2 ');
				$nbsRepas -> execute (array('id_menu' => $m));
				$nbEntree = $nbsRepas -> fetchColumn();
				$nbsRepas = $bdd -> prepare ('SELECT sum(mr.nb_x_recette) FROM mm_menu_recette  mr
					LEFT JOIN mm_recette r
					ON mr.id_recette = r.id
					WHERE mr.id_menu = :id_menu AND r.id_type_plat =1 ');
				$nbsRepas -> execute (array('id_menu' => $m));
				$nbPlat = $nbsRepas -> fetchColumn();
				$nbsRepas = $bdd -> prepare ('SELECT sum(mr.nb_x_recette) FROM mm_menu_recette  mr
					LEFT JOIN mm_recette r
					ON mr.id_recette = r.id
					WHERE mr.id_menu = :id_menu AND r.id_type_plat =3 ');
				$nbsRepas -> execute (array('id_menu' => $m));
				$nbDessert = $nbsRepas -> fetchColumn();










				echo '
				<div  class="col-xs-12">';
					$dateR = date_create($recette->date_creation_recette);


					echo '
					<div class="col-xs-12 col-sm-11 panel panel-default blocRecetteHome">
					
						
					
						<div class="col-sm-4 hidden-xs">';
								if(isset($recette->nom_photo) && $recette->nom_photo!= ''){
									echo '<img class="center-block  photoRecetteHome" src="'.$recette->nom_photo.'" alt="photo de la recette" onerror="imgError(this);">';
								}
								else{
									echo '<img class="center-block  photoRecetteHome" src="photo-recette/recette_par_default.jpg" alt="photo de la recette" onerror="imgError(this);">';
								}
								echo '	
						</div>
						<a href="index.php?page=recette&id='.$recette->id.'">
								<h3 class="pacifico titreRecetteHome col-sm-5 col-xs-9">'.$recette->nom_recette.'</h3>
						</a>
						<div class="col-xs-2">
							<h3 class="" id="nbXRepas_'.$recette->id.'">	x '.$recette->nb_x_recette.'</h3>
						</div>
						<div class="col-xs-1 plusMoins text-left">
							<div class="col-xs-12">
								<i class="fa fa-plus-circle " onclick="ajoutNbXRepas(\''.$recette->id.'_'.$recette->id_menu.'_'.$recette->id_type_plat.'\');"></i>
							</div>
							<div class="col-xs-12">
								<i class="fa fa-minus-circle" onclick="supprNbXRepas(\''.$recette->id.'_'.$recette->id_menu.'_'.$recette->id_type_plat.'\');"></i>
							</div>
						</div>
		
					<div class="col-sm-8 col-xs-12 text-center"><hr>
						<div class="col-sm-4 col-xs-12">
							<i class="fa fa-user"></i> '.$recette->pseudo.'
						</div>
						<div class="col-sm-3 col-xs-12">
							<div>
								<i class="fa fa-pie-chart"></i> '.$recette->part.'
							</div>
						</div>
						<div class="col-sm-5 col-xs-12">
							<i class="fa fa-calendar"></i> '.date_format($dateR, 'd/m/Y').'
						</div>

					</div>

					<div class="row ">
						<div class="col-sm-2 col-xs-6 text-center unHome">
							<i class="fa fa-eur"></i>  ';
							if(isset($recette->prixRepas) && $recette->prixRepas!=0 || $recette->prixRepas!='' ){
								echo round($recette->prixRepas,1);
							}
							else{
								echo '&nbsp';
							}
							echo '
						</div>
						<div class="col-sm-2  col-xs-6 text-center deuxHome">';
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
						<div class="col-sm-2  col-xs-6 text-center troisHome">';
							if($recette->moyenne!=''){
								for ($i=0 ; $i < $recette->moyenne; $i++) echo '<i class="fa fa-star"></i>';
									for ($i=$recette->moyenne ; $i < 5; $i++) echo '<i class="fa fa-star-o"></i>';
								}
							else{
								echo '&nbsp';
							}
							echo '
						</div>
						<div class="col-sm-2  col-xs-6 text-center quatreHome">
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
					</div>';

				// Bouton pour la suppression de la recette
					echo '
				</div>
				<div class="col-sm-1 col-xs-12">
					<i onclick="supprRepasMenu(\''.$recette->id.'_'.$recette->id_menu.'_'.$recette->id_type_plat.'\');" class="supprRepasMenu hidden-xs fa fa-trash-o fa-2x"></i>
					<button class="btn btn-primary visible-xs col-xs-12" onclick="supprRepasMenu(\''.$recette->id.'_'.$recette->id_menu.'_'.$recette->id_type_plat.'\');" class="fa fa-trash-o fa-2x"><i class="fa fa-trash-o"></i> Supprimer</button>
				</div>';

				echo '
			</div>
			';
		}




			// Pavé information

			$m = $_GET[numArch];

			// Nb repas
				$nbsRepas = $bdd -> prepare ('SELECT sum(mr.nb_x_recette) FROM mm_menu_recette  mr
					LEFT JOIN mm_recette r
					ON mr.id_recette = r.id
					WHERE mr.id_menu = :id_menu AND r.id_type_plat =4 ');
				$nbsRepas -> execute (array('id_menu' => $m));
				$nbAperitif = $nbsRepas -> fetchColumn();
				$nbsRepas = $bdd -> prepare ('SELECT sum(mr.nb_x_recette) FROM mm_menu_recette  mr
					LEFT JOIN mm_recette r
					ON mr.id_recette = r.id
					WHERE mr.id_menu = :id_menu AND r.id_type_plat =2 ');
				$nbsRepas -> execute (array('id_menu' => $m));
				$nbEntree = $nbsRepas -> fetchColumn();
				$nbsRepas = $bdd -> prepare ('SELECT sum(mr.nb_x_recette) FROM mm_menu_recette  mr
					LEFT JOIN mm_recette r
					ON mr.id_recette = r.id
					WHERE mr.id_menu = :id_menu AND r.id_type_plat =1 ');
				$nbsRepas -> execute (array('id_menu' => $m));
				$nbPlat = $nbsRepas -> fetchColumn();
				$nbsRepas = $bdd -> prepare ('SELECT sum(mr.nb_x_recette) FROM mm_menu_recette  mr
					LEFT JOIN mm_recette r
					ON mr.id_recette = r.id
					WHERE mr.id_menu = :id_menu AND r.id_type_plat =3 ');
				$nbsRepas -> execute (array('id_menu' => $m));
				$nbDessert = $nbsRepas -> fetchColumn();

			// Calcul du Prix Total du Menu
			$prixMenus = $bdd -> prepare ('SELECT ROUND(SUM(mr.nb_x_recette*r.prixRepas),2) FROM mm_menu_recette mr LEFT JOIN mm_recette r ON r.id = mr.id_recette WHERE mr.id_menu= :id_menu');
			$prixMenus -> execute(array('id_menu' => $m));
			$prixMenu = $prixMenus -> fetchColumn();

			$menus = $bdd -> prepare ('SELECT * FROM mm_menu WHERE id = :id_menu');
			$menus -> execute(array('id_menu' => $m));
			$menu = $menus -> fetch();

			$dateM = date_create($menu->date_menu);
			// Affichage
			echo '</div>';

	

	// Calcul du Prix Total du Menu
	$prixMenus = $bdd -> prepare ('SELECT ROUND(SUM(mr.nb_x_recette*r.prixRepas),2) FROM mm_menu_recette mr LEFT JOIN mm_recette r ON r.id = mr.id_recette WHERE mr.id_menu= :id_menu');
	$prixMenus -> execute(array('id_menu' => $m));
	$prixMenu = $prixMenus -> fetchColumn();

	$menus = $bdd -> prepare ('SELECT * FROM mm_menu WHERE id = :id_menu');
	$menus -> execute(array('id_menu' => $m));
	$menu = $menus -> fetch();

	$dateM = date_create($menu->date_menu);
	// Affichage

	

	
		echo '
		<div class="col-xs-12 col-md-12 col-lg-4 panel panel-success hidden-xs">
			<div class="row">
		
				<h2 class="text-center pacifico">Informations</h2><hr>
				<div>
					<div class="col-xs-12"><i class="fa fa-spoon"></i>  Vous avez commencé ce menu le '.date_format($dateM, 'd/m/Y').'</div>
					<div class="col-xs-12"><i class="fa fa-spoon"></i>  Il comporte :</div>
					<div class="col-xs-11 col-xs-offset-1"><div class="col-xs-1"><i class="fa fa-glass"></i></div> <span id="nb_aperitif">';
					if ($nbAperitif >0) {echo $nbAperitif;} else {echo '0';}
					echo '</span> apéritif(s)</div>
					<div class="col-xs-11 col-xs-offset-1"><div class="col-xs-1"><i class="fa fa-circle-o"></i></div> <span id="nb_entree">';
					if ($nbEntree >0) {echo $nbEntree;} else {echo '0';}
					echo '</span> entrée(s)</div>
					<div class="col-xs-11 col-xs-offset-1"><div class="col-xs-1"><i class="fa fa-cutlery"></i></div> <span id="nb_plat">';
					if ($nbPlat >0) {echo $nbPlat;} else {echo '0';}
					echo '</span> plat(s)</div>
					<div class="col-xs-11 col-xs-offset-1"><div class="col-xs-1"><i class="fa fa-birthday-cake"></i></div> <span id="nb_dessert">';
					if ($nbDessert >0) {echo $nbDessert;} else {echo '0';}
					echo '</span> dessert(s)</div>
					<div class="col-xs-12"><i class="fa fa-spoon"></i>  Pour un montant total estimé de <span id="prix_menu"> '.$prixMenu.'</span> €</div>';
					echo '</div>';
				}

				if(isset($m)) {
					echo '<div class="text-center">
					<a href="index.php?page=liste-course&m='.$m.'"><button class="btn btn-success">Voir la liste de courses associées</button></a>
				</div>';

				if (isset($_GET['numArch']) && $_GET['numArch']!='') {
					if ($menuEnCours == false) {
						echo '<div class="text-center">
						<a href="index.php?page=menu&arch=n&m='.$m.'"><button class="btn btn-default">Desarchiver le menu</button></a>
					</div>';
				}
				else {
					echo '<div class="boutonAncienMenu col-xs-12">Vous avez déja un menu en cours, veuillez l\'archiver si vous voulez récupérer un ancien menu</div>';
				}
			}
			else {
				echo '<div class="text-center">
				<a href="index.php?page=menu&arch=o&m='.$m.'"><button class="btn btn-default">Archiver le menu</button></a>
			</div>		
			</div>
			</div>';
		
	}
				}
			echo '</div>
			</div>
</div></div>
		</div>
		</div>';




?>
<script src="./js/menu.js"></script