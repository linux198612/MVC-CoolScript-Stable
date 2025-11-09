<?php
namespace System\Core;

class Render
{
    protected function render(string $view, array $data = [])
    {
        extract($data);

        // --- View path ---
        $viewPath = __DIR__ . '/../../App/Views/' . $view . '.php';

        // --- Ha nincs ilyen view, fallback 404-re ---
        if (!file_exists($viewPath)) {
            ob_start();
            require __DIR__ . '/../../App/Views/404.php';
            $content = ob_get_clean();
            return $content;
        }

        // --- View tartalom összegyűjtése ---
        ob_start();
        require $viewPath;
        $viewContent = ob_get_clean();

        // --- Subdir meghatározása (pl. user/dashboard -> user) ---
        $parts = explode('/', $view);
        $subdir = count($parts) > 1 ? $parts[0] : null;

        // --- Lehetséges template/layout fájlok ---
        $templateFiles = [];
        if ($subdir) {
            $templateFiles[] = __DIR__ . "/../../App/Views/$subdir/template.php";
            $templateFiles[] = __DIR__ . "/../../App/Views/$subdir/layout.php";
        }
        $templateFiles[] = __DIR__ . "/../../App/Views/template.php";
        $templateFiles[] = __DIR__ . "/../../App/Views/layout.php";

        // --- Megfelelő template/layout keresése ---
        $templatePath = null;
        foreach ($templateFiles as $tpl) {
            if (file_exists($tpl)) {
                $templatePath = $tpl;
                break;
            }
        }

        // --- Ha van template/layout, abba ágyazzuk a view-t ---
        if ($templatePath) {
            // $viewContent elérhető lesz a template-ben
            ob_start();
            include $templatePath;
            return ob_get_clean();
        }

        // --- Ha nincs template/layout, csak a view-t jelenítjük meg ---
        return $viewContent;
    }
}