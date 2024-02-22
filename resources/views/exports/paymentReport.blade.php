<table>
	<thead>
		<tr>
			<th>No</th>
			<th>Nama</th>
			<th>Simpanan Pokok</th>
			<th>Simpanan Wajib</th>
			<th>Simpanan Wajib Khusus</th>
			<th>Simpanan Sukarela</th>
			<th>Tabungan Rekreasi</th>
			<th>Piutang S/P</th>
			<th>Piutang Dagang</th>
			<th>Jumlah</th>
		</tr>
	</thead>
	<tbody>
		@foreach ($row_data as $data)
			<tr>
				<td>{{ $loop->index + 1 }}</td>
				<td>{{ $data['name'] }}</td>
				<td>{{ $data['principalSaving'] }}</td>
				<td>{{ $data['mandatorySaving'] }}</td>
				<td>{{ $data['specialMandatorySaving'] }}</td>
				<td>{{ $data['voluntarySaving'] }}</td>
				<td>{{ $data['recretionalSaving'] }}</td>
				<td>{{ $data['receivable'] }}</td>
				<td>{{ $data['accountReceivable'] }}</td>
				<td>{{ $data['totalRow'] }}</td>
			</tr>
		@endforeach
		<tr>
			<td colspan="2">Jumlah</td>
			<td>{{ $total_principal_saving }}</td>
			<td>{{ $total_mandatory_saving }}</td>
			<td>{{ $total_special_mandatory_saving }}</td>
			<td>{{ $total_voluntary_saving }}</td>
			<td>{{ $total_recretional_saving }}</td>
			<td>{{ $total_receivable }}</td>
			<td>{{ $total_account_receivable }}</td>
			<td>{{ $total_invoice }}</td>
		</tr>
	</tbody>
</table>