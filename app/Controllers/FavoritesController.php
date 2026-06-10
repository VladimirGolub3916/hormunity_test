<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\FavoritesServiceInterface;
use InvalidArgumentException;

class FavoritesController extends Controller
{
    public function __construct(private FavoritesServiceInterface $favorites)
    {
        parent::__construct();
    }

    public function show(Request $request): Response
    {
        return $this->json(['favorites' => $this->favorites->state()]);
    }

    public function toggle(Request $request): Response
    {
        $productId = (string) $request->input('productId', '');

        try {
            $active = $this->favorites->toggle($productId);
        } catch (InvalidArgumentException $exception) {
            return $this->json(['error' => $exception->getMessage()], 422);
        }

        return $this->json([
            'active' => $active,
            'favorites' => $this->favorites->state(),
        ]);
    }
}
