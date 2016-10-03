<div class="jumbotron">
	<div class="container">

		<?php


			// Formulaire Modif Avatar
			if(isset($_GET['mod']) && $_GET['mod']=='avatar'){
				echo '<form class="form-horizontal formular col-xs-12" action="index.php?page=mon-compte" method="post" enctype="multipart/form-data">
					<div class="form-group">
						<label class="col-xs-4 control-label vertical-center" for="avatar_user"><h2 class="pacifico">Avatar</h2></label><br/>
						<div class="col-xs-8">
							<input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
			     			<input type="file" class="form-control" name="avatar_user"/><br/>
			     		</div>
			     	</div>
			     	
			     	<div class="form-group text-center col-xs-12">
					     <div>
					     	<a href="index.php?page=mon-compte"><button type="" class="btn btn-default">Annuler</button></a>
					     	<button type="submit" class="btn btn-primary">Envoyer</button>
					     </div>
					</div>
			    </form>';
			}

			// Affichage
			else{

				// Traitement formulaire modification mot de passe
				if ( isset($_POST['pass']) && $_POST['pass']!='' && (strlen($_POST['pass']) < 5 || preg_match('#[a-z]+[0-9]+#i',$_POST['pass'])==false)) {
					if (strlen($_POST['pass']) < 5){
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
		    	elseif ( !empty($_POST['pass']))
		    	{
					$userUpdateProfil = $bdd->prepare('UPDATE  mm_user SET password = :pass WHERE id = :id_user');
					$userUpdateProfil->execute(array(	'pass' => md5(sha1($_POST['pass'])),
			     							'id_user' => (int)$user->id));
				}

				// Traitement formulaire modification des informations
				if ($_POST['email']!='' ){
					$userUpdateProfil = $bdd->prepare('UPDATE  mm_user SET prenom = :prenom, nom = :nom, sexe = :sexe, date_naissance = :date_naissance, email = :email, presentation = :presentation WHERE id = :id_user');
					$userUpdateProfil->execute(array(	'prenom' => htmlspecialchars($_POST['prenom']),
			     							'nom' => htmlspecialchars($_POST['nom']),
			     							'sexe' => (int)$_POST['sexe'],
			     							'date_naissance' => preg_replace('#^([0-3][0-9])/([0-1][0-9])/([1-2][0-9]{3})#', '$3-$2-$1', $_POST['date_naissance']),

											'email' => htmlspecialchars($_POST['email']),
											'presentation' => htmlspecialchars($_POST['presentation']),
			     							'id_user' => (int)$user->id));

					$userUpdateProfil->closeCursor(); //Termine le traitement de la requete


					$user->prenom = $_POST['prenom'];
					$user->nom = $_POST['nom'];
					$user->sexe = $_POST['sexe'];
					$user->date_naissance = $_POST['date_naissance'];
					$user->email = $_POST['email'];
					$user->presentation = $_POST['presentation'];

				}

				// Traitement formulaire modification avatar
				elseif (isset($_FILES)){
					// traitement de l'image
					$extensions_valides = array( 'jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG' );
					$nom = explode('.', $_FILES['avatar_user']['name']);

					$new_nom_photo = strtr($user->pseudo, 'ÁÀÂÄÃÅÇÉÈÊËÍÏÎÌÑÓÒÔÖÕÚÙÛÜÝ ', 'AAAAAACEEEEEIIIINOOOOOUUUUY_');
					$new_nom_photo = strtr($new_nom_photo, 'áàâäãåçéèêëíìîïñóòôöõúùûüýÿ ', 'aaaaaaceeeeiiiinooooouuuuyy_');

					$new_nom = str_replace(' ','',$new_nom_photo).'.'.$nom['1'];

					// Test les erreurs
					if ($_FILES['avatar_user']['error'] > 0 ) {
						switch ($_FILES['avatar_user']['error']){
		       				case 1: // UPLOAD_ERR_INI_SIZE
		           				 echo "Le fichier dépasse la limite autorisée par le serveur !";
		        			break;
		        			case 2: // UPLOAD_ERR_FORM_SIZE
		           				echo "Le fichier dépasse la limite autorisée dans le formulaire HTML !";
		        			break;
					        case 3: // UPLOAD_ERR_PARTIAL
					            echo "L'envoi du fichier a été interrompu pendant le transfert !";
					        break;
					        case 4: // UPLOAD_ERR_NO_FILE
					        break;
					    }
					}
					// Test la validité de l'extension
					elseif (in_array($nom['1'],$extensions_valides)==false && isset($_FILES['avatar_user'])) {
					    echo "Extension incorrecte";
					}
					// Transfert du fichier
					elseif(isset($_FILES['avatar_user'])){
						// echo $_FILES['avatar_user']['tmp_name'];
						// $resultatUpload = copy($_FILES['avatar_user']['tmp_name'], 'avatar/'.$new_nom);
		   				$resultatUpload = @move_uploaded_file($_FILES['avatar_user']['tmp_name'],'avatar/'.$new_nom);
		   				// var_dump($_FILES['avatar_user']['tmp_name']);
		   				// var_dump($new_nom);
						if ($resultatUpload > 0) {
							echo '<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Transfert réussi</div>';
							$userUpdateProfil = $bdd->prepare('UPDATE  mm_user SET avatar = :avatar WHERE id = :id_user');
							$userUpdateProfil->execute(array( 'avatar' => $new_nom,
																'id_user' => (int)$user->id));

							$userUpdateProfil->closeCursor(); //Termine le traitement de la requete

							$user->avatar = $new_nom;
						}
						else{
							echo '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Transfert impossible !</div>';
						}
					}
				}


				// Affichage
				if (isset($_GET['mod']) && $_GET['mod']=='mod') {
					echo '<form class="form-horizontal formular" action="index.php?page=mon-compte" method="post" onSubmit="">';
				}
				echo '<div class="col-xs-12">
					<h1 class="pacifico col-xs-7 col-sm-10">Mon Compte</h1>
					<div class="col-xs-5 col-sm-2 text-left boutonTitreCompte">';
						if (isset($_GET['mod']) && $_GET['mod']=='mod') {
							echo '<a href="index.php?page=mon-compte"><button type="" class="btn btn-default">Annuler</button></a>';
						}
						else  {
							echo '<a href="index.php?page=mon-compte&mod=mod" class="col-xs-12" title="Modifier ces informations"><button class="btn btn-success">
							<i class="fa fa-pencil-square-o"></i> Modifier</button></a>';
						}
					echo '</div>
				</div>
				<hr>
				<div class="col-sm-5 col-xs-12 text-center">
					<h2 class="col-xs-12 pacifico text-center">Informations</h2>
					<div class="col-xs-12  espaceTitreCompte text-center">
						<img src="img/rond.png" alt="rond dégradé de couleur" id="rondDegrade">';
						if(isset($user->avatar) && $user->avatar!= ''){
							echo '<img src="avatar/'.$user->avatar.'" alt="avatar" id="avatarCompte" class="img-circle avatar relative">';
						}
						else {
							echo '<img class="img-circle avatar relative" src="avatar/avatar_defaut.png" alt="avatar" id="avatarCompte">';
						}
						echo '</div>';
						if (isset($_GET['mod']) && $_GET['mod']=='mod') {
						}
						else {
							echo '<a href="index.php?page=mon-compte&mod=avatar" title="Modifier avatar" 
							<button class="btn btn-success espaceTitreCompte"><i class="fa fa-pencil-square-o"></i> Modifier Votre Avatar</button> </a>';
						}
					echo '
					<div class="col-xs-12 text-left">
						<div class="col-xs-12 ligneInfoCompte">
							<strong class="col-xs-5 col-xs-offset-1 text-left">Prénom</strong>';
							if (isset($_GET['mod']) && $_GET['mod']=='mod') {
								echo '<div class="col-xs-6 text-left">
									<input type="text" class="form-control" id="prenom" name="prenom" placeholder="Prenom" value="'.$user->prenom.'">
								</div>';
							}
							else {
								echo '<div class="col-xs-5 text-left"> ';
									if($user->prenom!=''){echo $user->prenom; }
									else{echo '&nbsp';}
								echo '</div>';
							}
						echo '</div>
						<div class="col-xs-12 ligneInfoCompte">
							<strong class="col-xs-5 col-xs-offset-1 text-left">Nom</strong>';
							if (isset($_GET['mod']) && $_GET['mod']=='mod') {
								echo '<div class="col-xs-6 text-left">
									<input type="text" class="form-control" id="nom" name="nom" placeholder="Nom" value="'.$user->nom.'">
								</div>';
							}
							else {
								echo '<div class="col-xs-5 text-left">';
									if($user->nom!=''){echo $user->nom; }
									else{echo '&nbsp';}
								echo '</div>';
							}
						echo '</div>
						<div class="col-xs-12 ligneInfoCompte">
							<strong class="col-xs-5 col-xs-offset-1 text-left">Sexe</strong>';
							if (isset($_GET['mod']) && $_GET['mod']=='mod') {
								echo '
						     		<div class="col-xs-6 text-left radio-inline">
						     			<label for="homme">
						     				<input type="radio" name="sexe" id="homme" value="1"';
						     				if ($user->sexe==1){echo 'checked';}
						     				echo '>Homme
						     			</label>
						     		</div>
						     		</div>

						<div class="col-xs-6 col-xs-offset-6 ligneInfoCompte">
						     		<div class="text-left radio-inline">
						     			<label for="femme">
						     				<input type="radio" name="sexe" id="femme" value="2"';
						     				if ($user->sexe==2){echo 'checked';}
						     				echo '>Femme
						     			</label>
						     		</div>
								';
							}
							else {
								echo '<div class="col-xs-5 text-left"> ';
									if($user->sexe==1) { echo 'Homme'; }
									elseif ($user->sexe==2) { echo 'Femme'; }
									else{ echo '&nbsp'; }
								echo '</div>';
							}
						echo '</div>
						<div class="col-xs-12 ligneInfoCompte">
							<strong class="col-xs-5 col-xs-offset-1 text-left">Age</strong>
							<div class="col-xs-5 text-left"> ';
								if($user->date_naissance!='0000-00-00 00:00:00') {
									$dateUs = preg_replace('#^([0-3][0-9])/([0-1][0-9])/([1-2][0-9]{3})#', '$3-$2-$1', $user->date_naissance);
									$age = (time() - strtotime($dateUs)) / 3600 / 24 / 365; echo floor($age).' ans';
								}
								else{ echo '&nbsp'; }
							echo '</div>
						</div>
						<div class="col-xs-12 ligneInfoCompte">
							<strong class="col-xs-5 col-xs-offset-1 text-left">Date de naissance</strong>';
							if (isset($_GET['mod']) && $_GET['mod']=='mod') {
								echo '<div class="col-xs-6 text-left">
									<input type="date" class="form-control" id="date_naissance" name="date_naissance" placeholder="Date de naissance" infobulle="format jj/mm/aaaa" value="'.$user->date_naissance.'" >
								</div>';
							}
							else {
								echo '<div class="col-xs-5 text-left"> ';
									if($user->date_naissance!='0000-00-00 00:00:00') { $date = date_create($user->date_naissance); echo date_format($date,'d/m/Y');}
									else{ echo '&nbsp'; }
								echo '</div>';
							}
						echo '</div>
					</div>
				</div>

				<div class="col-sm-6 col-sm-offset-1">
					<h2 class="col-sm-12 pacifico text-center espaceTitreCompte">Compte MiniMenu</h2>
					<div class="col-sm-12 ligneInfoCompte">
						<strong class="col-sm-offset-1 col-sm-5 text-left">Pseudo</strong>
						<div class="col-sm-5 text-left">'
							.$user->pseudo.'
						</div>
					</div>
					<div class="col-sm-12 ligneInfoCompte">
						<strong class="col-sm-offset-1 col-sm-5 text-left">E-mail</strong>';
						if (isset($_GET['mod']) && $_GET['mod']=='mod') {
							echo '	<div class="col-sm-6 text-left">
								<input type="text" class="form-control validate[required] text-input" id="email" name="email" placeholder="Email" value="'.$user->email.'" >
							</div>
						</div>
						<div class="col-sm-12 ligneInfoCompte">
							<strong class="col-sm-offset-1 col-sm-5 text-left">Confirmation de l\'E-mail</strong>
							<div class="col-sm-6 text-left">
								<input type="text" class="form-control  validate[required,equals[email]] text-input" id="email_confirm" name="email_confirm" placeholder="Email" value="'.$user->email.'" >
							</div>';
						}
						else {
							echo '<div class="col-sm-5 text-left">';
								if($user->email!='') { echo $user->email; }
								else{ echo '&nbsp'; }
							echo '</div>';
						}
					echo '</div>';
					if (isset($_GET['mod']) && $_GET['mod']=='mod') {
						echo '<div class="col-sm-12 ligneInfoCompte">
							<strong class="col-sm-offset-1 col-sm-5 text-left">Nouveau Mot de Passe</strong>
							<div class="col-sm-6 text-left">
								<input type="password" class="form-control validate[required] text-input" id="pass" name="pass" placeholder="Votre nouveau mot de passe">
							</div>
						</div>
						<div class="col-sm-12 ligneInfoCompte">
							<strong class="col-sm-offset-1 col-sm-5 text-left">Confirmation</strong>
							<div class="col-sm-6 text-left">
								<input type="password" class="form-control validate[required,equals[pass]] text-input" id="pass_confirm" name="pass_confirm" placeholder="Retapez votre mot de passe">
							</div>
						</div>';
					}
					else { echo '
					<div class="col-sm-12 ligneInfoCompte">
						<strong class="col-sm-offset-1 col-sm-5 text-left">Mot de Passe</strong>
						<div class="col-sm-5 text-left">
							********
						</div>
					</div>';
					}
					echo '<div class="col-sm-12 ligneInfoCompte">
						<strong class="col-sm-offset-1 col-sm-5 text-left">Date de dernière connexion</strong>
						<div class="col-sm-5 text-left"> ';
							if($user->date_last_connexion!='0000-00-00 00:00:00') {  $date = date_create($user->date_last_connexion); echo date_format($date,'d/m/Y'); }
							else{ echo '&nbsp'; }
						echo '</div>
					</div>
					<div class="col-sm-12 ligneInfoCompte">
						<strong class="col-sm-offset-1 col-sm-5 text-left">Date de création du compte</strong>
							<div class="col-sm-5 text-left"> ';
								if($user->date_creation!='0000-00-00 00:00:00') {  $date = date_create($user->date_creation); echo date_format($date,'d/m/Y'); }
								else{ echo '&nbsp'; }
						echo '</div>
					</div>

				</div>

				<div class="col-xs-12">
					<h2 class="col-xs-12 pacifico text-center espaceTitreCompte">Présentation</h2>';
					if(isset($_GET['mod']) && $_GET['mod']=='mod'){
					 echo '<div class="col-xs-12">
						<div class="form-group">
							<textarea name="presentation" class="form-control col-xs-12" rows="4" >'.$user->presentation.'</textarea>
						</div>
					</div>';
				}
				else {
					echo '<div class="col-xs-12 text-center espaceTitreCompte">';
						if(isset($user->presentation) && $user->presentation!='') {  echo $user->presentation; }
						else{ echo 'Votre présentation n\'a pas encore été renseignée'; }
					echo '</div>';
				}
				echo '</div>';

				if(isset($_GET['mod']) && $_GET['mod']=='mod'){
					echo '<div class="col-xs-12 espaceTitreCompte">
						<div class="form-group text-center">
						     	<a href="index.php?page=mon-compte"><button type="" class="btn btn-default">Annuler</button></a>
						     	<button type="submit" class="btn btn-primary">Envoyer</button>
						</div>
					</div>
				</form>';
				}

			}
		?>
	</div>
</div>


<script src="./js/mon-compte.js" type="text/javascript"></script>
