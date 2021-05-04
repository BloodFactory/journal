<?php

namespace App\EventSubscriber;

use App\Entity\Log\RequestLog;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Controller\ErrorController;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Security\Core\Security;

class ControllerEventSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.controller' => ['logRequest', 1000]
        ];
    }

    public function logRequest(ControllerEvent $event)
    {
        if ($event->getController() instanceof ErrorController) return;

        $request = $event->getRequest();

        $requestLog = new RequestLog();

        $content = is_string($request->getContent()) ? json_decode($request->getContent(), true) : null;
        $query = !empty($request->query->all()) ? $request->query->all() : null;
        $req = !empty($request->request->all()) ? $request->request->all() : null;
        /** @var User $user */
        $user = $this->security->getUser();

        try {
            $requestLog->setMoment(new \DateTime())
                       ->setMethod($request->getMethod())
                       ->setContent($content)
                       ->setRequest($req)
                       ->setQuery($query)
                       ->setUsr($user)
                       ->setPath($request->get('_route'));
        } catch (\Throwable $e) {
            dd($request);
        }

        $this->em->persist($requestLog);
        $this->em->flush();
    }
}
