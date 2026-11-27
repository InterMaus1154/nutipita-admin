{{-- Stand-in for pdf.invoice, used by the InvoicePdfService tests so they exercise the service, not the template --}}
<html>
<body>
<h1>{{ $data->invoice->invoice_number }}</h1>
</body>
</html>
