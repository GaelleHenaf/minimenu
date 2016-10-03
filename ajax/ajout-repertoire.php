<?php 
 	session_start();
 	require_once ('../connexion-bdd.php');

	echo "coucou";echo $_POST['id_recette'];
	echo $_SESSION['id'];
	if (isset($_SESSION['id']) && $_SESSION['id']!='' && isset($_POST['id_recette']) && $_POST['id_recette']!=''){
		
		$repertoire = $bdd -> prepare('SELECT * FROM mm_repertoire WHERE id_recette = :id_recette && id_user = :id_user');
		$repertoire -> execute(array( 	'id_recette' => (int)$_POST['id_recette'],
											'id_user' => (int)$_SESSION['id'] ));

		
	

		if ($repertoire->fetch() == false){
			
			$addsRepertoire = $bdd->prepare('INSERT INTO mm_repertoire(id_recette, id_user) VALUES (:id_recette, :id_user)');
			$addsRepertoire -> execute(array( 	'id_recette' => (int)$_POST['id_recette'],
												'id_user' => (int)$_SESSION['id'] ));
		}
	}
 ?>