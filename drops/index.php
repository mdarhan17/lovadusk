<?php
$pageTitle = "Drops | LOVA DUSK";
include "../includes/header.php";

$drops = $conn->query("SELECT * FROM drops ORDER BY id DESC");
?>

<section class="page-hero">
    <p class="eyebrow">Limited Collections</p>
    <h1>LOVA DUSK Drops</h1>
    <p>Editorial collections released in limited quantity.</p>
</section>

<section class="featured-section">
    <div class="drop-grid">
        <?php if ($drops->num_rows > 0): ?>
            <?php while ($drop = $drops->fetch_assoc()): ?>
                <a href="view-drop.php?id=<?= $drop["id"]; ?>" class="drop-card">
                    <p class="eyebrow"><?= clean($drop["status"]); ?></p>
                    <h2><?= clean($drop["title"]); ?></h2>
                    <p><?= clean($drop["description"] ?? "Explore this limited fashion collection."); ?></p>
                    <span>View Drop</span>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No drops added yet.</p>
        <?php endif; ?>
    </div>
</section>

<?php include "../includes/footer.php"; ?>