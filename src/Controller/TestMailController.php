<?php


namespace App\Controller;


use App\Dto\MailDto;
use App\Service\MailService;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TestMailController
{

    /**
     * @var MailService
     */
    protected $mailService;

    /**
     * TestMailController constructor.
     * @param MailService $mailService
     */
    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * @Route(
     *     name="Send Mail Test",
     *     path="/api/test-mail",
     *     methods={"GET"}
     * )
     * @param Request $request
     * @return JsonResponse
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function __invoke(Request $request)
    {
        $mail = new MailDto();
        $mail->setSubject('Test Subject');
        $mail->setTo('farerock@gmail.com');
        $mail->setName('Tamer Agaoglu');
        $mail->setMessage('This is a testing mail');

        $status = $this->mailService->sendFormAction($mail);
        return new JsonResponse(["status" => $status]);
    }

}