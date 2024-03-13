<table cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
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
                @foreach ($sub_categories_loan as $item)
                    <th>Rp. {{ number_format($member[$item->name], 0, ',', '.') }}</th>
                @endforeach
                <th>Rp. {{ number_format($member['total_loan'], 0, ',', '.') }}</th>
            </tr>
        @endforeach
        <tr>
            <td colspan="2">Jumlah</td>
            @foreach ($sub_categories_loan as $item)
                <th>Rp. {{ number_format($total_cols[$item->name], 0, ',', '.') }}</th>
            @endforeach
            <th>Rp. {{ number_format($total_cols['total_col_loan'], 0, ',', '.') }}</th>
        </tr>
    </tbody>
</table>
