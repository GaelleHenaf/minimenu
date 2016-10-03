
<div class="jumbotron">
<h1 class="pacifico">Ma liste de courses</h1><hr>
<div class="container">
<?php
	$m=$_GET['m'];
	$dateM = date_create($menu->date_menu);
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

	$ingredients = $bdd->prepare('	SELECT  SUM(ri.qte_ingredient*mr.nb_x_recette) AS qte,  i.nom_ingredient, tq.nom_type_qte, i.id AS id, r.nom_recette
											FROM mm_ingredient i
												LEFT JOIN mm_recette_ingredient ri
													ON ri.id_ingredient = i.id
													LEFT JOIN mm_recette r
													ON r.id = ri.id_recette
												LEFT JOIN mm_type_qte tq
													ON tq.id = ri.id_type_qte
												LEFT JOIN mm_menu_recette mr
													ON mr.id_recette = ri.id_recette
														WHERE  mr.id_menu = :id_menu
														GROUP BY i.nom_ingredient
															ORDER BY i.nom_ingredient
																');
	$ingredients -> execute(array('id_menu' => $_GET['m']));





//

	echo '
	<div class="col-xs-12">

		<form class="form-horizontal" action="fpdf/course.php?m='.$_GET['m'].'" method="post" accept-charset="utf-8">
			<div class="col-md-7 col-xs-12">
			<h3 class="text-center josefin"> Décochez les produits que vous avez déja chez vous</h3>
			<div class="col-xs-offset-1  col-xs-11">';
				while ($ingredient = $ingredients -> fetch()) {
					echo '<div class="col-md-4 col-xs-6">
						<div class="form-group text-left">
     		 				<div class="checkbox">
        						<label>';
        							echo '<input type="checkbox"  name="'.$ingredient->id.'" checked>';
									if ($ingredient->qte>0) { echo ' '.$ingredient->qte.' '.$ingredient->nom_type_qte;}
									echo ' '.$ingredient->nom_ingredient;
								echo ' </label>
							</div>
							</div>
  					</div>';
  				}
				echo '	</div>
<div class="col-xs-12">
				<div class="col-xs-12"><p> Vous pouvez ici ajouter des éléments pour completer votre liste de courses, par exemple, du dentifrice, du gel douche, ou tout autre article</p></div><div class="col-xs-12">
					';
						$listeCourses = $bdd -> prepare('SELECT * FROM mm_liste_course WHERE id_menu = :id_menu');
						$listeCourses -> execute (array('id_menu' => $_GET['m']));
						$i=0;
						while ($listeCourse = $listeCourses->fetch()){
							echo
								'<div class="form-group">
									<div class="col-xs-3">
										<input class="form-control" type="number" min="0" step="0.5" name="qte_'.$i.'_'.$listeCourse->id.'" id="qte_'.$i.'" value="

										'.$listeCourse->qte.'">
									</div>
									<div class="col-xs-3">
										<input class="form-control" type="text"  name="type_qte_'.$i.'_'.$listeCourse->id.'" id="type_qte_'.$i.'" value="'.$listeCourse->type_qte.'">
							   		</div>
							    	<div class="col-xs-3">
							    		<input class="form-control" type="text" class="ingredient" name="element_'.$i.'_'.$listeCourse->id.'" id="ingredient_'.$i.'" value="'.$listeCourse->element.'">
							   	 	</div>
							   	 	<div class="col-xs-3">
										<input class="form-control" type="number" min="0" step="0.01" name="prix_'.$i.'_'.$listeCourse->id.'" id="prix_'.$i.'" value="'.$listeCourse->prix.'">
									</div>
								</div>';
							$i++;
						}

				echo '</div><div class="col-xs-12 text-center" id="test">
						<span onclick="ajoutElement(this);$(\'#titre_colonne\').toggle(true);" id="element_999"><i class="fa fa-plus-square-o"></i><div class="col-xs-12"> Ajouter un élément à votre liste de cours</span>
						<div id="titre_colonne" class="col-xs-12" style="display:none;">
							<div class="col-xs-4 col-xs-offset-2"><label class="center">Quantité</label></div>
							<div class="col-xs-4"><label class="text-center">Eléments*</label></div>
						</div></div>

						<div class="col-xs-12 panel panel-success">

							<h3 class="pacifico">Options d\'impression</h3>

							<div class="text-justify col-xs-12">Voulez vous imprimer la liste des recettes ? Si oui, cocher ci-dessous les types de plats de plats que vous souhaitez imprimer.</div>
							<div class="col-xs-12">
								<span class="col-xs-3">
									<label><input type="checkbox"  name="tp_4"> Apéritif</label>
								</span>
								<span class="col-xs-3">
									<label><input type="checkbox"  name="tp_2"> Entrée</label>
								</span>
								<span class="col-xs-3">
									<label><input type="checkbox"  name="tp_1"> Plat</label>
								</span>
								<span class="col-xs-3">
									<label><input type="checkbox"  name="tp_3"> Dessert</label>
								</span>
							</div>
						</div>

		
			</div>
				</div>

			<div class="col-xs-1"></div>
			</div>
			<div class="col-md-4 col-md-offset-1 col-xs-12 panel panel-primary">
				<div class="text-left pager">
				<h3 class="pacifico">Liste des plats</h3>
				<hr>';

				$nbsRepas = $bdd -> prepare ('SELECT sum(mr.nb_x_recette) FROM mm_menu_recette  mr
			LEFT JOIN mm_recette r
			ON mr.id_recette = r.id
			WHERE mr.id_menu = :id_menu AND r.id_type_plat =1 ');
		$nbsRepas -> execute (array('id_menu' => $m));
		$nbRepas = $nbsRepas -> fetchColumn();

				$recettes = $bdd->prepare('	SELECT   r.nom_recette
												FROM mm_recette r
													LEFT JOIN mm_menu_recette mr
														ON r.id = mr.id_recette
															WHERE  mr.id_menu = :id_menu

																	');
				$recettes -> execute(array('id_menu' => $_GET['m']));
				while ($recette = $recettes -> fetch()) {
					echo '<div class="col-xs-12 text-left"><i class="fa fa-cutlery"></i> '.$recette->nom_recette.'</div>';
				}
				echo '</div><div class="text-left pager">
				<h3 class="pacifico">Informations</h3>
				<hr>';

				$prixMenus = $bdd -> prepare ('SELECT ROUND(SUM(mr.nb_x_recette*r.prixRepas),2) FROM mm_menu_recette mr LEFT JOIN mm_recette r ON r.id = mr.id_recette WHERE mr.id_menu= :id_menu');
				$prixMenus -> execute(array('id_menu' => $m));
				$prixMenu = $prixMenus -> fetchColumn();


				echo '<div class="text-left">

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
				<div class="col-xs-12"><i class="fa fa-spoon"></i>  Pour un montant total estimé de <span id="prix_menu"> '.$prixMenu.'</span> €</div>

				</div>


			</div>
			</div>
			<div class="form-group text-center col-xs-12">
						<a href="index.php?page=menu"><button type="" class="btn btn-default">Annuler</button></a>
						<button type="submit" class="btn btn-primary">Imprimer</button>
				
				</div>
		</form>
	</div>
		</div>';



?>
</div>
</div>


<script src="js/ajoutLC.js" type="text/javascript"></script>
