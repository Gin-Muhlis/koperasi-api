<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Piutang Anggota Koperasi - {{ $data['name'] }}</title>
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
            <div class="profile-app">
                <h2>Zie Koperasi</h2>
                <p class="address">{{ $profile->address }}</p>
            </div>
            <div class="profile-member">
                <p class="title">Laporan Pinjaman Anggota Koperasi {{ $year_now }}</p>
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
                        <th>Keterangan</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($filtered_sub_categories as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td class="center">Rp. {{ number_format($data[$item->name], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="center">Jumlah</td>
                        <td class="center">Rp. {{ number_format($data['total_loan'], 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>x
