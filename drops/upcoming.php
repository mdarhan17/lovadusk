<?php
$pageTitle = "Upcoming Drops | LOVA DUSK";
include "../includes/header.php";

$drops = $conn->query("SELECT * FROM drops WHERE status = 'upcoming' ORDER BY launch_date ASC");
?>

<section class="page-hero">
    <p class="eyebrow">Coming Soon</p>
    <h1>Upcoming Drops</h1>
    <p>Future LOVA DUSK collections.</p>
</section>

<section class="featured-section">
    <div class="drop-grid">
        <?php if ($drops && $drops->num_rows > 0): ?>
            <?php while ($drop = $drops->fetch_assoc()): ?>
                <a href="view-drop.php?id=<?= $drop["id"]; ?>" class="drop-card">
                    <p class="eyebrow">Upcoming</p>
                    <h2><?= clean($drop["title"]); ?></h2>
                    <p><?= clean($drop["description"] ?? ""); ?></p>
                    <span><?= clean($drop["launch_date"] ?? "Launch date coming soon"); ?></span>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No upcoming drops yet.</p>
        <?php endif; ?>
    </div>
</section>

<?php include "../includes/footer.php"; ?>