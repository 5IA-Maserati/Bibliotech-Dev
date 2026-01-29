<?php

/**
 *
 * Reusable form input component
 *
 * @var string|null $type        The type of input (text, password, email, etc.)
 * @var string       $id         The ID and name of the input
 * @var string       $label      The label text
 * @var string|null  $placeholder Optional placeholder text
 * @var bool|null    $required   Whether the input is required (default: true)
 * @var string|null  $aria_label ARIA label (defaults to $label)
 */

declare(strict_types=1);

// Set default values if not provided
$type        = $type ?? 'text';
$placeholder = $placeholder ?? null;
$required    = $required ?? true;

// $aria_label is nullable, defaults to $label
$aria_label = $aria_label ?? $label;


// Build attributes array
$attrs = [
    'type'       => htmlspecialchars($type, ENT_QUOTES, 'UTF-8'),
    'id'         => htmlspecialchars($id, ENT_QUOTES, 'UTF-8'),
    'name'       => htmlspecialchars($id, ENT_QUOTES, 'UTF-8'),
    'aria-label' => htmlspecialchars($aria_label, ENT_QUOTES, 'UTF-8'),
];

// Add conditional attributes
if ($required) {
    // Boolean attributes have no value
    $attrs['required'] = null;
}

if ($placeholder !== null) {
    $attrs['placeholder'] = htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8');
}

// Convert attributes array into HTML-safe string
$attrString = '';
foreach ($attrs as $key => $value) {
    $attrString .= $value === null
        ? " {$key}"
        : " {$key}=\"{$value}\"";
}
?>

<div class="input-group">
    <input<?= $attrString ?>>
    <label for="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">
        <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
    </label>
</div>
