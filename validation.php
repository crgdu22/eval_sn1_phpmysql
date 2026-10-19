<?php
require_once 'config.php';

// Vérification du rôle admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Validation d'un événement
if (isset($_POST['id'])) {
    $stmt = $pdo->prepare('UPDATE event SET is_validated = 1 WHERE id = ?');
    $stmt->execute([$_POST['id']]);
    header('Location: validation.php');
    exit;
}

// Récupérer les événements non validés
$stmt = $pdo->query('SELECT * FROM event WHERE is_validated = 0');
$events = $stmt->fetchAll();

include 'header.php';
?>

<div class="narrow">
    <h1>Validation des événements</h1>
    <p class="muted">Liste des événements en attente.</p>

    <?php if (empty($events)): ?>
        <p class="empty">Aucun événement en attente de validation.</p>
    <?php else: ?>
        <?php foreach ($events as $event): ?>
            <div class="card" style="margin-bottom: 20px;">
                <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                <p class="muted"><?php echo htmlspecialchars($event['date']); ?> · <?php echo htmlspecialchars($event['category']); ?></p>
                <p><?php echo nl2br(htmlspecialchars($event['information'])); ?></p>

                <form method="post">
                     <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
                    <button type="submit" class="btn btn-success">Valider</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
include 'footer.php';
?>