<?php

namespace App\Controller;

use App\Entity\Journal;
use App\Entity\User;
use DateTime;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $organization = $user->getOrganization();

        $query = $request->query;

        if ($query->has('date')) {
            $date = $query->get('date');
            try {
                $date = new DateTime($date);
            } catch (Exception $e) {
                $date = new DateTime();
            }
        } else {
            $date = new DateTime();
        }

        $journal = $this->getDoctrine()->getRepository(Journal::class)->findBy([
            'date' => $date,
            'organization' => $organization,
            'isActive' => true
        ], [
            'date' => 'ASC'
        ]);

        return $this->render('homepage/index.html.twig', [
            'journal' => $journal,
            'date' => $date
        ]);
    }
}
