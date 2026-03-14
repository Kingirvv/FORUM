<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

if ($_POST) {
    include '_conf.php';
    $bdd = mysqli_connect($serveurBDD, $userBDD, $mdpBDD, $nomBDD);
    
    $question_id = $_POST['question_id'];
    $contenu = mysqli_real_escape_string($bdd, $_POST['contenu']);
    $user_id = $_SESSION['user_id'];
    
    // Vérifier que la question existe
    $question_check = mysqli_query($bdd, "SELECT q_id FROM questions WHERE q_id = $question_id");
    if (mysqli_num_rows($question_check) == 0) {
        $error = "Cette question n'existe pas";
    } else {
        // Insérer la réponse
        $result = mysqli_query($bdd, "INSERT INTO reponse (r_contenu, r_fk_question_id, user_id, r_date_ajout) 
                           VALUES ('$contenu', '$question_id', $user_id, NOW())");
        
        if ($result) {
            header("Location: detail.php?id=$question_id");
            exit;
        } else {
            $error = "Erreur lors de l'ajout de la réponse: " . mysqli_error($bdd);
        }
    }
}

$question_id = $_GET['question_id'];

// Récupérer les infos de la question pour l'affichage
include '_conf.php';
$bdd = mysqli_connect($serveurBDD, $userBDD, $mdpBDD, $nomBDD);
$question_query = mysqli_query($bdd, "SELECT q_titre FROM questions WHERE q_id = $question_id");
$question = mysqli_fetch_assoc($question_query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Répondre à la question</title>
    <link rel="stylesheet" href="monstyle.css?v=11">
</head>
<body>
<div class="container">
    <h1>💬 Répondre à la question</h1>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>
    
    <!-- Afficher la question -->
    <div class="card" style="margin-bottom: 2rem;">
        <h2>Question :</h2>
        <p style="font-size: 1.2rem; color: var(--darker); font-weight: 500;">
            <?= htmlspecialchars($question['q_titre']) ?>
        </p>
        <div class="meta">
            <strong>👤 <?= htmlspecialchars($_SESSION['login']) ?></strong>
            <span>📅 Vous répondez en tant que <?= htmlspecialchars($_SESSION['login']) ?></span>
        </div>
    </div>
    
    <div class="form-card">
        <form method="post">
            <input type="hidden" name="question_id" value="<?= $question_id ?>">
            
            <div class="form-group">
                <label class="form-label">Votre réponse :</label>
                <textarea name="contenu" class="form-textarea" placeholder="Partagez votre expertise, posez des questions complémentaires ou apportez votre aide..." required rows="8"></textarea>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-top: 0.5rem;">
                    💡 Conseil : Soyez clair et précis dans votre réponse pour aider au mieux la communauté.
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem; align-items: center;">
                <button type="submit" class="button">
                    📤 Publier la réponse
                </button>
                <div style="font-size: 0.875rem; color: var(--gray-500);">
                    Votre réponse sera visible par tous les membres du forum.
                </div>
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
// Ajouter un compteur de caractères
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.querySelector('textarea[name="contenu"]');
    const formGroup = textarea.closest('.form-group');
    
    // Créer le compteur
    const counter = document.createElement('div');
    counter.style.fontSize = '0.875rem';
    counter.style.color = 'var(--gray-500)';
    counter.style.marginTop = '0.5rem';
    counter.style.textAlign = 'right';
    counter.textContent = '0 caractères';
    
    formGroup.appendChild(counter);
    
    // Mettre à jour le compteur
    textarea.addEventListener('input', function() {
        const length = this.value.length;
        counter.textContent = length + ' caractères';
        
        if (length < 10) {
            counter.style.color = 'var(--error)';
        } else if (length < 50) {
            counter.style.color = 'var(--warning)';
        } else {
            counter.style.color = 'var(--success)';
        }
    });
});
</script>
</body>
</html>