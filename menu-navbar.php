<?php
	// Création d'un nombre aléatoire pour la recette au hasard
	$recettesAuHasard = $bdd->query('SELECT id FROM mm_recette ORDER BY RAND() LIMIT 1');
	$recetteAuHasard = $recettesAuHasard->fetchColumn();

	if(isset($_SESSION['authentify']) &&  $_SESSION['authentify'] < 3) {
		$menus = $bdd->prepare('SELECT r.* ,u.id AS id_user, u.pseudo, tp.nom_type_plat, tc.nom_type_cuisine
									FROM mm_recette r
										LEFT JOIN mm_user u
											ON r.id_user = u.id
										LEFT JOIN mm_type_plat tp
											ON r.id_type_plat = tp.id
										LEFT JOIN mm_type_cuisine tc
											ON r.id_type_cuisine = tc.id
										LEFT JOIN mm_menu_recette mr
											ON r.id = mr.id_recette
										LEFT JOIN mm_menu_user mu
											ON mr.id = mu.id_menu_recette
												WHERE  mu.id_user = :id_user
													ORDER BY r.id DESC');
		$menus -> execute(array( 	'id_user' => (int)$_SESSION['id'] ));
	}

?>

<nav class="navbar navbar-default">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand pacifico" href="index.php?page=home">MiniMenu.fr</a>
		</div>

		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">

			<!-- RECETTE -->

				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-cutlery"></i> Repas <span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="index.php?page=liste-recettes">Tous les repas</a></li>
						<li><a href="index.php?page=recette&id=<?php echo $recetteAuHasard.'&p=alea'?>">Au hasard</a></li>
						<!-- <li><a href="index.php?page=liste-recettes&tp=">Par type de plats</a></li>
						<li><a href="index.php?page=liste-recettes&tc=">Par type de cuisine</a></li> -->

						<?php
							if(isset($_SESSION['authentify']) && $_SESSION['authentify'] < 3) {
								echo 	'<li role="separator" class="divider"></li><li><a href="index.php?page=new-recette">Ajouter un repas</a></li>
										'
								;
					}
						?>
					</ul>
				</li>

				<!-- Si authentifié , CARNET -->

				<?php
					if(isset($_SESSION['authentify']) &&  $_SESSION['authentify'] < 3) {
						echo '<li class="dropdown">
							<a href="#" class="dropdown-toggle black" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-pencil"></i> Mon carnet <span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="index.php?page=menu"><i class="fa fa-calendar"></i> Menu de la semaine</a></li>
				<li><a href="index.php?page=archive"><i class="fa fa-list-alt"></i> Anciens Menus<span class="sr-only">(current)</span></a></li>
				<li><a href="index.php?page=repertoire"><i class="fa fa-book"></i> Mes repas préférés</a></li>
							</ul>
						</li>';
					}
					else{
						echo '<li><a href="index.php?page=" class="log"></a></li>';
					}
				?>

			</ul>

			<!-- BARRE DE RECHERCHE -->

			<ul class="nav navbar-nav navbar-right josefin">
				<?php
				echo ' <form class="navbar-form navbar-left" role="search" action="index.php?page=recherche" method="post">
        <div class="form-group">
          <input type="text" name="recherche" class="form-control" placeholder="Rechercher un repas">
        </div>
        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
      </form>';
					if(isset($_SESSION['authentify']) &&  $_SESSION['authentify'] < 3) {

						//  Si authentifié, MON COMPTE

						echo '<li class="dropdown" id="lien_avatar_menu">
							<a href="#"  class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">';
							if(isset($user->avatar) && $user->avatar!= ''){ echo '<img src="avatar/'.$user->avatar.'" alt="avatar" class="img-circle" id="avatar_menu">'; }
							else{ echo '<img src="avatar/avatar_defaut.png" class="img-circle" alt="avatar">'; }
							echo '<span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="index.php?page=mon-compte">Mon Compte</a></li>
								<li><a href="index.php?page=logout"><i class="fa fa-lock"></i> Se Déconnecter</a></li>
							</ul>
						</li>';
					}
					else{
						echo '<li><a href="index.php?page=sign-in" class="log"><i class="fa fa-lock"></i> Se Connecter</a></li>';
					}
				?>
			</ul>
			<ul class="nav navbar-nav navbar-right search">
			<i class="fa fa-search"></i>
</ul>
		</div>
	</div>

</nav>
