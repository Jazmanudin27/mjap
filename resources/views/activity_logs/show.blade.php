@foreach ($logs as $key => $log)
    <tr>
        <td>{{ $logs->firstItem() + $key }}</td>
        <td>{{ $log->name ?? 'Guest' }}</td>
        <td>{{ $log->action }}</td>
        <td>{{ $log->description }}</td>
        <td>{{ $log->ip_address }}</td>
        <td>{{ $log->created_at }}</td>
    </tr>
@endforeach
{{ $logs->links() }}
