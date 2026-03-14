<?php
// Activer l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Définir $error à null par défaut
$error = null;

if ($_POST) {
    include '_conf.php';
    $bdd = mysqli_connect($serveurBDD, $userBDD, $mdpBDD, $nomBDD);
    
    // Vérifier la connexion à la BDD
    if (!$bdd) {
        $error = "Erreur de connexion à la base de données: " . mysqli_connect_error();
    } else {
        $login = mysqli_real_escape_string($bdd, $_POST['login']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $date_naissance = $_POST['date_naissance'];
        
        // Validation
        if (empty($login) || empty($password) || empty($date_naissance)) {
            $error = "Tous les champs sont obligatoires";
        } elseif ($password !== $confirm_password) {
            $error = "Les mots de passe ne correspondent pas";
        } elseif (strlen($password) < 6) {
            $error = "Le mot de passe doit contenir au moins 6 caractères";
        } else {
            // Vérifier si l'utilisateur existe déjà
            $check_user = mysqli_query($bdd, "SELECT user_id FROM utilisateur WHERE login = '$login'");
            if (!$check_user) {
                $error = "Erreur de requête: " . mysqli_error($bdd);
            } elseif (mysqli_num_rows($check_user) > 0) {
                $error = "Ce nom d'utilisateur est déjà pris";
            } else {
                // Trouver le prochain ID disponible
                $max_id_result = mysqli_query($bdd, "SELECT COALESCE(MAX(user_id), 0) as max_id FROM utilisateur");
                if (!$max_id_result) {
                    $error = "Erreur lors de la recherche de l'ID: " . mysqli_error($bdd);
                } else {
                    $max_id_row = mysqli_fetch_assoc($max_id_result);
                    $next_id = $max_id_row['max_id'] + 1;
                    
                    // Hasher le mot de passe en MD5
                    $hashed_password = md5($password);
                    
                    // DEBUG: Afficher les valeurs
                    echo "<!-- DEBUG: ID: $next_id, Login: $login, Date: $date_naissance -->";
                    
                    // Insérer le nouvel utilisateur avec l'ID manuel
                    $result = mysqli_query($bdd, "INSERT INTO utilisateur (user_id, login, password, date_naissance) 
                                       VALUES ($next_id, '$login', '$hashed_password', '$date_naissance')");
                    
                    if ($result) {
                        $_SESSION['success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                        header('Location: connexion.php');
                        exit;
                    } else {
                        $error = "Erreur lors de l'inscription: " . mysqli_error($bdd);
                        
                        // Essayer sans spécifier l'ID (au cas où AUTO_INCREMENT fonctionne)
                        $result2 = mysqli_query($bdd, "INSERT INTO utilisateur (login, password, date_naissance) 
                                           VALUES ('$login', '$hashed_password', '$date_naissance')");
                        if ($result2) {
                            $_SESSION['success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                            header('Location: connexion.php');
                            exit;
                        } else {
                            $error .= "<br>Deuxième tentative échouée: " . mysqli_error($bdd);
                        }
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Forum</title>
    <link rel="stylesheet" href="monstyle.css?v=12">
</head>
<body>
<div class="container">
    <div class="hero">
        <h1>Inscription</h1>
        <p>Rejoignez notre communauté de discussion</p>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-error">
            <strong>Erreur :</strong><br>
            <?= $error ?>
        </div>
    <?php endif; ?>
    
    <div class="form-card">
        <form method="post" id="formInscription">
            <div class="form-group">
                <label class="form-label">Nom d'utilisateur :</label>
                <input type="text" name="login" class="form-input" placeholder="Choisissez un nom d'utilisateur" 
                       value="<?= isset($_POST['login']) ? htmlspecialchars($_POST['login']) : '' ?>" required
                       minlength="3" maxlength="50">
                <div class="form-hint">3 à 50 caractères</div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Date de naissance :</label>
                <input type="date" name="date_naissance" class="form-input" 
                       value="<?= isset($_POST['date_naissance']) ? $_POST['date_naissance'] : '' ?>" required
                       max="<?= date('Y-m-d', strtotime('-13 years')) ?>">
                <div class="form-hint">Vous devez avoir au moins 13 ans</div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Mot de passe :</label>
                <input type="password" name="password" class="form-input" placeholder="Au moins 6 caractères" required
                       minlength="6" id="password">
                <div class="form-hint">Minimum 6 caractères</div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Confirmer le mot de passe :</label>
                <input type="password" name="confirm_password" class="form-input" placeholder="Répétez votre mot de passe" required
                       minlength="6" id="confirm_password">
                <div class="form-hint" id="passwordMatch"></div>
            </div>
            
            <button type="submit" class="button" id="submitBtn">📝 S'inscrire</button>
        </form>
    </div>
    
    <div class="nav-actions">
        <a href="connexion.php" class="button secondary">🔐 Déjà un compte ? Se connecter</a>
        <a href="index.php" class="button secondary">🏠 Accueil</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const passwordMatch = document.getElementById('passwordMatch');
    const submitBtn = document.getElementById('submitBtn');
    
    function checkPasswordMatch() {
        if (password.value && confirmPassword.value) {
            if (password.value === confirmPassword.value) {
                passwordMatch.innerHTML = '✅ Les mots de passe correspondent';
                passwordMatch.style.color = 'var(--success)';
                submitBtn.disabled = false;
            } else {
                passwordMatch.innerHTML = '❌ Les mots de passe ne correspondent pas';
                passwordMatch.style.color = 'var(--error)';
                submitBtn.disabled = true;
            }
        } else {
            passwordMatch.innerHTML = '';
            submitBtn.disabled = false;
        }
    }
    
    password.addEventListener('input', checkPasswordMatch);
    confirmPassword.addEventListener('input', checkPasswordMatch);
    
    // Validation côté client
    document.getElementById('formInscription').addEventListener('submit', function(e) {
        const login = document.querySelector('input[name="login"]').value.trim();
        const dateNaissance = document.querySelector('input[name="date_naissance"]').value;
        const today = new Date();
        const birthDate = new Date(dateNaissance);
        const age = today.getFullYear() - birthDate.getFullYear();
        
        if (login.length < 3) {
            e.preventDefault();
            alert('Le nom d\'utilisateur doit contenir au moins 3 caractères');
            return false;
        }
        
        if (age < 13) {
            e.preventDefault();
            alert('Vous devez avoir au moins 13 ans pour vous inscrire');
            return false;
        }
        
        if (password.value.length < 6) {
            e.preventDefault();
            alert('Le mot de passe doit contenir au moins 6 caractères');
            return false;
        }
        
        if (password.value !== confirmPassword.value) {
            e.preventDefault();
            alert('Les mots de passe ne correspondent pas');
            return false;
        }
    });
});
</script>

<style>
.form-hint {
    font-size: 0.875rem;
    color: var(--gray-500);
    margin-top: 0.25rem;
    font-style: italic;
}
</style>
</body>
</html>