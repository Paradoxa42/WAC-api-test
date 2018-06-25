<?php

namespace App\Controller;

use App\Entity\Skill;
use App\Entity\User;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Fos;
use Symfony\Component\HttpFoundation\JsonResponse;

class SkillController extends Controller
{
    /**
     * @Fos\Get("/skill/{id}")
     */
    public function getskillsAction(Request $request, $id)
    {
        $orm = $this->getDoctrine()->getManager();
        /** @var Skill $skill */
        $skill = $orm->getRepository("App:Skill")->find($id);
        $response = new JsonResponse();
        if ($skill) {
            $response->setStatusCode(200);
            $response->setContent(json_encode($skill));
        }
        else {
            $response->setStatusCode(404);
            $response->setContent(json_encode(["message" => "skill not found"]));
        }
        return $response;
    }

    /**
     * @Fos\Post("/skill")
     */
    public function PostskillAction(Request $request)
    {
        $orm = $this->getDoctrine()->getManager();
        $user_rep = $orm->getRepository('App:User');
        $response = new JsonResponse();
        $response->setStatusCode(200);
        $response->setContent(json_encode(["message" => "successful operation"]));

        $payload = json_decode($request->getContent());

        try {
            if (gettype($payload->id) == "integer"
                && gettype($payload->userId) == "integer"
                && gettype($payload->name) == "string"
                && gettype($payload->type) == "string"
                && gettype($payload->note) == "integer"
            ) {
                $new_skill = new skill();
                $new_skill->setId($payload->id);
                $new_skill->setName($payload->name);
                $new_skill->setType($payload->type);
                $new_skill->setNote($payload->note);
                $user = $user_rep->find($payload->userId);
                if (!$user) {
                    throw new \Exception("User Not Found");
                }
                $new_skill->setUser($user);
                $orm->persist($new_skill);
                $orm->flush();
            }
            else
                throw new \Exception("invalide Data Type");
        }
        catch (\Throwable $ex) {
            $response->setStatusCode(405);
            $response->setContent(json_encode(["message" => "Invalid Input - ".$ex->getMessage()]));
        }
        return $response;
    }

    /**
     * @Fos\Put("/skill/{id}")
     */
    public function PutskillAction(Request $request, $id)
    {
        $orm = $this->getDoctrine()->getManager();
        $user_rep = $orm->getRepository('App:User');
        $skill_rep = $orm->getRepository("App:skill");
        $response = new JsonResponse();
        $response->setStatusCode(200);
        $response->setContent(json_encode(["message" => "successful operation"]));
        $payload = json_decode($request->getContent());
        try {
            $skill = $skill_rep->find($id);
            if (!$skill) {
                $response->setStatusCode(404);
                $response->setContent(json_encode(["message" => "Skill not found"]));
                return $response;
            }

            if ($payload->id) {
                if (gettype($payload->id) == "integer" && $payload->id != $skill->getId()) {
                    if ($skill_rep->find($payload->id)) {
                        throw new \Exception("skill ID already exist");
                    }
                    $skill->setId($payload->id);
                }
            }

            if ($payload->userId) {
                if (gettype($payload->userId) == "integer" && $payload->userId != $skill->getUser()->getId()) {
                    $user = $user_rep->find($payload->userId);
                    if (!$user) {
                        throw new \Exception("User not found");
                    }
                    $skill->setUser($user);
                }
            }

            if ($payload->name) {
                if (gettype($payload->name) == "string") {
                    $skill->setName($payload->name);
                }
            }

            if ($payload->note) {
                if (gettype($payload->note) == "integer") {
                    $skill->setNote($payload->note);
                }
            }

            $orm->persist($skill);
            $orm->flush();
        }
        catch (\Throwable $ex) {
            $response->setStatusCode(405);
            $response->setContent(json_encode(["message" => "Invalid input - ".$ex->getMessage()]));
        }
        return $response;
    }

    /**
     * @Fos\Delete("/skill/{id}")
     */
    public function DeleteskillAction(Request $request, $id) {
        $response = new JsonResponse();
        $response->setStatusCode(200);
        $response->setContent(json_encode(["message" => "skill deleted"]));
        $orm = $this->getDoctrine()->getManager();
        /** @var skill $skill */
        $skill = $orm->getRepository('App:skill')->findOneBy(['id' => $id]);
        if ($skill) {
            $orm->remove($skill);
            $orm->flush();
        }
        else {
            $response->setStatusCode(404);
            $response->setContent(json_encode(["message" => "skill not found"]));
        }
        return $response;
    }
}
