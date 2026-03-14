<?php
session_start();
include '_conf.php';
$bdd = mysqli_connect($serveurBDD, $userBDD, $mdpBDD, $nomBDD);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Questions du Forum 2025</title>
    <link rel="stylesheet" href="monstyle.css?v=10">
</head>
<body>
<div class="container">
    <h1>Questions du Forum 2025</h1>
    
    <div class="nav-actions">
        <a href="ajout_question.php" class="button">➕ Nouvelle Question</a>
        <a href="index.php" class="button secondary">🏠 Accueil</a>
    </div>
    
    <div class="questions-grid">
        <?php
        $req = "SELECT q.*, u.login, 
                       (SELECT COUNT(*) FROM reponse r WHERE r.r_fk_question_id = q.q_id) as nb_reponses
                FROM questions q 
                LEFT JOIN utilisateur u ON q.user_id = u.user_id 
                ORDER BY q.q_id DESC";
        $res = mysqli_query($bdd, $req);
        
        while($question = mysqli_fetch_assoc($res)) {
            $badge_class = $question['nb_reponses'] > 0 ? 'badge' : 'badge secondary';
            $badge_text = $question['nb_reponses'] > 0 ? "💬 {$question['nb_reponses']}" : "💬 0";
            $can_edit = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $question['user_id'];
            
            $actions = '';
            if ($can_edit) {
                $actions = "
                <div style='margin-top: 0.75rem; display: flex; gap: 0.5rem;'>
                    <a href='modifier_question.php?id={$question['q_id']}' class='button' style='padding: 0.4rem 0.85rem; font-size: 0.8rem;'>✏️ Modifier</a>
                    <a href='supprimer_question.php?id={$question['q_id']}' class='button secondary' style='padding: 0.4rem 0.85rem; font-size: 0.8rem;' onclick='return confirm(\"Supprimer cette question et ses réponses ?\")'>🗑️ Supprimer</a>
                </div>";
            }
            
            echo "
            <div class='question-card'>
                <div class='question-title'>
                    <a href='detail.php?id={$question['q_id']}'>{$question['q_titre']}</a>
                    <span class='{$badge_class}'>{$badge_text}</span>
                </div>
                <div class='question-content'>".substr($question['q_contenu'], 0, 150)."...</div>
                <div class='meta'>
                    <strong>👤 {$question['login']}</strong> 
                    <span>📅 {$question['q_date_ajout']}</span>
                    {$actions}
                </div>
            </div>";
        }
        ?>
    </div>
</div>
</body>
</html>