function verif(element1, element2) {
	var passed=false

			if (element1.value!=element2.value ) {
			alert("Les deux email ne condordent pas");
			element3.select();
			}
			else {
				passed=true;
			}


		return passed
}

	function verifExPass( ex_pass) {
		$.ajax( {
			type: "POST",
			url: "ajax/verif-ex-pass.php",
			data: {ex_pass : ex_pass},
			success : function(data){
				console.log(data);
			}
		});
	}
