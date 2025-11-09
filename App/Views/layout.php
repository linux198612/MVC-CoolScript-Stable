<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= isset($title) ? $title : 'Coolscript MVC Framework' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">Coolscript MVC</a>
        </div>
    </nav>
    <?= isset($viewContent) ? $viewContent : '' ?>
    <footer class="bg-light text-center py-3 mt-5">
        <small>&copy; <?= date('Y') ?> Coolscript MVC Framework</small>
    </footer>
</body>
</html>
