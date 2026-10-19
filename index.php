<?php
require_once 'config.php';

// On récupère tous les événements validés et à venir 
$stmt = $pdo->prepare('SELECT * FROM event WHERE is_validated = 1 AND date > NOW() ORDER BY date ASC');
$stmt->execute();
$events = $stmt->fetchAll();

$category = $_GET['categorie'] ?? '';

include 'header.php';
?>

<section class="hero" style="background-image: url('images/rock.jpg')">
    <div class="hero-content">
        <span class="hero-kicker">Agenda local</span>
        <h1>Les événements près de chez vous</h1>
        <p>Concerts, conférences, ateliers et sport : trouvez votre prochaine sortie ou proposez la vôtre.</p>
        <a class="btn" href="ajouter.php">+ Ajouter un événement</a>
    </div>
</section>

<div class="section-head">
    <h2>À venir</h2>
    <ul class="legend">
        <li><a class="<?php echo empty($category) ? 'active' : ''; ?>" href="index.php">Toutes</a></li>
        <li class="cat-concert"><a class="<?php echo $category === 'concert' ? 'active' : ''; ?>" href="index.php?categorie=concert">Concert</a></li>
        <li class="cat-conference"><a class="<?php echo $category === 'conference' ? 'active' : ''; ?>" href="index.php?categorie=conference">Conférence</a></li>
        <li class="cat-atelier"><a class="<?php echo $category === 'atelier' ? 'active' : ''; ?>" href="index.php?categorie=atelier">Atelier</a></li>
        <li class="cat-sport"><a class="<?php echo $category === 'sport' ? 'active' : ''; ?>" href="index.php?categorie=sport">Sport</a></li>
    </ul>
</div>

<?php 
// Petit if pour vérifier s'il y a des événements à afficher pour cette catégorie
$hasEvents = false;
foreach ($events as $event) {
    if (empty($category) || $event['category'] === $category) {
        $hasEvents = true;
        break;
    }
}
?>

<?php if (!$hasEvents): ?>
    <p class="empty">Aucun événement à venir pour le moment.</p>
<?php else: ?>
    <ul class="event-list">
        <?php foreach ($events as $event): ?>
            <?php 
            if (!empty($category) && $event['category'] !== $category) {
                continue;
            }

            $timestamp = strtotime($event['date']);
            $day = date('d', $timestamp);
            
            $months = ['janv.', 'févr.', 'mars', 'avr.', 'mai', 'juin', 'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.'];
            $month = $months[(int)date('n', $timestamp) - 1];
            
            $imagePath = !empty($event['image']) ? $event['image'] : 'images/concert.jpg';
            ?>
            <li class="event-card cat-<?php echo htmlspecialchars($event['category']); ?>">
                <div class="event-media">
                    <img class="event-image" src="<?php echo htmlspecialchars($imagePath); ?>" alt="">
                    <div class="event-date">
                        <span class="day"><?php echo $day; ?></span>
                        <span class="month"><?php echo $month; ?></span>
                    </div>
                    <span class="badge"><?php echo ucfirst(htmlspecialchars($event['category'])); ?></span>
                </div>
                <h3 class="event-title">
                    <a href="evenement.php?id=<?php echo $event['id']; ?>"><?php echo htmlspecialchars($event['title']); ?></a>
                </h3>
                <span class="muted"><?php echo date('d/m/Y · H\hi', $timestamp); ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php
include 'footer.php';
?>