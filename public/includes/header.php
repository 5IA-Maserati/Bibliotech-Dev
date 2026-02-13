<?php

/**
 * Reusable header component
 *
 * @psalm-var string|null $title
 * @psalm-var string|null $subtitle
 * @psalm-var bool|null $show_nav
 * @psalm-var array<string, string>|null $nav_items
 */

$title ??= 'Biblioteca Digitale';
assert(is_string($title), '$title must be a string');
$subtitle ??= null;
$show_nav ??= false;
$nav_items ??= [];
?>

<header class="navbar">
    <div class="logo-area">
        <div class="logo-placeholder" aria-hidden="true">LOGO 1</div>
        <div class="logo-placeholder" aria-hidden="true">LOGO 2</div>
    </div>

    <!-- Dynamic login/logout -->
    <div class="auth-area">
        <?php include __DIR__ . '/header.auth.php'; ?>
    </div>

    <div class="container">
        <h1><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>

        <?php if ($subtitle !== null && $subtitle !== '') : ?>
            <p><?= htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>
</header>

<?php if ($show_nav && $nav_items !== []) : ?>
<nav class="quick-nav" aria-label="Navigazione rapida">
    <?php foreach ($nav_items as $link => $label) : ?>
        <a href="<?= htmlspecialchars($link, ENT_QUOTES, 'UTF-8') ?>" class="btn-nav">
            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
        </a>
    <?php endforeach; ?>
</nav>
<?php endif; ?>
