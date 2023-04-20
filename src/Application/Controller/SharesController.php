<?php

namespace App\Application\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SharesController extends AbstractController
{
    #[Route('/shares', name: 'app_application_shares_index')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Application/Controller/ShareController.php',
        ]);
    }
}
