<?php
/**
 * Reusable form input component
 *
 * @var string|null $type
 * @var string $id
 * @var string $label
 * @var string|null $placeholder
 * @var bool|null $required
 * @var string|null $aria_label
 */

$type = isset($type) ? $type : 'text';
$placeholder = isset($placeholder) ? $placeholder : null;
$required = isset($required) ? $required : true;
$aria_label = isset($aria_label) ? $aria_label : $label;
?>

<div class="input-group">
    <input
        type="<?= htmlspecialchars($type) ?>"
        id="<?= htmlspecialchars($id) ?>"
        name="<?= htmlspecialchars($id) ?>"
        <?= $required === true ? 'required' : '' ?>
        <?= $placeholder !== null && $placeholder !== '' ? 'placeholder="' . htmlspecialchars($placeholder) . '"' : '' ?>
        <?= $aria_label !== null && $aria_label !== '' ? 'aria-label="' . htmlspecialchars($aria_label) . '"' : '' ?>
    >
    <label for="<?= htmlspecialchars($id) ?>">
        <?= htmlspecialchars($label) ?>
    </label>
</div>
