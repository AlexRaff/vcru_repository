<?php

namespace App\Services;

class ViewService
{
    /**
     * @param string $view
     * @param array $params
     * @return string
     */
    public static function render(string $view, array $params = []): string
    {
        extract($params);

        ob_start();
        require __DIR__ . '/../../views/' . $view . '.php';
        $content = ob_get_clean();

        ob_start();
        require __DIR__ . '/../../views/layout.php';
        return ob_get_clean();
    }
}