<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Negotiation\Exception\InvalidArgument;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Fos;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

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
            $response->setContent($results);
        }
        else {
            $response->setStatusCode(404);
            $response->setContent("Users not found");
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
        $response->setContent("successful operation");
        if ($user_rep->findOneBy(['email' => $request->get('email')])) {
            $response->setStatusCode(401);
            $response->setContent("User already exist");
            return $response;
        }

/*        if (gettype($request->get("id")) == "integer"
            && gettype($request->get("salaryClaims")) == "integer"
            && gettype($request->get("description")) == "string"
            && gettype($request->get("email")) == "string"
            && gettype($request->get("firstName")) == "string"
            && gettype($request->get("lastName")) == "string"
        )*/


        /** @var User $new_user */
        $new_user = new User();
        try {
            $new_user->setId($request->get("id"));
            $new_user->setDescription($request->get("description"));
            $new_user->setEmail($request->get("email"));
            $new_user->setFirstName($request->get("firstName"));
            $new_user->setLastName($request->get("lastName"));
            $new_user->setSalaryClaims($request->get("salaryClaims"));
            $orm->persist($new_user);
            $orm->flush();
        }
        catch (InvalidArgumentException $ex) {
            $response->setStatusCode(400);
            $response->setContent("Invalid data");
        }

        return $response;
    }

    /**
     * @Fos\Get("/user/{id}")
     */
    public function getUserAction(Request $request, $id)
    {
        $response = new JsonResponse();
        if (gettype($id) != "integer") {
            $response->setStatusCode(500);
            return $response;
        }
        $orm = $this->getDoctrine()->getManager();
        $results = $orm->getRepository("App:User")->findOneBy(['id' => $id]);
        if ($results) {
            $response->setStatusCode(200);
            $response->setContent($results);
        }
        else {
            $response->setStatusCode(404);
            $response->setContent("User not found");
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
        $response->setContent("successful operation");
        /** @var User $user */
        $user = $orm->getRepository("App:User")->findOneBy(['id' => $id]);
        if (!$user) {
            $response->setStatusCode(404);
            $response->setContent("User not found");
            return $response;
        }
        try {
            if ($request->get("id")) {
                $user->setId($request->get("id"));
            }
            if ($request->get("description")) {
                $user->setDescription($request->get("description"));
            }
            if ($request->get("email")) {
                if ($user_rep->findOneBy(['email' => $request->get("email")]))
                    throw new InvalideArgumentException();
                $user->setEmail($request->get("email"));
            }
            if ($request->get("firstName")) {
                $user->setFirstName($request->get("firstName"));
            }
            if ($request->get("lastName")) {
                $user->setLastName($request->get("lastName"));
            }
            if ($request->get("salaryClaims")) {
                $user->setSalaryClaims($request->get("salaryClaims"));
            }
            $orm->persist($user);
            $orm->flush();
        }
        catch (InvalidArgumentException $ex) {
            $response->setStatusCode(400);
            $response->setContent("Invalid data");
        }
        return $response;
    }

    /**
     * @Fos\Delete("/user/{id}")
     */
    public function DeleteUserAction(Request $request, $id) {
        $response = new JsonResponse();
        $response->setStatusCode(200);
        $response->setContent("User deleted");
        $orm = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $orm->getRepository('App:User')->findOneBy(['id' => $id]);
        if ($user) {
            $orm->remove($user);
            $orm->flush();
        }
        else {
            $response->setStatusCode(404);
            $response->setContent("User not found");
        }
        return $response;
    }

    
}
