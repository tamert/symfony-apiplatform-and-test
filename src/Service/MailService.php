<?php


namespace App\Service;

use App\Dto\MailDto;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as TwigEnvironment;
use Swift_Message;
use Swift_Mailer;
use Swift_Plugins_Loggers_ArrayLogger;
use Swift_Plugins_LoggerPlugin;

class MailService
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var TwigEnvironment
     */
    private $template;

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * MailManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     * @param Swift_Mailer $mailer
     * @param TwigEnvironment $template
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Swift_Mailer $mailer,
        TwigEnvironment $template,
        UrlGeneratorInterface $urlGenerator,
        ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->template = $template;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param MailDto $mailDto
     * @return Mail|bool
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function sendFormAction(MailDto $mailDto)
    {

        $mailLogger = new Swift_Plugins_Loggers_ArrayLogger();
        $this->mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($mailLogger));

        $context = [
            'title' => $mailDto->getSubject(),
            'message' => $mailDto->getMessage(),
            'footer' => $this->parameterBag->get("app_name"),
        ];

        $message = (new Swift_Message())
            ->setFrom($this->parameterBag->get("sender_mail"))
            ->setTo($mailDto->getTo())
            ->setBody($this->template->render("mail/default.html.twig", $context), 'text/html')
            ->addPart($this->template->render("mail/default.txt.twig", $context), 'text/plain');

        if (!$this->mailer->send($message)) {
            $this->mailLogger->critical($mailLogger->dump());
            return false;
        }

        return true;

    }

}