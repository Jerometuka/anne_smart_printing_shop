<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Debug uploads</title>
    <link rel="stylesheet" href="{{ asset('css/debug_orders.css') }}">
</head>
<body>
    <h2>Columns ng orders table</h2>
    <p>{{ implode(', ', $cols) }}</p>
    <p>May "document_file" column? <b class="{{ in_array('document_file', $cols) ? 'ok' : 'bad' }}">{{ in_array('document_file', $cols) ? 'OO' : 'WALA - ito ang problema!' }}</b></p>

    <h2>Huling 10 orders</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Code</th>
            <th>Created</th>
            <th>document_file </th>
            <th>in uploads/?</th>
        </tr>
        @foreach ($rows as $r)
            @php $f = $r->document_file ?? ''; @endphp
            <tr>
                <td>{{ $r->order_id }}</td>
                <td>{{ $r->order_code }}</td>
                <td>{{ $r->created_at }}</td>
                <td>{!! $f === '' || $f === null ? '<span class="bad">WALANG LAMAN (NULL)</span>' : e($f) !!}</td>
                <td>{!! $f ? (file_exists(public_path('uploads/' . $f)) ? '<span class="ok">Oo</span>' : '<span class="bad">Wala</span>') : '-' !!}</td>
            </tr>
        @endforeach
    </table>

    <h2>Mga file sa uploads/ folder (pinakabago)</h2>
    <p>{!! $files ? e(implode(' | ', $files)) : '<span class="bad">Walang uploads folder o walang laman</span>' !!}</p>
</body>
</html>