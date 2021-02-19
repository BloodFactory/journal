<?php

namespace App\Controller;

use App\Entity\Journal;
use App\Entity\Organization;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class DailyReportController extends AbstractController
{
    /**
     * @Route("/report", name="daily_report")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function index(Request $request): Response
    {
        $reportID = $request->query->getInt('id');
        $report = $reportID ? $this->getDoctrine()->getRepository(Journal::class)->find($reportID) : new Journal();

        /** @var User $user */
        $user = $this->getUser();
        $organization = 0 !== $reportID ? $report->getOrganization() : $user->getOrganization();
        if ($organization->getBranches() && !$report->getBranches()[0]) {
            $branch = new Journal();
            $report->addBranch($branch);
        }

        if ('POST' === $request->getMethod()) {
            try {
                $this->save($request, $report, $organization, $reportID);
                return $this->redirectToRoute('homepage', $request->getSession()->get(HomepageController::SESSION_KEY));
            } catch (Throwable $e) {
                $error = $e->getMessage();

            }
        }

        return $this->render('daily_report/index.html.twig', [
            'report' => $report,
            'organization' => $organization,
            'query' => $request->getSession()->get(HomepageController::SESSION_KEY),
            'error' => $error ?? ''
        ]);
    }

    /**
     * @param Request $request
     * @param Journal $report
     * @param Organization $organization
     * @param int $reportID
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    private function save(Request $request, Journal $report, Organization $organization, int $reportID): void
    {

        $data = $request->request->all();

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $date = new DateTime($data['journalForm_date']);

        if (0 === $reportID && null !== $this->getDoctrine()->getRepository(Journal::class)->findOneBy([
                'organization' => $organization,
                'date' => $date,
                'isActive' => true
            ])) {
            throw new Exception("В системе уже имеется запись датированная {$date->format('d.m.Y')} от организации {$organization->getName()}");
        }

        $report
            ->setOrganization($organization)
            ->setDate($date)
            ->setTotal((int)$data['journalForm_atWork'] + (int)$data['journalForm_onHoliday'] + (int)$data['journalForm_remoteTotal'] + (int)$data['journalForm_onTwoWeekQuarantine'] +
                (int)$data['journalForm_onSickLeave'] + (int)$data['journalForm_ShiftRest'])
            ->setAtWork((int)$data['journalForm_atWork'])
            ->setOnHoliday((int)$data['journalForm_onHoliday'])
            ->setRemoteTotal((int)$data['journalForm_remoteTotal'])
            ->setRemotePregnant((int)$data['journalForm_remotePregnant'])
            ->setRemoteWithChildren((int)$data['journalForm_remoteWithChildren'])
            ->setRemoteOver60((int)$data['journalForm_remoteOver60'])
            ->setOnTwoWeekQuarantine((int)$data['journalForm_onTwoWeekQuarantine'])
            ->setOnSickLeave((int)$data['journalForm_onSickLeave'])
            ->setSickCOVID((int)$data['journalForm_sickCOVID'])
            ->setShiftRest((int)$data['journalForm_ShiftRest'])
            ->setDie((int)$data['journalForm_Die'])
            ->setNote($data['journalForm_note']);

        if (null !== $reportBranch = $report->getBranches()[0]) {

            $reportBranch
                ->setDate($date)
                ->setTotal((int)$data['journalBranchesForm_atWork'] + (int)$data['journalBranchesForm_onHoliday'] + (int)$data['journalBranchesForm_remoteTotal'] + (int)$data['journalBranchesForm_onTwoWeekQuarantine'] +
                    (int)$data['journalBranchesForm_onSickLeave'] + (int)$data['journalBranchesForm_ShiftRest'])
                ->setAtWork((int)$data['journalBranchesForm_atWork'])
                ->setOnHoliday((int)$data['journalBranchesForm_onHoliday'])
                ->setRemoteTotal((int)$data['journalBranchesForm_remoteTotal'])
                ->setRemotePregnant((int)$data['journalBranchesForm_remotePregnant'])
                ->setRemoteWithChildren((int)$data['journalBranchesForm_remoteWithChildren'])
                ->setRemoteOver60((int)$data['journalBranchesForm_remoteOver60'])
                ->setOnTwoWeekQuarantine((int)$data['journalBranchesForm_onTwoWeekQuarantine'])
                ->setOnSickLeave((int)$data['journalBranchesForm_onSickLeave'])
                ->setSickCOVID((int)$data['journalBranchesForm_sickCOVID'])
                ->setShiftRest((int)$data['journalBranchesForm_ShiftRest'])
                ->setDie((int)$data['journalBranchesForm_Die'])
                ->setNote($data['journalBranchesForm_note']);

            $em->persist($reportBranch);
        }

        /** @var Journal $lastReport */
        $lastReport = $em->createQueryBuilder()
                         ->from(Journal::class, 'J')
                         ->select('J')
                         ->andWhere('J.organization = :organization')
                         ->andWhere('J.date < :date')
                         ->andWhere('J.isActive = 1')
                         ->setParameter('organization', $organization)
                         ->setParameter('date', $date)
                         ->orderBy('J.date', 'DESC')
                         ->setMaxResults(1)
                         ->getQuery()
                         ->getOneOrNullResult();

        $curDie = (int)$data['journalForm_Die'] ?: 0;


        if (null !== $lastReport && ($lastDie = $lastReport->getDie()) > $curDie) {
            throw new Exception("Количество скончавшихся от COVID-19 не может быть менее $lastDie человек(а), так как данное значение вводится нарастающим итогом.");
        }


        if ($data['journalForm_atWork'] == 0 && $data['journalForm_onHoliday'] == 0 && $data['journalForm_remoteTotal'] == 0
            && $data['journalForm_remotePregnant'] == 0 && $data['journalForm_remoteWithChildren'] == 0 && $data['journalForm_remoteOver60'] == 0 && $data['journalForm_onTwoWeekQuarantine'] == 0
            && $data['journalForm_onSickLeave'] == 0 && $data['journalForm_sickCOVID'] == 0 && $data['journalForm_ShiftRest'] == 0 && $data['journalForm_Die'] == 0) {
            throw new Exception('Все поля структуры "Количество работников" не могут быть нулями');
        }
        if ($organization->getBranches()) {
            $curDieBranch = (int)$data['journalBranchesForm_Die'] ?: 0;

            if (null !== $lastReport && $lastReport->getBranches()->count() !== 0 && ($lastDie = $lastReport->getBranches()[0]->getDie()) > $curDieBranch) {
                throw new Exception("Количество скончавшихся от COVID-19 у филиалов не может быть менее $lastDie человек(а), так как данное значение вводится нарастающим итогом.");
            }

            if ($data['journalBranchesForm_atWork'] == 0 && $data['journalBranchesForm_onHoliday'] == 0 && $data['journalBranchesForm_remoteTotal'] == 0
                && $data['journalBranchesForm_remotePregnant'] == 0 && $data['journalBranchesForm_remoteWithChildren'] == 0 && $data['journalBranchesForm_remoteOver60'] == 0 && $data['journalBranchesForm_onTwoWeekQuarantine'] == 0
                && $data['journalBranchesForm_onSickLeave'] == 0 && $data['journalBranchesForm_sickCOVID'] == 0 && $data['journalBranchesForm_ShiftRest'] == 0 && $data['journalBranchesForm_Die'] == 0) {
                throw new Exception('Все поля структуры "Количество работников" у филиалов не могут быть нулями');
            }
        }
        $em->persist($report);
        $em->flush();
    }
}
