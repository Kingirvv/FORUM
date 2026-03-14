<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

include '_conf.php';
$bdd = mysqli_connect($serveurBDD, $userBDD, $mdpBDD, $nomBDD);

$question_id = $_GET['id'];

// Récupérer la question pour vérifier les permissions
$question_query = mysqli_query($bdd, 
    "SELECT q.*, u.login 
     FROM questions q 
     LEFT JOIN utilisateur u ON q.user_id = u.user_id 
     WHERE q.q_id = $question_id");
$question = mysqli_fetch_assoc($question_query);

// Vérifier que l'utilisateur peut supprimer cette question
if ($_SESSION['user_id'] != $question['user_id']) {
    header('Location: detail.php?id=' . $question_id);
    exit;
}

// Supprimer d'abord les réponses associées (à cause des contraintes de clé étrangère)
mysqli_query($bdd, "DELETE FROM reponse WHERE r_fk_question_id = $question_id");

// Puis supprimer la question
mysqli_query($bdd, "DELETE FROM questions WHERE q_id = $question_id");

header('Location: questions.php');
exit;
?>