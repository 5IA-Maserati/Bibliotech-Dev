<?php

/**
 * Reusable header component
 *
 * @psalm-var string|null $title
 * @psalm-var string|null $subtitle
 * @psalm-var bool|null $show_nav
 * @psalm-var bool|null $show_auth
 * @psalm-var array<string, string>|null $nav_items
 */

$title ??= 'Biblioteca Digitale';
assert(is_string($title), '$title must be a string');
$subtitle ??= null;
$show_nav ??= false;
$show_auth ??= true;
$nav_items ??= [];
?>

<header class="navbar">
    <div class="header-top">
        <div class="logo-area">
            <div class="logo-placeholder" aria-hidden="true">LOGO 1</div>
            <div class="logo-placeholder" aria-hidden="true">LOGO 2</div>
        </div>

        <!-- Dynamic login/logout -->
        <?php if ($show_auth) : ?>
        <div class="auth-area">
            <?php if (isset($_SESSION['user'])) : ?>
                <a href="/pages/profile.php" class="btn-auth-text">Profilo</a>
                <a href="/auth/logout.php" class="btn-auth-text">Logout</a>
            <?php else : ?>
                <a href="/pages/login.php" class="btn-auth-text">Accedi all'area riservata</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="header-center">
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
