<?php
/**
 * Reusable form input component
 *
 * @var string|null $type The type of input (text, password, email, etc.)
 * @var string $id The ID and name of the input
 * @var string $label The label text
 * @var string|null $placeholder Optional placeholder text
 * @var bool|null $required Whether the input is required (default: true)
 * @var string|null $aria_label Optional ARIA label (defaults to $label)
 */

declare(strict_types=1);

// Imposta valori di default solo se non definiti
$type = $type ?? 'text';
$placeholder = $placeholder ?? null;
$required = $required ?? true;
$aria_label = $aria_label ?? $label;

?>

<div class="input-group">
    <input
        type="<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>"
        id="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>"
        name="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>"
        <?= $required ? 'required' : '' ?>
        <?= $placeholder !== null ? 'placeholder="' . htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8') . '"' : '' ?>
        <?= $aria_label !== null ? 'aria-label="' . htmlspecialchars($aria_label, ENT_QUOTES, 'UTF-8') . '"' : '' ?>
    >
    <label for="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">
        <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
    </label>
</div>
