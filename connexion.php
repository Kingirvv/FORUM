<?php
session_start();
if ($_POST) {
    include '_conf.php';
    $bdd = mysqli_connect($serveurBDD, $userBDD, $mdpBDD, $nomBDD);
    
    $login = mysqli_real_escape_string($bdd, $_POST['login']);
    $password = $_POST['password'];
    
    if (empty($login) || empty($password)) {
        $error = "Tous les champs sont obligatoires";
    } else {
        $user_query = mysqli_query($bdd, "SELECT * FROM utilisateur WHERE login = '$login'");
        if (mysqli_num_rows($user_query) > 0) {
            $user = mysqli_fetch_assoc($user_query);
            
            // Utiliser MD5 au lieu de password_verify
            if (md5($password) === $user['password']) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['login'] = $user['login'];
                $_SESSION['date_naissance'] = $user['date_naissance'];
                
                header('Location: index.php');
                exit;
            } else {
                $error = "Mot de passe incorrect";
            }
        } else {
            $error = "Utilisateur non trouvé";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Forum</title>
    <link rel="stylesheet" href="monstyle.css?v=11">
</head>
<body>
<div class="container">
    <div class="hero">
        <h1>Connexion</h1>
        <p>Accédez à votre espace personnel</p>
    </div>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>
    
    <div class="form-card">
        <form method="post">
            <div class="form-group">
                <label class="form-label">Nom d'utilisateur :</label>
                <input type="text" name="login" class="form-input" placeholder="Votre nom d'utilisateur" 
                       value="<?= isset($_POST['login']) ? htmlspecialchars($_POST['login']) : '' ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Mot de passe :</label>
                <input type="password" name="password" class="form-input" placeholder="Votre mot de passe" required>
            </div>
            
            <button type="submit" class="button">🔐 Se connecter</button>
        </form>
    <div class="nav-actions">
        <a href="inscription.php" class="button secondary">📝 S'inscrire</a>
        <a href="index.php" class="button secondary">🏠 Accueil</a>
    </div>
</div>
</body>
</html>