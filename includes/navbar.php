<header class="site-header">
    <a href="<?= BASE_URL; ?>" class="logo image-logo-link">
        <img src="<?= BASE_URL; ?>assets/images/logo/lovadusk.png" alt="Lova Dusk" class="navbar-logo-img">
    </a>

    <form method="GET" action="<?= BASE_URL; ?>search.php" class="header-search">
        <input type="text" name="q" placeholder="Search drops..." aria-label="Search products">
        <button type="submit">Search</button>
    </form>

    <nav class="nav-links">
        <a href="<?= BASE_URL; ?>shop.php">Shop</a>
        <a href="<?= BASE_URL; ?>drops/">Drops</a>
        <a href="<?= BASE_URL; ?>about.php">About</a>
        <a href="<?= BASE_URL; ?>contact.php">Contact</a>
        <a href="<?= BASE_URL; ?>cart/">Cart</a>

        <?php if (isLoggedIn()): ?>
            <a href="<?= BASE_URL; ?>user/dashboard.php">Account</a>
        <?php else: ?>
            <a href="<?= BASE_URL; ?>auth/login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>
