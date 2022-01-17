<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController
{

    /**
     * @Route(
     *     name="Home",
     *     path="/",
     *     methods={"GET"}
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request)
    {
        return new JsonResponse(["hello"]);
    }

}