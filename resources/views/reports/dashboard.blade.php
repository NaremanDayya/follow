@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Reports Dashboard</h1>
            <p class="text-muted mb-0">Analytics and insights for client management</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.analytics') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-line me-2"></i>Analytics
            </a>
            <a href="{{ route('reports.monthly') }}" class="btn btn-outline-secondary">
                <i class="fas fa-calendar-alt me-2"></i>Monthly Reports
            </a>
            <a href="{{ route('reports.late-clients') }}" class="btn btn-outline-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>Late Clients
            </a>
        </div>
    </div>

    <!-- Statistics Overview -->
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

    <div class="row">
        <!-- Recent Activity -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history text-primary me-2"></i>Recent Activity
                        </h5>
                        <small class="text-muted">Last 10 updates</small>
                    </div>
                    
                    @if($recentUpdates->count() > 0)
                        <div class="activity-timeline">
                            @foreach($recentUpdates as $update)
                                <div class="activity-item mb-3">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                                <i class="fas fa-{{ $update->update_type === 'call' ? 'phone' : ($update->update_type === 'email' ? 'envelope' : ($update->update_type === 'meeting' ? 'users' : 'edit')) }} text-primary small"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <div>
                                                    <span class="badge bg-{{ $update->update_type === 'call' ? 'info' : ($update->update_type === 'email' ? 'success' : ($update->update_type === 'meeting' ? 'warning' : 'secondary')) }} me-1">
                                                        {{ ucfirst($update->update_type) }}
                                                    </span>
                                                    <strong>{{ $update->client->name }}</strong>
                                                </div>
                                                <small class="text-muted">{{ $update->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-0 small text-muted">{{ Str::limit($update->update_content, 80) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-history fa-2x mb-2"></i>
                            <div>No recent activity</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Late Clients -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-exclamation-triangle text-danger me-2"></i>Late Clients
                        </h5>
                        <a href="{{ route('reports.late-clients') }}" class="btn btn-sm btn-outline-danger">
                            View All
                        </a>
                    </div>
                    
                    @if($lateClients->count() > 0)
                        <div class="late-clients-list">
                            @foreach($lateClients as $client)
                                <div class="late-client-item mb-3 p-3 border rounded">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $client->name }}</h6>
                                            @if($client->company)
                                                <small class="text-muted">{{ $client->company }}</small>
                                            @endif
                                            <div class="mt-2">
                                                <span class="badge bg-{{ $client->late_status === 'late' ? 'danger' : 'warning' }} me-2">
                                                    {{ ucfirst($client->late_status) }}
                                                </span>
                                                <small class="text-muted">
                                                    Last contact: {{ $client->last_contact_date?->format('M d, Y') ?: 'Never' }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="text-muted small mb-2">
                                                {{ $client->last_contact_date ? $client->last_contact_date->diffForHumans() : 'Never contacted' }}
                                            </div>
                                            <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-outline-primary">
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                            <div>No late clients!</div>
                            <small>All clients are up to date</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Follow-ups -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-alt text-info me-2"></i>Upcoming Follow-ups
                        </h5>
                        <small class="text-muted">Next 7 days</small>
                    </div>
                    
                    @if($upcomingFollowups->count() > 0)
                        <div class="row">
                            @foreach($upcomingFollowups as $client)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border-left-info border-left-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="mb-1">{{ $client->name }}</h6>
                                                    @if($client->assignedEmployee)
                                                        <small class="text-muted">{{ $client->assignedEmployee->name }}</small>
                                                    @endif
                                                </div>
                                                <div class="text-end">
                                                    <div class="badge bg-info">
                                                        {{ $client->next_followup_date->format('M d') }}
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    {{ $client->next_followup_date->diffForHumans() }}
                                                </small>
                                                <div>
                                                    <a href="{{ route('clients.chat', $client) }}" class="btn btn-sm btn-outline-success me-1">
                                                        <i class="fas fa-comment"></i>
                                                    </a>
                                                    <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-calendar-check fa-2x mb-2"></i>
                            <div>No upcoming follow-ups</div>
                            <small>No follow-ups scheduled for the next 7 days</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-bolt text-warning me-2"></i>Quick Actions
                    </h5>
                    <div class="row">
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-primary w-100 mb-2" onclick="exportReport('pdf')">
                                <i class="fas fa-file-pdf me-2"></i>Export PDF Report
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-success w-100 mb-2" onclick="exportReport('excel')">
                                <i class="fas fa-file-excel me-2"></i>Export Excel Report
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-warning w-100 mb-2" onclick="sendReminders()">
                                <i class="fas fa-bell me-2"></i>Send Reminders
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-info w-100 mb-2" onclick="refreshDashboard()">
                                <i class="fas fa-sync-alt me-2"></i>Refresh Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Export report
function exportReport(format) {
    window.open(`{{ route('reports.export') }}?format=${format}`, '_blank');
}

// Send reminders
function sendReminders() {
    if (confirm('Send reminder notifications to all employees with late clients?')) {
        fetch('/api/send-reminders', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', `Reminders sent to ${data.count} employees`);
            } else {
                showAlert('error', 'Failed to send reminders');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'An error occurred while sending reminders');
        });
    }
}

// Refresh dashboard
function refreshDashboard() {
    location.reload();
}

// Show alert
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
    alertDiv.style.zIndex = '9999';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 5000);
}

// Auto-refresh dashboard every 5 minutes
setInterval(() => {
    // Only refresh if page is visible
    if (!document.hidden) {
        fetch('{{ route('reports.dashboard') }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Update statistics without full page reload
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Update stat cards
            const statCards = doc.querySelectorAll('.card-body h4');
            const currentCards = document.querySelectorAll('.card-body h4');
            
            currentCards.forEach((card, index) => {
                if (statCards[index]) {
                    card.textContent = statCards[index].textContent;
                }
            });
        })
        .catch(error => console.error('Error refreshing dashboard:', error));
    }
}, 300000); // 5 minutes
</script>

<style>
.border-left-info {
    border-left: 4px solid #0dcaf0 !important;
}

.border-left-3 {
    border-left-width: 3px !important;
}

.activity-item,
.late-client-item {
    transition: all 0.2s ease;
}

.activity-item:hover,
.late-client-item:hover {
    background-color: #f8f9fa;
    border-radius: 0.375rem;
}
</style>
@endsection
