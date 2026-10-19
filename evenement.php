<?php
require_once 'config.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    include '404.php';
    exit;
}

// Récupération de l'événement
$stmt = $pdo->prepare('SELECT * FROM event WHERE id = ?');
$stmt->execute([$id]);
$event = $stmt->fetch();

if (!$event) {
    include '404.php';
    exit;
}

// Gestion de l'inscription / désinscription
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Vérifier si déjà inscrit
    $check = $pdo->prepare('SELECT * FROM reservation WHERE user_id = ? AND event_id = ?');
    $check->execute([$userId, $id]);
    $isRegistered = $check->fetch();

    if ($isRegistered) {
        // Se désinscrire
        $del = $pdo->prepare('DELETE FROM reservation WHERE user_id = ? AND event_id = ?');
        $del->execute([$userId, $id]);
    } else {
        // Compter les places prises
        $count = $pdo->prepare('SELECT COUNT(*) FROM reservation WHERE event_id = ?');
        $count->execute([$id]);
        $nb = $count->fetchColumn();

        if ($nb < $event['places']) {
            $ins = $pdo->prepare('INSERT INTO reservation (user_id, event_id) VALUES (?, ?)');
            $ins->execute([$userId, $id]);
        }
    }

    header('Location: evenement.php?id=' . $id);
    exit;
}

// Compter les réservations
$stmtCount = $pdo->prepare('SELECT COUNT(*) FROM reservation WHERE event_id = ?');
$stmtCount->execute([$id]);
$nbInscrits = $stmtCount->fetchColumn();

$placesRestantes = $event['places'] - $nbInscrits;

// Vérifier si l'utilisateur est inscrit
$userInscrit = false;
if (isset($_SESSION['user_id'])) {
    $checkUser = $pdo->prepare('SELECT * FROM reservation WHERE user_id = ? AND event_id = ?');
    $checkUser->execute([$_SESSION['user_id'], $id]);
    if ($checkUser->fetch()) {
        $userInscrit = true;
    }
}

include 'header.php';
?>

<a class="back-link" href="index.php">← Retour aux événements</a>

<article class="cat-<?php echo htmlspecialchars($event['category']); ?>">
    <header class="detail-hero" style="background-image: url('<?php echo htmlspecialchars($event['image'] ?? 'images/concert.jpg'); ?>')">
        <div class="detail-hero-content">
            <span class="badge"><?php echo htmlspecialchars($event['category']); ?></span>
            <h1><?php echo htmlspecialchars($event['title']); ?></h1>
            <p><?php echo htmlspecialchars($event['date']); ?></p>
        </div>
    </header>

    <div class="detail-grid">
        <section class="card">
            <h2>Informations</h2>
            <p><?php echo nl2br(htmlspecialchars($event['information'])); ?></p>
        </section>

        <aside class="card booking">
            <?php if ($placesRestantes <= 0): ?>
                <p class="places"><span class="badge badge-full">Complet</span></p>
                <button class="btn btn-block" disabled>Complet</button>
            <?php else: ?>
                <p class="places"><strong><?php echo $placesRestantes; ?></strong> places restantes sur <?php echo $event['places']; ?></p>

                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a class="btn btn-block" href="connexion.php">Connectez-vous pour vous inscrire</a>
                <?php elseif ($userInscrit): ?>
                    <p class="muted">Vous êtes déjà inscrit.</p>
                    <form method="post">
                        <button type="submit" class="btn btn-secondary btn-block">Se désinscrire</button>
                    </form>
                <?php else: ?>
                    <form method="post">
                        <button type="submit" class="btn btn-block">S'inscrire</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </aside>
    </div>
</article>

<?php
include 'footer.php';
?>