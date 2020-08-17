<?php 
declare(strict_types=1);

namespace App\Controller;
use App\Entity\Journal;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteDaileReportController extends AbstractController
{
    /**
     * @Route("/report/{id}/delete", name="delete_daily_report")
     * @IsGranted("ROLE_USER")
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $organization = $user->getOrganization();

        $dailyRecord = $this
            ->getDoctrine()
            ->getRepository(Journal::class)
            ->findOneBy([
                'id' => $id,
                'organization' => $organization
            ]);
        $dailyRecordBranches = $this
            ->getDoctrine()
            ->getRepository(Journal::class)
            ->findOneBy([
                'headOffice' => $dailyRecord
            ]);

        $dailyRecord->setIsActive(false);
        $dailyRecordBranches->setIsActive(false);

        $em = $this->getDoctrine()->getManager();
        $em->persist($dailyRecordBranches);
        $em->persist($dailyRecord);
        $em->flush();

        return $this->redirectToRoute('homepage');
    }
}