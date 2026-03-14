<?php
// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

if ($_POST) {
    include '_conf.php';
    $bdd = mysqli_connect($serveurBDD, $userBDD, $mdpBDD, $nomBDD);
    
    $titre = mysqli_real_escape_string($bdd, $_POST['titre']);
    $contenu = mysqli_real_escape_string($bdd, $_POST['contenu']);
    $user_id = $_SESSION['user_id'];
    
    // Trouver le prochain ID disponible pour les questions
    $max_id_result = mysqli_query($bdd, "SELECT COALESCE(MAX(q_id), 0) as max_id FROM questions");
    $max_id_row = mysqli_fetch_assoc($max_id_result);
    $next_id = $max_id_row['max_id'] + 1;
    
    // Insérer avec l'ID manuel
    $result = mysqli_query($bdd, "INSERT INTO questions (q_id, q_titre, q_contenu, user_id, q_date_ajout) 
                       VALUES ($next_id, '$titre', '$contenu', $user_id, NOW())");
    
    if ($result) {
        header('Location: questions.php');
        exit;
    } else {
        // Essayer sans spécifier l'ID
        $result2 = mysqli_query($bdd, "INSERT INTO questions (q_titre, q_contenu, user_id, q_date_ajout) 
                           VALUES ('$titre', '$contenu', $user_id, NOW())");
        if ($result2) {
            header('Location: questions.php');
            exit;
        } else {
            $error = "Erreur lors de l'ajout de la question: " . mysqli_error($bdd);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle Question</title>
    <link rel="stylesheet" href="monstyle.css?v=12">
</head>
<body>
<div class="container">
    <h1>💬 Poser une Question</h1>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>
    
    <div class="form-card">
        <form method="post">
            <div class="form-group">
                <label class="form-label">Titre de la question :</label>
                <input type="text" name="titre" class="form-input" placeholder="Que voulez-vous demander à la communauté ?" required
                       maxlength="50">
                <div class="form-hint">Maximum 50 caractères</div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Détails de votre question :</label>
                <textarea name="contenu" class="form-textarea" placeholder="Décrivez votre problème ou posez votre question en détail..." required
                          rows="8" maxlength="150"></textarea>
                <div class="form-hint">Maximum 150 caractères</div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Auteur :</label>
                <input type="text" class="form-input" value="<?= htmlspecialchars($_SESSION['login']) ?>" disabled style="background: var(--gray-100);">
                <div class="form-hint">Vous posez cette question en tant que <strong><?= htmlspecialchars($_SESSION['login']) ?></strong></div>
            </div>
            
            <div style="display: flex; gap: 1rem; align-items: center; margin-top: 2rem;">
                <button type="submit" class="button">
                    📤 Publier la question
                </button>
                <div style="font-size: 0.875rem; color: var(--gray-500);">
                    Votre question sera visible par tous les membres du forum.
                </div>
            </div>
        </form>
    </div>
    
    <div class="nav-actions">
        <a href="questions.php" class="button secondary">← Retour aux questions</a>
        <a href="index.php" class="button secondary">🏠 Accueil</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const titreInput = document.querySelector('input[name="titre"]');
    const contenuTextarea = document.querySelector('textarea[name="contenu"]');
    
    // Compteur pour le titre
    const titreCounter = document.createElement('div');
    titreCounter.style.fontSize = '0.875rem';
    titreCounter.style.color = 'var(--gray-500)';
    titreCounter.style.marginTop = '0.5rem';
    titreCounter.style.textAlign = 'right';
    titreCounter.textContent = '0/50 caractères';
    titreInput.parentNode.appendChild(titreCounter);
    
    // Compteur pour le contenu
    const contenuCounter = document.createElement('div');
    contenuCounter.style.fontSize = '0.875rem';
    contenuCounter.style.color = 'var(--gray-500)';
    contenuCounter.style.marginTop = '0.5rem';
    contenuCounter.style.textAlign = 'right';
    contenuCounter.textContent = '0/150 caractères';
    contenuTextarea.parentNode.appendChild(contenuCounter);
    
    titreInput.addEventListener('input', function() {
        const length = this.value.length;
        titreCounter.textContent = length + '/50 caractères';
        titreCounter.style.color = length > 45 ? 'var(--error)' : length > 35 ? 'var(--warning)' : 'var(--gray-500)';
    });
    
    contenuTextarea.addEventListener('input', function() {
        const length = this.value.length;
        contenuCounter.textContent = length + '/150 caractères';
        contenuCounter.style.color = length > 140 ? 'var(--error)' : length > 120 ? 'var(--warning)' : 'var(--gray-500)';
    });
});
</script>
</body>
</html>