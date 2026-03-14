<?php
// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

include '_conf.php';
$bdd = mysqli_connect($serveurBDD, $userBDD, $mdpBDD, $nomBDD);

$question_id = $_GET['id'];

// Récupérer la question actuelle
$question_query = mysqli_query($bdd, 
    "SELECT q.*, u.login 
     FROM questions q 
     LEFT JOIN utilisateur u ON q.user_id = u.user_id 
     WHERE q.q_id = $question_id");
$question = mysqli_fetch_assoc($question_query);

// Vérifier que l'utilisateur peut modifier cette question
if ($_SESSION['user_id'] != $question['user_id']) {
    header('Location: detail.php?id=' . $question_id);
    exit;
}

if ($_POST) {
    $titre = mysqli_real_escape_string($bdd, $_POST['titre']);
    $contenu = mysqli_real_escape_string($bdd, $_POST['contenu']);
    
    $result = mysqli_query($bdd, "UPDATE questions SET q_titre = '$titre', q_contenu = '$contenu' WHERE q_id = $question_id");
    
    if ($result) {
        header('Location: detail.php?id=' . $question_id);
        exit;
    } else {
        $error = "Erreur lors de la modification: " . mysqli_error($bdd);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la question</title>
    <link rel="stylesheet" href="monstyle.css?v=12">
</head>
<body>
<div class="container">
    <h1>✏️ Modifier la question</h1>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>
    
    <div class="form-card">
        <form method="post">
            <div class="form-group">
                <label class="form-label">Titre de la question :</label>
                <input type="text" name="titre" class="form-input" placeholder="Titre de votre question" 
                       value="<?= htmlspecialchars($question['q_titre']) ?>" required maxlength="50">
                <div class="form-hint">Maximum 50 caractères</div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Détails de votre question :</label>
                <textarea name="contenu" class="form-textarea" placeholder="Décrivez votre problème ou posez votre question en détail..." 
                          required rows="8" maxlength="150"><?= htmlspecialchars($question['q_contenu']) ?></textarea>
                <div class="form-hint">Maximum 150 caractères</div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Auteur :</label>
                <input type="text" class="form-input" value="<?= htmlspecialchars($question['login']) ?>" disabled style="background: var(--gray-100);">
                <div class="form-hint">Question posée par <strong><?= htmlspecialchars($question['login']) ?></strong></div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Date de création :</label>
                <input type="text" class="form-input" value="<?= $question['q_date_ajout'] ?>" disabled style="background: var(--gray-100);">
            </div>
            
            <div style="display: flex; gap: 1rem; align-items: center; margin-top: 2rem;">
                <button type="submit" class="button">
                    💾 Enregistrer les modifications
                </button>
                <a href="detail.php?id=<?= $question_id ?>" class="button secondary" style="text-decoration: none;">
                    ❌ Annuler
                </a>
            </div>
        </form>
    </div>
    
    <div class="nav-actions">
        <a href="detail.php?id=<?= $question_id ?>" class="button secondary">← Retour à la question</a>
        <a href="questions.php" class="button secondary">📋 Toutes les questions</a>
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
    titreCounter.textContent = titreInput.value.length + '/50 caractères';
    titreInput.parentNode.appendChild(titreCounter);
    
    // Compteur pour le contenu
    const contenuCounter = document.createElement('div');
    contenuCounter.style.fontSize = '0.875rem';
    contenuCounter.style.color = 'var(--gray-500)';
    contenuCounter.style.marginTop = '0.5rem';
    contenuCounter.style.textAlign = 'right';
    contenuCounter.textContent = contenuTextarea.value.length + '/150 caractères';
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