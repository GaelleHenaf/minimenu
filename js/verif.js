	function verif(element1, element2, element3, element4) {
 	 	var passed=false

   		if (element1.value!=element2.value ) {
    		alert("Les deux mots de passe ne condordent pas");
    		element1.select();
   		}
  		else {
   			if (element3.value!=element4.value ) {
    		alert("Les deux email ne condordent pas");
    		element3.select();
   			}
  			else {
   				passed=true;
   			}
   		}

  		return passed
 	}
