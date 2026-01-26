<?php
/**
 * Reusable header component
 * Parameters:
 * - $title: Page title (required)
 * - $subtitle: Page subtitle (optional)
 * - $show_nav: Whether to show quick-nav (optional, default false)
 */
$title = $title ?? 'Biblioteca Digitale';
$subtitle = $subtitle ?? '';
$show_nav = $show_nav ?? false;
$nav_items = $nav_items ?? [];
?>

<header class="navbar">
    <div class="logo-area">
        <div class="logo-placeholder" aria-hidden="true">LOGO 1</div>
        <div class="logo-placeholder" aria-hidden="true">LOGO 2</div>
    </div>
    <div class="container">
        <h1><?php echo htmlspecialchars($title); ?></h1>
        <?php if ($subtitle): ?>
            <p><?php echo htmlspecialchars($subtitle); ?></p>
        <?php endif; ?>
    </div>
</header>

<?php if ($show_nav && !empty($nav_items)): ?>
<nav class="quick-nav" aria-label="Navigazione rapida">
    <?php foreach ($nav_items as $link => $label): ?>
        <a href="<?php echo htmlspecialchars($link); ?>" class="btn-nav"><?php echo htmlspecialchars($label); ?></a>
    <?php endforeach; ?>
</nav>
<?php endif; ?>
