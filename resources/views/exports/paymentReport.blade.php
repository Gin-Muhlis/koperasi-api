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
			<th>Jumlah</th>
		</tr>
	</thead>
	<tbody>
		@foreach ($members_data as $data)
			<tr>
				<td>{{ $loop->index + 1 }}</td>
				<td>{{ $data['name'] }}</td>
				<td>{{ $data['principal_saving'] }}</td>
				<td>{{ $data['mandatory_saving'] }}</td>
				<td>{{ $data['special_mandatory_saving'] }}</td>
				<td>{{ $data['voluntary_saving'] }}</td>
				<td>{{ $data['recretional_saving'] }}</td>
				<td>{{ $data['total_payment_member'] }}</td>
			</tr>
		@endforeach
		<tr>
			<td colspan="2">Jumlah</td>
			<td>{{ $total_principal_saving }}</td>
			<td>{{ $total_mandatory_saving }}</td>
			<td>{{ $total_special_mandatory_saving }}</td>
			<td>{{ $total_voluntary_saving }}</td>
			<td>{{ $total_recretional_saving }}</td>
			<td>{{ $total_payment }}</td>
		</tr>
	</tbody>
</table>