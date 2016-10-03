
$('.confirm').on('click', function () {
var url = $(this).data('alertify-url');
var msg = $(this).data('alertify-msg');
var movable =  alertify.confirm('Movable: false').set('movable', false);
var btnReverse = alertify.confirm('Buttons are reversed').set('reverseButtons', true);
var cancelByDefault = alertify.confirm('Cancel button is focused by default.').set('defaultFocus', 'cancel');
var labels =  alertify.confirm('labels changed!').set('labels', {ok:'Oui, je veux la supprimer', cancel:'Annuler'});
$('.ajs-header').empty();
$('.ajs-header').append('<h3 class="pacifico">MiniMenu - Supprimer une recette</h3>');
alertify.confirm(msg, function (e) {
if (e) {
window.location = url;
} else {
// user clicked "cancel"
}
});
return false;
});
