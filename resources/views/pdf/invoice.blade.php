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
            /* font-weight: bold; */

        }

        .header .address {
            font-weight: bold;
            margin-bottom: 30px;
            font-size: 1.1rem
        }

        .header .title {
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .header h2 {
            margin-bottom: 5px;
        }

        .content {
            width: 100%;
            color: #000;
            margin-bottom: 30px;
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

        .sign {
            width: 100%;
            font-size: .9rem;
            position: relative;
        }

        .sign .sign-content {
            width: 30%;
            position: absolute;
            right: 0;
        }

        .sign .sign-treasurer {
            margin-bottom: 50px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>{{ $data['profile']->app_name }}</h2>
            <p class="address">{{ $data['profile']->address }}</p>
            <p class="title">Daftar Potongan Bulan {{ $data['time'] }}</p>
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
                                <td class="center">Rp. {{ number_format($invoice[$item->name], 0, ',', '.') }}</td>
                            @endforeach
                            <td>Rp. {{ $invoice['total_row'] }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="center" colspan="2">Jumlah</td>
                        @foreach ($data['sub_categories'] as $item)
                            <td class="center">Rp. {{ number_format($data['total_cols'][$item->name], 0, ',', '.') }}
                            </td>
                        @endforeach

                        <td class="center">Rp. {{ number_format($data['total_invoice'], 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="sign">
            <div class="sign-content">
                <p class="address">Cianjur {{ $data['now'] }}</p>
                <p class="sign-treasurer">Bendahara</p>

                <p>{{ $data['profile']->treasurer_name }}</p>
            </div>
        </div>
    </div>
</body>

</html>


