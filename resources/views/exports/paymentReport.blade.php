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
					<td class="center">{{ $invoice[$item->name] }}</td>
				@endforeach
				<td>{{ $invoice['total_row'] }}</td>
			</tr>
		@endforeach
		<tr>
			<td class="center" colspan="2">Jumlah</td>
			@foreach ($data['sub_categories'] as $item)
				<td class="center">{{ $data['total_cols'][$item->name] }}</td>
			@endforeach

			<td class="center">{{ $data['total_invoice'] }}</td>
		</tr>
	</tbody>
</table>