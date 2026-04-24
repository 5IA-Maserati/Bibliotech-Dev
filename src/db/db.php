<?php

require_once __DIR__ . '/../backend/libs/DatabasePDO.php';

use src\backend\libs\DatabasePDO;

$host = getenv('DB_HOST') ?: 'localhost';
$db   = getenv('DB_NAME') ?: 'bibliotech';
$user = getenv('DB_USER') ?: 'bibliotech';
$pass = getenv('DB_PASS') ?: '';
$charset = getenv('DB_CHARSET') ?: 'utf8mb4';

if ($pass === false || $pass === '') {
    die("Errore di configurazione: variabile d'ambiente DB_PASS non impostata.");
}

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $database = new DatabasePDO($dsn, $user, $pass);
} catch (Exception $e) {
    die("Errore di connessione: " . $e->getMessage());
}

$filename = 'Libri_Lista_FINALE.csv';

if (($handle = fopen($filename, "r")) !== false) {
    fgetcsv($handle, 1000, ","); // Skip header

    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
        $inventory      = trim($data[0]);
        $author         = trim($data[1]);
        $title          = trim($data[2]);
        $publisher      = trim($data[3]);
        $year           = trim($data[4]);
        $isbn           = trim($data[5]);
        $genres_string  = $data[6];

        try {
            if ($year === "-") {
                $year = null;
            }

            // --- CHECK IF THE BOOK EXISTS ---
            $book = $database->queryOne(
                "SELECT id FROM books WHERE (isbn <> '' AND isbn = ?) OR (title = ? AND author = ?)",
                [$isbn, $title, $author]
            );

            if ($book) {
                $bookId = $book['id'];
            } else {
                // --- INSERT BOOK ---
                $database->execute(
                    "INSERT INTO books (author, title, publisher, publication_year, isbn)
                     VALUES (?, ?, ?, ?, ?)",
                    [$author, $title, $publisher, $year, $isbn]
                );

                $bookId = $database->lastInsertId();

                // --- INSERT GENRES ---
                $genresArray = explode(',', $genres_string);

                foreach ($genresArray as $genreName) {
                    $genreName = trim($genreName);
                    if ($genreName === '') {
                        continue;
                    }

                    $database->execute(
                        "INSERT IGNORE INTO categories (name) VALUES (?)",
                        [$genreName]
                    );

                    $genreId = $database->queryOne(
                        "SELECT id FROM categories WHERE name = ?",
                        [$genreName]
                    )['id'] ?? null;

                    if ($genreId) {
                        $database->execute(
                            "INSERT IGNORE INTO books_categories (book_id, category_id) VALUES (?, ?)",
                            [$bookId, $genreId]
                        );
                    }
                }
            }

            // --- PHYSIC COPIES ---
            $database->execute(
                "INSERT IGNORE INTO copies (book_id, inventory_number) VALUES (?, ?)",
                [$bookId, $inventory]
            );
        } catch (Exception $e) {
            echo "Errore con inventario $inventory ($title): " . $e->getMessage() . "<br>";
        }
    }

    fclose($handle);
    echo "Importazione completata con successo!";
} else {
    echo "Impossibile aprire il file CSV.";
}

return $database;
