<?php 
require_once ('../connexion-bdd.php');
	if (isset($_POST['nbPersonne']) && $_POST['nbPersonne']>0  ) {
		$nbsRepas = $bdd -> prepare ('SELECT SUM(r.part)/:nbPersonne 
			FROM mm_menu_recette  mr
			LEFT JOIN mm_recette r
			ON mr.id_recette = r.id
			WHERE mr.id_menu = :id_menu AND r.id_type_plat =1 ');
		$nbsRepas -> execute (array('nbPersonne' => $_POST['nbPersonne'], 'id_menu' => $_POST['id_menu'])); 
		$nbRepas = $nbsRepas -> fetchColumn();
	}
	else {
		$nbsRepas = $bdd -> prepare ('SELECT COUNT(*) FROM mm_menu_recette  mr
			LEFT JOIN mm_recette r
			ON mr.id_recette = r.id
			WHERE mr.id_menu = :id_menu AND r.id_type_plat =1 ');
		$nbsRepas -> execute (array('id_menu' => $_POST['id_menu'])); 
		$nbRepas = $nbsRepas -> fetchColumn();
	}
	$_SESSION['nbRepas']=$nbRepas;
	echo $nbRepas;
?>