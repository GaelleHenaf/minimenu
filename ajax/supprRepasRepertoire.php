<?php
	session_start();
	require_once ('../connexion-bdd.php');
	if(isset($_SESSION['id']) && isset($_POST['id_recette']) && $_SESSION['id']!='' && $_POST['id_recette']!=''){
		$supprRepR = $bdd -> prepare('DELETE FROM mm_repertoire
										WHERE id_user = :id_user AND id_recette = :id_recette');
		$supprRepR -> execute(array(	'id_user' => $_SESSION['id'], 
										'id_recette' => $_POST['id_recette'] ));

		$nomRecettes = $bdd -> prepare('SELECT nom_recette FROM mm_recette WHERE id = :id_recette');
		$nomRecettes -> execute(array(	'id_recette' => $_POST['id_recette'] ));
		$nomRecette = $nomRecettes -> fetchColumn();
		echo $nomRecette;
	}
?>