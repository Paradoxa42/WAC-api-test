<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Fos;
use Symfony\Component\HttpFoundation\JsonResponse;

class PingController extends Controller
{
    /**
     * Ping
     * @Fos\Get("/ping")
     *
     * @return array
     */
    public function pingAction(Request $request)
    {
        return new JSONResponse(['status' => 'ok']);
    }
}
