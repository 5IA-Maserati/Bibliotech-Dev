<?php
// 1. Connessione al database (Dati aggiornati)
$host = 'localhost';
$db   = 'bibliotech';
$user = 'bibliotech';
$pass = 'x325rqweT2dftg'; 
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
     $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
     die("Errore di connessione: " . $e->getMessage());
}

$filename = 'Libri_Lista_FINALE.csv';
if (($handle = fopen($filename, "r")) !== FALSE) {
    
    fgetcsv($handle, 1000, ","); // Salta intestazione

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

            // --- FASE 1: CONTROLLO SE IL LIBRO ESISTE GIÀ ---
            // Cerchiamo per ISBN (se c'è) o per Titolo + Autore
            $stmt = $pdo->prepare("SELECT id FROM books WHERE (isbn <> '' AND isbn = ?) OR (title = ? AND author = ?)");
            $stmt->execute([$isbn, $author, $title]);
            $book = $stmt->fetch();

            if ($book) {
                // Il libro esiste già, prendiamo il suo ID
                $bookId = $book['id'];
            } else {
                // Il libro NON esiste, lo inseriamo
                $sqlBook = "INSERT INTO books (author, title, publisher, publication_year, isbn) 
                            VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sqlBook);
                $stmt->execute([$author, $title, $publisher, $year, $isbn]);
                $bookId = $pdo->lastInsertId();

                // Gestione dei generi (solo se il libro è nuovo per non duplicare i legami)
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

            // --- FASE 2: INSERIMENTO DELLA COPIA FISICA ---
            // Inseriamo il numero d'inventario collegandolo all'ID del libro (nuovo o esistente)
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