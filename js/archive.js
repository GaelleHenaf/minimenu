 function supprMenu(id_menu) {
	var id_menu = parseInt(id_menu);
	$.ajax( {
		type: "POST",
		url: "ajax/supprMenu.php",
		data: { id_menu : id_menu},
		success: function(data) {
		$('#menuASuppr_'+id_menu).empty();
		var notification = alertify.notify('Le menu a bien été supprimé de votre archive', 'custom', 5, function(){  console.log('dismissed'); });
		}
 });
 }