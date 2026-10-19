<?php
require_once 'config.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $erreur = "Tous les champs sont obligatoires.";
    } else {
        $stmt = $pdo->prepare('SELECT * FROM user WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            $_SESSION['flash'] = "Connexion réussie !";
            header('Location: index.php');
            exit;
        } else {
            $erreur = "Identifiants invalides.";
        }
    }
}

include 'header.php';
?>

<div class="narrow">
    <div class="card">
        <h1>Connexion</h1>

        <?php if (!empty($erreur)): ?>
            <div class="flash flash-error"><?php echo htmlspecialchars($erreur); ?></div>
        <?php endif; ?>

        <form class="form" method="post">
            <div>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" autocomplete="email" required autofocus>
            </div>

            <div>
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" autocomplete="current-password" required>
            </div>

            <button type="submit">Se connecter</button>
        </form>

        <p class="form-footer">Pas encore de compte ? <a href="inscription.php">Créer un compte</a></p>
    </div>
</div>

<?php
include 'footer.php';
?>