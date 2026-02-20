<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Shoaib3375\PhpDocExporter\DocumentExporter;

class ExportController extends Controller
{
    /**
     * Export invoice using Blade template
     */
    public function exportInvoice(Request $request)
    {
        $token = $request->bearerToken();
        
        $data = [
            'title' => 'Sales Invoice',
            'invoiceNumber' => 'INV-2024-001',
            'customerName' => 'মোহাম্মদ শোয়েব',
            'date' => date('Y-m-d'),
            'items' => [
                ['name' => 'Product A', 'quantity' => 2, 'price' => 500],
                ['name' => 'পণ্য বি', 'quantity' => 1, 'price' => 1000],
            ],
            'total' => 2000
        ];

        $exporter = new DocumentExporter();
        $content = $exporter->exportFromView('pdf', 'invoice', $data, [
            'paper' => 'A4',
            'orientation' => 'portrait'
        ], $token);

        return response($content)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="invoice.pdf"');
    }

    /**
     * Export report using array data (original method)
     */
    public function exportReport(Request $request)
    {
        $token = $request->bearerToken();
        
        $data = [
            ['name' => 'Shoaib', 'age' => 25, 'city' => 'Dhaka'],
            ['name' => 'মাইনুল', 'age' => 30, 'city' => 'Sylhet']
        ];

        $exporter = new DocumentExporter();
        $format = $request->input('format', 'pdf');
        $content = $exporter->export($format, $data, [], $token);

        $extension = match($format) {
            'excel' => 'xlsx',
            'word' => 'docx',
            default => $format
        };

        return response($content)
            ->header('Content-Type', $this->getMimeType($format))
            ->header('Content-Disposition', "attachment; filename=\"report.{$extension}\"");
    }

    private function getMimeType($format)
    {
        return match($format) {
            'pdf' => 'application/pdf',
            'excel' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'word' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'csv' => 'text/csv',
        };
    }
}
