<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

include '_conf.php';
$bdd = mysqli_connect($serveurBDD, $userBDD, $mdpBDD, $nomBDD);

$reponse_id = $_GET['id'];

// Récupérer la réponse actuelle
$reponse_query = mysqli_query($bdd, 
    "SELECT r.*, q.q_id, q.q_titre 
     FROM reponse r 
     JOIN questions q ON r.r_fk_question_id = q.q_id 
     WHERE r.r_id = $reponse_id");
$reponse = mysqli_fetch_assoc($reponse_query);

// Vérifier que l'utilisateur peut modifier cette réponse
if ($_SESSION['user_id'] != $reponse['user_id']) {
    header('Location: detail.php?id=' . $reponse['q_id']);
    exit;
}

if ($_POST) {
    $contenu = mysqli_real_escape_string($bdd, $_POST['contenu']);
    
    mysqli_query($bdd, "UPDATE reponse SET r_contenu = '$contenu' WHERE r_id = $reponse_id");
    
    header('Location: detail.php?id=' . $reponse['q_id']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la réponse</title>
    <link rel="stylesheet" href="monstyle.css?v=11">
</head>
<body>
<div class="container">
    <h1>✏️ Modifier la réponse</h1>
    
    <div class="card" style="margin-bottom: 2rem;">
        <h2>Question :</h2>
        <p><?= htmlspecialchars($reponse['q_titre']) ?></p>
    </div>
    
    <div class="form-card">
        <form method="post">
            <div class="form-group">
                <label class="form-label">Votre réponse :</label>
                <textarea name="contenu" class="form-textarea" placeholder="Modifiez votre réponse..." required><?= htmlspecialchars($reponse['r_contenu']) ?></textarea>
            </div>
            
            <button type="submit" class="button">💾 Enregistrer les modifications</button>
        </form>
    </div>
    
    <div class="nav-actions">
        <a href="detail.php?id=<?= $reponse['q_id'] ?>" class="button secondary">← Retour à la question</a>
        <a href="questions.php" class="button secondary">📋 Questions</a>
        <a href="index.php" class="button secondary">🏠 Accueil</a>
    </div>
</div>
</body>
</html>