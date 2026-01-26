<?php
/**
 * Reusable form input component
 * Parameters:
 * - $type: Input type (text, password, email, etc.) - default: text
 * - $id: Input ID and name
 * - $label: Label text
 * - $placeholder: Placeholder text (optional)
 * - $required: Whether field is required (optional, default: true)
 * - $aria_label: Aria label for accessibility (optional)
 */
$type = $type ?? 'text';
$id = $id ?? '';
$label = $label ?? '';
$placeholder = $placeholder ?? '';
$required = $required ?? true;
$aria_label = $aria_label ?? $label;
?>

<div class="input-group">
    <input 
        type="<?php echo htmlspecialchars($type); ?>" 
        id="<?php echo htmlspecialchars($id); ?>" 
        <?php if ($required): ?>required<?php endif; ?>
        <?php if (!empty($placeholder)): ?>placeholder="<?php echo htmlspecialchars($placeholder); ?>"<?php endif; ?>
        <?php if (!empty($aria_label)): ?>aria-label="<?php echo htmlspecialchars($aria_label); ?>"<?php endif; ?>
    >
    <label for="<?php echo htmlspecialchars($id); ?>"><?php echo htmlspecialchars($label); ?></label>
</div>
