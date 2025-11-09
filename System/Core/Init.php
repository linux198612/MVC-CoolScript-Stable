<?php
namespace System\Core;

if (version_compare(PHP_VERSION, '8.2.0', '<')) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Coolscript Framework - PHP Version Error</title>
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
            <h3 class="mb-3 text-danger">PHP Version Error</h3>
            <p class="mb-2">Coolscript Framework requires at least <b>PHP 8.2</b>.</p>
            <p class="mb-0">Your current PHP version: <b><?= htmlspecialchars(PHP_VERSION) ?></b></p>
            <hr>
            <p class="text-muted small mb-0">Please upgrade your PHP version to use this application.</p>
        </div>
    </div>
    </body>
    </html>
    <?php
    exit;
}