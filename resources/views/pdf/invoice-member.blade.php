
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

        p { 
            margin-top: 10px;
            text-align: center;
        }

        .content {
            width: 50%;
            transform: translateX(50%);
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
            padding: 7px;
            font-size: .9rem;
        }

        .center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>ZIE KOPERASI</h1>
            <h3>Nama: {{ $result['member_name'] }}</h3>
            <h3>Bulan: {{ $result['invoice_month'] }}</h3>
        </div>
        <div class="content">
            <table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th>Keterangan</th>
                        <th>Potongan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($result['sub_categories'] as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td class="center">{{ number_format($result[$item->name], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p>Cianjur {{ $result['now'] }}</p>
    </div>
</body>

</html>x
