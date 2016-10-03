<?php

	// traitement de l'image
	$extensions_valides = array( 'jpg', 'jpeg', 'png' );
	$nom = explode('.', $_FILES['photo']['name']);


	$new_nom_photo = strtr($_POST['nom_recette'], 'áàâäãåçéèêëíìîïñóòôöõúùûüýÿ', 'aaaaaaceeeeiiiinooooouuuuyy');

	$new_nom = 'photo-recette/'.str_replace(' ','',$new_nom_photo).$_SESSION['id'].'.'.$nom['1'];

	// Test les erreurs
	if ($_FILES['photo']['error'] > 0 ) {
		switch ($_FILES['photo']['error']){
	        case 1: // UPLOAD_ERR_INI_SIZE
	            echo"Le fichier dépasse la limite autorisée par le serveur !";
	        break;
	        case 2: // UPLOAD_ERR_FORM_SIZE
	            echo "Le fichier dépasse la limite autorisée dans le formulaire HTML !";
	        break;
	        case 3: // UPLOAD_ERR_PARTIAL
	            echo "L'envoi du fichier a été interrompu pendant le transfert !";
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
	   	$resultat = @move_uploaded_file($_FILES['photo']['tmp_name'],$new_nom);
		if ($resultat) {
			echo '<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Transfert réussi</div>';
			$userUpdateProfil = $bdd->prepare('INSERT INTO mm_recette( nom_photo) = :nom_photo');
			$userUpdateProfil->execute(array( 	'nom_photo' => $new_nom ));

			$userUpdateProfil->closeCursor(); //Termine le traitement de la requete
		}
		else{
			echo '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Transfert impossible !</div>';
		}
	}

	// traitement du formulaire

    // insertion dans la base ingredient
    $prixRepas=0;
	$j=0;
   	while(isset($_POST['ingredient_'.$j]) && $id_recette!=0){
   		$prixRepas+=$_POST['prix_'.$j];
   		if ($_POST['ingredient_'.$j]) {
		    $ingredient = $bdd->prepare('INSERT INTO mm_ingredient(nom_ingredient, prix_ingredient) VALUES (:nom_ingredient, :prix_ingredient)');
		   	$insert_ingredient =  $ingredient->execute(array('nom_ingredient' => htmlspecialchars($_POST['ingredient_'.$j]),
		                        	'prix_ingredient' => (float)$_POST['prix_'.$j]
		                        	));

		    $id_ingredient = $bdd->LastInsertId();

		    $recette_ingredient = $bdd->prepare('INSERT INTO mm_recette_ingredient(id_recette, id_ingredient, qte_ingredient, id_type_qte) VALUES (:id_recette, :id_ingredient, :qte_ingredient, :id_type_qte)');

		    $recette_ingredient->execute(array(
		    							'id_recette' => (int)$id_recette,
		                         		'id_ingredient' => (int)$id_ingredient,
		                        		'qte_ingredient' => (int)$_POST['qte_'.$j],
		                        		'id_type_qte' => (int)$_POST['type_qte_'.$j]
		   	                    		));
		}
	    $j++;
	}

	// insertion dans la base recette
	if($_POST['nom_recette']!=''){
	    $recette = $bdd->prepare('INSERT INTO mm_recette (nom_recette, temps_preparation, temps_cuisson, note, id_type_plat, id_type_cuisine, id_user, date_creation_recette, part, diffusion, difficulte, moyenne, prixRepas) VALUES (:nom_recette, :temps_preparation, :temps_cuisson, :note, :id_type_plat, :id_type_cuisine, :id_user, NOW(), :part, :diffusion, :difficulte, :moyenne, :prixRepas)');
	    $insert_recette = $recette->execute(array('nom_recette' => htmlspecialchars($_POST['nom_recette']),
	                        'temps_preparation' => (int)$_POST['temps_prepa'],
	                        'temps_cuisson' => (int)$_POST['temps_cuisson'],
	                        'note' => (int)$_POST['note'],
	                        'id_type_plat' => (int)$_POST['type_plat'],
	                        'id_type_cuisine' => (int)$_POST['type_cuisine'],
	                        'id_user' => (int)$_SESSION['id'],
	                        'part' => (int)$_POST['part'],
	                        'diffusion' => (int)$_POST['diffusion'],
	                        'difficulte' => (int)$_POST['difficulte'],
	                        'moyenne' => (int)$_POST['note'],
	                        'prixRepas' => $prixRepas
	                        ));
	    $id_recette = $bdd->LastInsertId();
	    $addsRepertoire = $bdd->prepare('INSERT INTO mm_repertoire(id_recette, id_user) VALUES (:id_recette, :id_user)');
			$addsRepertoire -> execute(array( 	'id_recette' => (int)$id_recette,
												'id_user' => (int)$_SESSION['id']
				));
	}


	 // insertion dans la base etape

	$k=1;
   	while(isset($_POST['etape_'.$k]) && $id_recette!=0){
   		if($_POST['etape_'.$k]!='') {
		    $etape = $bdd->prepare('INSERT INTO mm_etape(texte_etape, id_texte_etape) VALUES ( :texte_etape, :id_texte_etape)');
		    $insert_etape = $etape->execute(array('texte_etape' => htmlspecialchars($_POST['etape_'.$k]),
		                        	'id_texte_etape' => (int)$k
		                        	));

		    $id_etape = $bdd->LastInsertId();

		    $recette_etape = $bdd->prepare('INSERT INTO mm_recette_etape(id_recette, id_etape) VALUES (:id_recette, :id_etape)');

		    $recette_etape->execute(array(	'id_recette' => (int)$id_recette,
		                         			'id_etape' => (int)$id_etape
		   	                    	));
		}
	    $k++;
	}

	// Insertion dans la base ingredient

	$k2=0;
   	while(isset($_POST['ingredient_'.$k2]) && $id_recette!=0){
   		if($_POST['ingredient_'.$k2]!='') {
		    $etape = $bdd->prepare('INSERT INTO mm_ingredient(nom_ingredient, prix_ingredient) VALUES ( :nom_ingredient, :prix_ingredient)');
		    $insert_etape = $etape->execute(array('nom_ingredient' => htmlspecialchars($_POST['ingredient_'.$k2]),
		                        	'prix_ingredient' => (int)$_POST['prix_'.$k2]
		                        	));

		    $id_ingredient = $bdd->LastInsertId();

		    $recette_etape = $bdd->prepare('INSERT INTO mm_recette_ingredient(id_recette, id_ingredient) VALUES (:id_recette, :id_ingredient)');

		    $recette_etape->execute(array(	'id_recette' => (int)$id_recette,
		                         			'id_ingredient' => (int)$id_ingredient
		   	                    	));
		}
	    $k2++;
	}

	if ($insert_recette) {
		echo '<script language="Javascript">
<!--
document.location.replace("index.php");
// -->
</script>';
	}
?>

<form class="form-horizontal col-lg-12 josefin jumbotron" action="" method="post" enctype="multipart/form-data" accept-charset="utf-8">
	<fieldset>
		<div class="col-lg-12"><h3 class="pacifico alinea">Titre de la Recette*</h3><hr></div>
		<div class="form-group">
		<div class="col-lg-12">
			<input class="form-control" type="text" name="nom_recette" placeholder="Ex : Gratin Dauphinois"  maxlength="255" id="nom_recette" required>
		</div>
		</div>

		<div class="col-lg-12"><h3 class="pacifico alinea">Photo</h3><hr></div>
		<div class="form-group">
			<div class="col-lg-12">
				<input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
				<input class="form-control" type="file" name="photo" id="photo"/><br/>
			</div>
     	</div>

		<div class="col-lg-12"><h3 class="pacifico alinea">Informations</h3><hr></div>
		<div class="form-group">
			<label for="part" class="col-lg-3 control-label">Nombre de parts* : </label>
			<div class="col-lg-9">
				<input class="form-control" type="number" min="0" step="1" name="part" id="part" required>
			</div>
		</div>
		<div class="form-group">
			<label for="temps_prepa" class="col-lg-3 control-label">Temps de préparation* (min) : </label>
			<div class="col-lg-9">
				<input class="form-control" type="number" min="0" step="1" name="temps_prepa" id="temps_prepa" required>
			</div>
		</div>
		<div class="form-group">
			<label for="temps_cuisson" class="col-lg-3 control-label">Temps de cuisson* (min) : </label>
			<div class="col-lg-9">
				<input class="form-control" type="number" min="0" step="1" name="temps_cuisson" id="temps_cuisson" required>
			</div>
		</div>
		<div class="form-group">
			<label for="note" class="col-lg-3 control-label">Note : </label>
			<div class="col-lg-9">
				<input class="form-control" type="number" min="0" max="5" step="1" name="note" id="note">
			</div>
		</div>
		<div class="form-group">
       		<label for="type_plat" class="col-lg-3 control-label">Type de plat* :</label>
       		<div class="col-lg-9">
	      	 	<select name="type_plat" id="type_plat" class="form-control" required>
		           <?php
		           		$plats = $bdd->query('SELECT * FROM mm_type_plat');
		           		echo '<option value=""></option>';
		           		while($plat = $plats->fetch()){
		           			echo '<option value="'.$plat->id.'">'.$plat->nom_type_plat.'</option>';
		           		}
		           ?>
	       		</select>
	       	</div>
   		</div>
   		<div class="form-group">
       		<label for="type_cuisine" class="col-lg-3 control-label">Type de cuisine* :</label>
       		<div class="col-lg-9">
	      	 	<select name="type_cuisine" id="type_cuisine" class="form-control" required>
		           <?php
		           		$cuisines = $bdd->query('SELECT * FROM mm_type_cuisine');
		           		echo '<option value=""></option>';
		           		while($cuisine = $cuisines->fetch()){
		           			echo '<option value="'.$cuisine->id.'">'.$cuisine->nom_type_cuisine.'</option>';
		           		}
		           ?>
	       		</select>
	       	</div>
   		</div>
   		<div class="form-group">
	    		<label for="difficulte" class="col-lg-3 control-label">Difficulté* :</label>
	    		<div id="difficulte" class="col-lg-9">
			    	<div class="radio-inline">
						<label for="facile">
						    <input type="radio" name="difficulte" id="facile" value="1" required> Facile
						</label>
					</div>
					<div class="radio-inline">
						<label for="moyen">
							<input type="radio" name="difficulte" id="moyen" value="2"> Moyen
						</label>
					</div>
					<div class="radio-inline">
						<label for="difficile">
							<input type="radio" name="difficulte" id="difficile" value="3"> Difficile
						</label>
					</div>
				</div>
	    </div>

   		<!-- Type de quantité dans un array, evite de refare 5 fois la requete -->
 		<?php
			$quantites = $bdd->query('SELECT * FROM mm_type_qte');
			$tabUnite = array(); $i=0;
			while($quantite = $quantites->fetch()){
				$tabUnite[$quantite->id] = $quantite->nom_type_qte;
			}
		?>

		<div class="col-lg-12"><h3 class="pacifico alinea">Ingrédients</h3><hr></div>
		<div class="form-horizontal col-lg-10 col-lg-offset-1">

			<!-- Affichage de 5 champs ingrédients par défaut -->
			<div class="col-lg-12">
				<div class="col-xs-3"><label class="center">Quantité</label></div>
				<div class="col-xs-3"><label class="text-center">Unité</label></div>
				<div class="col-xs-3"><label class="text-center">Ingrédients</label></div>
				<div class="col-xs-3"><label class="text-center">Prix</label></div>
				<?php
					$i=0;
					while ($i<5){
						echo
							'<div class="form-group">
								<div class="col-xs-3">
									<input class="form-control" type="number" min="0" step="0.5" name="qte_'.$i.'" id="qte_'.$i.'">
								</div>
								<div class="col-xs-3">
									<select name="type_qte_'.$i.'" id="type_qte_'.$i.'" class="form-control">
										<option value=""></option>';
											foreach ($tabUnite as $key => $value) {
											echo '<option value="'.$key.'">'.$value.'</option>';
											}
										echo '
									</select>
						   		</div>
						    	<div class="col-xs-3">
						    		<input class="form-control" type="text" class="ingredient" name="ingredient_'.$i.'"id="ingredient_'.$i.'"/>
						   	 	</div>
						   	 	<div class="col-xs-3">
									<input class="form-control" type="number" min="0" step="0.01" name="prix_'.$i.'" id="prix_'.$i.'"/>
								</div>
							</div>';
						$i++;
					}
				?>
			</div>

			<!-- Bouton ajouter un ingrédient -->
			<div class="col-lg-12 text-center" id="test">
				<span onclick="ajoutIngredient(this);" id="ingr_4"><i class="fa fa-plus-square-o"></i><div> Ajouter un ingrédient</div></span><br/>
			</div>
		</div>

		<div class="col-lg-12"><h3 class="pacifico alinea">Recette</h3><hr></div>
		<div class="form-horizontal col-lg-10 col-lg-offset-1">
			<div class="col-lg-12">
				<?php
					$a=1;
					while ($a<6){
						echo
							'<div class="form-group">
								<label for="etape_'.$a.'" class="col-lg-2 control-label">Etape '.$a.' :</label>
								<div class="col-lg-8">
									<textarea class="form-control" rows="3" name="etape_'.$a.'" ></textarea>
								</div>
							</div>';
						$a++;
					}
				?>
			</div>
			<div class="col-lg-12 text-center" id="etape">
				<span onclick="ajoutEtape(this);" id="etape_5">
					<i class="fa fa-plus-square-o"></i>
					<div> Ajouter une etape</div>
				</span>
				<br/>
			</div>

		</div>
		<div class="col-lg-1"></div>

		<div class="col-lg-12"><h3 class="pacifico alinea">Type de diffusion</h3><hr></div>
		<div class="form-group">
	    	<div class="col-lg-10 col-lg-offset-1 text-center">
	    		<p>Voulez-vous que votre recette soit visible de tous (publique) ou accessible seulement dans votre répertoire (privé) ? </p>
		    	<div class="radio-inline">
					<label for="publique">
					    <input type="radio" name="diffusion" id="publique" value="2" checked> Publique
					</label>
				</div>
				<div class="radio-inline">
					<label for="prive">
						<input type="radio" name="diffusion" id="prive" value="1"> Privé
					</label>
				</div>
			</div>
	    </div>
	    <div class="form-group text-center">
			<div >
				<a href="index.php?page=liste-recettes"><button type="reset" class="btn btn-default col-md-offset-5 col-md-3"> Annuler</button></a>
				<button type="submit" class="btn btn-primary col-md-3">Enregistrer</button>
			</div>
		</div>
	</fieldset>
</form>

<script src="js/ajoutIE.js" type="text/javascript"></script>

<!-- Traitement du formulaire -->




  <!--   // $fdp = $bdd->exec("INSERT INTO recette (nom_recette, temps_preparation, temps_cuisson, texte_recette, note, gras, id_type_plat, id_type_cuisine, nom_photo, id_user, date_creation_recette)
    //     VALUES (
    //         '".$_POST['nom_recette']."',
    //         '".(int)$_POST['temps_prepa']."',
    //         '".(int)$_POST['temps_cuisson']."',
    //         '".$_POST['texte_recette']."',
    //         '".(int)$_POST['note']."',
    //         '".(int)$_POST['gras']."',
    //         '".(int)$_POST['type_plat']."',
    //         '".(int)$_POST['type_cuisine']."',
    //         '".$new_nom."',
    //         '".(int)$_POST['id_user']."',
    //         NOW()
    //         ) ");

    // if(!$fdp){
    //     print_r($bdd->errorInfo());
    // }

   // $recette->closeCursor(); //Termine le traitement de la requete -->
