<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Anggota Koperasi - {{ $data['name'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: sans-serif;
        }

        body {
            padding: 50px 10px;
        }

        .container {
            width: 100vw;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .header {
            width: 100%;
            font-size: 1.1rem;
            margin-bottom: 50px;
        }

        .header .profile-app {
            text-align: center;
            margin-bottom: 30px;

        }

        .header .property {
            width: 100px;
        }


        .header h2 {
            font-weight: bold;
        }

        .header p {
            margin-bottom: 2px;
        }

        .header .title {
            margin-bottom: 10px;
            font-weight: bold;
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
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="profile-app">
                <h2>Zie Koperasi</h2>
                <p class="address">{{ $profile->address }}</p>
            </div>
            <div class="profile-member">
                <p class="title">Laporan Anggota Koperasi {{ $year_now }}</p>
                <div>
                    <span class="property">Nama Anggota</span>
                    <span class="value">: {{ $data['name'] }}</span>
                </div>
                <div>
                    <span class="property">Jabatan</span>
                    <span class="value">: {{ $data['position'] }}</span>
                </div>
            </div>
        </div>
        <div class="content">
            <table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th class="center">Bulan</th>
                        @foreach ($sub_categories_saving as $item)
                            <th>{{ $item->name }}</th>
                        @endforeach
                        <th>Jumlah Simpanan</th>
                        @foreach ($sub_categories_loan as $item)
                            <th>{{ $item->name }}</th>
                        @endforeach
                        <th>Jumlah Piutang</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($months as $key => $month)
                        <tr>
                            <td>{{ $month }}</td>
                            @foreach ($sub_categories_saving as $item)
                                <td>Rp.
                                    {{ number_format($data['result_amount'][$key][$item->name]['amount'], 0, ',', '.') }}
                                </td>
                            @endforeach
                            <td>Rp.
                                {{ number_format($data['result_amount'][$key]['total_per_month_saving'], 0, ',', '.') }}
                            </td>
                            @foreach ($sub_categories_loan as $item)
                                <td>Rp.
                                    {{ number_format($data['result_amount'][$key][$item->name]['amount'], 0, ',', '.') }}
                                </td>
                            @endforeach
                            <td>Rp.
                                {{ number_format($data['result_amount'][$key]['total_per_month_loan'], 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            @foreach ($sub_categories_saving as $item)
                                <td>Rp.
                                    {{ number_format($data['result_total'][$key][$item->name]['amount'], 0, ',', '.') }}
                                </td>
                            @endforeach
                            <td>Rp.
                                {{ number_format($data['result_total'][$key]['total_col_per_month_saving'], 0, ',', '.') }}
                            </td>
                            @foreach ($sub_categories_loan as $item)
                                <td>Rp.
                                    {{ number_format($data['result_total'][$key][$item->name]['amount'], 0, ',', '.') }}
                                </td>
                            @endforeach
                            <td>Rp.
                                {{ number_format($data['result_total'][$key]['total_col_per_month_loan'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
