# Architecture & Flow Diagrams

## System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     Client Application                       │
│  (Web App, Mobile App, External Service)                    │
└────────────────────────┬────────────────────────────────────┘
                         │
                         │ HTTP Request + Bearer Token
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                    Laravel API Routes                        │
│  Route::post('/export/invoice', [Controller::class])        │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                   Export Controller                          │
│  - Validates request                                         │
│  - Extracts bearer token                                     │
│  - Prepares data                                             │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                  DocumentExporter Class                      │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  exportFromView(format, view, data, options, token)  │   │
│  │  - Validates token                                    │   │
│  │  - Renders Blade template                            │   │
│  │  - Calls PdfExporter                                 │   │
│  └──────────────────────────────────────────────────────┘   │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                    Blade Template Engine                     │
│  - Loads view file (invoice.blade.php)                      │
│  - Processes Blade directives (@foreach, @if, etc.)         │
│  - Renders HTML with data                                   │
└────────────────────────┬────────────────────────────────────┘
                         │
                         │ HTML String
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                      PdfExporter                             │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  exportFromHtml(html, options)                       │   │
│  │  - Configures Dompdf                                 │   │
│  │  - Sets DejaVu Sans font (Bangla support)           │   │
│  │  - Converts HTML to PDF                              │   │
│  └──────────────────────────────────────────────────────┘   │
└────────────────────────┬────────────────────────────────────┘
                         │
                         │ PDF Binary Content
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                    Response Handler                          │
│  - Sets Content-Type: application/pdf                       │
│  - Sets Content-Disposition: attachment                     │
│  - Returns PDF to client                                    │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                    Client Receives PDF                       │
│  - Downloads file                                            │
│  - Displays in browser                                       │
│  - Saves to storage                                          │
└─────────────────────────────────────────────────────────────┘
```

## Request Flow with Token Authentication

```
┌──────────┐
│  Client  │
└────┬─────┘
     │
     │ POST /api/export/invoice
     │ Authorization: Bearer abc123xyz
     │ Body: { "invoiceId": 123 }
     │
     ▼
┌────────────────┐
│  Middleware    │ ◄── Validates Sanctum token
│  (auth:sanctum)│
└────┬───────────┘
     │ ✓ Authenticated
     │
     ▼
┌────────────────┐
│  Controller    │
│  - Get token   │
│  - Fetch data  │
└────┬───────────┘
     │
     ▼
┌────────────────────┐
│  DocumentExporter  │
│  - Validate token  │ ◄── Checks PHP_DOC_EXPORTER_SAFE_TOKEN
│  - Render view     │
└────┬───────────────┘
     │ ✓ Token valid
     │
     ▼
┌────────────────┐
│  Blade Engine  │
│  - Load view   │
│  - Render HTML │
└────┬───────────┘
     │
     ▼
┌────────────────┐
│  PdfExporter   │
│  - HTML to PDF │
│  - Apply font  │
└────┬───────────┘
     │
     ▼
┌────────────────┐
│  Response      │
│  - PDF binary  │
│  - Headers     │
└────┬───────────┘
     │
     ▼
┌──────────┐
│  Client  │ ◄── Downloads invoice.pdf
└──────────┘
```

## Data Flow: Array vs Blade Template

### Array-Based Export (Original)
```
Array Data
    │
    ▼
DocumentExporter.export()
    │
    ▼
PdfExporter.export()
    │
    ▼
generateHtml() ◄── Generates simple table HTML
    │
    ▼
Dompdf
    │
    ▼
PDF Output
```

### Blade Template Export (New)
```
Blade View + Data
    │
    ▼
DocumentExporter.exportFromView()
    │
    ▼
Blade::render() ◄── Custom HTML with full control
    │
    ▼
PdfExporter.exportFromHtml()
    │
    ▼
Dompdf
    │
    ▼
PDF Output
```

## Token Validation Flow

```
┌─────────────────┐
│  Request Token  │
└────────┬────────┘
         │
         ▼
┌─────────────────────────┐
│  Config::canAccessSafeApi│
└────────┬────────────────┘
         │
         ├─── Compare with MAIN_TOKEN ──┐
         │                               │
         └─── Compare with SAFE_TOKEN ──┤
                                         │
                                         ▼
                              ┌──────────────────┐
                              │  Match Found?    │
                              └────┬─────────┬───┘
                                   │         │
                              YES  │         │  NO
                                   │         │
                                   ▼         ▼
                          ┌──────────┐  ┌──────────────────┐
                          │ Continue │  │ Throw Exception  │
                          └──────────┘  │ InvalidToken     │
                                        └──────────────────┘
```

## Component Interaction

```
┌──────────────────────────────────────────────────────────┐
│                    DocumentExporter                       │
│  ┌────────────────┐  ┌──────────────────────────────┐   │
│  │ export()       │  │ exportFromView()             │   │
│  │ (Array data)   │  │ (Blade template)             │   │
│  └───────┬────────┘  └──────────┬───────────────────┘   │
│          │                       │                        │
│          │                       │                        │
└──────────┼───────────────────────┼────────────────────────┘
           │                       │
           │                       ▼
           │              ┌─────────────────┐
           │              │ Blade Engine    │
           │              │ view()->render()│
           │              └────────┬────────┘
           │                       │
           │                       │ HTML
           │                       │
           ▼                       ▼
    ┌──────────────────────────────────────┐
    │         PdfExporter                   │
    │  ┌────────────────┐  ┌──────────────┐│
    │  │ export()       │  │exportFromHtml││
    │  │ (generates HTML)│  │(uses HTML)   ││
    │  └───────┬────────┘  └──────┬───────┘│
    │          │                   │        │
    │          └───────┬───────────┘        │
    │                  │                    │
    │                  ▼                    │
    │          ┌──────────────┐            │
    │          │   Dompdf     │            │
    │          │ (HTML to PDF)│            │
    │          └──────┬───────┘            │
    └─────────────────┼────────────────────┘
                      │
                      ▼
                 PDF Binary
```

## Use Case Scenarios

### Scenario 1: Simple Report Export
```
User Request → Array Data → export() → PDF
```

### Scenario 2: Custom Invoice with Blade
```
User Request → Blade Template + Data → exportFromView() → PDF
```

### Scenario 3: API with Authentication
```
API Request + Token → Validate → Blade Template → PDF → Response
```

### Scenario 4: Multi-Format Export
```
Same Data → export('pdf') → PDF
          → export('excel') → XLSX
          → export('word') → DOCX
          → export('csv') → CSV
```

## Error Handling Flow

```
┌─────────────────┐
│  Export Request │
└────────┬────────┘
         │
         ▼
┌─────────────────────┐
│  Validate Token     │
└────┬───────────┬────┘
     │           │
  Valid      Invalid
     │           │
     ▼           ▼
┌─────────┐  ┌──────────────────────┐
│Continue │  │InvalidTokenException │
└────┬────┘  └──────────────────────┘
     │
     ▼
┌─────────────────────┐
│  Check Data/View    │
└────┬───────────┬────┘
     │           │
  Valid      Invalid
     │           │
     ▼           ▼
┌─────────┐  ┌──────────────────────┐
│Continue │  │EmptyDataException    │
└────┬────┘  │InvalidFormatException│
     │        └──────────────────────┘
     ▼
┌─────────────────────┐
│  Generate Document  │
└────┬────────────────┘
     │
     ▼
┌─────────────────────┐
│  Return PDF         │
└─────────────────────┘
```

## Performance Considerations

```
Array Export:
  Data → Generate HTML (Fast) → PDF (Medium) → Output
  Time: ~100-500ms

Blade Export:
  Data → Render Blade (Medium) → PDF (Medium) → Output
  Time: ~150-600ms

Factors:
  - Data size
  - Template complexity
  - Image count
  - Font loading (DejaVu Sans cached)
```

## Deployment Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    Production Server                     │
│  ┌───────────────────────────────────────────────────┐  │
│  │              Laravel Application                   │  │
│  │  ┌─────────────────────────────────────────────┐  │  │
│  │  │  php-doc-exporter Package                   │  │  │
│  │  │  - DocumentExporter                         │  │  │
│  │  │  - PdfExporter                              │  │  │
│  │  │  - Blade Templates                          │  │  │
│  │  └─────────────────────────────────────────────┘  │  │
│  │                                                     │  │
│  │  ┌─────────────────────────────────────────────┐  │  │
│  │  │  .env Configuration                         │  │  │
│  │  │  PHP_DOC_EXPORTER_MAIN_TOKEN=xxx           │  │  │
│  │  │  PHP_DOC_EXPORTER_SAFE_TOKEN=yyy           │  │  │
│  │  └─────────────────────────────────────────────┘  │  │
│  └───────────────────────────────────────────────────┘  │
│                                                          │
│  ┌───────────────────────────────────────────────────┐  │
│  │              Web Server (Nginx/Apache)            │  │
│  │  - HTTPS enabled                                  │  │
│  │  - Rate limiting                                  │  │
│  └───────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────┘
                         │
                         │ HTTPS
                         │
                         ▼
┌─────────────────────────────────────────────────────────┐
│                    API Clients                           │
│  - Web Applications                                      │
│  - Mobile Apps                                           │
│  - Third-party Services                                  │
└─────────────────────────────────────────────────────────┘
```
