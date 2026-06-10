<?php

namespace App\Core;

abstract class Controller
{
    public function __construct(protected ?View $view = null)
    {
    }

    protected function html(string $template, array $data = [], int $status = 200): Response
    {
        return new Response($this->view->render($template, $data), $status, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }

    protected function json(array $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }
}
