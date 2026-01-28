<?php
/**
 * Reusable form input component
 *
 * @var string $type The type of input (text, password, email, etc.)
 * @var string $id The ID and name of the input
 * @var string $label The label text
 * @var string|null $placeholder Optional placeholder text
 * @var bool|null $required Whether the input is required (default: true)
 * @var string $aria_label ARIA label (defaults to $label)
 */

declare(strict_types=1);

// Set default values if not provided
$type = $type ?? 'text';
$placeholder = $placeholder ?? null;
$required = $required ?? true;

// Ensure $aria_label is always a string, default to $label
$aria_label = $aria_label ?? $label;

?>

<div class="input-group">
    <input
        type="<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>"
        id="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>"
        name="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>"
        <?= $required ? 'required' : '' ?>
        <?= $placeholder !== null ? 'placeholder="' . htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8') . '"' : '' ?>
        aria-label="<?= htmlspecialchars($aria_label, ENT_QUOTES, 'UTF-8') ?>"
    >
    <label for="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">
        <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
    </label>
</div>
