<?php
	require_once ('../connexion-bdd.php');
	if (isset($_POST['id_menu']) && isset($_POST['id_recette']) && $_POST['id_menu']!='' && $_POST['id_recette']!='') {
		$ajoutXRecettes = $bdd -> prepare('UPDATE mm_menu_recette SET nb_x_recette = nb_x_recette+1 WHERE id_menu = :id_menu AND id_recette = :id_recette ');
		$ajoutXRecettes -> execute(array(	'id_menu' => $_POST['id_menu'],
										'id_recette' => $_POST['id_recette'] ));
		$nbs_x_repas = $bdd -> prepare('SELECT nb_x_recette FROM mm_menu_recette  WHERE id_menu = :id_menu AND id_recette = :id_recette ');
		$nbs_x_repas -> execute(array(	'id_menu' => $_POST['id_menu'],
										'id_recette' => $_POST['id_recette'] ));
		$nb_x_repas = $nbs_x_repas -> fetchColumn();

		$prix_repax = $bdd -> prepare('SELECT prixRepas FROM mm_recette where id = :id_recette');
		$prix_repax -> execute(array('id_recette' => $_POST['id_recette']));
		$prix_repas = $prix_repax -> fetchColumn();
		echo $nb_x_repas.'_'.$prix_repas;
	}


 ?>
