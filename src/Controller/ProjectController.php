<?php

namespace App\Controller;

use App\Entity\Project;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Fos;
use Symfony\Component\HttpFoundation\JsonResponse;


class ProjectController extends Controller
{
    /**
     * @Fos\Get("/project")
     */
    public function getProjectsAction(Request $request)
    {
        $orm = $this->getDoctrine()->getManager();
        $results = $orm->getRepository("App:Project")->findAll();
        $response = new JsonResponse();
        if (count($results) != 0) {
            $response->setStatusCode(200);
            $response->setContent(json_encode($results));
        }
        else {
            $response->setStatusCode(404);
            $response->setContent(json_encode(["message" => "Projects not found"]));
        }
        return $response;
    }

    /**
     * @Fos\Get("/project/{id}")
     */
    public function getProjectAction(Request $request, $id)
    {
        $response = new JsonResponse();
        $orm = $this->getDoctrine()->getManager();
        $results = $orm->getRepository("App:Project")->findOneBy(['id' => $id]);
        if ($results) {
            $response->setStatusCode(200);
            $response->setContent(json_encode($results));
        }
        else {
            $response->setStatusCode(404);
            $response->setContent(json_encode(["message" => "Project not found"]));
        }
        return $response;
    }

    /**
     * @Fos\Post("/project")
     */
    public function PostProjectAction(Request $request)
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
                && gettype($payload->descriptive) == "string"
                && $payload->languages && $payload->links
            ) {
                $payload->languages = json_decode(json_encode($payload->languages), true);
                $payload->links = json_decode(json_encode($payload->links), true);
                $new_project = new Project();
                $new_project->setId($payload->id);
                $new_project->setName($payload->name);
                $new_project->setDescriptive($payload->descriptive);
                $new_project->setLanguages($payload->languages);
                $new_project->setLinks($payload->links);
                $user = $user_rep->find($payload->userId);
                if (!$user) {
                    throw new \Exception("User Not Found");
                }
                $new_project->setUser($user);
                $orm->persist($new_project);
                $orm->flush();
            }
        }
        catch (\Throwable $ex) {
            $response->setStatusCode(405);
            $response->setContent(json_encode(["message" => "Invalid Input - ".$ex->getMessage()]));
        }
        return $response;
    }

    /**
     * @Fos\Put("/project/{id}")
     */
    public function PutProjectAction(Request $request, $id)
    {
        $orm = $this->getDoctrine()->getManager();
        $user_rep = $orm->getRepository('App:User');
        $project_rep = $orm->getRepository("App:Project");
        $response = new JsonResponse();
        $response->setStatusCode(200);
        $response->setContent(json_encode(["message" => "successful operation"]));
        $payload = json_decode($request->getContent());
        try {
            $project = $orm->getRepository("App:Project")->findOneBy(['id' => $id]);
            if (!$project) {
                $response->setStatusCode(404);
                $response->setContent(json_encode(["message" => "Project not found"]));
                return $response;
            }

            if ($payload->id) {
                if (gettype($payload->id) == "integer" && $payload->id != $project->getId()) {
                    if ($project_rep->find($payload->id)) {
                        throw new \Exception("Project ID already exist");
                    }
                    $project->setId($payload->id);
                }
            }

            if ($payload->userId) {
                if (gettype($payload->userId) == "integer" && $payload->userId != $project->getUser()->getId()) {
                    $user = $user_rep->find($payload->userId);
                    if (!$user) {
                        throw new \Exception("User not found");
                    }
                    $project->setUser($user);
                }
            }

            if ($payload->name) {
                if (gettype($payload->name) == "string") {
                    $project->setName($payload->name);
                }
            }

            if ($payload->descriptive) {
                if (gettype($payload->descriptive) == "string") {
                    $project->setDescriptive($payload->descriptive);
                }
            }

            if ($payload->languages) {
                $project->setLanguages(json_decode(json_encode($payload->languages), true));
            }

            if ($payload->links) {
                $project->setLinks(json_decode(json_encode($payload->links), true));
            }

            $orm->persist($project);
            $orm->flush();
        }
        catch (\Throwable $ex) {
            $response->setStatusCode(405);
            $response->setContent(json_encode(["message" => "Invalid input - ".$ex->getMessage()]));
        }
        return $response;
    }

    /**
     * @Fos\Delete("/project/{id}")
     */
    public function DeleteProjectAction(Request $request, $id) {
        $response = new JsonResponse();
        $response->setStatusCode(200);
        $response->setContent(json_encode(["message" => "Project deleted"]));
        $orm = $this->getDoctrine()->getManager();
        /** @var Project $user */
        $project = $orm->getRepository('App:Project')->findOneBy(['id' => $id]);
        if ($project) {
            $orm->remove($project);
            $orm->flush();
        }
        else {
            $response->setStatusCode(404);
            $response->setContent(json_encode(["message" => "Project not found"]));
        }
        return $response;
    }

}
