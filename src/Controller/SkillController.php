<?php

namespace App\Controller;

use App\Entity\Skill;
use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Fos;
use Symfony\Component\HttpFoundation\JsonResponse;

class SkillController extends Controller
{
    /**
     * @Fos\Get("skill/{id}")
     */
    public function getSkillsAction(Request $request, $id)
    {
        $orm = $this->getDoctrine()->getManager();

    }
}
