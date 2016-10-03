	function supprRepasMenu(id_recette_menu_tp) {
		var prixMenu = parseFloat($('#prix_menu').text());
		var champ = id_recette_menu_tp.split('_');
		var id_recette = parseInt(champ[0]);
		var id_menu = parseInt(champ[1]);
		var id_type_plat = parseInt(champ[2]);
		var nbAperitif = parseInt($('#nb_aperitif').text());
		var nbEntree = parseInt($('#nb_entree').text());
		var nbPlat = parseInt($('#nb_plat').text());
		var nbDessert = parseInt($('#nb_dessert').text());
		switch (id_type_plat) {
			case 4:
				var nbAperitif = parseInt($('#nb_aperitif').text())-1;
				break;
			case 2:
				var nbEntree = parseInt($('#nb_entree').text())-1;
				break;
			case 1:
				var nbPlat = parseInt($('#nb_plat').text())-1;
				break;
			case 3:
				var nbDessert = parseInt($('#nb_dessert').text())-1;
				break;
		}
		$.ajax( {
			type: "POST",
			url: "ajax/supprRepasMenu.php",
			data: {id_recette : id_recette, id_menu : id_menu},
			success: function(data) {
			$('#recetteASuppr_'+id_recette).empty();
				console.log(data);
				var champ = data.split('_');
				var nomRepas = champ[0];
				console.log(nomRepas);
				var prixRepas = parseFloat(champ[1]);
				var prix = prixMenu - prixRepas;
				console.log(prix);
				$('#nb_aperitif').empty();
			 	$('#nb_aperitif').append(nbAperitif);
				$('#nb_entree').empty();
			 	$('#nb_entree').append(nbEntree);
				$('#nb_plat').empty();
			 	$('#nb_plat').append(nbPlat);
				$('#nb_dessert').empty();
			 	$('#nb_dessert').append(nbDessert);$('#prix_menu').empty();
				$('#prix_menu').append(prix.toFixed(2));
				var notification = alertify.notify('Le repas "'+nomRepas+'" a bien été supprimé de votre menu', 'custom', 5, function(){  console.log('dismissed'); });
			}
	});
	}

	function ajoutNbXRepas(id_recette_menu_tp) {
		var prixMenu = parseFloat($('#prix_menu').text());
		var champ = id_recette_menu_tp.split('_');
		var id_recette = parseInt(champ[0]);
		var id_menu = parseInt(champ[1]);
		var id_type_plat = parseInt(champ[2]);
		var nbAperitif = parseInt($('#nb_aperitif').text());
		var nbEntree = parseInt($('#nb_entree').text());
		var nbPlat = parseInt($('#nb_plat').text());
		var nbDessert = parseInt($('#nb_dessert').text());
		switch (id_type_plat) {
			case 4:
				var nbAperitif = parseInt($('#nb_aperitif').text())+1;
				break;
			case 2:
				var nbEntree = parseInt($('#nb_entree').text())+1;
				break;
			case 1:
				var nbPlat = parseInt($('#nb_plat').text())+1;
				break;
			case 3:
				var nbDessert = parseInt($('#nb_dessert').text())+1;
				break;
		}
		$.ajax( {
			type: "POST",
			url: "ajax/ajoutNbXRepas.php",
			data: {id_recette : id_recette, id_menu : id_menu},
			success: function(data) {
				console.log(data);
				var champ = data.split('_');
				var nbXrepas = parseInt(champ[0]);
				var prixRepas = parseFloat(champ[1]);
				var prix = prixMenu + prixRepas;
				$('#nbXRepas_'+id_recette).empty();
				$('#nbXRepas_'+id_recette).append('x '+nbXrepas);
				$('#nb_aperitif').empty();
			 	$('#nb_aperitif').append(nbAperitif);
				$('#nb_entree').empty();
			 	$('#nb_entree').append(nbEntree);
				$('#nb_plat').empty();
			 	$('#nb_plat').append(nbPlat);
				$('#nb_dessert').empty();
			 	$('#nb_dessert').append(nbDessert);
				$('#prix_menu').empty();
				$('#prix_menu').append(prix.toFixed(2));
			 	var notification = alertify.notify('Votre modification a bien été prise en compte', 'custom', 5, function(){  console.log('dismissed'); });
			}
		});
	}

	function supprNbXRepas(id_recette_menu_tp) {
		var prixMenu = parseFloat($('#prix_menu').text());
		var champ = id_recette_menu_tp.split('_');
		var id_recette = parseInt(champ[0]);
		var id_menu = parseInt(champ[1]);
		var id_type_plat = parseInt(champ[2]);
		var nbAperitif = parseInt($('#nb_aperitif').text());
		var nbEntree = parseInt($('#nb_entree').text());
		var nbPlat = parseInt($('#nb_plat').text());
		var nbDessert = parseInt($('#nb_dessert').text());
		switch (id_type_plat) {
			case 4:
				var nbAperitif = parseInt($('#nb_aperitif').text())-1;
				break;
			case 2:
				var nbEntree = parseInt($('#nb_entree').text())-1;
				break;
			case 1:
				var nbPlat = parseInt($('#nb_plat').text())-1;
				break;
			case 3:
				var nbDessert = parseInt($('#nb_dessert').text())-1;
				break;
		}
		$.ajax( {
			type: "POST",
			url: "ajax/supprNbXRepas.php",
			data: {id_recette : id_recette, id_menu : id_menu},
			success: function(data) {
				console.log(data);
				var champ = data.split('_');
				var nbXrepas = parseInt(champ[0]);
				var prixRepas = parseFloat(champ[1]);
				var prix = prixMenu - prixRepas;
				console.log(prix);
				$('#nbXRepas_'+id_recette).empty();
			 	$('#nbXRepas_'+id_recette).append('x '+nbXrepas);
				$('#nb_aperitif').empty();
			 	$('#nb_aperitif').append(nbAperitif);
				$('#nb_entree').empty();
			 	$('#nb_entree').append(nbEntree);
				$('#nb_plat').empty();
			 	$('#nb_plat').append(nbPlat);
				$('#nb_dessert').empty();
			 	$('#nb_dessert').append(nbDessert);
				$('#prix_menu').empty();
				$('#prix_menu').append(prix.toFixed(2));
			 	var notification = alertify.notify('Votre modification a bien été prise en compte', 'custom', 5, function(){  console.log('dismissed'); });
			}
		});
	}