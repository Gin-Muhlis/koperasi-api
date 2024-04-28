<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zie Koperasi - Barang</title>
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

        .header .sub-header {
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

        table tr td img {
            object-fit: cover;
        }

        .center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Zie Koperasi</h2>
            <p class="sub-header">Data Barang</p>
        </div>
        <div class="content">
            <table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th width="20">No</th>
                        <th>Nama Barang</th>
                        <th>Harga</th>
                        <th>Gambar</th>
                        <th>Produk</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $stuff)
                        <tr>
                            <td width="20" class="center">{{ $loop->index + 1 }}</td>
                            <td>{{ $stuff->name }}</td>
                            <td>{{ $stuff->price }}</td>
                            <td>
                                @if (isset($stuff->image))
                                    <img src="{{ Storage::url($stuff->image) }}" width="50" height="50" alt="Gambar barang">
                                @else
                                    <span>-</span>
                                @endif
                            </td>
                            <td>{{ $stuff->product->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>


