<?php
/**
 * Reusable form input component
 *
 * @param string $id The ID of the input
 * @param string $label The label text
 * @param string $type The input type (default: 'text')
 * @param string|null $placeholder Optional placeholder text
 * @param bool $required Whether the input is required (default: true)
 * @param string|null $aria_label Optional ARIA label (defaults to $label)
 */

declare(strict_types=1);

// Ensure required variables exist
$id = $id ??= '';
$label = $label ??= '';
$type = $type ??= 'text';
$placeholder = $placeholder ??= '';
$required = $required ??= true;
$aria_label = $aria_label ??= $label;

?>

<div class="input-group">
    <input
        type="<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>"
        id="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>"
        name="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>"
        <?= $required ? 'required' : '' ?>
        <?= $placeholder !== '' ? 'placeholder="' . htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8') . '"' : '' ?>
        <?= $aria_label !== '' ? 'aria-label="' . htmlspecialchars($aria_label, ENT_QUOTES, 'UTF-8') . '"' : '' ?>
    >
    <label for="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">
        <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
    </label>
</div>
