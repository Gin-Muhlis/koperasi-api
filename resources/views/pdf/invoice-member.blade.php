<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $result['profile']->name }}</title>
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
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h2 {
            margin-bottom: 5px;
        }

        .header p {
            font-size: 1rem;
            margin-bottom: 5px
        }

        .content {
            width: 60%;
            color: #000;
            margin-bottom: 20px;
            position: relative;
            left: 50%;
            transform: translateX(-50%);
        }

        .content p {
            margin-bottom: 10px;
            font-size: 1rem
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

        table .center {
            text-align: center;
        }

        .sign {
            text-align: center;
        }

        .sign .address {
            margin-bottom: 70px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>{{ $result['profile']->app_name }}</h2>
            <p>{{ $result['profile']->address }}</p>
            <p>Potongan Bulan: {{ $result['invoice_month'] }}</p>
        </div>
        <div class="content">
            <p class="member-name">Nama Anggota: {{ $result['member_name'] }}</p>
            <table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th>Keterangan</th>
                        <th>Potongan</th>
                        <th>Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($result['sub_categories'] as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td class="center">Rp. {{ number_format($result[$item->name], 0, ',', '.') }}</td>
                            <td class="center">Rp. {{ number_format($result['total_sub_categories'][$item->name], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="center">Jumlah</td>
                        <td class="center">Rp. {{ number_format($result['total'], 0, ',', '.') }}</td>
                        <td class="center">Rp. {{ number_format($result['total_balance'], 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="sign">
            <p class="address">Cianjur {{ $result['now'] }}</p>

            <p>{{  $result['profile']->treasurer_name }}</p>
        </div>
    </div>
</body>

</html>x
