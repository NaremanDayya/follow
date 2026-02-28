<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-1.5 h-6 bg-gradient-to-b from-indigo-600 to-purple-600 rounded-full"></div>
                <h2 class="font-black text-xl text-gray-900 dark:text-white leading-tight">
                    لوحة التقارير
                </h2>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('reports.analytics') }}" class="inline-flex items-center px-4 py-2 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-xl transition-colors duration-200">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    التحليلات
                </a>
                <a href="{{ route('reports.monthly') }}" class="inline-flex items-center px-4 py-2 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-xl transition-colors duration-200">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    التقارير الشهرية
                </a>
                <a href="{{ route('reports.late-clients') }}" class="inline-flex items-center px-4 py-2 bg-yellow-100 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400 hover:bg-yellow-200 dark:hover:bg-yellow-900/30 rounded-xl transition-colors duration-200">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    العملاء المتأخرين
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Statistics Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">إجمالي العملاء</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_clients'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">نشط</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['active_clients'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">متأخر</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['late_clients'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">هذا الشهر</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['contacted_this_month'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">بدون متابعة</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['without_followup'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">التحديثات</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['updates_this_month'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
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
