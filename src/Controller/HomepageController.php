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
    public const SESSION_KEY = 'homepage';

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

        $filters = [
            'date' => $date,
            'isActive' => true,
            'headOffice' => null
        ];
        if (!$this->isGranted('ROLE_MORFLOT')) {
            $filters['organization'] = $organization;
        }

        $journal = $this->getDoctrine()
                        ->getRepository(Journal::class)
                        ->findBy($filters, [
                            'date' => 'ASC'
                        ]);

        $request->getSession()
                ->set(self::SESSION_KEY, $query->all());

        $now = new DateTime();

        return $this->render('homepage/index.html.twig', [
            'journal' => $journal,
            'date' => $date,
            'now' => $now
        ]);
    }
}
