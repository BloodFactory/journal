<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Journal;
use App\Entity\Organization;
use App\Entity\User;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Security\Core\Security;

class JournalExcel
{
    private Security $security;
    private EntityManagerInterface $em;

    public function __construct(Security $security, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->em = $em;
    }

    /**
     * @param DateTimeInterface $date
     * @param string $filename
     * @param User|null $user
     * @throws Exception
     */
    public function save(DateTimeInterface $date, string $filename, ?User $user = null): void
    {
        $filters = [
            'date' => $date,
            'isActive' => true
        ];

        if (null !== $user && !$this->security->isGranted('ROLE_MORFLOT')) {
            $organization = $user->getOrganization();
            $filters['organization'] = $organization;
        }

        $journal = $this->em->getRepository(Journal::class)
                            ->createQueryBuilder('j')
                            ->leftJoin('j.organization', 'o')
                            ->andWhere('j.date = :date')
                            ->setParameter('date', $date)
                            ->andWhere('j.isActive = 1')
                            ->andWhere('j.organization IS NOT NULL')
                            ->addOrderBy('o.sort', 'ASC')
                            ->getQuery()
                            ->getResult();
        $journal1 = $this->em->getRepository(Journal::class)
                             ->createQueryBuilder('j')
                             ->andWhere('j.date = :date')
                             ->setParameter('date', $date)
                             ->andWhere('j.isActive = 1')
                             ->andWhere('j.organization IS NULL')
                             ->getQuery()
                             ->getResult();
        $org = $this->em->getRepository(Organization::class)
                        ->createQueryBuilder('o')
                        ->addOrderBy('o.sort', 'ASC')
                        ->andWhere('o.isActive = 1')
                        ->getQuery()
                        ->getResult();
        $rez = [];
        $j = 0;
        $i = 1;
        foreach ($org as $key => $val) {
            $rez[$j]['Org_Id'] = $val->getId();
            $rez[$j]['Org_Name'] = $val->getName();
            $rez[$j]['Org_Number'] = (string)$i;
            foreach ($journal as $key1 => $val1) {
                if ($val1->getOrganization()
                         ->getId() == $val->getId()) {
                    $rez[$j]['Jornal'] = $val1;
                }
            }
            if ($val->getBranches()) {
                $j++;
                $rez[$j]['Org_Id'] = $val->getId();
                $rez[$j]['Org_Name'] = 'Филиалы ' . $val->getName();
                $rez[$j]['Org_Number'] = (string)$i . '.1';
                foreach ($journal1 as $key1 => $val1) {
                    if ($val1->getHeadOffice()
                             ->getOrganization()
                             ->getId() == $val->getId()) {
                        $rez[$j]['Jornal'] = $val1;
                    }
                }
            }
            $i++;
            $j++;
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getDefaultStyle()
                    ->getFont()
                    ->setName('Times New Roman')
                    ->setSize(12);

        $spreadsheet->getDefaultStyle()
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Таблица');

        $sheet->getColumnDimension('A')->setWidth(8.46);
        $sheet->getColumnDimension('B')->setWidth(34.92);
        $sheet->getColumnDimension('C')->setWidth(8.04);
        $sheet->getColumnDimension('D')->setWidth(8.72);
        $sheet->getColumnDimension('E')->setWidth(8.04);
        $sheet->getColumnDimension('F')->setWidth(8.04);
        $sheet->getColumnDimension('G')->setWidth(8.04);
        $sheet->getColumnDimension('H')->setWidth(8.04);
        $sheet->getColumnDimension('I')->setWidth(8.04);
        $sheet->getColumnDimension('J')->setWidth(8.04);
        $sheet->getColumnDimension('K')->setWidth(8.04);
        $sheet->getColumnDimension('L')->setWidth(8.04);
        $sheet->getColumnDimension('M')->setWidth(8.04);
        $sheet->getColumnDimension('N')->setWidth(8.04);
        $sheet->getColumnDimension('O')->setWidth(44.06);

        $FIELDS = ["N пп", "Организация", "Количество работников", "", "", "", "", "", "", "", "", "", "", "", "Примечание"];
        $FIELDS1 = ["", "", "фактическая численность", "на рабочем месте", "в отпуске", "на дистанционной форме работы", "", "", "", "на 2-х недельном карантине", "на больничном", "заболевших(COVD-19)", "Выходной/ межвахтовый отдых", "Скончалось от COVID-19 (нарастающим итогом)", ""];
        $FIELDS2 = ["", "", "", "", "", "Всего", "Беременные женщины", "Женщины с детьми до 14 лет", "Работники старше 60 лет", "", "", "", "", "", ""];
        $sheet->setCellValue('A1', "Ежедневный доклад оперативного дежурного Оперативного штаба Росморречфлота по предупреждению распространения коронавирусной инфекции(COVID-19)");
        $sheet->getStyle("A1:AA1")->applyFromArray(['font' => ['bold' => true]]); // выделяем жирным до АА+номер строки
        $sheet->setCellValue('A2', "по состоянию на {$date->format('d.m.Y')}");
        $count_sections = 3; // разделы ---- >> N ROW!
        $sheet->fromArray($FIELDS, NULL, "A$count_sections");//заголовки столбцов
        $sheet->getStyle("A$count_sections:AA$count_sections")
              ->applyFromArray(['font' => ['bold' => true]]); // выделяем жирным до АА+номер строки
        $sheet->getStyle("A$count_sections:AA$count_sections")
              ->getAlignment()
              ->setWrapText(true);
        $count_sections = 4; // разделы ---- >> N ROW!
        $sheet->fromArray($FIELDS1, NULL, "A$count_sections");//заголовки столбцов
        $sheet->getStyle("A$count_sections:AA$count_sections")
              ->applyFromArray(['font' => ['bold' => true]]); // выделяем жирным до АА+номер строки
        $sheet->getStyle("A$count_sections:AA$count_sections")
              ->getAlignment()
              ->setWrapText(true);
        $count_sections = 5; // разделы ---- >> N ROW!
        $sheet->fromArray($FIELDS2, NULL, "A$count_sections");//заголовки столбцов
        $sheet->getStyle("A$count_sections:AA$count_sections")
              ->applyFromArray(['font' => ['bold' => true]]); // выделяем жирным до АА+номер строки
        $sheet->getStyle("A$count_sections:AA$count_sections")
              ->getAlignment()
              ->setWrapText(true);
        $sheet->mergeCellsByColumnAndRow(1, 1, 15, 1); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(1, 2, 15, 2); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(3, 2, 12, 2); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(1, 3, 1, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(2, 3, 2, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(15, 3, 15, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(3, 3, 14, 3); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(6, 4, 9, 4); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(3, 4, 3, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(4, 4, 4, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(5, 4, 5, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(10, 4, 10, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(11, 4, 11, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(12, 4, 12, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(13, 4, 13, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(14, 4, 14, 5); //объединение ячеек;
        $count_sections = 6; // разделы ---- >> N ROW!

        foreach ($rez as $key => $val) {
            $sheet->setCellValue('A' . $count_sections, $val['Org_Number']);
            $sheet->setCellValue('B' . $count_sections, $val['Org_Name']);
            $sheet->getStyle("B$count_sections:B$count_sections")
                  ->getAlignment()
                  ->setWrapText(true);
            $sheet->getStyle("O$count_sections:O$count_sections")
                  ->getAlignment()
                  ->setWrapText(true); //заголовки столбцов
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
                $sheet->setCellValue('N' . $count_sections, $val['Jornal']->getDie());
                $sheet->setCellValue('O' . $count_sections, $val['Jornal']->getNote());
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
                $sheet->setCellValue('O' . $count_sections, '-');
            }
            $sheet->getRowDimension($count_sections)
                  ->setRowHeight(60);
            $count_sections++;
        }

        $prevRow = $count_sections - 1;

        $sheet->setCellValue('B' . $count_sections, 'Итого');
        $sheet->setCellValue('C' . $count_sections, sprintf('=SUM(C6:C%s)', $prevRow));
        $sheet->setCellValue('D' . $count_sections, sprintf('=SUM(D6:D%s)', $prevRow));
        $sheet->setCellValue('E' . $count_sections, sprintf('=SUM(E6:E%s)', $prevRow));
        $sheet->setCellValue('F' . $count_sections, sprintf('=SUM(F6:F%s)', $prevRow));
        $sheet->setCellValue('G' . $count_sections, sprintf('=SUM(G6:G%s)', $prevRow));
        $sheet->setCellValue('H' . $count_sections, sprintf('=SUM(H6:H%s)', $prevRow));
        $sheet->setCellValue('I' . $count_sections, sprintf('=SUM(I6:I%s)', $prevRow));
        $sheet->setCellValue('J' . $count_sections, sprintf('=SUM(J6:J%s)', $prevRow));
        $sheet->setCellValue('K' . $count_sections, sprintf('=SUM(K6:K%s)', $prevRow));
        $sheet->setCellValue('L' . $count_sections, sprintf('=SUM(L6:L%s)', $prevRow));
        $sheet->setCellValue('M' . $count_sections, sprintf('=SUM(M6:M%s)', $prevRow));
        $sheet->setCellValue('N' . $count_sections, sprintf('=SUM(N6:N%s)', $prevRow));

        $count_sections = $count_sections - 1;
        $sheet->getStyle("A3:N3")
              ->applyFromArray([
                                   'alignment' => [
                                       'vertical' => Alignment::VERTICAL_CENTER,
                                       'horizontal' => Alignment::HORIZONTAL_CENTER
                                   ]

                               ]
              );
        $sheet->getStyle("C4:N4")
              ->applyFromArray([
                                   'alignment' => [
                                       'vertical' => Alignment::VERTICAL_CENTER,
                                       'horizontal' => Alignment::HORIZONTAL_CENTER
                                   ]

                               ]
              );
        $sheet->getStyle("F5:I5")
              ->applyFromArray([
                                   'alignment' => [
                                       'vertical' => Alignment::VERTICAL_CENTER,
                                       'horizontal' => Alignment::HORIZONTAL_CENTER
                                   ]

                               ]
              );
        $sheet->getStyle("A3:A$count_sections")
              ->applyFromArray([
                                   'alignment' => [
                                       'horizontal' => Alignment::HORIZONTAL_CENTER
                                   ]

                               ]
              );
        $sheet->getStyle("C6:O$count_sections")
              ->applyFromArray([
                                   'alignment' => [
                                       'horizontal' => Alignment::HORIZONTAL_CENTER
                                   ]

                               ]
              );
        $sheet->getStyle("C2:C2")
              ->applyFromArray([
                                   'alignment' => [
                                       'horizontal' => Alignment::HORIZONTAL_CENTER
                                   ]

                               ]
              );
        $sheet->getStyle("A3:O$count_sections")
              ->applyFromArray([
                                   'borders' => [
                                       'allBorders' => [
                                           'borderStyle' => Border::BORDER_THIN,
                                           'color' => ['argb' => '00000000']
                                       ]
                                   ]
                               ]);
        $sheet->getStyle("O6:O$count_sections")
              ->getFont()
              ->setSize(10);
        $sheet->getRowDimension(4)->setRowHeight(30);
        $sheet->getRowDimension(5)->setRowHeight(64.90);

        $count_sections++;
        $sheet->getStyle("B$count_sections:N$count_sections")
              ->applyFromArray([
                                   'borders' => [
                                       'allBorders' => [
                                           'borderStyle' => Border::BORDER_MEDIUM,
                                           'color' => ['argb' => '00000000']
                                       ]
                                   ]
                               ]);

        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Контакты');
        $count_sections = 1;
        foreach ($org as $key => $val) {
            $sheet2->getStyle("A$count_sections:A$count_sections")
                   ->getAlignment()
                   ->setWrapText(true);
            $sheet2->getStyle("B$count_sections:B$count_sections")
                   ->getAlignment()
                   ->setWrapText(true);
            $sheet2->setCellValue('A' . $count_sections, $val->getName());
            $sheet2->setCellValue('B' . $count_sections, $val->getContact());
            $sheet2->getRowDimension($count_sections)
                   ->setRowHeight(80);
            $count_sections++;
        }
        $sheet2->getColumnDimension('A')
               ->setWidth(45);
        $sheet2->getColumnDimension('B')
               ->setWidth(145);
        $sheet2->getStyle("A1:B$count_sections")
               ->applyFromArray([
                                    'borders' => [
                                        'allBorders' => [
                                            'borderStyle' => Border::BORDER_THIN,
                                            'color' => ['argb' => '00000000']
                                        ]
                                    ]
                                ]);
        $sheet->getStyle("A1:A1")
              ->applyFromArray([
                                   'alignment' => [
                                       'horizontal' => Alignment::HORIZONTAL_CENTER
                                   ]
                               ]
              );
        $writer = new Xlsx($spreadsheet);

        $writer->save($filename);
    }
}