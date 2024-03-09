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
				<td>Rp. {{ number_format($invoice['total_row'], 0, ',', '.') }}</td>
			</tr>
		@endforeach
		<tr>
			<td class="center" colspan="2">Jumlah</td>
			@foreach ($data['sub_categories'] as $item)
				<td class="center">Rp. {{ number_format($data['total_cols'][$item->name], 0, ',', '.') }}</td>
			@endforeach

			<td class="center">Rp. {{ number_format($data['total_invoice'], 0, ',', '.') }}</td>
		</tr>
	</tbody>
</table>