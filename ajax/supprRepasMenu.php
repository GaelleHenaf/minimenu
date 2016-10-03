<?php 
	require_once ('../connexion-bdd.php');
	if (isset($_POST['id_recette']) && isset($_POST['id_menu']) && $_POST['id_recette']!='' && $_POST['id_menu']!=''){

		$supprMenuR = $bdd->prepare('DELETE FROM mm_menu_recette
									WHERE id_menu = :id_menu AND id_recette = :id_recette');
		$supprMenuR -> execute(array(	'id_menu' => $_POST['id_menu'], 
										'id_recette' => $_POST['id_recette'] ));
		$nomRecettes = $bdd -> prepare('SELECT nom_recette FROM mm_recette WHERE id = :id_recette');
		$nomRecettes -> execute(array(	'id_recette' => $_POST['id_recette'] ));
		$nomRecette = $nomRecettes -> fetchColumn();

		$prix_repax = $bdd -> prepare('SELECT prixRepas FROM mm_recette where id = :id_recette');
		$prix_repax -> execute(array('id_recette' => $_POST['id_recette']));
		$prix_repas = $prix_repax -> fetchColumn();

		echo $nomRecette.'_'.$prix_repas;
	}
 ?>