<?php
// scripts/remove-php-strings.php
// Usage: php scripts/remove-php-strings.php

$patterns = ['public/**/*.php'];

/**
 * Globbing ricorsivo (supporta **)
 */
function glob_recursive(array $patterns) {
    $files = [];
    foreach ($patterns as $pattern) {
        // usa glob per pattern semplice; per ** richiede aggiunta di GLOB_BRACE/recursive handling
        // qui usiamo recursive iterator per essere sicuri
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator('.', RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($iterator as $fileinfo) {
            $path = str_replace('\\', '/', $fileinfo->getPathname());
            // semplissimo: verifica se path matcha il pattern convertendo ** in .*
            $regex = '#^' . str_replace(['**','*','.'], ['.*', '[^/]*','\.'], $pattern) . '$#';
            if (preg_match($regex, ltrim($path, './'))) {
                $files[] = $path;
            }
        }
    }
    // rimuove duplicati
    return array_values(array_unique($files));
}

$files = glob_recursive($patterns);
echo "Found " . count($files) . " PHP files.\n\n";

foreach ($files as $file) {
    $code = file_get_contents($file);
    // tokenizza anche se il file contiene HTML + PHP: token_get_all gestisce T_INLINE_HTML
    $tokens = token_get_all($code);
    $out = '';
    $removed = [];

    foreach ($tokens as $tok) {
        if (is_array($tok)) {
            $id = $tok[0];
            $text = $tok[1];

            // T_CONSTANT_ENCAPSED_STRING -> '...' or "..."
            if ($id === T_CONSTANT_ENCAPSED_STRING) {
                $removed[] = $text;
                // sostituisci con due virgolette dello stesso tipo
                $quote = $text[0]; // ' o "
                $out .= $quote . $quote;
            } else {
                // ricomponi il testo così com'è
                $out .= $text;
            }
        } else {
            // singolo char token (es. ;, {, } etc.)
            $out .= $tok;
        }
    }

    // scrivi solo se differente
    if ($out !== $code) {
        file_put_contents($file, $out);
    }

    echo "📄 Processed file: {$file}\n";
    if (count($removed) > 0) {
        echo "  ❌ Removed PHP strings (" . count($removed) . "):\n";
        foreach ($removed as $r) {
            echo "    {$r}\n";
        }
    } else {
        echo "  ✅ No PHP strings removed\n";
    }
    echo "\n";
}

echo "✔ All PHP files processed.\n";
