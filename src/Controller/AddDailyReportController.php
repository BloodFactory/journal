<?php

namespace App\Controller;

use App\Entity\Journal;
use App\Form\DailyReportForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AddDailyReportController extends AbstractController
{
    /**
     * @Route("/add/daily/report", name="add_daily_report")
     */
    public function index()
    {
        $journal = new Journal();

        $form = $this->createForm(DailyReportForm::class, $journal);

        return $this->render('add_daily_report/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
