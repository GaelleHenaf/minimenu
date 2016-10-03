
	    function ajoutElement(champ) {
	    	var m = champ.id;
	    	var u = m.split('_');
	    	var i = parseInt(u[1])+1;
	    	var k = 'ingr_'+i;
	    	$("#"+m).attr('id', k );

 			var field = ''
 			+'<div class="form-group col-lg-12">'
					+'<div class=" col-lg-offset-2 col-lg-4">'
						+'<input class="form-control" type="text" name="qte_'+i+'" id="qte_'+i+'">'
					+'</div>'
				   	+'<div class="col-lg-4">'
				    	+'<input class="form-control" type="text" class="element" id="element_'+i+'" name="element_'+i+'" required/>'
				    +'</div>'
				+'</div>';


			$('#test').append(field);

 			//document.getElementById('divFields').innerHtml += field;
		}
