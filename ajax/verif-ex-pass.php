<?php
  session_start();
  require_once ('../connexion-bdd.php');
  $mdps = $bdd -> prepare('SELECT password FROM mm_user WHERE id = :id_user');
  $mdps -> execute(array('id_user' => $_SESSION['id']));
  $mdp = $mdps -> fetchColumn();



  if (md5(sha1($_POST['ex_pass'])) == $mdp ) {
  $passed=true;
  }
  else {
  $passed=false;
  }

  echo $passed;
?>
