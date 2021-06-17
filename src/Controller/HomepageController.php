<?php

namespace App\Controller;

use App\Entity\Alert;
use App\Entity\Journal;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\Query\ResultSetMapping;
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
                $date = new DateTimeImmutable($date);
            } catch (Exception $e) {
                $date = new DateTimeImmutable();
            }
        } else {
            $date = new DateTimeImmutable();
        }

        $journal = $this->getDoctrine()
                        ->getRepository(Journal::class)
                        ->createQueryBuilder('j')
                        ->addSelect('b')
                        ->addSelect('o')
                        ->leftJoin('j.branches', 'b')
                        ->leftJoin('j.organization', 'o')
                        ->andWhere('j.date = :date')
                        ->andWhere('j.headOffice IS NULL')
                        ->andWhere('j.isActive = 1')
                        ->setParameter('date', $date)
                        ->addOrderBy('j.date');


        $journalPrev = $this->getDoctrine()
                            ->getRepository(Journal::class)
                            ->createQueryBuilder('j')
                            ->addSelect('b')
                            ->addSelect('o')
                            ->leftJoin('j.branches', 'b')
                            ->leftJoin('j.organization', 'o')
                            ->andWhere('j.date = :date')
                            ->andWhere('j.headOffice IS NULL')
                            ->andWhere('j.isActive = 1')
                            ->setParameter('date', $date->sub(new \DateInterval('P1D')))
                            ->addOrderBy('j.date');

        if (!$this->isGranted('ROLE_MORFLOT')) {
            $journal->andWhere('j.organization = :org')->setParameter('org', $organization);
            $journalPrev->andWhere('j.organization = :org')->setParameter('org', $organization);
        }

        $journal = $journal->getQuery()->getResult();
        $journalPrev = $journalPrev->getQuery()->getResult();

        $result = [];

        $allowModify = $this->isGranted('ROLE_ALLOW_TO_MODIFY_ALL');

        /** @var Journal $item */
        foreach ($journal as $item) {
            $newItem = [
                'id' => $item->getId(),
                'organization' => $item->getOrganization()->getName(),
                'organization_id' => $item->getOrganization()->getId(),
                'total' => $item->getTotal(),
                'atWork' => $item->getAtWork(),
                'onHoliday' => $item->getOnHoliday(),
                'remoteTotal' => $item->getRemoteTotal(),
                'onTwoWeekQuarantine' => $item->getOnTwoWeekQuarantine(),
                'onSickLeave' => $item->getOnSickLeave(),
                'sickCOVID' => $item->getSickCOVID(),
                'sickCOVIDPrev' => '-',
                'shiftRest' => $item->getShiftRest(),
                'die' => $item->getDie(),
                'note' => $item->getNote(),
                'hasBranches' => $item->getOrganization()->getBranches(),
                'nextDay' => $item->nextDay($allowModify)
            ];

            if ($item->getBranches()->count()) {
                $branch = $item->getBranches()[0];

                $newItem['branch'] = [
                    'id' => $branch->getId(),
                    'total' => $branch->getTotal(),
                    'atWork' => $branch->getAtWork(),
                    'onHoliday' => $branch->getOnHoliday(),
                    'remoteTotal' => $branch->getRemoteTotal(),
                    'onTwoWeekQuarantine' => $branch->getOnTwoWeekQuarantine(),
                    'onSickLeave' => $branch->getOnSickLeave(),
                    'sickCOVID' => $branch->getSickCOVID(),
                    'sickCOVIDPrev' => '-',
                    'shiftRest' => $branch->getShiftRest(),
                    'die' => $branch->getDie(),
                    'note' => $branch->getNote()
                ];
            }

            $result[$item->getOrganization()->getId()] = $newItem;
        }

        foreach ($journalPrev as $item) {
            if (!isset($result[$item->getOrganization()->getId()])) continue;

            $existingItem = $result[$item->getOrganization()->getId()];
            $existingItem['sickCOVIDPrev'] = $item->getSickCOVID();

            if (isset($existingItem['branch']) && $item->getBranches()->count()) {
                $existingItem['branch']['sickCOVIDPrev'] = $item->getBranches()[0]->getSickCOVID();
            }

            $result[$item->getOrganization()->getId()] = $existingItem;
        }

        $request->getSession()
                ->set(self::SESSION_KEY, $query->all());

        $now = new DateTimeImmutable();

        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(Alert::class, 'a');
        $rsm->addFieldResult('a', 'id', 'id');
        $rsm->addFieldResult('a', 'message', 'message');
        $rsm->addFieldResult('a', 'once', 'once');
        $query = $this->getDoctrine()->getManager()->createNativeQuery(file_get_contents(__DIR__ . '/queries/select_alerts.sql'), $rsm);
        $query->setParameter(1, $user->getId());

        $alerts = $query->getResult();

        return $this->render('homepage/index.html.twig', [
            'journal' => $result,
            'journalPrev' => $journalPrev,
            'date' => $date,
            'now' => $now,
            'alerts' => $alerts
        ]);
    }
}
