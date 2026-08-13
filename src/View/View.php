<?php

namespace App\View;

class View
{
    public function render(
        string $template,
        array $data = [],
        ?string $layout = 'layout'
    ): void {
        extract($data);

        ob_start();

        require __DIR__ . '/../../templates/' . $template . '.php';

        $content = ob_get_clean();

        if ($layout !== null) {
            require __DIR__ . '/../../templates/' . $layout . '.php';

            return;
        }

        echo $content;
    }
}
