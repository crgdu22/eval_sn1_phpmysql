<?php
require_once 'config.php';

// Vérification du rôle admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

include 'header.php';
?>

<div class="narrow">
    <h1>Liste des inscrits</h1>
    <p class="muted">Gestion des participants par événement.</p>

    <?php
    // Requêtepour récupérer les événements validé
    $stmt = $pdo->query('SELECT * FROM event WHERE is_validated = 1');
    $events = $stmt->fetchAll();

    if (empty($events)) {
        echo '<p class="empty">Aucun événement.</p>';
    } else {
        foreach ($events as $event) {
            // Récupérer les inscrits pour chaque événement
             $stmt2 = $pdo->prepare('SELECT user.email FROM reservation JOIN user ON user.id = reservation.user_id WHERE reservation.event_id = ?');
            $stmt2->execute([$event['id']]);
            $inscrits = $stmt2->fetchAll();
            $nb = count($inscrits);
            ?>

            <div class="card" style="margin-bottom: 20px;">
                <h3><?php echo htmlspecialchars($event['title']); ?> (<?php echo $nb; ?> / <?php echo $event['places']; ?> places)</h3>
                
                <?php if ($nb >= $event['places']): ?>
                    <span class="badge badge-full">Complet</span>
                <?php endif; ?>

                <ul>
                    <?php if (empty($inscrits)): ?>
                        <li>Aucun inscrit pour le moment.</li>
                    <?php else: ?>
                        <?php foreach ($inscrits as $inscrit): ?>
                            <li><?php echo htmlspecialchars($inscrit['email']); ?></li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <?php
        }
    }
    ?>
</div>

<?php
include 'footer.php';
?>