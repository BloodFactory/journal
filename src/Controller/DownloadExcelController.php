<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\JournalExcel;
use DateTime;
use DateTimeImmutable;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @param JournalExcel $je
     * @return Response
     * @throws \Exception
     */
    public function download(Request $request, KernelInterface $kernel, JournalExcel $je): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $date = $request->query->get('date');

        if (!$date) {
            $date = new DateTimeImmutable();
        } else {
            try {
                $date = new DateTimeImmutable($date);
            } catch (Exception $e) {
                $date = new DateTimeImmutable();
            }
        }

        $filename = $kernel->getProjectDir() . '/tmp/' . bin2hex(random_bytes(10)) . '.xslx';

        $je->save($date, $filename, $user);

        $filesize = filesize($filename);
        $file = file_get_contents($filename);
        $y = $date->format('d.m.Y');
        $s = "attachment; filename=\"Доклад ОШ $y.xlsx\"";
        $response = new Response($file, Response::HTTP_OK, [
            'Accept-Ranges' => 'bytes',
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => $s,
            'Content-Length' => $filesize
        ]);

        unlink($filename);

        return $response;
    }
}