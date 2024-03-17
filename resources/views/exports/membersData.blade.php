<table cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Email</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $member)
            <tr>
                <td>{{ $loop->index + 1 }}</td>
                <td>{{ $member->name }}</td>
                <td>{{ $member->email }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
