<?php

namespace App\Controller;

use App\Entity\Alert;
use App\Entity\UserAlert;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/alerts")
 * Class AlertController
 * @package App\Controller
 */
class AlertController extends AbstractController
{
    /**
     * @Route("", methods={"DELETE"})
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            return new Response('', Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $id = $request->request->getInt('id');

        $alert = $this->getDoctrine()->getRepository(Alert::class)->find($id);

        $userAlert = new UserAlert();
        $userAlert->setUsr($this->getUser())
                  ->setAlert($alert)
                  ->setDate(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($userAlert);
        $em->flush();

        return new Response();
    }
}
