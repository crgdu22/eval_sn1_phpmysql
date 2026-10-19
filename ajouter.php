<?php
require_once 'config.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $title = trim($_POST['title'] ?? '');
    $date = $_POST['date'] ?? '';
    $information = trim($_POST['information'] ?? '');
    $category = $_POST['category'] ?? '';
    $places = (int)($_POST['places'] ?? 0);

    $imagePath = null;
    
    // Gestion de l'upload d'image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
         $tmpName = $_FILES['image']['tmp_name'];
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $imagePath = 'images/' . $fileName;
        move_uploaded_file($tmpName, $imagePath);
    }

    if (empty($title) || empty($date) || empty($category) || empty($places)) {
        $erreur = "Tous les champs obligatoires doivent être remplis.";
    } else {
        $stmt = $pdo->prepare('INSERT INTO event (title, date, information, category, places, image, is_validated) VALUES (?, ?, ?, ?, ?, ?, 0)');
        $stmt->execute([$title, $date, $information, $category, $places, $imagePath]);

        $_SESSION['flash'] = "Événement ajouté avec succès. Il est en attente de validation.";
        header('Location: index.php');
        exit;
    }
}

include 'header.php';
?>

<div class="narrow">
    <div class="card">
        <h1>Ajouter un événement</h1>

        <?php if (!empty($erreur)): ?>
            <div class="flash flash-error"><?php echo htmlspecialchars($erreur); ?></div>
        <?php endif; ?>

        <form class="form" method="post" enctype="multipart/form-data">
            <div>
                <label for="title">Titre</label>
                <input type="text" id="title" name="title" required>
            </div>

            <div>
                <label for="date">Date et heure</label>
                <input type="datetime-local" id="date" name="date" required>
            </div>

            <div>
                <label for="category">Catégorie</label>
                <select id="category" name="category" required>
                    <option value="concert">Concert</option>
                    <option value="conference">Conférence</option>
                    <option value="atelier">Atelier</option>
                    <option value="sport">Sport</option>
                </select>
            </div>

            <div>
                <label for="places">Nombre de places</label>
                <input type="number" id="places" name="places" min="1" required>
            </div>

            <div>
                <label for="information">Description</label>
                <textarea id="information" name="information" rows="4"></textarea>
            </div>

            <div>
                <label for="image">Image</label>
                <input type="file" id="image" name="image">
            </div>

            <button type="submit" class="btn">Proposer l'événement</button>
        </form>
    </div>
</div>

<?php
include 'footer.php';
?>