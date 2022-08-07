<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Tests\PdfToText;

use Symfony\Component\Process\Process;

/**
 * Extract the contents of a pdf file using pdftotext (apt-get install poppler-utils)
 */
class PdfToText
{
    private $pdftotext;

    public function __construct(string $pathPdfToText = '')
    {
        if ('' === $pathPdfToText) {
            $pathPdfToText = trim(strval(shell_exec('which pdftotext')));
            if ('' === $pathPdfToText) {
                throw new \RuntimeException('pdftotext command was not found');
            }
        }
        $this->pdftotext = $pathPdfToText;
    }

    /**
     * @param string $filename
     * @return string file contents
     */
    public function extract(string $filename): string
    {
        $process = new Process($this->buildCommand($filename));
        $exitStatus = $process->run();
        if (0 !== $exitStatus) {
            throw new \RuntimeException("Running pdftotext exit with error (exit status: $exitStatus)");
        }
        return $process->getOutput();
    }

    /**
     * @param string $pdfFile
     * @return string[]
     */
    public function buildCommand(string $pdfFile): array
    {
        return [$this->pdftotext, '-eol', 'unix', '-raw', '-q', $pdfFile, '-'];
    }
}
