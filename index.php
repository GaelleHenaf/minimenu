<?php
	ini_set('display_errors', 'On');
	error_reporting(E_ALL & ~E_NOTICE);
	require_once('connexion-bdd.php');

	if($_POST['remember']) {
		setcookie('user', session_id(), time() + 365*24*3600, null, null, false, true);
	}

	if($_GET['page']=='logout'){
			session_destroy();
			setcookie('user','',time()-1);
			header('Location:index.php');
	}
?>

<!DOCTYPE html>
<html lang="fr">
	<head>
	    <meta charset="utf-8">
		<meta property="og:image" content="http://minimenu.tld/img/logo.jpg"/>
	    <title>MiniMenu</title>
	    <link rel="shortcut icon" href="img/logo.ico">
	    <link rel="stylesheet" href="//cdn.jsdelivr.net/alertifyjs/1.5.0/css/alertify.min.css"/>
	    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"> -->
	    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.6/flatly/bootstrap.min.css">
	    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="css/validationEngine.jquery.css">
	    <link rel="stylesheet" href="css/style2.css">
	   	<link href='https://fonts.googleapis.com/css?family=Josefin+Sans:400,300,300italic,400italic,600,600italic,700italic,700|Pacifico' rel='stylesheet' type='text/css'>

	   	
	</head>

	<body class="josefin">
	<?php

			// register
			if ($_POST['email']!='' && isset($_POST['email']) && $_POST['password']!='' && isset($_POST['password'])) {
			$pseudoexistes = $bdd -> prepare('SELECT count(*) FROM mm_user WHERE pseudo  =:pseudo ');
			$pseudoexistes -> execute(array(	'pseudo' => $_POST['pseudo']));

			$pseudoexiste = $pseudoexistes->fetchColumn();

			$emailexistes = $bdd -> prepare('SELECT count(*) FROM mm_user WHERE email = :email ');
			$emailexistes -> execute(array(	'email' => $_POST['email']));

			$emailexiste = $emailexistes->fetchColumn();

				if($pseudoexiste!=0 || $emailexiste!=0) {
					if($pseudoexiste!=0){
						require_once('menu-navbar.php');
						echo '<div class="alert text-center alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Attention !</strong> Ce pseudo existe déja !</div>';
						$_GET['page']='register';
					}
					else {
						require_once('menu-navbar.php');
						echo '<div class="alert text-center alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Attention !</strong> Un compte à déjà été créé avec cet e-mail !</div>';
						$_GET['page']='register';
					}
				}
				elseif ( isset($_POST['password']) && (strlen($_POST['password']) < 5 || preg_match('#[a-zA-Z0-9]+#i',$_POST['password'])==false)) {
					if (strlen($_POST['password']) < 5){
						require_once('menu-navbar.php');
		    			echo '<div class="alert text-center alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Attention !</strong> Votre mot de passe est trop court !</div>';
						$_GET['page']='register';
		    		}
		    		else {
		    			require_once('menu-navbar.php');
		    			echo '<div class="alert text-center alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Attention !</strong> Votre mot de passe doit contenir au moins un chiffre et une lettre ! Les caractères spéciaux ne sont pas autorisés !</div>';
						$_GET['page']='register';
		    		}
		    	}
				elseif($_POST['pseudo']!='') {
					$register = $bdd->prepare('INSERT INTO mm_user(prenom, nom, pseudo, password, type_user, date_creation,  date_naissance, email, sexe) VALUES(:prenom, :nom, :pseudo, :password, 2, now(),  :date_naissance, :email, :sexe)');
					$register->execute(array(	'prenom' => htmlspecialchars(ucwords($_POST['prenom'])),
											'nom' => htmlspecialchars(ucwords($_POST['nom'])),
											'pseudo' => htmlspecialchars($_POST['pseudo']),
											'password' => md5(sha1($_POST['password'])),
											'date_naissance' => preg_replace('#^([0-9]{2})/([0-9]{2})/([0-9]{4})#', '$3-$2-$1', $_POST['date_naissance']),
											'email' => htmlspecialchars($_POST['email']),
											'sexe' => (int)$_POST['sexe']
											));
					$_SESSION['authentify'] = 2;
					$_SESSION['id'] = $bdd->LastInsertId();


					$register->closeCursor();
					require_once('menu-navbar.php');
				}

			}




		if(isset($_COOKIE['user'])){
			$users = $bdd->prepare('SELECT * FROM mm_user WHERE id_sess=:id_sess');
			$users->execute(array('id_sess' => $_COOKIE['user']));
			$user = $users->fetch();
  			$_SESSION['id'] = $user->id;
  			if($_SESSION['id']!= '' && $_GET['page']!='logout' ){
			$users = $bdd->prepare('SELECT * FROM mm_user WHERE id=:id');
			$users ->execute(array('id' => $_SESSION['id']));
			$user = $users->fetch();
				$_SESSION['authentify'] = $user->type_user;
				require_once('menu-navbar.php');
			}
			else{
				$_SESSION['authentify'] = 3;
				require_once('menu-navbar.php');
			}
        }


		$users = $bdd->prepare('SELECT * FROM mm_user WHERE id=:id');
		$users ->execute(array('id' => $_SESSION['id']));
		$user = $users->fetch();

			if (isset($_POST['emailsign']) && isset($_POST['passwordsign'])) {
				$users = $bdd->prepare('SELECT * FROM mm_user WHERE email=:email AND password=:password');
				$users->execute(array('email' => $_POST['emailsign'],'password'=>md5(sha1($_POST['passwordsign']))));
				if($user = $users->fetch()){
					$_SESSION['authentify'] = $user->type_user;
					$_SESSION['id'] = $user->id;

					require_once('menu-navbar.php');

					$dateLastConnect = $bdd->prepare('UPDATE mm_user SET date_last_connexion = NOW(), id_sess = :id_sess WHERE id = :id');
					$dateLastConnect->execute(array('id'=> $user->id,
													'id_sess' => session_id()));




				}
				elseif($_GET['page'] == ''){
					$_GET['page']='sign-in';
					require_once('menu-navbar.php');
					echo '<div class="alert text-center alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Attention !</strong> combinaison e-mail/mot de passe est incorrect.</div>';
				}
		 	}
		 	else {
		 		require_once('menu-navbar.php');
		 	}


 		$arrayPage = array('register','sign-in','recherche-traitement', 'home', 'mail-oublie', 'liste-recettes', 'recette', 'contact', 'ajout-note', 'test', 'recherche', 'user');
		$arrayPageAuth = array('new-recette', 'mon-compte', 'repertoire', 'menu', 'liste-course', 'course', 'archive', 'ancien-menu', 'user');



		echo '<div class="container">';


 		if (isset($_GET['page']) && $_GET['page']!= '' && $_GET['page']!='logout') {
 			if (in_array($_GET['page'], $arrayPage)) {
 				require_once('site/'.$_GET['page'].'.php');
 			}
 			elseif (in_array($_GET['page'], $arrayPageAuth) && isset($_SESSION['authentify']) && $_SESSION['authentify'] < 3) {
 				require_once('site/'.$_GET['page'].'.php');
 			}
 			else {
 				require_once('404.php');
 			}
 		}
 		else{
 			require_once('site/home.php');
 		}
 		?>
 		</div>

 	 <script src="js/imgError.js"></script>
 	 <script src="//cdn.jsdelivr.net/alertifyjs/1.5.0/alertify.min.js"  type="text/javascript" charset="utf-8"></script>
	    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"  type="text/javascript" charset="utf-8"></script>
	 	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"  type="text/javascript" charset="utf-8"></script>
		<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/jquery.validationEngine-fr.js" type="text/javascript" charset="utf-8"></script>
	<script>
		$(function () {
		  $('[data-toggle="tooltip"]').tooltip()
		})
	</script>



<script>
$(".va-en").validationEngine();
</script>
<noscript>Votre naviguateur ne supporte pas JavaScript!</noscript>



		<footer>
            <!-- <div class="jumbotron">
                <h2 onclick="$('#debugBar').toggle();">DebugBar</h2><hr>
                <div id="debugBar" style="display:none;">
                    <div class="debug well">$_SESSION<pre><?php print_r($_SESSION); ?></pre></div>
                    <div class="debug well">$_GET<pre><?php print_r($_GET); ?></pre></div>
                    <div class="debug well">$_FILES<pre><?php print_r($_FILES); ?></pre></div>
                    <div class="debug well">$_POST<pre><?php print_r($_POST); ?></pre></div>
                    <div class="debug well">$_SERVER<pre><?php print_r($_SERVER); ?></pre></div>
                </div> -->
            </div>             <div class="container text-right josefin">
            	Made with <i class="fa fa-heart-o"></i> by Gaëlle Henaf - Tous droits réservés - <?php echo date('Y');?>
            </div>
        </footer>

 	</body>
</html>
