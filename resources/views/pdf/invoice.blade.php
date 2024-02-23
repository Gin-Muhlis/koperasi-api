<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Pembayaran {{ $data['time_invoice'] }} </title>
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

		table tr td, table tr th {
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
			<h1>Pembayaran Invoice {{ $data['time_invoice'] }}</h1>
		</div>
		<div class="content">
			<table cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<th>No</th>
						<th>Nama</th>
						<th>Pokok</th>
						<th>Wajib</th>
						<th>Wajib Khusus</th>
						<th>Sukarela</th>
						<th>Tabungan Rekreasi</th>
						<th>Piutang S/P</th>
						<th>Piutang Dagang</th>
						<th>Jumlah</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($data['row_data'] as $invoice)
						<tr>
							<th>{{ $loop->index + 1 }}</th>
							<td>{{ $invoice['name'] }}</td>
							<td class="center">{{ $invoice['principalSaving'] }}</td>
							<td class="center">{{ $invoice['mandatorySaving'] }}</td>
							<td class="center">{{ $invoice['specialMandatorySaving'] }}</td>
							<td class="center">{{ $invoice['voluntarySaving'] }}</td>
							<td class="center">{{ $invoice['recretionalSaving'] }}</td>
							<td class="center">{{ $invoice['receivable'] }}</td>
							<td class="center">{{ $invoice['accountReceivable'] }}</td>
							<td class="center">{{ $invoice['totalRow'] }}</td>
						</tr>
					@endforeach
					<tr>
						<td class="center" colspan="2">Jumlah</td>
						<td class="center">{{ $data['total_principal_saving'] }}</td>
						<td class="center">{{ $data['total_mandatory_saving'] }}</td>
						<td class="center">{{ $data['total_special_mandatory_saving'] }}</td>
						<td class="center">{{ $data['total_voluntary_saving'] }}</td>
						<td class="center">{{ $data['total_recretional_saving'] }}</td>
						<td class="center">{{ $data['total_receivable'] }}</td>
						<td class="center">{{ $data['total_account_receivable'] }}</td>
						<td class="center">{{ $data['total_invoice'] }}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</body>
</html>