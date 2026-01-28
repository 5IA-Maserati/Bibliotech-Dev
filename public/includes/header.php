<?php
/**
 * Reusable header component
 *
 * @var string|null $title
 * @var string|null $subtitle
 * @var bool|null $show_nav
 * @var array<string, string>|null $nav_items
 */

$title = isset($title) ? $title : 'Biblioteca Digitale';
$subtitle = isset($subtitle) ? $subtitle : null;
$show_nav = isset($show_nav) ? $show_nav : false;
$nav_items = isset($nav_items) ? $nav_items : [];
?>

<header class="navbar">
    <div class="logo-area">
        <div class="logo-placeholder" aria-hidden="true">LOGO 1</div>
        <div class="logo-placeholder" aria-hidden="true">LOGO 2</div>
    </div>

    <div class="container">
        <h1><?= htmlspecialchars($title) ?></h1>

        <?php if ($subtitle !== null && $subtitle !== ''): ?>
            <p><?= htmlspecialchars($subtitle) ?></p>
        <?php endif; ?>
    </div>
</header>

<?php if ($show_nav === true && $nav_items !== []): ?>
<nav class="quick-nav" aria-label="Navigazione rapida">
    <?php foreach ($nav_items as $link => $label): ?>
        <a href="<?= htmlspecialchars($link) ?>" class="btn-nav">
            <?= htmlspecialchars($label) ?>
        </a>
    <?php endforeach; ?>
</nav>
<?php endif; ?>
