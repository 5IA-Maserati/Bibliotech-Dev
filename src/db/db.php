<?php

$host = getenv('DB_HOST') ?: 'localhost';
$db   = getenv('DB_NAME') ?: 'bibliotech';
$user = getenv('DB_USER') ?: 'bibliotech';
$pass = getenv('DB_PASS');
$charset = getenv('DB_CHARSET') ?: 'utf8mb4';

if ($pass === false || $pass === '') {
    die("Errore di configurazione: variabile d'ambiente DB_PASS non impostata.");
}

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
     $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
     die("Errore di connessione: " . $e->getMessage());
}

$filename = 'Libri_Lista_FINALE.csv';
if (($handle = fopen($filename, "r")) !== FALSE) {

    fgetcsv($handle, 1000, ","); // Skip header row

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

        $inventory      = trim($data[0]);
        $author         = trim($data[1]);
        $title          = trim($data[2]);
        $publisher      = trim($data[3]);
        $year           = trim($data[4]);
        $isbn           = trim($data[5]);
        $genres_string  = $data[6];

        try {

            if ($year == "-") {
                $year = null;
            }

            // --- PHASE 1: CHECK IF THE BOOK ALREADY EXISTS ---
            // Search by ISBN (if present) or by Title + Author
            $stmt = $pdo->prepare("SELECT id FROM books WHERE (isbn <> '' AND isbn = ?) OR (title = ? AND author = ?)");
            $stmt->execute([$isbn, $title, $author]);
            $book = $stmt->fetch();

            if ($book) {
                // The book already exists, retrieve its ID
                $bookId = $book['id'];
            } else {
                // The book does NOT exist, insert it
                $sqlBook = "INSERT INTO books (author, title, publisher, publication_year, isbn)
                            VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sqlBook);
                $stmt->execute([$author, $title, $publisher, $year, $isbn]);
                $bookId = $pdo->lastInsertId();

                // Genre handling (only for new books to avoid duplicate links)
                $genresArray = explode(',', $genres_string);
                foreach ($genresArray as $genreName) {
                    $genreName = trim($genreName);
                    if (empty($genreName)) continue;

                    $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
                    $stmt->execute([$genreName]);

                    $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
                    $stmt->execute([$genreName]);
                    $genreId = $stmt->fetchColumn();

                    $stmt = $pdo->prepare("INSERT IGNORE INTO books_categories (book_id, category_id) VALUES (?, ?)");
                    $stmt->execute([$bookId, $genreId]);
                }
            }

            // --- PHASE 2: INSERT PHYSICAL COPY ---
            // Insert the inventory number linked to the book ID (new or existing)
            $stmt = $pdo->prepare("INSERT IGNORE INTO copies (book_id, inventory_number) VALUES (?, ?)");
            $stmt->execute([$bookId, $inventory]);

        } catch (Exception $e) {
            echo "Errore con l'inventario $inventory ($title): " . $e->getMessage() . "<br>";
        }
    }
    fclose($handle);
    echo "Importazione completata con successo!";
} else {
    echo "Impossibile aprire il file CSV.";
}
?>