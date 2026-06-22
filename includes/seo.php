<?php
$pageTitle = $pageTitle ?? SITE_NAME;
$pageDescription = $pageDescription ?? "LOVA DUSK is a luxury drop-based fashion ecommerce brand.";
?>
<title><?= clean($pageTitle); ?></title>
<meta name="description" content="<?= clean($pageDescription); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
