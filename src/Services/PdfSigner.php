<?php

namespace App\Services;

use setasign\Fpdi\Tcpdf\Fpdi;

class PdfSigner
{
   public function __construct(
      private $certFile, 
      private $privateKeyFile, 
      private $privateKeyFilePassword)
   {}

   public function signPdf($inputFile = 'file:///path/to/volante.pdf', $outputFile = 'file:///path/to/volante-signed.pdf'): void
   {
      // Cargar el PDF existente
      $pdf = new Fpdi();
      $pageCount = $pdf->setSourceFile($inputFile);
      // Importar las páginas del PDF
      for ($i = 1; $i <= $pageCount; $i++) {
         $pdf->AddPage();
         $pdf->useTemplate($pdf->importPage($i));
      }
      // Anadir la firma
      $pdf->setSignature($this->certFile,$this->privateKeyFile,$this->privateKeyFilePassword,'', 1);
      $pdf->Output($outputFile,'F');
   }
}