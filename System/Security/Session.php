<?php
namespace System\Security;

use System\Database\Database;

class Session
{
    public static function start()
    {
        $settings = require __DIR__ . '/../../App/Config/settings.php';
        $handler = $settings['session_handler'] ?? 'cookie';

        // --- Csak akkor állítsd az ini_set-et, ha még nincs session ---
        if (session_status() === PHP_SESSION_NONE) {
            // Optionally set a custom session save path if needed
            // ini_set('session.save_path', '/tmp'); // Uncomment and set to a writable directory if needed

            // Set session timeout and garbage collection settings
            $timeout = $settings['session_timeout'] ?? 1440;
            $gc_probability = $settings['session_gc_probability'] ?? 1;
            $gc_divisor = $settings['session_gc_divisor'] ?? 100;

            ini_set('session.gc_maxlifetime', $timeout);
            ini_set('session.gc_probability', $gc_probability);
            ini_set('session.gc_divisor', $gc_divisor);

            if ($handler === 'sql') {
                try {
                    $db = Database::getInstance();
                    // Create session table if not exists
                    $db->query("CREATE TABLE IF NOT EXISTS sessions (
                        id VARCHAR(128) PRIMARY KEY,
                        data TEXT NOT NULL,
                        timestamp INT NOT NULL
                    )");

                    // Custom session handler
                    session_set_save_handler(
                        function($savePath, $sessionName) { return true; },
                        function() { return true; },
                        function($id) use ($db) {
                            $result = $db->query("SELECT data FROM sessions WHERE id='" . $db->real_escape_string($id) . "'");
                            if ($row = $result->fetch_assoc()) return $row['data'];
                            return '';
                        },
                        function($id, $data) use ($db) {
                            $id = $db->real_escape_string($id);
                            $data = $db->real_escape_string($data);
                            $timestamp = time();
                            $db->query("REPLACE INTO sessions (id, data, timestamp) VALUES ('$id', '$data', $timestamp)");
                            return true;
                        },
                        function($id) use ($db) {
                            $id = $db->real_escape_string($id);
                            $db->query("DELETE FROM sessions WHERE id='$id'");
                            return true;
                        },
                        function($maxlifetime) use ($db) {
                            $db->query("DELETE FROM sessions WHERE timestamp < " . (time() - $maxlifetime));
                            return true;
                        }
                    );
                } catch (\Exception $e) {
                    // Database not available or misconfigured
                    self::showSessionError($e->getMessage());
                    exit;
                }
            }
            session_start();
        }
        // ...ha már fut a session, ne csinálj semmit...
    }

    private static function showSessionError($message)
    {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Session Error</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body { background: #f8fafc; }
                .centered {
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
            </style>
        </head>
        <body>
        <div class="centered">
            <div class="card shadow p-4" style="max-width: 400px;">
                <h3 class="mb-3 text-danger">Session Error</h3>
                <p class="mb-2">Session handler is set to <b>sql</b>, but the database is not available or misconfigured.</p>
                <p class="mb-2 text-danger"><?= htmlspecialchars($message) ?></p>
                <hr>
                <p class="text-muted small mb-0">Check your <b>database.php</b> configuration and ensure the database is accessible.</p>
            </div>
        </div>
        </body>
        </html>
        <?php
    }
}