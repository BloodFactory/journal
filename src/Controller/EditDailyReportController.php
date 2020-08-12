<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Journal;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditDailyReportController extends AbstractController
{
    /**
     * @Route("/report/{id}", name="edit_daily_report")
     * @IsGranted("ROLE_USER")
     * @param int $id
     * @return Response
     */
    public function edit(int $id): Response
    {
        $report = $this->getDoctrine()->getRepository(Journal::class)->find($id);

        return $this->render('daily_report/index.html.twig', [
            'report' => $report
        ]);
    }
}