<?php

namespace App\Controller;

use App\Entity\Skill;
use App\Entity\User;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Fos;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{

    /**
     * @Fos\Get("/user")
     */
    public function getUsersAction(Request $request)
    {
        $orm = $this->getDoctrine()->getManager();
        $results = $orm->getRepository("App:User")->findAll();
        $response = new JsonResponse();
        if (count($results) != 0) {
            $response->setStatusCode(200);
            $response->setContent(json_encode($results));
        }
        else {
            $response->setStatusCode(404);
            $response->setContent(json_encode(["message" => "Users not found"]));
        }
        return $response;
    }

    /**
     * @Fos\Post("/user")
     */
    public function PostUserAction(Request $request)
    {
        $orm = $this->getDoctrine()->getManager();
        $user_rep = $orm->getRepository('App:User');
        $response = new JsonResponse();
        $response->setStatusCode(200);
        $response->setContent(json_encode(["message" => "successful operation"]));

        $payload = json_decode($request->getContent());

        /** @var User $new_user */
        try {
            if (filter_var($payload->email, FILTER_VALIDATE_EMAIL)
            ) {
                if ($user_rep->findOneBy(['email' => $payload->email])) {
                    $response->setStatusCode(401);
                    $response->setContent(json_encode(["message" => "User already exist"]));
                    return $response;
                }
                $new_user = new User();
                $new_user->setId($payload->id);
                $new_user->setDescription($payload->description);
                $new_user->setEmail($payload->email);
                $new_user->setFirstName($payload->firstName);
                $new_user->setLastName($payload->lastName);
                $new_user->setSalaryClaims($payload->salaryClaims);
                $orm->persist($new_user);
                $orm->flush();
            }
            else {
                $response->setStatusCode(400);
                $response->setContent(json_encode(["message" => "Invalid data - email not formatted"]));
            }
        }
        catch (\Throwable $ex) {
            $response->setStatusCode(400);
            $response->setContent(json_encode(["message" => "Invalid data - ".$ex->getMessage()]));
        }

        return $response;
    }

    /**
     * @Fos\Get("/user/{id}")
     */
    public function getUserAction(Request $request, $id)
    {
        $response = new JsonResponse();
        $orm = $this->getDoctrine()->getManager();
        $results = $orm->getRepository("App:User")->findOneBy(['id' => $id]);
        if ($results) {
            $response->setStatusCode(200);
            $response->setContent(json_encode($results));
        }
        else {
            $response->setStatusCode(404);
            $response->setContent(json_encode(["message" => "Users not found"]));
        }
        return $response;
    }

    /**
     * @Fos\Put("/user/{id}")
     */
    public function PutUserAction(Request $request, $id)
    {
        $orm = $this->getDoctrine()->getManager();
        $user_rep = $orm->getRepository('App:User');
        $response = new JsonResponse();
        $response->setStatusCode(200);
        $response->setContent(json_encode(["message" => "successful operation"]));

        $payload = json_decode($request->getContent());

        /** @var User $user */
        $user = $orm->getRepository("App:User")->findOneBy(['id' => $id]);
        if (!$user) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(["message" => "Users not found"]));
            return $response;
        }
        try {
            if (property_exists($payload, "id")) {
                $user->setId($payload->id);
            }
            if (property_exists($payload, "description")) {
                $user->setDescription($payload->description);
            }
            if (property_exists($payload, "email")) {
                if ($user_rep->findOneBy(['email' => $payload->email]) && filter_var($payload->email, FILTER_VALIDATE_EMAIL))
                    throw new \Exception("Email Already exist");
                $user->setEmail($payload->email);
            }
            else {
                throw new \Exception("Email not formatted");
            }
            if (property_exists($payload, "firstName")) {
                $user->setFirstName($payload->firstName);
            }
            if (property_exists($payload, "lastName")) {
                $user->setLastName($payload->lastName);
            }
            if (property_exists($payload, "salaryClaims")) {
                $user->setSalaryClaims($payload->salaryClaims);
            }
            $orm->persist($user);
            $orm->flush();
        }
        catch (\Throwable $ex) {
            $response->setStatusCode(400);
            $response->setContent(json_encode(["message" => "Invalid data supplied ".$ex->getMessage()]));
        }
        return $response;
    }

    /**
     * @Fos\Delete("/user/{id}")
     */
    public function DeleteUserAction(Request $request, $id) {
        $response = new JsonResponse();
        $response->setStatusCode(200);
        $response->setContent(json_encode(["message" => "User deleted"]));
        $orm = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $orm->getRepository('App:User')->findOneBy(['id' => $id]);
        if ($user) {
            $projects = $orm->getRepository("App:Project")->findBy(['user' => $user]);
            foreach ($projects as $item) {
                $orm->remove($item);
            }
            $skills = $orm->getRepository("App:Skill")->findBy(['user' => $user]);
            foreach ($skills as $item) {
                $orm->remove($item);
            }
            $orm->remove($user);
            $orm->flush();
        }
        else {
            $response->setStatusCode(404);
            $response->setContent(json_encode(["message" => "Users not found"]));
        }
        return $response;
    }

    /**
     * @Fos\Get("/user/{id}/skill")
     */
    public function getUserSkillsAction(Request $request, int $id) {
        $orm = $this->getDoctrine()->getManager();
        $response = new JsonResponse();
        $response->setStatusCode(200);
        /** @var User $user */
        $user = $orm->getRepository('App:User')->findOneBy(['id' => $id]);
        if ($user) {
            $skills = $orm->getRepository('App:Skill')->findBy(['user' => $user]);
            $response->setContent(json_encode($skills));
        }
        else {
            $response->setStatusCode(404);
            $response->setContent(json_encode(["message" => "Users not found"]));
        }

        return $response;
    }

    /**
     * @Fos\Get("/user/skill/{type}")
     */
    public function getUsersBySkillTypeAction(Request $request, $type) {
        $response = new JsonResponse();
        if (gettype($type) != "string") {
            $response->setStatusCode(400);
            $response->setContent(json_encode(["message" => "Invalid parameters supplied"]));
        }
        $orm = $this->getDoctrine()->getManager();
        $response->setStatusCode(200);
        $skillByTypes = $orm->getRepository('App:Skill')->findBy(['type' => $type]);
        $skillByName = $orm->getRepository('App:Skill')->findBy(['name' => $type]);
        $results = [];
        /** @var Skill $item */
        foreach ($skillByTypes as $item) {
            if (!in_array($item->getUser(), $results)) {
                $results[] = $item->getUser();
            }
        }
        foreach ($skillByName as $item) {
            if (!in_array($item->getUser(), $results)) {
                $results[] = $item->getUser();
            }
        }
        if (count($results) == 0) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(["message" => "Users not found"]));
        }
        else {
            $response->setContent(json_encode($results));
        }
        return $response;
    }

    /**
     * @Fos\Get("/user/skill/{type}/{note}")
     */
    public function getUsersBySkillTypeNdNoteAction(Request $request, $type, $note) {
        $response = new JsonResponse();
        if (gettype($type) != "string") {
            $response->setStatusCode(400);
            $response->setContent(json_encode(["message" => "Invalid parameters supplied"]));
        }
        $orm = $this->getDoctrine()->getManager();
        $response->setStatusCode(200);
        $skillByTypes = $orm->getRepository('App:Skill')->findBy(['type' => $type]);
        $skillByName = $orm->getRepository('App:Skill')->findBy(['name' => $type]);
        $results = [];
        /** @var Skill $item */
        foreach ($skillByTypes as $item) {
            if ($item->getNote() >= $note) {
                if (!in_array($item->getUser(), $results)) {
                    $results[] = $item->getUser();
                }
            }
        }
        foreach ($skillByName as $item) {
            if ($item->getNote() >= $note) {
                if (!in_array($item->getUser(), $results)) {
                    $results[] = $item->getUser();
                }
            }
        }
        if (count($results) == 0) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(["message" => "Users not found"]));
        }
        else {
            $response->setContent(json_encode($results));
        }
        return $response;
    }
}
