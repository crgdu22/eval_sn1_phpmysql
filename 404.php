<?php
http_response_code(404);
include 'header.php';
?>

<div class="error-page">
    <p class="code">404</p>
    <h1>Cet événement n'existe pas</h1>
    <p class="muted">La page que vous cherchez a été supprimée ou n'a jamais existé.</p>
    <a class="btn" href="index.php">Retour aux événements </a>
</div>

<?php
include 'footer.php';
?>