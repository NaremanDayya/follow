@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Client Management</h1>
            <p class="text-muted mb-0">Manage your clients and track follow-ups</p>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()->canManageClients())
                <a href="{{ route('clients.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>New Client
                </a>
            @endif
            <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">
                <i class="fas fa-sync-alt me-2"></i>Refresh
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-users text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 text-muted">Total Clients</h6>
                            <h4 class="mb-0">{{ $stats['total_clients'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 text-muted">Active</h6>
                            <h4 class="mb-0">{{ $stats['active_clients'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-exclamation-triangle text-danger"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 text-muted">Late</h6>
                            <h4 class="mb-0">{{ $stats['late_clients'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-calendar-check text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 text-muted">This Month</h6>
                            <h4 class="mb-0">{{ $stats['contacted_this_month'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-clock text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 text-muted">No Follow-up</h6>
                            <h4 class="mb-0">{{ $stats['without_followup'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-secondary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-edit text-secondary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 text-muted">Updates</h6>
                            <h4 class="mb-0">{{ $stats['updates_this_month'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('clients.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Name, email, company..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Employee</label>
                    <select name="employee_id" class="form-select">
                        <option value="">All Employees</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="prospect" {{ request('status') == 'prospect' ? 'selected' : '' }}>Prospect</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Late Status</label>
                    <select name="late_status" class="form-select">
                        <option value="">All</option>
                        <option value="active" {{ request('late_status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="late" {{ request('late_status') == 'late' ? 'selected' : '' }}>Late</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Unread Messages</label>
                    <select name="has_unread" class="form-select">
                        <option value="">All</option>
                        <option value="true" {{ request('has_unread') === 'true' ? 'selected' : '' }}>Has Unread</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <a href="{{ route('clients.index', ['sort_by' => 'name', 'sort_order' => request('sort_by') == 'name' && request('sort_order') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none">
                                    Client Name
                                    @if(request('sort_by') == 'name')
                                        <i class="fas fa-sort-{{ request('sort_order') }} ms-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Employee</th>
                            <th>
                                <a href="{{ route('clients.index', ['sort_by' => 'last_contact_date', 'sort_order' => request('sort_by') == 'last_contact_date' && request('sort_order') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none">
                                    Last Contact
                                    @if(request('sort_by') == 'last_contact_date')
                                        <i class="fas fa-sort-{{ request('sort_order') }} ms-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Next Follow-up</th>
                            <th>Updates</th>
                            <th>Status</th>
                            <th>Late Status</th>
                            <th>Unread</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="fas fa-user text-primary small"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $client->name }}</div>
                                            @if($client->company)
                                                <div class="text-muted small">{{ $client->company }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($client->assignedEmployee)
                                        <div class="d-flex align-items-center">
                                            <div class="bg-secondary bg-opacity-10 rounded-circle p-1 me-2">
                                                <i class="fas fa-user-tie text-secondary small"></i>
                                            </div>
                                            <span>{{ $client->assignedEmployee->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    @if($client->last_contact_date)
                                        <div>{{ $client->last_contact_date->format('M d, Y') }}</div>
                                        <div class="text-muted small">{{ $client->last_contact_date->diffForHumans() }}</div>
                                    @else
                                        <span class="text-muted">Never</span>
                                    @endif
                                </td>
                                <td>
                                    @if($client->next_followup_date)
                                        <div>{{ $client->next_followup_date->format('M d, Y') }}</div>
                                        <div class="text-muted small">{{ $client->next_followup_date->diffForHumans() }}</div>
                                    @else
                                        <span class="text-muted">Not set</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $client->total_updates_count }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $client->status === 'active' ? 'success' : ($client->status === 'inactive' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($client->status) }}
                                    </span>
                                </td>
                                <td>
                                    @switch($client->late_status)
                                        @case('active')
                                            <span class="badge bg-success">Active</span>
                                            @break
                                        @case('near_late')
                                            <span class="badge bg-warning">Near Late</span>
                                            @break
                                        @case('late')
                                            <span class="badge bg-danger">Late</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    @if($client->unread_count > 0)
                                        <span class="badge bg-danger">{{ $client->unread_count }}</span>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('clients.chat', $client) }}" class="btn btn-sm btn-outline-success position-relative" title="Chat">
                                            <i class="fas fa-comment"></i>
                                            @if($client->unread_count > 0)
                                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                    {{ $client->unread_count }}
                                                </span>
                                            @endif
                                        </a>
                                        @if(auth()->user()->canEditClient($client))
                                            <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <div>No clients found</div>
                                        <small>Try adjusting your filters or create a new client</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Showing {{ $clients->firstItem() }} to {{ $clients->lastItem() }} of {{ $clients->total() }} clients
                </div>
                {{ $clients->links() }}
            </div>
        </div>
    </div>
</div>

<script>
// Auto-refresh for real-time updates
setInterval(() => {
    // Check for new messages or status changes
    fetch('/clients/update-late-statuses', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.updated_count > 0) {
            // Optionally show notification or refresh page
            console.log('Updated late statuses for', data.updated_count, 'clients');
        }
    })
    .catch(error => console.error('Error updating statuses:', error));
}, 30000); // Check every 30 seconds
</script>
@endsection
