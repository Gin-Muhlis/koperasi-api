<table cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Nama</th>
            <th colspan="{{ count($sub_categories_saving) + 1 }}">Simpanan</th>
            <th colspan="{{ count($sub_categories_loan) + 1 }}">Piutang</th>
        </tr>
        <tr>
            @foreach ($sub_categories_saving as $item)
                <th>{{ $item->name }}</th>
            @endforeach
            <th>Jumlah</th>
            @foreach ($sub_categories_loan as $item)
                <th>{{ $item->name }}</th>
            @endforeach
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $member)
            <tr>
                <td>{{ $loop->index + 1 }}</td>
                <td>{{ $member['name'] }}</td>
                @foreach ($sub_categories_saving as $item)
                    <th>Rp. {{ number_format($member[$item->name], 0, ',', '.') }}</th>
                @endforeach
                <th>Rp. {{ number_format($member['total_saving'], 0, ',', '.') }}</th>
                @foreach ($sub_categories_loan as $item)
                    <th>Rp. {{ number_format($member[$item->name], 0, ',', '.') }}</th>
                @endforeach
                <th>Rp. {{ number_format($member['total_loan'], 0, ',', '.') }}</th>
            </tr>
        @endforeach
        <tr>
            <td colspan="2">Jumlah</td>
            @foreach ($sub_categories_saving as $item)
                <th>Rp. {{ number_format($total_cols[$item->name], 0, ',', '.') }}</th>
            @endforeach
            <th>Rp. {{ number_format($total_cols['total_col_saving'], 0, ',', '.') }}</th>
            @foreach ($sub_categories_loan as $item)
                <th>Rp. {{ number_format($total_cols[$item->name], 0, ',', '.') }}</th>
            @endforeach
            <th>Rp. {{ number_format($total_cols['total_col_loan'], 0, ',', '.') }}</th>
        </tr>
    </tbody>
</table>
