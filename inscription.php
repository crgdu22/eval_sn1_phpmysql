<?php
require_once 'config.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $erreur = "Tous les champs sont obligatoires.";
    } elseif (strlen($password) < 6) {
        $erreur = "Le mot de passe doit faire au moins 6 caractères.";
    } else {
        // Vérification si l'email existe déjà 
        $stmt = $pdo->prepare('SELECT * FROM user WHERE email = :email');
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $erreur = "Cet email est déjà utilisé.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare('INSERT INTO user (email, password, role, created_at) VALUES (:email, :password, "user", NOW())');
            $stmt->execute([
                'email' => $email,
                'password' => $hash
            ]);

            $_SESSION['flash'] = "Votre compte a bien été créé. Vous pouvez vous connecter.";
            header('Location: connexion.php');
            exit;
        }
    }
}

include 'header.php';
?>

<div class="narrow">
    <div class="card">
        <h1>Créer un compte</h1>

        <?php if (!empty($erreur)): ?>
            <div class="flash flash-error"><?php echo htmlspecialchars($erreur); ?></div>
        <?php endif; ?>

        <form class="form" method="post">
            <div>
                <label for="email" class="required">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" autocomplete="email" required>
            </div>

            <div>
                <label for="password" class="required">Mot de passe</label>
                <input type="password" id="password" name="password" autocomplete="new-password" required>
                <div class="help-text">6 caractères minimum.</div>
            </div>

            <button type="submit">Créer mon compte</button>
        </form>

        <p class="form-footer">Déjà inscrit ? <a href="connexion.php">Se connecter</a></p>
    </div>
</div>

<?php
include 'footer.php';
?>