

<?php
$archiveVide=true;

	$menus = $bdd->prepare('SELECT m.*, mr.id_recette AS id_recette
										FROM mm_menu m
										LEFT JOIN mm_menu_recette mr
											ON m.id = mr.id_menu
											WHERE  m.id_user = :id_user AND m.etat = 2
												GROUP BY m.id DESC');
	$menus -> execute(array( 	'id_user' => (int)$_SESSION['id']));
	echo '<div class="jumbotron">
	<div class="container">
	<h1 class="pacifico">Mes Anciens Menus</h1><hr>';




	while ($menu = $menus->fetch()) {
		$archiveVide=false;
		$m=$menu->id;
		// Calcul du Prix Total du Menu
		$prixMenus = $bdd -> prepare ('SELECT ROUND(SUM(mr.nb_x_recette*r.prixRepas),2) FROM mm_menu_recette mr LEFT JOIN mm_recette r ON r.id = mr.id_recette WHERE mr.id_menu= :id_menu');
		$prixMenus -> execute(array('id_menu' => $m));
		$prixMenu = $prixMenus -> fetchColumn();

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
												WHERE  m.id = :id_menu
													ORDER BY m.id DESC');
		$recettes -> execute(array('id_menu' => (int)$menu->id));

		while ($recette = $recettes->fetch()) {
			$prix = $bdd->prepare('	SELECT ROUND(SUM(prix_ingredient),2)
											FROM mm_ingredient i
												lEFT JOIN mm_recette_ingredient ri
	                                            	ON ri.id_ingredient = i.id
														WHERE ri.id_recette = :recette_id');
			$prix -> execute(array('recette_id' => $menu->id_recette));

			$prixT = $prix->fetchColumn();

				$prix_menu = $prix_menu + $prixT;
				$nb_repas++;
			}
					$dateM = date_create($menu->date_menu);
					$dateA = date_create($menu->date_archivage);

					echo '
					<div id="menuASuppr_'.$menu->id.'" class="panel panel-default">

					<div class="row">
					<div class="col-md-1 col-xs-12 text-center calendarArchive">
						<i class="fa fa-calendar-o fa-3x"></i>
					</div>
					<div class="col-md-8">
						<h2 class="pacifico col-xs-12 text-left">Du '.date_format($dateM, 'd/m/Y').' au '.date_format($dateA, 'd/m/Y').'</h2><hr>
						<div class="col-lg-2 col-xs-12"><i class="fa fa-spoon"></i>  Il comporte :</div>
						<div class="col-lg-6 col-xs-12">
							<div class="col-xs-3"><i class="fa fa-glass"></i><span id="nb_aperitif">';
							if ($nbAperitif >0) {echo $nbAperitif;} else {echo '0';}
							echo '</span> apéritif(s)</div>
							<div class="col-xs-3"><i class="fa fa-circle-o"></i> <span id="nb_entree">';
							if ($nbEntree >0) {echo $nbEntree;} else {echo '0';}
							echo '</span> entrée(s)</div>
							<div class="col-xs-3"><i class="fa fa-cutlery"></i> <span id="nb_plat">';
							if ($nbPlat >0) {echo $nbPlat;} else {echo '0';}
							echo '</span> plat(s)</div>
							<div class="col-xs-3"><i class="fa fa-birthday-cake"></i> <span id="nb_dessert">';
							if ($nbDessert >0) {echo $nbDessert;} else {echo '0';}
							echo '</span> dessert(s)</div>
							</div>
						<div class="col-lg-4 col-xs-12"><i class="fa fa-spoon"></i>  Montant total estimé : <span id="prix_menu">'.$prixMenu.'</span> €</div>
					</div>
					<div class="col-md-3 text-center">
						<a href="index.php?page=ancien-menu&numArch='.$menu->id.'">
							<button type="" class="btn btn-success">Voir la liste des repas</button>
						</a>
						<button class="btn btn-primary" onclick="supprMenu(\''.$menu->id.'\');" > Supprimer le Menu</button>
					</div>
</div></div>
			';
		}


		if ($archiveVide) {
			echo '<div class="col-xs-12">
				<h3 class="pacifico col-lg-8 col-xs-12">Vous n\'avez pas encore de menu archivé</h3><br>

				<p class="col-lg-8 col-xs-12">Pour archiver un menu, vous devez cliquer sur le bouton "Archiver ce menu" dans le pavé informations de votre Menu de la Semaine</p>

				<img class="center-block" src="img/filleoups.png" alt="avatar fille oups">
			</div>';
		}


		echo '
		</div>
		</div>';


 ?>
 <script src="./js/archive.js"></script>
