
	function ajoutIngredient(champ) {
		var m = champ.id;
    	var u = m.split('_');
    	var i = parseInt(u[1])+1;
    	var k = 'ingr_'+i;
    	$("#"+m).attr('id', k );
  
		var field = ''
		+'<div class="form-group">'
				+'<div class="col-lg-3">'
					+'<input class="form-control" type="number" min="0" step="0.5" name="qte_'+i+'" id="qte_'+i+'">'
				+'</div>'
				+'<div class="col-lg-3">'
					+'<select name="type_qte_'+i+'" id="type_qte_'+i+'" class="form-control">'
						+'<option value=""></option>'
						+'<option value="1">ml</option>'
						+'<option value="2">g</option>'
						+'<option value="3">cc</option>'
						+'<option value="4">cs</option>'
						+'<option value="5">pincée(s)</option>'
						+'<option value="6">L</option>'
						+'<option value="7">cl</option>'
						+'<option value="8">kg</option>'
						+'<option value="9">sachet(s)</option>'
			        +'</select>'
			    +'</div>'
			   	+'<div class="col-lg-3">'
			    	+'<input class="form-control" type="text" class="ingredient" id="ingredient_'+i+'" name="ingredient_'+i+'"/>'
			    +'</div>'
			    +'<div class="col-lg-3">'
					+'<input class="form-control" type="number" min="0" step="0.01" name="prix_'+i+'" id="prix_'+i+'">'
				+'</div>'
			+'</div>';
			

		$('#test').append(field);

		//document.getElementById('divFields').innerHtml += field;
	}

	function ajoutEtape(champ) {
    	var m = champ.id;
    	var u = m.split('_');
    	var i = parseInt(u[1])+1;
    	var d = parseInt(u[2])+1;
    	var k = 'etape_'+i+'_'+d;
    	$("#"+m).attr('id', k );

  
		var field = ''
		+'<div class="form-group">'
					+'<label for="etape_'+i+'" class="col-lg-2 control-label">Etape '+d+' :</label>'
					+'<div class="col-lg-8">'
						+'<textarea class="form-control" rows="3" name="etape_'+i+'" id="etape_'+i+'"></textarea> '
					+'</div>'
				+'</div>';
			

		$('#etape').append(field);

		//document.getElementById('divFields').innerHtml += field;
	}
