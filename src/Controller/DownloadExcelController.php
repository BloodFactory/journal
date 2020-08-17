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
//        $filters = [
//            'date' => $date,
//            'isActive' => true
//        ];
//        if (!$this->isGranted('ROLE_MORFLOT')) {
//            $filters['organization'] = $organization;
//        }

//        $journal = $this->getDoctrine()->getRepository(Journal::class)->findBy($filters, [
//            'date' => 'ASC'
//        ]);

        $journalE = $this
            ->getDoctrine()
            ->getRepository(Journal::class)
            ->createQueryBuilder('j')
            ->addSelect('org')
            ->addSelect('org_type')
            ->leftJoin('j.organization', 'org')
            ->leftJoin('org.type', 'org_type')
            ->andWhere('j.headOffice IS NULL')
            ->andWhere('j.date = :date')
            ->setParameter('date', $date)
            ->addOrderBy('org_type.id', 'ASC')
            ->addOrderBy('org.id', 'ASC')
            ->getQuery()
            ->getResult();
//        dd($j);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $FIELDS = ["N пп", "Организация", "Количество работников", "", "", "", "", "", "", "", "", "", "Примечание"];
        $FIELDS1 = ["", "", "фактическая численность", "на рабочем месте", "в отпуске", "на дистанционной форме работы", "", "", "", "на 2-х недельном карантине", "на больничном", "заболевших(COVD-19)", ""];
        $FIELDS2 = ["", "", "", "", "", "1", "2", "3", "4", "", "", "", ""];
        $FIELDSAll = ["N пп", "Организация", "фактическая численность", "на рабочем месте", "в отпуске", "1", "2", "3", "4", "на 2-х недельном карантине", "на больничном", "заболевших(COVD-19)"];
        $count_cols = count($FIELDSAll);//скок столбцов
        $sheet->setCellValue('B1', "Ежедневный доклад оперативного дежурного Оперативного штаба Росморречфлота попредупреждению\nраспространения короновирусной инспекции(COVID-19)");
        $sheet->getStyle("A1:AA1")->applyFromArray(['font' => ['bold' => true]]); // выделяем жирным до АА+номер строки
        $sheet->setCellValue('C2', "по состоянию на {$date->format('d.m.Y')}");
        $count_sections = 3; // разделы ---- >> N ROW!
        $sheet->fromArray($FIELDS, NULL, "A$count_sections");//заголовки столбцов
        $sheet->getStyle("A$count_sections:AA$count_sections")->applyFromArray(['font' => ['bold' => true]]); // выделяем жирным до АА+номер строки
        $sheet->getStyle("A$count_sections:AA$count_sections")->getAlignment()->setWrapText(true);
        $count_sections = 4; // разделы ---- >> N ROW!
        $sheet->fromArray($FIELDS1, NULL, "A$count_sections");//заголовки столбцов
        $sheet->getStyle("A$count_sections:AA$count_sections")->applyFromArray(['font' => ['bold' => true]]); // выделяем жирным до АА+номер строки
        $sheet->getStyle("A$count_sections:AA$count_sections")->getAlignment()->setWrapText(true);
        $count_sections = 5; // разделы ---- >> N ROW!
        $sheet->fromArray($FIELDS2, NULL, "A$count_sections");//заголовки столбцов
        $sheet->getStyle("A$count_sections:AA$count_sections")->applyFromArray(['font' => ['bold' => true]]); // выделяем жирным до АА+номер строки
        $sheet->getStyle("A$count_sections:AA$count_sections")->getAlignment()->setWrapText(true);
        $sheet->mergeCellsByColumnAndRow(3, 2, 12, 2); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(1, 3, 1, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(2, 3, 2, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(13, 3, 13, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(3, 3, 12, 3); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(6, 4, 9, 4); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(3, 4, 3, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(4, 4, 4, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(5, 4, 5, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(10, 4, 10, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(11, 4, 11, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(12, 4, 12, 5); //объединение ячеек;
        $count_sections = 6; // разделы ---- >> N ROW!
        $wd = 12;
        $sheet->getColumnDimension('B')->setWidth(45); //ширина 1 столбца
        $sheet->getColumnDimension('C')->setWidth($wd); //ширина 1 столбца
        $sheet->getColumnDimension('D')->setWidth($wd);
        $sheet->getColumnDimension('E')->setWidth($wd);
        $sheet->getColumnDimension('F')->setWidth($wd);
        $sheet->getColumnDimension('G')->setWidth($wd); //ширина 1 столбца
        $sheet->getColumnDimension('H')->setWidth($wd); //ширина 1 столбца
        $sheet->getColumnDimension('I')->setWidth($wd); //ширина 1 столбца
        $sheet->getColumnDimension('J')->setWidth($wd); //ширина 1 столбца
        $sheet->getColumnDimension('K')->setWidth($wd); //ширина 1 столбца
        $sheet->getColumnDimension('L')->setWidth($wd); //ширина 1 столбца
        $sheet->getColumnDimension('M')->setWidth(55); //ширина 1 столбца

        foreach ($journal as $key => $val) {
            if ($val->getOrganization() !== null) {
                $sheet->setCellValue('A' . $count_sections, $count_sections - 5);//поставили, увеличили;
                $sheet->setCellValue('B' . $count_sections, ($val->getOrganization() !== null) ? $val->getOrganization()->getName() : '');
                $sheet->getStyle("B$count_sections:B$count_sections")->getAlignment()->setWrapText(true); //заголовки столбцов
                $sheet->setCellValue('C' . $count_sections, $val->getTotal());
                $sheet->setCellValue('D' . $count_sections, $val->getAtWork());
                $sheet->setCellValue('E' . $count_sections, $val->getOnHoliday());
                $sheet->setCellValue('F' . $count_sections, $val->getRemoteTotal());
                $sheet->setCellValue('G' . $count_sections, $val->getRemotePregnant());
                $sheet->setCellValue('H' . $count_sections, $val->getRemoteWithChildren());
                $sheet->setCellValue('I' . $count_sections, $val->getRemoteOver60());
                $sheet->setCellValue('J' . $count_sections, $val->getOnTwoWeekQuarantine());
                $sheet->setCellValue('K' . $count_sections, $val->getOnSickLeave());
                $sheet->setCellValue('L' . $count_sections, $val->getSickCOVID());
                $sheet->setCellValue('M' . $count_sections, $val->getNote());
                $sheet->getRowDimension($count_sections)->setRowHeight(60);
                $count_sections++;
            }
        }
        $count_sections = $count_sections - 1;
        $sheet->getStyle("A3:M$count_sections")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000']
                ],
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ]

            ]
        ]);

        $filename = $kernel->getProjectDir() . '/tmp/' . bin2hex(random_bytes(10)) . '.xslx';
        $writer = new Xlsx($spreadsheet);

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