<?php
 	session_start();
 	require_once ('../connexion-bdd.php');
	if (isset($_POST['id_recette']) && $_POST['id_recette']!='' && isset($_SESSION['id']) && $_SESSION['id']!=''){

    $delMenu = $bdd->prepare('DELETE mm_menu_recette FROM mm_menu_recette INNER JOIN mm_menu ON mm_menu.id = mm_menu_recette.id_menu WHERE id_recette = :id_recette AND id_user = :id_user');
    $delMenu -> execute(array( 	'id_recette' => (int)$_POST['id_recette'],
                      'id_user' => (int)$_SESSION['id'] ));
	}
  echo $_SESSION['id'];
 ?>
