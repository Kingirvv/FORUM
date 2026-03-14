<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

include '_conf.php';
$bdd = mysqli_connect($serveurBDD, $userBDD, $mdpBDD, $nomBDD);

$reponse_id = $_GET['id'];

// Récupérer la réponse pour vérifier les permissions et rediriger après suppression
$reponse_query = mysqli_query($bdd, 
    "SELECT r.*, q.q_id 
     FROM reponse r 
     JOIN questions q ON r.r_fk_question_id = q.q_id 
     WHERE r.r_id = $reponse_id");
$reponse = mysqli_fetch_assoc($reponse_query);

// Vérifier que l'utilisateur peut supprimer cette réponse
if ($_SESSION['user_id'] != $reponse['user_id']) {
    header('Location: detail.php?id=' . $reponse['q_id']);
    exit;
}

// Supprimer la réponse
mysqli_query($bdd, "DELETE FROM reponse WHERE r_id = $reponse_id");

header('Location: detail.php?id=' . $reponse['q_id']);
exit;
?>