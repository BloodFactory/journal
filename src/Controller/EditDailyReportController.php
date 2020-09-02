<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Journal;
use App\Entity\User;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class EditDailyReportController extends AbstractController
{
    /**
     * @Route("/report/{id}", name="edit_daily_report")
     * @IsGranted("ROLE_USER")
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function edit(int $id, Request $request): Response
    {
        $report = $this->getDoctrine()->getRepository(Journal::class)->find($id);
        /** @var User $user */
        $user = $this->getUser();
        $organization = $user->getOrganization();

        if ('POST' === $request->getMethod()) {
            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager();
            $date = new DateTime($data['journalForm_date']);

            $report
                ->setOrganization($organization)
                ->setDate($date)
                ->setTotal((int)$data['journalForm_atWork']+(int)$data['journalForm_onHoliday']+(int)$data['journalForm_remoteTotal']+(int)$data['journalForm_onTwoWeekQuarantine']+
			(int)$data['journalForm_onSickLeave']+(int)$data['journalForm_ShiftRest'])
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

            if ($organization->getBranches()) {
                $reportBranch = new Journal();
                $report->addBranch($reportBranch);

                $reportBranch
                    ->setDate($date)
                ->setTotal((int)$data['journalBranchesForm_atWork']+(int)$data['journalBranchesForm_onHoliday']+(int)$data['journalBranchesForm_remoteTotal']+(int)$data['journalBranchesForm_onTwoWeekQuarantine']+
			(int)$data['journalBranchesForm_onSickLeave']+(int)$data['journalBranchesForm_ShiftRest'])
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
            if ($data['journalForm_atWork']==0 && $data['journalForm_onHoliday']==0 && $data['journalForm_remoteTotal']==0 
		&& $data['journalForm_remotePregnant']==0 && $data['journalForm_remoteWithChildren']==0 && $data['journalForm_remoteOver60']==0 && $data['journalForm_onTwoWeekQuarantine']==0
		&& $data['journalForm_onSickLeave']==0 && $data['journalForm_sickCOVID']==0 && $data['journalForm_ShiftRest']==0 && $data['journalForm_Die']==0) {
		        return $this->render('daily_report/index.html.twig', [
		            'report' => $report,
		            'query' => $request->getSession()->get(HomepageController::SESSION_KEY), 
			    'error' => 'Все поля структуры "Количество работников" не могут быть нулями'	
		        ]);
	    } 
            if ($organization->getBranches()) {
	            if ($data['journalBranchesForm_atWork']==0 && $data['journalBranchesForm_onHoliday']==0 && $data['journalBranchesForm_remoteTotal']==0 
			&& $data['journalBranchesForm_remotePregnant']==0 && $data['journalBranchesForm_remoteWithChildren']==0 && $data['journalBranchesForm_remoteOver60']==0 && $data['journalBranchesForm_onTwoWeekQuarantine']==0
			&& $data['journalBranchesForm_onSickLeave']==0 && $data['journalBranchesForm_sickCOVID']==0 && $data['journalBranchesForm_ShiftRest']==0 && $data['journalBranchesForm_Die']==0) {
		        return $this->render('daily_report/index.html.twig', [
		            'report' => $report,
		            'query' => $request->getSession()->get(HomepageController::SESSION_KEY), 
			    'error' => 'Все поля структуры "Количество работников" у филиалов не могут быть нулями'	
		        ]);
		    } 
	    }	
            $em->persist($report);
            $em->flush();
	    return $this->redirectToRoute('homepage',$request->getSession()->get(HomepageController::SESSION_KEY));	
        }

        return $this->render('daily_report/index.html.twig', [
            'report' => $report,
            'query' => $request->getSession()->get(HomepageController::SESSION_KEY)
        ]);
    }
}