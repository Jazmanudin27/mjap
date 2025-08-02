@foreach ($data as $u)
    @php
        $timeout = now()->subMinutes(5);
    @endphp
    <tr>
        <td>{{ $u->name }}</td>
        <td>{{ $u->nama_lengkap }}</td>
        <td>{{ $u->email }}</td>
        <td>{{ $u->role_name }}</td>

        <td>
            @if ($u->last_activity >= $timeout)
                <span class="text-success">🟢</span>
            @else
                <span class="text-danger">🔴</span>
            @endif
            @if ($u->last_activity)
                {{ \Carbon\Carbon::parse($u->last_activity)->translatedFormat('d F Y H:i') }}
            @else
                <span class="text-muted">Belum pernah aktif</span>
            @endif
        </td>
        <td>
            <a href="{{ route('users.toggleStatus', $u->id) }}"
                class="btn btn-sm {{ $u->status == 'Aktif' ? 'btn-success' : 'btn-danger' }}">
                <i class="fa {{ $u->status == 'Aktif' ? 'fa-thumbs-up' : 'fa-thumbs-down' }}" aria-hidden="true"></i>
            </a>
            @if (!empty($PermissionEdit))
                <a href="{{ url('users/' . $u->id . '/edit') }}" class="btn btn-sm btn-warning">
                    <i class="fa fa-pencil"></i>
                </a>
            @endif
            @if (!empty($PermissionDelete))
                <a data-href="{{ url('deleteUsers', $u->id) }}" class="btn btn-sm btn-danger delete">
                    <i class="fa fa-trash"></i>
                </a>
            @endif
        </td>
    </tr>
@endforeach
