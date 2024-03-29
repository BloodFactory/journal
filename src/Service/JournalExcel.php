<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Journal;
use App\Entity\Organization;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class JournalExcel
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param DateTimeImmutable $date
     * @param string $filename
     * @param User|null $user
     * @param bool $control
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function save(DateTimeImmutable $date, string $filename, ?User $user = null, bool $control = false): void
    {
        $prevDate = $date->sub(new \DateInterval('P1D'));

        $journal = $this->getJournal($date, true);
        $journal1 = $this->getJournal($date);
        $org = $this->em->getRepository(Organization::class)
                        ->createQueryBuilder('o')
                        ->addOrderBy('o.sort', 'ASC')
                        ->andWhere('o.isActive = 1')
                        ->getQuery()
                        ->getResult();

        $journalPrev = $this->getJournal($prevDate, true);
        $journal1Prev = $this->getJournal($prevDate);

        $rez = [];
        $j = 0;
        $i = 1;
        foreach ($org as $key => $val) {
            $rez[$j]['Org_Id'] = $val->getId();
            $rez[$j]['Org_Name'] = $val->getName();
            $rez[$j]['Org_Number'] = (string)$i;

            foreach ($journal as $key1 => $val1) {
                if ($val1->getOrganization()->getId() === $val->getId()) {
                    $rez[$j]['Jornal'] = $val1;
                    unset($journal[$key1]);
                    break;
                }
            }

            foreach ($journalPrev as $key1 => $val1) {
                if ($val1->getOrganization()->getId() === $val->getId()) {
                    $rez[$j]['JornalPrev'] = $val1;
                    unset($journalPrev[$key1]);
                    break;
                }
            }

            if ($val->getBranches()) {
                $j++;
                $rez[$j]['Org_Id'] = $val->getId();
                $rez[$j]['Org_Name'] = 'Филиалы ' . $val->getName();
                $rez[$j]['Org_Number'] = (string)$i . '.1';
                foreach ($journal1 as $key1 => $val1) {
                    if ($val1->getHeadOffice()->getOrganization()->getId() == $val->getId()) {
                        $rez[$j]['Jornal'] = $val1;
                        unset($journal1[$key1]);
                        break;
                    }
                }

                foreach ($journal1Prev as $key1 => $val1) {
                    if ($val1->getHeadOffice()->getOrganization()->getId() == $val->getId()) {
                        $rez[$j]['JornalPrev'] = $val1;
                        unset($journal1Prev[$key1]);
                        break;
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
        $sheet->getColumnDimension('C')->setWidth(9.82);
        $sheet->getColumnDimension('D')->setWidth(8.72);
        $sheet->getColumnDimension('E')->setWidth(9.82);
        $sheet->getColumnDimension('F')->setWidth(9.82);
        $sheet->getColumnDimension('G')->setWidth(9.82);
        $sheet->getColumnDimension('H')->setWidth(9.82);
        $sheet->getColumnDimension('I')->setWidth(9.82);
        $sheet->getColumnDimension('J')->setWidth(9.82);
        $sheet->getColumnDimension('K')->setWidth(9.82);
        $sheet->getColumnDimension('L')->setWidth(9.82);
        $sheet->getColumnDimension('M')->setWidth(44.06);

        $FIELDS = ["N пп", "Организация", "Количество работников", "", "", "", "", "", "", "", "", "", "Примечание"];
        $FIELDS1 = ["", "", "фактическая численность", "на рабочем месте", "в отпуске", "на дистанционной форме работы", "на карантине", "на больничном", "болеющих\r\n(COVID-19)", "", "Выходной/ межвахтовый отдых", "Скончалось от COVID-19 (нарастающим итогом)", ""];
        $FIELDS2 = ["", "", "", "", "", "", "", "", "Вчера", "Сегодня", "", "", ""];
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
        $sheet->mergeCellsByColumnAndRow(1, 1, 13, 1); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(1, 2, 13, 2); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(1, 3, 1, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(2, 3, 2, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(13, 3, 13, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(3, 3, 12, 3); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(3, 4, 3, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(4, 4, 4, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(5, 4, 5, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(6, 4, 6, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(7, 4, 7, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(8, 4, 8, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(9, 4, 10, 4); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(11, 4, 11, 5); //объединение ячеек;
        $sheet->mergeCellsByColumnAndRow(12, 4, 12, 5); //объединение ячеек;
        $count_sections = 6; // разделы ---- >> N ROW!

        if ($control) {
            $total = [
                'getTotal' => [0, 0],
                'getAtWork' => [0, 0],
                'getOnHoliday' => [0, 0],
                'getRemoteTotal' => [0, 0],
                'getRemotePregnant' => [0, 0],
                'getRemoteWithChildren' => [0, 0],
                'getRemoteOver60' => [0, 0],
                'getOnTwoWeekQuarantine' => [0, 0],
                'getOnSickLeave' => [0, 0],
                'getSickCOVIDPrev' => [0, 0],
                'getSickCOVID' => [0, 0],
                'getShiftRest' => [0, 0],
                'getDie' => [0, 0],
            ];
        }

        foreach ($rez as $key => $val) {
            $sheet->setCellValue('A' . $count_sections, $val['Org_Number']);
            $sheet->setCellValue('B' . $count_sections, $val['Org_Name']);
            $sheet->getStyle("B$count_sections:B$count_sections")
                  ->getAlignment()
                  ->setWrapText(true);
            $sheet->getStyle("K$count_sections:M$count_sections")
                  ->getAlignment()
                  ->setWrapText(true); //заголовки столбцов

            if (isset($val['Jornal'])) {
                if ($control) {
                    $this->generateControlCell($sheet, 'C' . $count_sections, $val, 'getTotal', $total);
                    $this->generateControlCell($sheet, 'D' . $count_sections, $val, 'getAtWork', $total);
                    $this->generateControlCell($sheet, 'E' . $count_sections, $val, 'getOnHoliday', $total);
                    $this->generateControlCell($sheet, 'F' . $count_sections, $val, 'getRemoteTotal', $total);
                    $this->generateControlCell($sheet, 'G' . $count_sections, $val, 'getOnTwoWeekQuarantine', $total);
                    $this->generateControlCell($sheet, 'H' . $count_sections, $val, 'getOnSickLeave', $total);
                    $this->generateControlCell($sheet, 'I' . $count_sections, $val, 'getSickCOVIDPrev', $total);
                    $this->generateControlCell($sheet, 'J' . $count_sections, $val, 'getSickCOVID', $total);
                    $this->generateControlCell($sheet, 'K' . $count_sections, $val, 'getShiftRest', $total);
                    $this->generateControlCell($sheet, 'L' . $count_sections, $val, 'getDie', $total);
                    $sheet->setCellValue('M' . $count_sections, $val['Jornal']->getNote());
                } else {
                    $sheet->setCellValue('C' . $count_sections, $val['Jornal']->getTotal());
                    $sheet->setCellValue('D' . $count_sections, $val['Jornal']->getAtWork());
                    $sheet->setCellValue('E' . $count_sections, $val['Jornal']->getOnHoliday());
                    $sheet->setCellValue('F' . $count_sections, $val['Jornal']->getRemoteTotal());
                    $sheet->setCellValue('G' . $count_sections, $val['Jornal']->getOnTwoWeekQuarantine());
                    $sheet->setCellValue('H' . $count_sections, $val['Jornal']->getOnSickLeave());
                    $sheet->setCellValue('I' . $count_sections, isset($val['JornalPrev']) ? $val['JornalPrev']->getSickCOVID() : '-');
                    $sheet->setCellValue('J' . $count_sections, $val['Jornal']->getSickCOVID());
                    $sheet->setCellValue('K' . $count_sections, $val['Jornal']->getShiftRest());
                    $sheet->setCellValue('L' . $count_sections, $val['Jornal']->getDie());
                    $sheet->setCellValue('M' . $count_sections, $val['Jornal']->getNote());
                }
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
            }
            $sheet->getRowDimension($count_sections)
                  ->setRowHeight(60);
            $count_sections++;
        }

        $prevRow = $count_sections - 1;

        $sheet->setCellValue('B' . $count_sections, 'Итого');
        if ($control) {
            $sheet->setCellValue('C' . $count_sections, $total['getTotal'][0] . ' (' . ($total['getTotal'][1] > 0 ? '+' : '') . $total['getTotal'][1] . ')');
            $sheet->setCellValue('D' . $count_sections, $total['getAtWork'][0] . ' (' . ($total['getAtWork'][1] > 0 ? '+' : '') . $total['getAtWork'][1] . ')');
            $sheet->setCellValue('E' . $count_sections, $total['getOnHoliday'][0] . ' (' . ($total['getOnHoliday'][1] > 0 ? '+' : '') . $total['getOnHoliday'][1] . ')');
            $sheet->setCellValue('F' . $count_sections, $total['getRemoteTotal'][0] . ' (' . ($total['getRemoteTotal'][1] > 0 ? '+' : '') . $total['getRemoteTotal'][1] . ')');
            $sheet->setCellValue('G' . $count_sections, $total['getOnTwoWeekQuarantine'][0] . ' (' . ($total['getOnTwoWeekQuarantine'][1] > 0 ? '+' : '') . $total['getOnTwoWeekQuarantine'][1] . ')');
            $sheet->setCellValue('H' . $count_sections, $total['getOnSickLeave'][0] . ' (' . ($total['getOnSickLeave'][1] > 0 ? '+' : '') . $total['getOnSickLeave'][1] . ')');
            $sheet->setCellValue('I' . $count_sections, $total['getSickCOVID'][0] . ' (' . ($total['getSickCOVID'][1] > 0 ? '+' : '') . $total['getSickCOVID'][1] . ')');
            $sheet->setCellValue('J' . $count_sections, $total['getSickCOVID'][0] . ' (' . ($total['getSickCOVID'][1] > 0 ? '+' : '') . $total['getSickCOVID'][1] . ')');
            $sheet->setCellValue('K' . $count_sections, $total['getShiftRest'][0] . ' (' . ($total['getShiftRest'][1] > 0 ? '+' : '') . $total['getShiftRest'][1] . ')');
            $sheet->setCellValue('L' . $count_sections, $total['getDie'][0] . ' (' . ($total['getDie'][1] > 0 ? '+' : '') . $total['getDie'][1] . ')');
        } else {
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
        }

        $count_sections = $count_sections - 1;
        $sheet->getStyle("A3:L3")
              ->applyFromArray([
                      'alignment' => [
                          'vertical' => Alignment::VERTICAL_CENTER,
                          'horizontal' => Alignment::HORIZONTAL_CENTER
                      ]

                  ]
              );
        $sheet->getStyle("C4:L4")
              ->applyFromArray([
                      'alignment' => [
                          'vertical' => Alignment::VERTICAL_CENTER,
                          'horizontal' => Alignment::HORIZONTAL_CENTER
                      ]

                  ]
              );
        $sheet->getStyle("I5:J5")
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
        $sheet->getStyle("C6:M$count_sections")
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
        $sheet->getStyle("A3:M$count_sections")
              ->applyFromArray([
                  'borders' => [
                      'allBorders' => [
                          'borderStyle' => Border::BORDER_THIN,
                          'color' => ['argb' => '00000000']
                      ]
                  ]
              ]);
        $sheet->getStyle("M6:K$count_sections")
              ->getFont()
              ->setSize(10);
        $sheet->getRowDimension(4)->setRowHeight(46);
        $sheet->getRowDimension(5)->setRowHeight(64.90);

        $count_sections++;
        $sheet->getStyle("B$count_sections:L$count_sections")
              ->applyFromArray([
                  'borders' => [
                      'allBorders' => [
                          'borderStyle' => Border::BORDER_MEDIUM,
                          'color' => ['argb' => '00000000']
                      ]
                  ]
              ]);

        $sheet->freezePaneByColumnAndRow(0, 6);

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

    private function getJournal(\DateTimeInterface $date, bool $org = false): array
    {
        $qb = $this->em->getRepository(Journal::class)
                       ->createQueryBuilder('j')
                       ->leftJoin('j.organization', 'o')
                       ->andWhere('j.date = :date')
                       ->setParameter('date', $date)
                       ->andWhere('j.isActive = 1')
                       ->addOrderBy('o.sort', 'ASC');

        if (false === $org) {
            $qb->andWhere('j.organization IS NULL');
        } else {
            $qb->andWhere('j.organization IS NOT NULL');
        }

        return $qb->getQuery()->getResult();
    }

    private function generateControlCell(Worksheet $sheet, string $cellIndex, array $val, string $method, array &$total): void
    {
        if ('getSickCOVIDPrev' === $method || 'getSickCOVID' === $method) {
            if ('getSickCOVID' === $method) {
                $total[$method][0] += call_user_func([$val['Jornal'], 'getSickCOVID']);
                $sheet->setCellValue($cellIndex, (string)call_user_func([$val['Jornal'], 'getSickCOVID']));
            }

            if ('getSickCOVIDPrev' === $method) {                
                if (isset($val['JornalPrev'])) {
                    $total[$method][0] += call_user_func([$val['JornalPrev'], 'getSickCOVID']);
                    $sheet->setCellValue($cellIndex, (string)call_user_func([$val['JornalPrev'], 'getSickCOVID']));
                } else {
                    $sheet->setCellValue($cellIndex,'-');
                }
            }
        } else {
            $total[$method][0] += call_user_func([$val['Jornal'], $method]);
            $txt = (string)call_user_func([$val['Jornal'], $method]);
            $color = 'default';

            if (isset($val['JornalPrev']) && call_user_func([$val['Jornal'], $method]) !== call_user_func([$val['JornalPrev'], $method])) {
                $diff = call_user_func([$val['Jornal'], $method]) - call_user_func([$val['JornalPrev'], $method]);
                $total[$method][1] += $diff;
                $txt .= ' (' . ($diff > 0 ? '+' : '') . $diff . ')';
                switch (true) {
                    case (abs($diff) <= 5):
                        $color = 'f0ffa7';
                        break;
                    case (abs($diff) > 5):
                        $color = 'ff8f8f';
                        break;
                }
            }

            $sheet->setCellValue($cellIndex, $txt);

            if ($color !== 'default') {
                $sheet->getStyle($cellIndex)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);
            }
        }


    }
}
