<?php

namespace src\backend\libs;

class DatabaseSchema
{
    public static function ensureFavoritesTableExists(DatabasePDO $database): void
    {
        try {
            $database->queryOne('SELECT 1 FROM favorites LIMIT 1');
            return;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if (strpos($message, '1146') === false
                && stripos($message, 'doesn\'t exist') === false
                && stripos($message, 'no such table') === false) {
                throw $e;
            }

            $database->execute(
                'CREATE TABLE IF NOT EXISTS favorites (
                    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    user_id INT UNSIGNED NOT NULL,
                    book_id INT UNSIGNED NOT NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY unique_user_book (user_id, book_id),
                    KEY idx_favorites_user_id (user_id),
                    KEY idx_favorites_book_id (book_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
            );
        }
    }
}
