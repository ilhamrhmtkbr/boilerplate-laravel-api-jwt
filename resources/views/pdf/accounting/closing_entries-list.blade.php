<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; }
        .page { padding: 20px 30px; }
        .header { display: flex; justify-content: space-between; border-bottom: 3px solid #1F4E79; padding-bottom: 12px; margin-bottom: 20px; }
        .company-name { font-size: 18px; font-weight: bold; color: #1F4E79; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        thead th { background: #1F4E79; color: #fff; padding: 7px 8px; text-align: left; }
        tbody td { padding: 6px 8px; border-bottom: 1px solid #eee; }
        tbody tr:nth-child(even) td { background: #f8f9fa; }
        .footer { margin-top: 30px; border-top: 1px solid #ddd; padding-top: 10px; text-align: center; font-size: 9px; color: #999; }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <div>
                <div class="company-name">ERP System</div>
                <div>{{ $title }}</div>
            </div>
            <div style="text-align:right; font-size:10px; color:#666">
                <div>Date: {{ now()->format('d F Y') }}</div>
                <div>Total: {{ count($items) }} records</div>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width:40px">No.</th>
                    <th>Name</th>
                    {{-- TODO: add more columns --}}
                    <th style="width:120px">Created At</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $i => $item)
                    @php $data = is_object($item) && method_exists($item, 'toArray') ? $item->toArray() : (array) $item; @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $data['name'] ?? '-' }}</td>
                        <td>{{ $data['created_at'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="text-align:center; color:#999; padding:20px">No data available.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="footer">ERP System &mdash; Generated on {{ now()->format('d/m/Y H:i:s') }}</div>
    </div>
</body>
</html>
