<?php

namespace App\Controller;

use App\Entity\Journal;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @IsGranted("ROLE_USER")
     */
    public function index():Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $organization = $user->getOrganization();

        $journal = $this->getDoctrine()->getRepository(Journal::class)->findBy([
            'organization' => $organization,
            'isActive' => true
        ], [
            'date' => 'ASC'
        ]);

        return $this->render('homepage/index.html.twig', [
            'journal' => $journal
        ]);
    }
}
