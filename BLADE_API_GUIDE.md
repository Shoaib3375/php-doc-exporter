# Blade Template + API Usage Guide

## Setup

### 1. Install the Package
```bash
composer require shoaib3375/php-doc-exporter
```

### 2. Configure Tokens (Optional)
Add to `.env`:
```env
PHP_DOC_EXPORTER_MAIN_TOKEN=your-main-token-here
PHP_DOC_EXPORTER_SAFE_TOKEN=your-safe-token-here
```

### 3. Create Blade Template
Create `resources/views/reports/invoice.blade.php`:

```blade
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; margin: 40px; }
        .header { text-align: center; margin-bottom: 30px; }
        .info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #333; padding: 10px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .total { text-align: right; font-size: 18px; margin-top: 20px; }
        .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $companyName ?? 'Company Name' }}</h1>
        <h2>{{ $title ?? 'Invoice' }}</h2>
    </div>

    <div class="info">
        <p><strong>Invoice Number:</strong> {{ $invoiceNumber }}</p>
        <p><strong>Date:</strong> {{ $date }}</p>
        <p><strong>Customer:</strong> {{ $customerName }}</p>
        @if(isset($customerAddress))
        <p><strong>Address:</strong> {{ $customerAddress }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item['name'] }}</td>
                <td>{{ $item['description'] ?? '-' }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td>{{ number_format($item['price'], 2) }}</td>
                <td>{{ number_format($item['quantity'] * $item['price'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        <p><strong>Subtotal:</strong> {{ number_format($subtotal, 2) }}</p>
        @if(isset($tax))
        <p><strong>Tax ({{ $taxRate }}%):</strong> {{ number_format($tax, 2) }}</p>
        @endif
        <p style="font-size: 20px;"><strong>Total:</strong> {{ number_format($total, 2) }}</p>
    </div>

    <div class="footer">
        <p>Thank you for your business!</p>
        @if(isset($footerNote))
        <p>{{ $footerNote }}</p>
        @endif
    </div>
</body>
</html>
```

## API Implementation

### Controller
Create `app/Http/Controllers/Api/DocumentController.php`:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Shoaib3375\PhpDocExporter\DocumentExporter;

class DocumentController extends Controller
{
    /**
     * Export invoice with Blade template
     * 
     * POST /api/export/invoice
     * Headers: Authorization: Bearer {token}
     * Body: { "invoiceId": 123 }
     */
    public function exportInvoice(Request $request)
    {
        $token = $request->bearerToken();
        
        // Fetch invoice data from database
        $invoice = $this->getInvoiceData($request->input('invoiceId'));
        
        $exporter = new DocumentExporter();
        
        try {
            $content = $exporter->exportFromView(
                'pdf',
                'reports.invoice',
                $invoice,
                [
                    'paper' => 'A4',
                    'orientation' => 'portrait'
                ],
                $token
            );
            
            return response($content)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="invoice-' . $invoice['invoiceNumber'] . '.pdf"');
                
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Export report with array data
     * 
     * POST /api/export/report
     * Headers: Authorization: Bearer {token}
     * Body: { "format": "pdf", "type": "sales" }
     */
    public function exportReport(Request $request)
    {
        $token = $request->bearerToken();
        $format = $request->input('format', 'pdf');
        
        // Fetch report data
        $data = $this->getReportData($request->input('type'));
        
        $exporter = new DocumentExporter();
        
        try {
            $content = $exporter->export($format, $data, [], $token);
            
            $extension = match($format) {
                'excel' => 'xlsx',
                'word' => 'docx',
                default => $format
            };
            
            return response($content)
                ->header('Content-Type', $this->getMimeType($format))
                ->header('Content-Disposition', "attachment; filename=\"report.{$extension}\"");
                
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    
    private function getInvoiceData($invoiceId)
    {
        // Example data - replace with actual database query
        return [
            'companyName' => 'ABC Company Ltd.',
            'title' => 'Sales Invoice',
            'invoiceNumber' => 'INV-2024-' . str_pad($invoiceId, 4, '0', STR_PAD_LEFT),
            'date' => date('F d, Y'),
            'customerName' => 'মোহাম্মদ শোয়েব',
            'customerAddress' => 'Dhaka, Bangladesh',
            'items' => [
                [
                    'name' => 'Product A',
                    'description' => 'High quality product',
                    'quantity' => 2,
                    'price' => 500.00
                ],
                [
                    'name' => 'পণ্য বি',
                    'description' => 'বাংলা পণ্য',
                    'quantity' => 1,
                    'price' => 1000.00
                ],
            ],
            'subtotal' => 2000.00,
            'taxRate' => 15,
            'tax' => 300.00,
            'total' => 2300.00,
            'footerNote' => 'Payment due within 30 days'
        ];
    }
    
    private function getReportData($type)
    {
        // Example data
        return [
            ['name' => 'Shoaib', 'sales' => 5000, 'region' => 'Dhaka'],
            ['name' => 'মাইনুল', 'sales' => 7000, 'region' => 'Sylhet'],
        ];
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
```

### Routes
Add to `routes/api.php`:

```php
use App\Http\Controllers\Api\DocumentController;

Route::middleware('auth:sanctum')->prefix('export')->group(function () {
    Route::post('/invoice', [DocumentController::class, 'exportInvoice']);
    Route::post('/report', [DocumentController::class, 'exportReport']);
});
```

## API Usage Examples

### Using cURL

```bash
# Export invoice with Blade template
curl -X POST https://your-api.com/api/export/invoice \
  -H "Authorization: Bearer your-token-here" \
  -H "Content-Type: application/json" \
  -d '{"invoiceId": 123}' \
  --output invoice.pdf

# Export report as Excel
curl -X POST https://your-api.com/api/export/report \
  -H "Authorization: Bearer your-token-here" \
  -H "Content-Type: application/json" \
  -d '{"format": "excel", "type": "sales"}' \
  --output report.xlsx
```

### Using JavaScript (Fetch API)

```javascript
// Export invoice
async function exportInvoice(invoiceId) {
    const response = await fetch('https://your-api.com/api/export/invoice', {
        method: 'POST',
        headers: {
            'Authorization': 'Bearer your-token-here',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ invoiceId })
    });
    
    const blob = await response.blob();
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `invoice-${invoiceId}.pdf`;
    a.click();
}

// Export report
async function exportReport(format, type) {
    const response = await fetch('https://your-api.com/api/export/report', {
        method: 'POST',
        headers: {
            'Authorization': 'Bearer your-token-here',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ format, type })
    });
    
    const blob = await response.blob();
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `report.${format === 'excel' ? 'xlsx' : format}`;
    a.click();
}
```

### Using Postman

1. **Method**: POST
2. **URL**: `https://your-api.com/api/export/invoice`
3. **Headers**:
   - `Authorization`: `Bearer your-token-here`
   - `Content-Type`: `application/json`
4. **Body** (raw JSON):
   ```json
   {
       "invoiceId": 123
   }
   ```
5. **Send & Save Response**: Click "Send and Download" to save the PDF

## Security Best Practices

1. **Always use HTTPS** in production
2. **Validate tokens** on every request
3. **Rate limit** export endpoints
4. **Sanitize user input** before passing to templates
5. **Log export activities** for audit trails

## Troubleshooting

### Bangla characters showing as boxes
- Ensure your Blade template uses `DejaVu Sans` font
- Add charset meta tag: `<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>`

### Token validation fails
- Check `.env` configuration
- Verify token is passed correctly in Authorization header
- Ensure token matches `PHP_DOC_EXPORTER_SAFE_TOKEN` or `PHP_DOC_EXPORTER_MAIN_TOKEN`

### PDF layout issues
- Test your Blade template HTML separately
- Use inline CSS (external stylesheets may not work)
- Avoid complex CSS features (flexbox, grid)
