<?php if (isset($_SESSION['user'])) : ?>
    <a href="/auth/logout.php" class="btn-auth-text">Logout</a>
<?php else : ?>
    <a href="/pages/login.php" class="btn-auth-text">Accedi all'area riservata</a>
<?php endif; ?>
