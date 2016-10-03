<?php
	require_once ('../connexion-bdd.php');
	if (isset($_POST['id_menu']) && $_POST['id_menu']!=''){

		$supprMenuR = $bdd->prepare('DELETE FROM mm_menu
									WHERE id = :id_menu ');
		$supprMenuR -> execute(array(	'id_menu' => $_POST['id_menu'] ));

	}
 ?>
