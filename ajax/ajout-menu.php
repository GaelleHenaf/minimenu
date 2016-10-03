<?php 
 	session_start();
 	require_once ('../connexion-bdd.php');
	if (isset($_POST['id_recette']) && $_POST['id_recette']!='' && isset($_SESSION['id']) && $_SESSION['id']!=''){

		$menus = $bdd->prepare('SELECT m.*, mr.id_recette
									FROM mm_menu m 
										LEFT JOIN mm_menu_recette mr 
											ON m.id = mr.id_menu 
											WHERE  m.id_user = :id_user AND m.etat = 1
												GROUP BY m.id	');
		$menus -> execute(array( 'id_user' => (int)$_SESSION['id']));

		if ($menu = $menus->fetch()) {
		
			// Si pas archivé, test la présence de la recette
			$recetteInMenu = $bdd->prepare('SELECT m.*, mr.id_recette
									FROM mm_menu m 
										LEFT JOIN mm_menu_recette mr 
											ON m.id = mr.id_menu 
											WHERE  m.id_user = :id_user  AND m.etat = 1 AND mr.id_recette = :id_recette');
			$recetteInMenu -> execute(array( 	'id_user' => (int)$_SESSION['id'],
									'id_recette' => (int)$_POST['id_recette']
			));
			
				// Si la recette n'y est pas, on l'ajoute à la bdd menu_recette
			if ($recetteInMenu->fetch()==false){
				$addsMenuRecette = $bdd->prepare('INSERT INTO mm_menu_recette(id_menu, id_recette) VALUES (:id_menu, :id_recette)');
			 	$addsMenuRecette -> execute(array( 	'id_menu' => (int)$menu->id,
													'id_recette' => (int)$_POST['id_recette'] ));
			}
		}

				// Si archivé, on créé un nouveau menu
		else {
			$addsMenu = $bdd->prepare('INSERT INTO mm_menu(id_user, date_menu, etat) VALUES (:id_user, NOW(), 1)');
			$addsMenu -> execute(array( 	'id_user' => (int)$_SESSION['id'] ));
			$id_menu = $bdd->LastInsertId();

			$addsMenuRecette = $bdd->prepare('INSERT INTO mm_menu_recette(id_menu, id_recette) VALUES (:id_menu, :id_recette)');
			$addsMenuRecette -> execute(array( 	'id_menu' => $id_menu,
													'id_recette' => (int)$_POST['id_recette'] ));

				
		}
	}
 ?>