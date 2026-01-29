<?php
/**
 * Reusable header component
 *
 * @var string $title
 * @var string|null $subtitle
 * @var bool $show_nav
 * @var array<string, string> $nav_items
 */

$title     = $title ?? 'Biblioteca Digitale';
$subtitle  = $subtitle ?? null;
$show_nav  = $show_nav ?? false;
$nav_items = $nav_items ?? [];
?>

<header class="navbar">
    <div class="logo-area">
        <div class="logo-placeholder" aria-hidden="true">LOGO 1</div>
        <div class="logo-placeholder" aria-hidden="true">LOGO 2</div>
    </div>

    <div class="container">
        <h1><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>

        <?php if ($subtitle !== null && $subtitle !== ''): ?>
            <p><?= htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>
</header>

<?php if ($show_nav && $nav_items !== []): ?>
<nav class="quick-nav" aria-label="Navigazione rapida">
    <?php foreach ($nav_items as $link => $label): ?>
        <a href="<?= htmlspecialchars($link, ENT_QUOTES, 'UTF-8') ?>" class="btn-nav">
            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
        </a>
    <?php endforeach; ?>
</nav>
<?php endif; ?>
