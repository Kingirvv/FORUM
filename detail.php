<?php
session_start();
include '_conf.php';
$bdd = mysqli_connect($serveurBDD, $userBDD, $mdpBDD, $nomBDD);

$question_id = $_GET['id'];
$question = mysqli_fetch_assoc(mysqli_query($bdd, 
    "SELECT q.*, u.login 
     FROM questions q 
     LEFT JOIN utilisateur u ON q.user_id = u.user_id 
     WHERE q.q_id = $question_id"));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $question['q_titre'] ?></title>
    <link rel="stylesheet" href="monstyle.css?v=11">
</head>
<body>
<div class="container">
    <h1><?= $question['q_titre'] ?></h1>
    
    <!-- Question -->
    <div class="question-card">
        <div class="question-content"><?= nl2br($question['q_contenu']) ?></div>
        <div class="meta">
            <strong>👤 <?= $question['login'] ?></strong> 
            <span>📅 <?= $question['q_date_ajout'] ?></span>
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $question['user_id']): ?>
            <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                <a href="modifier_question.php?id=<?= $question_id ?>" class="button" style="padding: 0.5rem 1rem; font-size: 0.875rem;">✏️ Modifier</a>
                <a href="supprimer_question.php?id=<?= $question_id ?>" class="button secondary" style="padding: 0.5rem 1rem; font-size: 0.875rem;" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette question ? Les réponses seront aussi supprimées.')">🗑️ Supprimer</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Réponses -->
    <h2>💬 Réponses (<?php
        $count_reponses = mysqli_query($bdd, "SELECT COUNT(*) as total FROM reponse WHERE r_fk_question_id = $question_id");
        $count = mysqli_fetch_assoc($count_reponses);
        echo $count['total'];
    ?>)</h2>
    
    <div class="questions-grid">
        <?php
        $reponses = mysqli_query($bdd, 
            "SELECT r.*, u.login 
             FROM reponse r 
             LEFT JOIN utilisateur u ON r.user_id = u.user_id 
             WHERE r.r_fk_question_id = $question_id 
             ORDER BY r.r_id ASC");
             
        if (mysqli_num_rows($reponses) > 0) {
            while($reponse = mysqli_fetch_assoc($reponses)) {
                $can_edit = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $reponse['user_id'];
                
                echo "
                <div class='card'>
                    <div class='question-content'>".nl2br($reponse['r_contenu'])."</div>
                    <div class='meta'>
                        <strong>👤 {$reponse['login']}</strong> 
                        <span>📅 {$reponse['r_date_ajout']}</span>";
                        
                if ($can_edit) {
                    echo "
                        <div style='margin-top: 1rem; display: flex; gap: 0.5rem;'>
                            <a href='modifier_reponse.php?id={$reponse['r_id']}' class='button' style='padding: 0.5rem 1rem; font-size: 0.875rem;'>✏️ Modifier</a>
                            <a href='supprimer_reponse.php?id={$reponse['r_id']}' class='button secondary' style='padding: 0.5rem 1rem; font-size: 0.875rem;' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer cette réponse ?\")'>🗑️ Supprimer</a>
                        </div>";
                }
                
                echo "
                    </div>
                </div>";
            }
        } else {
            echo "
            <div class='card text-center'>
                <div class='question-content'>
                    <p style='color: var(--gray-500); font-style: italic; font-size: 1.1rem;'>
                        🗣️ Aucune réponse pour le moment.<br>
                        Soyez le premier à répondre à cette question !
                    </p>
                </div>
            </div>";
        }
        ?>
    </div>
    
    <!-- Actions -->
    <div class="nav-actions">
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $question['user_id']): ?>
            <a href="ajout_reponse.php?question_id=<?= $question_id ?>" class="button">✏️ Répondre à cette question</a>
        <?php elseif (!isset($_SESSION['user_id'])): ?>
            <a href="connexion.php" class="button">🔐 Connectez-vous pour répondre</a>
        <?php endif; ?>
        <a href="questions.php" class="button secondary">📋 Retour aux questions</a>
        <a href="index.php" class="button secondary">🏠 Accueil</a>
    </div>
</div>
</body>
</html>