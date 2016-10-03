<div class="jumbotron">
<div class="row">
<?php
$fetchIng=0;
$fetchEtape=0;

	// Base de données

	$recettes = $bdd->prepare('	SELECT r.* ,u.id AS id_user, u.pseudo, u.prenom, tp.nom_type_plat, tp.id AS id_type_plat, tc.nom_type_cuisine, tc.id AS id_type_cuisine
									FROM mm_recette r
										LEFT JOIN mm_user u
											ON r.id_user = u.id
										LEFT JOIN mm_type_plat tp
											ON r.id_type_plat = tp.id
										LEFT JOIN mm_type_cuisine tc
											ON r.id_type_cuisine = tc.id
												WHERE r.id =  :recette_id');
	$recettes -> execute(array('recette_id' => $_GET['id']));
	$recette = $recettes->fetch();

	// Suppression d'un commentaires

	if (isset($_GET['suppr']) && $_GET['suppr']=='com' && isset($_GET['n']) && $_GET['n']!='') {
		$CommentaireSuppr = $bdd->prepare('DELETE FROM mm_commentaire WHERE id = :id_com');
		$CommentaireSuppr -> execute(array('id_com' => $_GET['n']));
	}

	// Nouvelle recette Aleatoire

	elseif (isset($_GET['p']) && $_GET['p']=='alea') {
		$recettesAuHasard = $bdd->query('SELECT id FROM mm_recette ORDER BY RAND() LIMIT 1');
		$recetteAuHasard = $recettesAuHasard->fetchColumn();
		//ok
		echo '<div class="text-center pacifico">
				<a href="index.php?page=recette&id='.$recetteAuHasard.'&p=alea">
					<h4 id="rec_hasard">Une autre recette au hasard ? Cliquez ici</h4>
				</a>
		</div>';
	}

	// Ajout d'un commentaire a la BDD

	elseif (isset($_POST['commentaire']) && $_POST['commentaire']!='' && isset($_SESSION['authentify']) && $_SESSION['authentify']<3) {
		$ttlesnotes = $bdd -> prepare ('SELECT COUNT(*) FROM mm_commentaire WHERE id_recette = :id_recette AND note > 0');
		$ttlesnotes -> execute(array('id_recette' => $_GET['id']));

		$nbNote = $ttlesnotes -> fetchColumn();
		$nbNote++;

		$totalNotes = $bdd -> prepare ('SELECT SUM(note) FROM mm_commentaire WHERE id_recette = :id_recette AND note > 0');
		$totalNotes -> execute(array('id_recette' => $_GET['id']));

		$totalNote = $totalNotes -> fetchColumn();
		$totalNote = $totalNote + $_POST['note_commentaire'];

		$noteRepas = $bdd -> prepare('UPDATE mm_recette r LEFT JOIN mm_commentaire c on c.id_recette = r.id SET moyenne = :total/:nb_note WHERE r.id = :id');
		$noteRepas -> execute(array('total' => $totalNote,
			'nb_note' => $nbNote,
			'id' => $_GET['id']));

		$commentaireInsert = $bdd->prepare('INSERT INTO mm_commentaire(id_recette, date_commentaire, texte_commentaire, user, note) VALUES (:id_recette, NOW(), :texte_commentaire, :user, :note)');
		$commentaireInsert -> execute(array('id_recette' => (int)$_GET['id'],
											'texte_commentaire' => htmlspecialchars($_POST['commentaire']),
											'user' => $_SESSION['id'],
											'note' => (int)$_POST['note_commentaire']));
	}
	
	// Modification général sur la recette
	if (isset($_GET['mod']) && $_GET['mod']=='mod') {

		// Suppression d'une etape
	 	if (isset($_GET['suppr']) && $_GET['suppr']=='supprEtape' && isset($_GET['what']) && $_GET['what']!='') {
			$supprEtape = $bdd->prepare('DELETE FROM mm_etape WHERE id = :id_etape');
			$supprEtape -> execute(array('id_etape' => $_GET['what']));

			$supprRecEtape = $bdd->prepare('DELETE FROM mm_recette_etape WHERE id_etape = :id_etape');
			$supprRecEtape -> execute(array('id_etape' => $_GET['what']));

			$etapesDown = $bdd->prepare('	UPDATE mm_etape e
											LEFT JOIN mm_recette_etape re
													ON re.id_etape = e.id
													 SET id_texte_etape = id_texte_etape-1
														WHERE e.id > :id_etape AND re.id_recette = :id_recette');
			$etapesDown -> execute(array('id_etape' => $_GET['what'],
										'id_recette' => $recette->id));
		}

		// Suppression d'un ingredient
		elseif (isset($_GET['suppr']) && $_GET['suppr']=='supprIngredient' && isset($_GET['what']) && $_GET['what']!=''){
			$supprIngredient = $bdd->prepare('DELETE FROM mm_ingredient WHERE id = :id_ingredient');
			$supprIngredient -> execute(array('id_ingredient' => $_GET['what']));

			$supprRecIngredient = $bdd->prepare('DELETE FROM mm_recette_ingredient WHERE id_ingredient = :id_ingredient');
			$supprRecIngredient -> execute(array('id_ingredient' => $_GET['what']));

			$updatePrixSuppr = $bdd -> prepare('UPDATE mm_recette r LEFT JOIN mm_recette_ingredient ri ON ri.id_recette = r.id
				LEFT JOIN mm_ingredient i ON ri.id_ingredient = i.id SET prixrepas = (prixRepas - prix_ingredient) WHERE r.id = :id_recette');
			$updatePrixSuppr -> execute(array('id_recette' => $recette->id));
		}
	 	echo '<form class="form-horizontal col-md-10 col-md-offset-1 jumbotron" action="index.php?page=recette&id='.$recette->id.'" method="post" enctype="multipart/form-data" accept-charset="utf-8">
			<fieldset>
				<h2 class="pacifico alinea">Titre de la Recette*</h2>
				<hr>
				<div class="form-group">
					<div class="col-md-12">
						<input class="form-control" type="text" name="nom_recette" placeholder="Ex : Gratin Dauphinois" value="'.$recette->nom_recette.'"  maxlength="255" id="nom_recette" required>
					</div>
				</div>

				<h3 class="pacifico alinea">Informations</h3><hr>
				<div class="form-group">
					<label for="part" class="col-md-3 control-label">Nombre de parts* : </label>
					<div class="col-md-9">
						<input class="form-control" type="number" min="0" step="1" name="part" id="part" required value="'.$recette->part.'">
					</div>
				</div>
				<div class="form-group">
					<label for="temps_prepa" class="col-md-3 control-label">Temps de préparation* (min) : </label>
					<div class="col-md-9">
						<input class="form-control" type="number" min="0" step="1" name="temps_prepa" id="temps_prepa" required value="'.$recette->temps_preparation.'">
					</div>
				</div>
				<div class="form-group">
					<label for="temps_cuisson" class="col-md-3 control-label">Temps de cuisson* (min) : </label>
					<div class="col-md-9">
						<input class="form-control" type="number" min="0" step="1" name="temps_cuisson" id="temps_cuisson" required value="'.$recette->temps_cuisson.'">
					</div>
				</div>
				<div class="form-group">
					<label for="note" class="col-md-3 control-label">Note : </label>
					<div class="col-md-9">
						<input class="form-control" type="number" min="0" max="5" step="1" name="note" id="note" value="'.$recette->note.'">
					</div>
				</div>
				<div class="form-group">
		       		<label for="type_plat" class="col-md-3 control-label">Type de plat* :</label>
		       		<div class="col-md-9">
			      	 	<select name="type_plat" id="type_plat" class="form-control" required">';
				           	$plats = $bdd->query('SELECT * FROM mm_type_plat');
				           	echo '<option value="'.$recette->id_type_plat.'">'.$recette->nom_type_plat.'</option>';
				           	while ($plat = $plats->fetch()) {
				           		if ($plat->id!=$recette->id_type_plat) {
				           			echo '<option value="'.$plat->id.'">'.$plat->nom_type_plat.'</option>';
				           		}
				           	}
				        echo '</select>
			       	</div>
		   	</div>
		   	<div class="form-group">
		       	<label for="type_cuisine" class="col-md-3 control-label">Type de cuisine* :</label>
		       	<div class="col-md-9">
			       	<select name="type_cuisine" id="type_cuisine" class="form-control" required>';
				       	$cuisines = $bdd->query('SELECT * FROM mm_type_cuisine');
				       	echo '<option value="'.$recette->id_type_cuisine.'">'.$recette->nom_type_cuisine.'</option>';
				        while ($cuisine = $cuisines->fetch()) {
				          if ($cuisine->id!=$recette->id_type_cuisine) {
				           	echo '<option value="'.$cuisine->id.'">'.$cuisine->nom_type_cuisine.'</option>';
				    			}
				       	}
			      	echo '</select>
						</div>
		   	</div>
	   		<div class="form-group">
		    	<label for="difficulte" class="col-md-3 control-label">Difficulté* :</label>
		    	<div id="difficulte" class="col-md-9">
				  	<div class="radio-inline">
							<label for="facile">
						    <input type="radio" name="difficulte" id="facile" value="1" required';
							  if ($recette->difficulte == 1) {
							  	echo ' checked';
							  }
							  echo '> Facile
							</label>
						</div>
						<div class="radio-inline">
							<label for="moyen">
								<input type="radio" name="difficulte" id="moyen" value="2" ';
							    if ($recette->difficulte == 2) {
							    	echo ' checked';
							    }
							    echo '> Moyen
							</label>
						</div>
						<div class="radio-inline">
							<label for="difficile">
								<input type="radio" name="difficulte" id="difficile" value="3" ';
							    if ($recette->difficulte == 3) {
							    	echo ' checked';
							    }
							    echo '> Difficile
							</label>
						</div>
					</div>
		    </div>';

	   		// Type de quantité dans un array, evite de refare 5 fois la requete

				$quantites = $bdd->query('SELECT * FROM mm_type_qte');
				$tabUnite = array();
				$i=0;
				while($quantite = $quantites->fetch()){
					$tabUnite[$quantite->id] = $quantite->nom_type_qte;
				}

				echo '<h3 class="pacifico alinea">Ingrédients</h3>
				<hr>
				<div class="form-horizontal col-md-10 col-md-offset-1">
					<div class="col-md-12">
						<div class="col-md-3">
							<label class="center">Quantité</label>
						</div>
						<div class="col-md-3">
							<label class="text-center">Unité</label>
						</div>
						<div class="col-md-3">
							<label class="text-center">Ingrédients</label>
						</div>
						<div class="col-md-3">
							<label class="text-center">Prix (€)</label>
						</div>';

						$ingredients = $bdd->prepare('	SELECT i.*, tq.nom_type_qte
												FROM mm_ingredient i
													LEFT JOIN mm_recette_ingredient ri
														ON ri.id_ingredient = i.id
													LEFT JOIN mm_type_qte tq
														ON tq.id = ri.id_type_qte
															WHERE ri.id_recette =:recette_id');
						$ingredients -> execute(array('recette_id' => $recette->id));

						while($ingredient = $ingredients->fetch()){
							echo '<div class="form-group">
								<div class="col-md-3">
									<input class="form-control" type="number" min="0" step="0.5" name="qte_'.$ingredient->id.'" id="qte_'.$ingredient->id.'" value ="'.$ingredient->qte_ingredient.'">
								</div>
								<div class="col-md-3">
									<select name="type_qte_'.$ingredient->id.'" id="type_qte_'.$ingredient->id.'" class="form-control">
										<option value="'.$ingredient->id_type_qte.'">'.$ingredient->nom_type_qte.'</option>';
											foreach ($tabUnite as $key => $value) {
												if ($ingredient->id_type_qte != $key) {
													echo '<option value="'.$key.'">'.$value.'</option>';
												}
											}
									echo '</select>
								</div>
								<div class="col-md-3">
								    <input class="form-control" type="text" class="ingredient" name="ingredient_'.$ingredient->id.'"id="ingredient_'.$ingredient->id.'" value ="'.$ingredient->nom_ingredient.'"/>
								</div>
								<div class="col-md-2">
									<input class="form-control" type="number" min="0" step="0.01" name="prix_'.$ingredient->id.'" id="prix_'.$ingredient->id.'" value ="'.$ingredient->prix_ingredient.'"/>
								</div>
								<div class="col-md-1">
									<a href="index.php?page=recette&id='.$recette->id.'&mod=mod&suppr=supprIngredient&what='.$ingredient->id.'" class="col-md-12 text-right" title="Supprimer un Ingrédient"><i class="fa fa-minus-square-o"></i></a>
								</div>
							</div>';
						}
					echo '</div>

					<!-- Bouton ajouter un ingrédient -->
					<div class="col-md-12 text-center" id="test">
						<span onclick="ajoutIngredient(this);" id="ingr_1000"><i class="fa fa-plus-square-o"></i><div> Ajouter un ingrédient</div></span><br>
					</div>
				</div>

				<h2 class="pacifico alinea">Recette</h2><hr>
				<div class="form-horizontal col-md-10 col-md-offset-1">
					<div class="col-md-12">';

						$etapes = $bdd->prepare('	SELECT *
											FROM mm_etape e
												lEFT JOIN mm_recette_etape re
													ON re.id_etape = e.id
														WHERE re.id_recette =:recette_id');
						$etapes -> execute(array('recette_id' => $recette->id));

						while($etape = $etapes->fetch()){
							echo '<div class="form-group">
								<label for="etape_'.$etape->id.'" class="col-md-2 control-label">Etape '.$etape->id_texte_etape.' :</label>
									<div class="col-md-8">
										<textarea class="form-control" rows="3" name="etape_'.$etape->id.'" id="etape_'.$etape->id.'">'.$etape->texte_etape.'</textarea>
									</div>

								<a href="index.php?page=recette&id='.$recette->id.'&mod=mod&suppr=supprEtape&what='.$etape->id.'" class="col-md-1" title="Supprimer une étape"><i class="fa fa-minus-square-o"></i></a>
							</div>';
						}

						$nbEtapes = $bdd->prepare ('SELECT COUNT(*) FROM mm_etape e
													LEFT JOIN mm_recette_etape re
														ON re.id_etape = e.id
													WHERE re.id_recette = :id_recette');
						$nbEtapes -> execute(array('id_recette' => (int)$recette->id));
						$nbEtape = $nbEtapes->fetchColumn();

					echo '</div>

					<div class="col-md-12 text-center" id="etape">
						<span onclick="ajoutEtape(this);"  id="etape_1000_'.$nbEtape.'"><i class="fa fa-plus-square-o"></i>
						<div>
							Ajouter une etape
						</div></span><br/>
					</div>
				</div>

				<h3 class="pacifico alinea col-md-12">Type de diffusion</h3><hr class="col-md-12">
				<div class="form-group">
		    	<div class="col-md-10 col-md-offset-1 text-center">
		    		<p>Voulez-vous que votre recette soit visible de tous (publique) ou accessible seulement dans votre répertoire (privé) ? </p>
			    		<div class="radio-inline">
								<label for="publique">
						 	  <input type="radio" name="diffusion" id="publique" value="2" ';
							  	if ($recette->diffusion == 2) {
							  	  echo ' checked';
							  	 }
							   	echo '> Publique
								</label>
							</div>
							<div class="radio-inline">
								<label for="prive">
									<input type="radio" name="diffusion" id="prive" value="1" ';
								   	 	if ($recette->diffusion == 1) {
								   	 		echo ' checked';
								   	 	}
								   		echo '> Privé
								</label>
							</div>
					</div>
		    </div>
		   	<div class="form-group text-center">
					<div id="bouton_form_sign_in">
						<a href="index.php?page=recette&id='.$recette->id.'"><button type="" class="btn btn-default">Annuler</button></a>
						<button type="submit" class="btn btn-primary">Enregistrer</button>
					</div>
				</div>
			</fieldset>
		</form>';
	}

	// Modification de la photo

	elseif (isset($_GET['mod']) && $_GET['mod']=='photo' && isset($_SESSION['authentify']) && $_SESSION['authentify']<3 && $_SESSION['id']==$recette->id_user) {
		echo '<form class="form-horizontal col-md-8 col-md-offset-2 jumbotron" action="index.php?page=recette&id='.$recette->id.'" method="post" enctype="multipart/form-data">
			<h3 class="pacifico alinea">Photo</h3><hr>
			<div class="form-group">
				<div class="col-md-12">
					<input type="hidden" name="MAX_FILE_SIZE" value="1048521376" />
					<input class="form-control" type="file" name="photo" id="photo"/><br/>
				</div>
	     </div>
	     <div class="form-group">
				<div id="bouton_form_sign_in">
					<a href="index.php?page=recette&id='.$recette->id.'"><button type="" class="btn btn-default col-md-offset-5 col-md-3">Annuler</button></a>
					<button type="submit" class="btn btn-primary col-md-3">Enregistrer</button>
				</div>
			</div>
		</form>';
	}


	// Affichage de la recette
	else {

		// insertion dans la base ingredient
		$j=1001;
   		while (isset($_POST['ingredient_'.$j])) {
   			if ($_POST['ingredient_'.$j]!='') {
		   		$ingredient = $bdd->prepare('INSERT INTO mm_ingredient(nom_ingredient, prix_ingredient) VALUES (:nom_ingredient, :prix_ingredient)');
		   		$ingredient->execute(array('nom_ingredient' => htmlspecialchars($_POST['ingredient_'.$j]),
		                        	'prix_ingredient' => (float)$_POST['prix_'.$j]
		                        	));

		   		$id_ingredient = $bdd->LastInsertId();

		   		$updatePrixAdd = $bdd -> prepare('UPDATE mm_recette r LEFT JOIN mm_recette_ingredient ri ON ri.id_recette = r.id
				LEFT JOIN mm_ingredient i ON ri.id_ingredient = i.id SET prixrepas = (prixRepas + :prix) WHERE r.id = :id');
				$updatePrixAdd -> execute(array('id' => (int)$recette->id, 'prix' => (float)$_POST['prix_'.$j]));

		  		$recette_ingredient = $bdd->prepare('INSERT INTO mm_recette_ingredient(id_recette, id_ingredient, qte_ingredient, id_type_qte) VALUES (:id_recette, :id_ingredient, :qte_ingredient, :id_type_qte)');

		   		$recette_ingredient->execute(array(
		    							'id_recette' => (int)$recette->id,
		                         		'id_ingredient' => (int)$id_ingredient,
		                        		'qte_ingredient' => (int)$_POST['qte_'.$j],
		                        		'id_type_qte' => (int)$_POST['type_qte_'.$j]
		   	                    		));
			}
	   	 	$j++;
		}

	 	// insertion dans la base etape
		$nbEtapes = $bdd->prepare ('SELECT COUNT(*) FROM mm_etape e
												LEFT JOIN mm_recette_etape re
													ON re.id_etape = e.id
												WHERE re.id_recette = :id_recette');
		$nbEtapes -> execute(array('id_recette' => (int)$recette->id));
		$nbEtape = $nbEtapes->fetchColumn();

		$k=1001;
		$e=1;
   	while (isset($_POST['etape_'.$k])) {
   		if ($_POST['etape_'.$k]!='') {
		   	 	$etapeInsert = $bdd->prepare('INSERT INTO mm_etape(texte_etape, id_texte_etape) VALUES ( :texte_etape, :id_texte_etape)');
		   	 	$etapeInsert->execute(array('texte_etape' => htmlspecialchars($_POST['etape_'.$k]),
		                        	'id_texte_etape' => (int)$nbEtape+$e
		                        	));

		    	$id_etapeInsert = $bdd->LastInsertId();

		    	$recette_etapeInsert = $bdd->prepare('INSERT INTO mm_recette_etape(id_recette, id_etape) VALUES (:id_recette, :id_etape)');

		    	$recette_etapeInsert->execute(array(	'id_recette' => (int)$recette->id,
		                         			'id_etape' => (int)$id_etapeInsert
		   	                    	));
			}
		    $k++;
		    $e++;
		}

		$etapesId = $bdd->prepare('	SELECT e.id
										FROM mm_etape e
											lEFT JOIN mm_recette_etape re
												ON re.id_etape = e.id
													WHERE re.id_recette =:recette_id ');
		$etapesId -> execute(array('recette_id' => $recette->id));

		while ($etapeId = $etapesId->fetch()) {
			$z=$etapeId->id;
   			if ($_POST['etape_'.$z]!='') {
		   	 	$etapeUpdate = $bdd->prepare('UPDATE mm_etape SET texte_etape = :texte_etape  WHERE id = :id_etape');
		    	$etapeUpdate->execute(array('texte_etape' => htmlspecialchars($_POST['etape_'.$z]),
		                        	'id_etape' => (int)$z));
			}
	    	$z++;
		}

		if ($_POST['nom_recette']!=''){
			$recetteUpdate = $bdd->prepare('UPDATE mm_recette SET nom_recette = :nom_recette, temps_preparation = :temps_preparation, temps_cuisson = :temps_cuisson, note = :note, id_type_plat = :id_type_plat, id_type_cuisine = :id_type_cuisine, diffusion = :diffusion, part = :part, difficulte = :difficulte WHERE id = :id_recette');
			$recetteUpdate->execute(array(	'nom_recette' => htmlspecialchars($_POST['nom_recette']),
		     							'temps_preparation' => (int)$_POST['temps_prepa'],
		     							'temps_cuisson' => (int)$_POST['temps_cuisson'],
		     							'note' => (int)$_POST['note'],
		     							'id_type_plat' => (int)$_POST['type_plat'],
		     							'id_type_cuisine' => (int)$_POST['type_cuisine'],
		     							'diffusion' => (int)$_POST['diffusion'],
		     							'part' => (int)$_POST['part'],
		     							'difficulte' => (int)$_POST['difficulte'],
		     							'id_recette' => (int)$recette->id));

			$recette->nom_recette = $_POST['nom_recette'];
			$recette->temps_preparation = $_POST['temps_prepa'];
			$recette->temps_cuisson = $_POST['temps_cuisson'];
			$recette->note = $_POST['note'];
			$recette->id_type_plat = $_POST['id_type_plat'];
			$recette->id_type_cuisine = $_POST['id_type_cuisine'];
			$recette->diffusion = $_POST['diffusion'];
			$recette->part = $_POST['part'];
			$recette->difficulte = $_POST['difficulte'];
		}
		elseif (isset($_FILES)) {
			$extensions_valides = array( 'jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG' );
			$nom = explode('.', $_FILES['photo']['name']);

			$new_nom_photo = strtr($recette->nom_recette, 'ÁÀÂÄÃÅÇÉÈÊËÍÏÎÌÑÓÒÔÖÕÚÙÛÜÝ ', 'AAAAAACEEEEEIIIINOOOOOUUUUY_');
			$new_nom_photo = strtr($new_nom_photo, 'áàâäãåçéèêëíìîïñóòôöõúùûüýÿ ', 'aaaaaaceeeeiiiinooooouuuuyy_');

			$new_nom = 'photo-recette/'.str_replace(' ','',$new_nom_photo).$_SESSION['id'].'.'.strtolower($nom['1']);

			// Test les erreurs
			if ($_FILES['photo']['error'] > 0 ) {
				switch ($_FILES['photo']['error']){
			        case 1: // UPLOAD_ERR_INI_SIZE
			            echo '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Le fichier dépasse la limite autorisée par le serveur !</div>';
			        break;
			        case 2: // UPLOAD_ERR_FORM_SIZE
			            echo '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Le fichier dépasse la limite autorisée dans le formulaire HTML !</div>';
			        break;
			        case 3: // UPLOAD_ERR_PARTIAL
			            echo '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>L\'envoi du fichier a été interrompu pendant le transfert !</div>';
			        break;
			        case 4: // UPLOAD_ERR_NO_FILE
			        break;
			    }
			}
			// Test la validité de l'extension
			elseif (in_array($nom['1'],$extensions_valides)==false && isset($_FILES['photo'])) {
			    echo "Extension incorrecte";
			}
			// Transfert du fichier
			elseif(isset($_FILES['photo'])){
			   	$resultat = move_uploaded_file($_FILES['photo']['tmp_name'],$new_nom);
				if ($resultat) {
					echo '<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Transfert réussi</div>';
					$recetteUpdatePhoto = $bdd->prepare('UPDATE mm_recette SET nom_photo =:nom_photo WHERE id = :id_recette');
					$recetteUpdatePhoto->execute(array( 	'nom_photo' => $new_nom, 'id_recette' => $recette->id ));

					$recetteUpdatePhoto->closeCursor(); //Termine le traitement de la requete
					$recette->nom_photo = $new_nom;
				}
				else{
					echo '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Transfert impossible !</div>';
				}
			}
		}

		// vrai affichage de la recette
		$dateR = date_create($recette->date_creation_recette);
		echo '
			<a href="index.php?page=liste-recettes"><button class="btn btn-success"><i class="fa fa-reply"></i> Revenir à la liste de recettes</button></a>
			<div class="col-md-12">
				<div class="row">
				<div class="row">

				<div class="col-md-8">

					<div>
					<div class="row" >
						<h2 class="pacifico text-center">'.$recette->nom_recette.'</h2>
						<hr>
								<div class="col-md-4 col-xs-6 text-center">
									<p>
										<i class="fa fa-user"></i>';
										if (isset($recette->pseudo) && $recette->pseudo!='') {
											echo '<a href="index.php?page=user&mb='.$recette->id_user.'"> '.$recette->pseudo.'</a>';
										}
										else {
											echo ' Anonyme';
										}
										echo '
									</p>
								</div>
							<div class="col-md-4 col-xs-6 text-center">
								<p>
										<i class="fa fa-pie-chart"></i> '.$recette->part.' parts
								</p>
							</div>
							<div class="col-md-4 col-xs-12 text-center">
								<p>
									<i class="fa fa-calendar"></i> '.date_format($dateR, 'd/m/Y').'
								</p>
							</div>
					</div>

					<p class="col-md-3 unR text-center">
						<i class="fa fa-eur"></i>  ';
						if(isset($recette->prixRepas) && $recette->prixRepas!=0 || $recette->prixRepas!='' ){
							echo round($recette->prixRepas,1);
						}
						else{
							echo '&nbsp';
						}
					echo '</p>

					<p class="col-md-3 deuxR text-center">';
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
					echo '</p>
					<p class="col-md-3 troisR  text-center">';
						if($recette->moyenne!=''){
							for ($i=0 ; $i < floor($recette->moyenne); $i++) echo '<i class="fa fa-star"></i>';
							for ($i=$recette->moyenne ; $i < 5; $i++) echo '<i class="fa fa-star-o"></i>';
						}
						else{
							echo '&nbsp';
						}
						echo '
					</p>
					<p class="col-md-3 quatreR text-center">
						<i class="fa fa-clock-o"></i>  ';
						if($recette->temps_preparation!=''  && $recette->temps_cuisson!=''){
							echo $recette->temps_preparation + $recette->temps_cuisson;
						}
						else{
							echo '&nbsp';
						}
						echo ' m
					</p>';



				echo '</div>';

				

				echo '</div>
				<div class="col-md-4 text-center hidden-xs" id="divPhotoRecette">';
					if (isset($recette->nom_photo) && $recette->nom_photo!= '') {
						echo '<img src="'.$recette->nom_photo.'" alt="photo de la recette" id="photoRecette">';
					}
					else {
						echo '<img src="photo-recette/recette_par_default.jpg" alt="photo de la recette" id="photoRecette">';
					}
					if (($recette->id_user == $_SESSION['id'] && isset($_SESSION['authentify']) && $_SESSION['authentify'] < 3) || isset($_SESSION['authentify']) && $_SESSION['authentify'] ==1) {
						echo '	<a href="index.php?page=recette&id='.$recette->id.'&mod=photo" class="col-md-1" title="Modifier paramètres de connexion" id="modifPhotoRecette"><i class="fa fa-5x fa-pencil-square-o"></i></a>';
					}

					echo '</div>';
					echo '
						';
						if (($recette->id_user == $_SESSION['id'] && isset($_SESSION['authentify']) && $_SESSION['authentify'] < 3) || isset($_SESSION['authentify']) && $_SESSION['authentify'] ==1){


		echo '</div>';

													
						// Test déjà au repartoire
						$repertoire = $bdd->prepare('SELECT * FROM mm_repertoire WHERE id_recette = :id_recette && id_user = :id_user');
						$repertoire -> execute(array( 	'id_recette' => $recette->id,
														'id_user' => (int)$_SESSION['id'] ));
						echo '<div class="col-xs-6 col-md-4 text-center">';
						if ($repertoire->fetch()){
							echo '<div><button class="btn btn-default"><i class="fa fa-bookmark"></i> Déjà dans votre liste de repas </button></div>';
						}
						else{ // <a href="">index.php?page=repertoire&ajout=o&recette=</a>
							echo '<div><a href="index.php?page=repertoire&ajout=o&recette='.$_GET['id'].'"><button class="btn btn-default"><i class="fa fa-book"></i>  Ajouter à votre liste de repas </button></a></div>';
						}

						echo '</div>
							';

							// Test déjà au menu
						$menu = $bdd->prepare('SELECT * FROM mm_menu_recette mr
													LEFT JOIN mm_menu m
														ON m.id = mr.id_menu
														WHERE mr.id_recette = :id_recette AND m.id_user = :id_user AND m.etat = 1');
						$menu -> execute(array( 	'id_recette' => $recette->id,
													'id_user' => (int)$_SESSION['id'] ));
						echo '<div class="col-xs-6 col-md-4 text-center">';
						if ($menu->fetch()){
							echo '<div><button class="btn btn-primary"><i class="fa fa-calendar"></i> Déjà dans votre menu </button></div>';
						}
						else{
							echo '<div><a href="index.php?page=menu&ajout=o&recette='.$_GET['id'].'"><button class="btn btn-primary"><i class="fa fa-calendar-plus-o"></i>  Ajouter au Menu  </button></a></div>';
						}
						echo '</div>';



											echo '<div class="col-xs-6 col-md-2 text-center">
								<a href="index.php?page=recette&id='.$recette->id.'&mod=mod"  title="Modifier recette"><button class="btn btn-warning"><i class="fa fa-pencil-square-o"></i> Editer </button></a>
							</div>
							<div class="col-md-2 text-center"  id="supprRecette">
								<div class="col-xs-6">
								<a href="" class="confirm" data-alertify-msg="Attention !<br/><br/>Etes-vous bien sûr de vouloir supprimer cette recette ?" data-alertify-url="index.php?page=liste-recettes&id='.$recette->id.'&suppr=suppr" title="Supprimer"><button class="btn btn-danger"><i class="fa fa-trash-o"></i> Supprimer </button></a>
								</div>
							</div>';
						}
					echo '';


				echo '
			</div></div>'; // Fin du jumbotron

		$ings = $bdd->prepare('	SELECT i.nom_ingredient, ri.qte_ingredient, tq.nom_type_qte
												FROM mm_ingredient i
													LEFT JOIN mm_recette_ingredient ri
														ON ri.id_ingredient = i.id
													LEFT JOIN mm_type_qte tq
														ON tq.id = ri.id_type_qte
															WHERE ri.id_recette =:recette_id');
						$ings -> execute(array('recette_id' => $recette->id));
						while ($ing = $ings->fetch()) {
							$fetchIng++;
				   	}


		$ingredients = $bdd->prepare('SELECT i.nom_ingredient, ri.qte_ingredient, tq.nom_type_qte
										FROM mm_ingredient i
											LEFT JOIN mm_recette_ingredient ri
												ON ri.id_ingredient = i.id
											LEFT JOIN mm_type_qte tq
												ON tq.id = ri.id_type_qte
													WHERE ri.id_recette =:recette_id');
		$ingredients -> execute(array('recette_id' => $recette->id));

		echo '
<div class="espace">
		<div class="col-md-12">';
		 	if ($fetchIng!=0) {
				echo '<div class="col-md-3 recette_ingredient">
					<h3 class="pacifico text-center">Ingrédients</h3><hr>';
					while ($ingredient = $ingredients->fetch()) {
						echo '<p>';
							if($ingredient->qte_ingredient>0){
								echo $ingredient->qte_ingredient.' ';
							}
							echo $ingredient->nom_type_qte.' '.$ingredient->nom_ingredient.'
						</p>';
					}
				echo'</div>';}

				$etapes = $bdd->prepare('	SELECT *
								FROM mm_etape e
									lEFT JOIN mm_recette_etape re
										ON re.id_etape = e.id
											WHERE re.id_recette =:recette_id');
				$etapes -> execute(array('recette_id' => $recette->id));

				while ($etape = $etapes->fetch()) {
						$fetchEtape++;
			 	}

				$etapes = $bdd->prepare('	SELECT *
										FROM mm_etape e
											lEFT JOIN mm_recette_etape re
												ON re.id_etape = e.id
													WHERE re.id_recette =:recette_id');
				$etapes -> execute(array('recette_id' => $recette->id));

				if ($fetchEtape!=0) {


				echo '<div class="col-md-9">
					<div class="col-md-12">
						<h3 class="pacifico text-center">Recette</h3><hr>';

						while ($etape = $etapes->fetch()) {
							echo '<div class="etape col-md-12">
								<p class="pacifico col-md-2 text-center">Etape '.$etape->id_texte_etape.'</p>
			 					<p class="col-md-10">
			  					'.$etape->texte_etape.'
			  				</p>
			  			</div>';
						}

					echo '
					</div>
				</div>';
				}
				echo '
				</div>
				</div>';



				$nbcommentaires = $bdd -> prepare('SELECT COUNT(*) FROM mm_commentaire WHERE id_recette = :id_recette');
				$nbcommentaires -> execute(array('id_recette' => $recette->id));
				$nbcommentaire = $nbcommentaires -> fetchColumn();


				$commentairesAffichage = $bdd->prepare('SELECT * FROM mm_commentaire WHERE id_recette = :id_recette ORDER BY id DESC');
				$commentairesAffichage -> execute(array('id_recette' => $recette->id));


				echo '<div class="col-md-12 espace" >
						<div class="col-md-12">
								<div class="col-xs-12">
									<h3 class="text-center pacifico col-xs-10 col-xs-offset-1" >Commentaires
										<span class="badge">'.$nbcommentaire.'</span>
									</h3>
									<i  id="OnOff"  onclick="showHide();" class="fa fa-plus pull-right"></i>
									
								</div>
								<hr>
								<div id="coms" style="display:none;">';

									while ($commentaireAffichage = $commentairesAffichage->fetch()) {
										$usersConnu = $bdd->prepare('SELECT pseudo, prenom, avatar FROM mm_user WHERE id = :id_user');
										$usersConnu -> execute(array('id_user' => $commentaireAffichage->user));
										$userConnu = $usersConnu->fetch();
										if(isset($userConnu->pseudo) && $userConnu->pseudo!='' ){
											$pseudo = $userConnu->pseudo;
										}
										elseif(isset($userConnu->prenom) && $userConnu->prenom!='' ){
											$pseudo = $userConnu->prenom;
										}
										if(isset($userConnu->avatar) && $userConnu->avatar!=''){
											$avatar = $userConnu->avatar;
										}
										else {
											$avatar = '../avatar/avatar_defaut.png';
										}
										$date_bdd = $commentaireAffichage->date_commentaire;
										$date_commentaire = date_create($date_bdd);
										$new_date_commentaire = date_format($date_commentaire, "d/m/Y à H:i:s");
										if($_SESSION['id'] == $commentaireAffichage->user){
											echo '
												<div class="col-xs-2 col-md-1 supprCommentaire">
													<div>
														<a href="index.php?page=recette&id='.$_GET['id'].'&suppr=com&n='.$commentaireAffichage->id.'"><i class="fa fa-trash-o fa-2x"></i></a>
													</div>
												</div>
											';
										}
										else{
											echo '
												<div class="col-xs-2 col-md-1">
												</div>';
										}

										echo '<div class="col-xs-10 col-md-11 commentaire" id="com_'.$commentaireAffichage->id.'">

											<img id="avatarCommentaire" src="avatar/'.$avatar.'" alt="avatar" class="img-circle relative">
											<strong>'.$pseudo.' </strong> - posté le '.$new_date_commentaire.' ';


											if ($commentaireAffichage->note>0) {
													echo '- '.$commentaireAffichage->note.'/5' ;
											}
											echo '<div class="col-xs-12">
													<p>'.$commentaireAffichage->texte_commentaire.'</p>
												</div>';

												echo '</div>';
											}

									if (isset($_SESSION['authentify']) && $_SESSION['authentify']<3) {
										echo '<div class="col-xs-10 col-xs-offset-1">
											<h3 class="text-center pacifico" >Poster un commentaire</h3><hr>
										</div>
										<form class="form-horizontal" action="index.php?page=recette&id='.$recette->id.'&n='.$commentaireAffichage->id.'" method="post" accept-charset="utf-8">
											<div class="form-group">
												<label for="select_note" class="col-xs-12 text-center"> Votre Note sur 5</label>
												<div class="col-xs-6 col-xs-offset-3">
													<input class="form-control" type="number" min="0" max="5" step="1" name="note_commentaire" id="note" >
												</div>
											</div>
											<div class="form-group">
												<label class="col-xs-12 text-center"> Votre Commentaire*</label>
												<div class="col-xs-12 col-md-6 col-md-offset-3">
													<textarea class="form-control" rows="3" name="commentaire" required></textarea>
												</div>
											</div>
											<div class="form-group">
												<div class="col-xs-12 text-center">
													<button type="submit" class="btn btn-primary">Envoyer</button>
												</div>
											</div>
										</form>';
									}
							echo '
						</div>
						</div>
					</div>';

	 }

?>
</div>
</div>
</div>



<script src="js/ajoutIEMod.js" type="text/javascript" charset="utf-8"></script>
<script src="js/alertify-confirm.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
	function showHide() {
		if ($('#OnOff').hasClass('fa-plus'))
		{
			$('#coms').show();
			$('#OnOff').removeClass('fa-plus').addClass('fa-minus');
		}
		else {
			{
				$('#coms').hide();
				$('#OnOff').removeClass('fa-minus').addClass('fa-plus');
			}
		}
	}
</script>
