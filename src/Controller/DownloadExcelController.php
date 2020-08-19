<?php
declare(strict_types=1);

namespace App\Controller;         

use App\Entity\Journal;
use App\Entity\Organization;
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
Use Doctrine\ORM\Query\ResultSetMapping;
Use Doctrine\ORM\EntityManager;

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
        $filters = [
            'date' => $date,
            'isActive' => true
        ];
        if (!$this->isGranted('ROLE_MORFLOT')) {
            $filters['organization'] = $organization;
        }
        $journal = $this->getDoctrine()->getRepository(Journal::class)->createQueryBuilder('j')->andWhere('j.date = :date')->setParameter('date', $date)
	->andWhere('j.isActive = 1')->andWhere('j.organization IS NOT NULL')->addOrderBy('j.organization', 'ASC')->getQuery()->getResult();                                           
        $journal1 = $this->getDoctrine()->getRepository(Journal::class)->createQueryBuilder('j')->andWhere('j.date = :date')->setParameter('date', $date)
	->andWhere('j.isActive = 1')->andWhere('j.organization IS NULL')->getQuery()->getResult();                                           
        $org = $this->getDoctrine()->getRepository(Organization::class)->createQueryBuilder('o')->addOrderBy('o.id', 'ASC')->getQuery()->getResult();
	$rez =[];
	$j=0;
	$i=1;
        foreach ($org as $key => $val) {
		$rez[$j]['Org_Id']=$val->getId();
		$rez[$j]['Org_Name']=$val->getName();
		$rez[$j]['Org_Number']=(string)$i;
	        foreach ($journal as $key1 => $val1) {
			if ($val1->getOrganization()->getId()==$val->getId()) {
				$rez[$j]['Jornal']=$val1;
			}
		} 
		if ($val->getBranches()) {
			$j++;
			$rez[$j]['Org_Id']=$val->getId();
			$rez[$j]['Org_Name']='Филиалы '.$val->getName();
			$rez[$j]['Org_Number']=(string)$i.'.1';
		        foreach ($journal1 as $key1 => $val1) {
				if ($val1->getHeadOffice()->getOrganization()->getId()==$val->getId()) {
					$rez[$j]['Jornal']=$val1;
				}
			} 
		}
		$i++;
		$j++;			
        }
//	dd($rez);
/*	$rsm = new ResultSetMapping();
        $rsm->addEntityResult(Journal::class, 'd');
        $rsm->addFieldResult('d', 'total', 'total');
        $rsm->addFieldResult('d', 'at_work', 'atWork');
        $rsm->addFieldResult('d', 'on_holiday', 'onHoliday');
        $rsm->addFieldResult('d', 'remote_total', 'remoteTotal');
        $rsm->addFieldResult('d', 'remote_pregnant', 'remotePregnant');
        $rsm->addFieldResult('d', 'remote_with_children', 'remoteWithChildren');
        $rsm->addFieldResult('d', 'remote_over60', 'remoteOver60');
        $rsm->addFieldResult('d', 'on_two_week_quarantine', 'onTwoWeekQuarantine');
        $rsm->addFieldResult('d', 'on_sick_leave', 'onSickLeave');
        $rsm->addFieldResult('d', 'sick_covid', 'sickCOVID');
        $rsm->addFieldResult('d', 'note', 'note');
        $rsm->addFieldResult('d', 'shift_rest', 'shift_rest'); 
        $rsm->addFieldResult('d', 'org_id', 'org_id'); 
        $rsm->addFieldResult('d', 'Org_name', 'OrgName'); 
	$rsm->addFieldResult('d', 'id', 'id');
	$query = $this->getDoctrine()->getManager()->createNativeQuery( 'EXEC [dbo].[sp_GetJournal] ?',$rsm)
        	    ->setParameters( array(
                	1 => $date
        	    ) );
	$result = $query->getResult();
	dd($result);

        $journal = $this
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
            ->getResult(); */
//        dd($result);

        $spreadsheet = new Spreadsheet();
	$sheet= $spreadsheet->getActiveSheet();
	$sheet->setTitle('Таблица');
        $FIELDS = ["N пп", "Организация", "Количество работников", "", "", "", "", "", "", "", "", "", "", "Примечание"];
        $FIELDS1 = ["", "", "фактическая численность", "на рабочем месте", "в отпуске", "на дистанционной форме работы", "", "", "", "на 2-х недельном карантине", "на больничном", "заболевших(COVD-19)", "Выходной/ межвахтовый отдых", ""];
        $FIELDS2 = ["", "", "", "", "", "1", "2", "3", "4", "", "", "", "", ""];
        $sheet->setCellValue('C1', "Ежедневный доклад оперативного дежурного Оперативного штаба Росморречфлота попредупреждению\nраспространения короновирусной инспекции(COVID-19)");
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
        $sheet->mergeCellsByColumnAndRow(14, 3, 14, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(3, 3, 13, 3); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(6, 4, 9, 4); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(3, 4, 3, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(4, 4, 4, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(5, 4, 5, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(10, 4, 10, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(11, 4, 11, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(12, 4, 12, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(13, 4, 13, 5); //объединение ячеек;
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
        $sheet->getColumnDimension('M')->setWidth($wd); //ширина 1 столбца
        $sheet->getColumnDimension('N')->setWidth(55); //ширина 1 столбца

        foreach ($rez as $key => $val) {
                $sheet->setCellValue('A' . $count_sections, $val['Org_Number']);//поставили, увеличили;
                $sheet->setCellValue('B' . $count_sections, $val['Org_Name']);
                $sheet->getStyle("B$count_sections:B$count_sections")->getAlignment()->setWrapText(true); //заголовки столбцов
                $sheet->getStyle("N$count_sections:N$count_sections")->getAlignment()->setWrapText(true); //заголовки столбцов
		if (isset($val['Jornal'])) {
	                $sheet->setCellValue('C' . $count_sections, $val['Jornal']->getTotal());
        	        $sheet->setCellValue('D' . $count_sections, $val['Jornal']->getAtWork());
	                $sheet->setCellValue('E' . $count_sections, $val['Jornal']->getOnHoliday());
        	        $sheet->setCellValue('F' . $count_sections, $val['Jornal']->getRemoteTotal());
                	$sheet->setCellValue('G' . $count_sections, $val['Jornal']->getRemotePregnant());
	                $sheet->setCellValue('H' . $count_sections, $val['Jornal']->getRemoteWithChildren());
        	        $sheet->setCellValue('I' . $count_sections, $val['Jornal']->getRemoteOver60());
	                $sheet->setCellValue('J' . $count_sections, $val['Jornal']->getOnTwoWeekQuarantine());
        	        $sheet->setCellValue('K' . $count_sections, $val['Jornal']->getOnSickLeave());
	                $sheet->setCellValue('L' . $count_sections, $val['Jornal']->getSickCOVID());
        	        $sheet->setCellValue('M' . $count_sections, $val['Jornal']->getShiftRest());
	                $sheet->setCellValue('N' . $count_sections, $val['Jornal']->getNote());
		} else {
	                $sheet->setCellValue('C' . $count_sections, '-');
        	        $sheet->setCellValue('D' . $count_sections, '-');
	                $sheet->setCellValue('E' . $count_sections, '-');
        	        $sheet->setCellValue('F' . $count_sections, '-');
                	$sheet->setCellValue('G' . $count_sections, '-');
	                $sheet->setCellValue('H' . $count_sections, '-');
        	        $sheet->setCellValue('I' . $count_sections, '-');
	                $sheet->setCellValue('J' . $count_sections, '-');
        	        $sheet->setCellValue('K' . $count_sections, '-');
	                $sheet->setCellValue('L' . $count_sections, '-');
        	        $sheet->setCellValue('M' . $count_sections, '-');
	                $sheet->setCellValue('N' . $count_sections, '-');
		}
                $sheet->getRowDimension($count_sections)->setRowHeight(60);
                $count_sections++;
        }
        $count_sections = $count_sections - 1;
        $sheet->getStyle("A3:N3")->applyFromArray([
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]

            ]
        );
        $sheet->getStyle("A3:A$count_sections")->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]

            ]
        );
        $sheet->getStyle("C6:N$count_sections")->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]

            ]
        );
        $sheet->getStyle("C2:C2")->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]

            ]
        );
        $sheet->getStyle("A3:N$count_sections")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000']
                ]
            ]
        ]);

	$sheet2= $spreadsheet->createSheet();
	$sheet2->setTitle('Контакты');
	$count_sections = 1;
        foreach ($org as $key => $val) {
                $sheet2->getStyle("A$count_sections:A$count_sections")->getAlignment()->setWrapText(true); 
                $sheet2->getStyle("B$count_sections:B$count_sections")->getAlignment()->setWrapText(true); 
                $sheet2->setCellValue('A' . $count_sections, $val->getName());
       	        $sheet2->setCellValue('B' . $count_sections, $val->getContact());
                $sheet2->getRowDimension($count_sections)->setRowHeight(80);
                $count_sections++;
	}
        $sheet2->getColumnDimension('A')->setWidth(45); //ширина 1 столбца
        $sheet2->getColumnDimension('B')->setWidth(145); //ширина 1 столбца
        $sheet2->getStyle("A1:B$count_sections")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000']
                ]
            ]
        ]);
        $sheet->getStyle("A1:A1")->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]
        );
       $filename = $kernel->getProjectDir() . '/tmp/' . bin2hex(random_bytes(10)) . '.xslx';
        $writer = new Xlsx($spreadsheet);

        $writer->save($filename);

        $filesize = filesize($filename);
        $file = file_get_contents($filename);
	$y=$date->format('d.m.Y');
	$s= "attachment; filename=\"Доклад ОШ $y.xlsx\"";
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