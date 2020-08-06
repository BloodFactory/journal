<?php

namespace App\Controller;

use App\Entity\Journal;
use App\Form\DailyReportForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddDailyReportController extends AbstractController
{
    /**
     * @Route("/report", name="add_daily_report")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $journal = new Journal();

        $form = $this->createForm(DailyReportForm::class, $journal);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $journal = $form->getData();

                $em = $this->getDoctrine()->getManager();

                $em->persist($journal);
                $em->flush();
            }
        }

        return $this->render('add_daily_report/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
