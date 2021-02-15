<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\Journal;
use App\Service\JournalExcel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SaveExcelCommand extends Command
{
    private JournalExcel $je;

    public function __construct(JournalExcel $je, string $name = 'excel:save')
    {
        $this->je = $je;

        parent::__construct($name);
    }

    public function configure()
    {
        $this
            ->addArgument('date', InputArgument::REQUIRED, 'Дата')
            ->addArgument('folder', InputArgument::OPTIONAL, 'Диретория, куда будет сохранен файл', $_ENV['EXCEL_DIR']);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
	        $io = new SymfonyStyle($input, $output);

	        $date = $input->getArgument('date');
        	$folder = $input->getArgument('folder');

            $date = new \DateTime($date);

	        $filters = [
        	    'date' => $date,
	            'isActive' => true,
        	    'headOffice' => null
	        ];

        	$y = $date->format('d.m.Y');
	        $s = "Доклад ОШ $y.xlsx";

	        $filename = $folder . $s;

	        $io->title('Сохранение EXCEL файла');
	        $io->block('Путь файла: ' . $filename);

        	$this->je->save($date, $filename);

	        $io->success('Файл успешно сохранен');

	        return 0;
        } catch (\Throwable $e) {
            $d = new DateTime();
            file_put_contents('C:\BackUp\#php.log', "\r\n\r\n\r\n");
     	    file_put_contents('C:\BackUp\#php.log', print_r('Ошибка ExcelCovid' . $d->format('d.m.Y H:i:s') . 'mess: ' . $e->getMessage() . 'Trace: ' . $e->getTraceAsString(),true), FILE_APPEND); 
            $io->error('Ошибка: ' . $e->getMessage());
            return 1;
        }

    }
}