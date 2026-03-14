<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Forum - Accueil</title>
    <link rel="stylesheet" href="monstyle.css?v=10">
</head>
<body>
<div class="container">
    <div class="hero">
        <h1>Forum de Discussion</h1>
        <p>Bienvenue sur le forum ! Posez vos questions et partagez vos connaissances.</p>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <div style="margin-top: 2rem; padding: 1rem; background: rgba(255,255,255,0.2); border-radius: var(--radius);">
                <p style="color: white; margin: 0;">
                    👋 Bonjour <strong><?= htmlspecialchars($_SESSION['login']) ?></strong> ! 
                    <a href="deconnexion.php" style="color: var(--primary-light); margin-left: 1rem;">🚪 Déconnexion</a>
                </p>
            </div>
        <?php else: ?>
            <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: center;">
                <a href="connexion.php" class="button">🔐 Se connecter</a>
                <a href="inscription.php" class="button secondary">📝 S'inscrire</a>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="grid">
        <div class="card">
            <h2>📋 Questions</h2>
            <p>Consultez toutes les questions du forum</p>
            <a href="questions.php" class="button">Voir les questions</a>
        </div>
        
        <div class="card">
            <h2>💬 Poser une question</h2>
            <p>Partagez vos interrogations avec la communauté</p>
            <a href="ajout_question.php" class="button">Nouvelle question</a>
        </div>
    </div>
</div>
</body>
</html>