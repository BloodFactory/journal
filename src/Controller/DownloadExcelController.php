<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Journal;
use App\Entity\User;
use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class DownloadExcelController extends AbstractController
{
    /**
     * @Route("/download")
     * @IsGranted("ROLE_MORFLOT")
     * @param Request $request
     * @param KernelInterface $kernel
     * @return Response
     * @throws Exception
     */
    public function download(Request $request, KernelInterface $kernel): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $organization = $user->getOrganization();

        $spreadsheet = new Spreadsheet();
        $writer = new Xlsx($spreadsheet);
        $date = $request->query->get('date');

        if (!$date) {
            $date = new DateTime();
        } else {
            try {
                $date = new DateTime($date);
            } catch (\Exception $e) {
                $date = new DateTime();
            }
        }

        $journal = $this->getDoctrine()->getRepository(Journal::class)->findBy([
            'date' => $date,
            'organization' => $organization,
            'isActive' => true
        ], [
            'date' => 'ASC'
        ]);

        //TODO: Сгенерировать сам файл

        $filename = $kernel->getProjectDir() . '/tmp/' . bin2hex(random_bytes(10)) . '.xslx';

        $writer->save($filename);

        $filesize = filesize($filename);
        $file = file_get_contents($filename);

        $response = new Response($file, Response::HTTP_OK, [
            'Accept-Ranges' => 'bytes',
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename=journal.xlsx',
            'Content-Length' => $filesize
        ]);

        unlink($filename);

        return $response;
    }
}