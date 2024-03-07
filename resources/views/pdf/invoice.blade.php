<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZIE KOPERASI</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: sans-serif;
        }

        .container {
            width: 100vw;
            padding: 50px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 30px;
        }

        h1 {
            margin-bottom: 10px;
        }

        .content {
            width: 100%;
            color: #000;
        }

        table {
            width: 100%;
            border: 1px solid #000;
        }

        table tr {
            border: 1px solid #000;
        }

        table tr td,
        table tr th {
            border: 1px solid #000;
            padding: 5px;
            font-size: .7rem;
        }

        .center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Zie Koperasi Invoice {{ $data['time_invoice'] }}</h1>
        </div>
        <div class="content">
            <table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        @foreach ($data['sub_categories'] as $item)
                            <th>{{ $item->name }}</th>
                        @endforeach
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['rows'] as $invoice)
                        <tr>
                            <th>{{ $loop->index + 1 }}</th>
                            <td>{{ $invoice['member_name'] }}</td>
                            @foreach ($data['sub_categories'] as $item)
                                <td class="center">{{ number_format($invoice[$item->name], 0, ',', '.') }}</td>
                            @endforeach
                            <td>{{ $invoice['total_row'] }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="center" colspan="2">Jumlah</td>
                        @foreach ($data['sub_categories'] as $item)
                            <td class="center">{{ number_format($data['total_cols'][$item->name], 0, ',', '.') }}</td>
                        @endforeach

                        <td class="center">{{ number_format($data['total_invoice'], 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
