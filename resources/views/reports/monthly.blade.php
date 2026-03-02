@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white">التقارير الشهرية</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">تحليل تواصل العملاء وتتبع المتابعات</p>
            </div>
            <div class="flex items-center gap-3">
                <input type="month" class="px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white" id="monthSelector" value="{{ $month }}" onchange="changeMonth()">
                <button type="button" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-colors duration-200" onclick="exportReport()">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0v-6h6m0 0v-6h-6m-11 7h18" />
                    </svg>
                    تصدير
                </button>
                <a href="{{ route('reports.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-xl transition-colors duration-200">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m14 14l-7-7m-14 14l7-7m-7 7l-7 7" />
                    </svg>
                    العودة للوحة التحكم
                </a>
            </div>
        </div>
    </div>

    <!-- Month Summary -->
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <svg class="w-5 h-5 inline ml-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ $date->format('F Y') }} ملخص
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <h4 class="text-2xl font-bold text-indigo-600">{{ count($reports) }}</h4>
                    <small class="text-gray-500 dark:text-gray-400">إجمالي العملاء</small>
                </div>
                <div class="text-center">
                    <h4 class="text-2xl font-bold text-green-600">{{ collect($reports)->sum('total_updates') }}</h4>
                    <small class="text-gray-500 dark:text-gray-400">إجمالي التحديثات</small>
                </div>
                <div class="text-center">
                    <h4 class="text-2xl font-bold text-blue-600">{{ collect($reports)->sum('contact_days') }}</h4>
                    <small class="text-gray-500 dark:text-gray-400">أيام التواصل</small>
                </div>
                <div class="text-center">
                    <h4 class="text-2xl font-bold text-yellow-600">{{ collect($reports)->sum('late_days') }}</h4>
                    <small class="text-gray-500 dark:text-gray-400">أيام التأخير</small>
                </div>
            </div>
        </div>
    </div>
                    
                    <!-- Progress Overview -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted">Contact Coverage</small>
                                    <small class="text-muted">{{ round(collect($reports)->avg('contact_days') / $date->daysInMonth * 100, 1) }}%</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-info" style="width: {{ collect($reports)->avg('contact_days') / $date->daysInMonth * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted">Avg Updates/Day</small>
                                    <small class="text-muted">{{ round(collect($reports)->avg('contact_frequency'), 1) }}</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ min(collect($reports)->avg('contact_frequency') * 20, 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted">Late Day Rate</small>
                                    <small class="text-muted">{{ round(collect($reports)->sum('late_days') / (count($reports) * $date->daysInMonth) * 100, 1) }}%</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: {{ min(collect($reports)->sum('late_days') / (count($reports) * $date->daysInMonth) * 100, 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted">Active Rate</small>
                                    <small class="text-muted">{{ round(collect($reports)->filter(fn($r) => $r['contact_days'] > 0)->count() / count($reports) * 100, 1) }}%</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: {{ collect($reports)->filter(fn($r) => $r['contact_days'] > 0)->count() / count($reports) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Client Reports -->
    <div class="row">
        @foreach($reports as $report)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="card-title mb-1">{{ $report['client']->name }}</h6>
                                @if($report['client']->company)
                                    <small class="text-muted">{{ $report['client']->company }}</small>
                                @endif
                            </div>
                            <div class="text-end">
                                <span class="badge bg-{{ $report['client']->status === 'active' ? 'success' : ($report['client']->status === 'inactive' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($report['client']->status) }}
                                </span>
                            </div>
                        </div>

                        <!-- Stats Grid -->
                        <div class="row text-center mb-3">
                            <div class="col-4">
                                <div class="bg-primary bg-opacity-10 rounded p-2">
                                    <h6 class="mb-0">{{ $report['contact_days'] }}</h6>
                                    <small class="text-muted">Days</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-success bg-opacity-10 rounded p-2">
                                    <h6 class="mb-0">{{ $report['total_updates'] }}</h6>
                                    <small class="text-muted">Updates</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-warning bg-opacity-10 rounded p-2">
                                    <h6 class="mb-0">{{ $report['late_days'] }}</h6>
                                    <small class="text-muted">Late</small>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Frequency -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted">Contact Frequency</small>
                                <small class="text-muted">{{ $report['contact_frequency'] }}/day</small>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-info" style="width: {{ min($report['contact_frequency'] * 25, 100) }}%"></div>
                            </div>
                        </div>

                        <!-- Recent Updates -->
                        @if($report['updates']->count() > 0)
                            <div class="mb-3">
                                <small class="text-muted d-block mb-2">Recent Updates</small>
                                <div class="small">
                                    @foreach($report['updates']->take(3) as $update)
                                        <div class="d-flex align-items-center mb-1">
                                            <span class="badge bg-{{ $update->update_type === 'call' ? 'info' : ($update->update_type === 'email' ? 'success' : ($update->update_type === 'meeting' ? 'warning' : 'secondary')) }} me-1" style="font-size: 0.6rem;">
                                                {{ ucfirst($update->update_type) }}
                                            </span>
                                            <small class="text-muted flex-grow-1">{{ Str::limit($update->update_content, 30) }}</small>
                                            <small class="text-muted">{{ $update->contact_date->format('M d') }}</small>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                @if($report['client']->assignedEmployee)
                                    <small class="text-muted">
                                        <i class="fas fa-user-tie me-1"></i>{{ $report['client']->assignedEmployee->name }}
                                    </small>
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('reports.client.monthly', $report['client']) }}?month={{ $month }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fas fa-chart-line"></i>
                                </a>
                                <a href="{{ route('clients.show', $report['client']) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-calendar-times fa-3x mb-3 text-muted"></i>
                        <h5>No client data for {{ $date->format('F Y') }}</h5>
                        <p class="text-muted">No client activity was recorded during this period.</p>
                        <a href="{{ route('clients.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add Client
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Export Options Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Export Monthly Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Export Format</label>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary" onclick="downloadExport('pdf')">
                                <i class="fas fa-file-pdf me-2"></i>PDF Report
                            </button>
                            <button type="button" class="btn btn-outline-success" onclick="downloadExport('excel')">
                                <i class="fas fa-file-excel me-2"></i>Excel Spreadsheet
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="downloadExport('csv')">
                                <i class="fas fa-file-csv me-2"></i>CSV Data
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Include Options</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="includeDetails" checked>
                            <label class="form-check-label" for="includeDetails">
                                Detailed client information
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="includeUpdates" checked>
                            <label class="form-check-label" for="includeUpdates">
                                All update details
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="includeCharts">
                            <label class="form-check-label" for="includeCharts">
                                Charts and graphs
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Change month
function changeMonth() {
    const month = document.getElementById('monthSelector').value;
    window.location.href = `{{ route('reports.monthly') }}?month=${month}`;
}

// Export report
function exportReport() {
    const modal = new bootstrap.Modal(document.getElementById('exportModal'));
    modal.show();
}

// Download export
function downloadExport(format) {
    const month = document.getElementById('monthSelector').value;
    const includeDetails = document.getElementById('includeDetails').checked;
    const includeUpdates = document.getElementById('includeUpdates').checked;
    const includeCharts = document.getElementById('includeCharts').checked;
    
    const params = new URLSearchParams({
        format: format,
        month: month,
        include_details: includeDetails,
        include_updates: includeUpdates,
        include_charts: includeCharts
    });
    
    window.open(`{{ route('reports.export') }}?${params.toString()}`, '_blank');
    
    // Close modal
    bootstrap.Modal.getInstance(document.getElementById('exportModal')).hide();
}

// Auto-refresh data every 10 minutes
setInterval(() => {
    if (!document.hidden) {
        const month = document.getElementById('monthSelector').value;
        fetch(`{{ route('reports.monthly') }}?month=${month}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Update statistics without full page reload
            updateStatistics(data);
        })
        .catch(error => console.error('Error refreshing data:', error));
    }
}, 600000); // 10 minutes

// Update statistics dynamically
function updateStatistics(data) {
    // Update summary statistics
    const summaryCards = document.querySelectorAll('.card-body h4');
    if (data.summary) {
        summaryCards[0].textContent = data.summary.total_clients;
        summaryCards[1].textContent = data.summary.total_updates;
        summaryCards[2].textContent = data.summary.contact_days;
        summaryCards[3].textContent = data.summary.late_days;
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + E for export
    if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
        e.preventDefault();
        exportReport();
    }
    
    // Ctrl/Cmd + R for refresh (override browser refresh)
    if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
        e.preventDefault();
        location.reload();
    }
});

// Print functionality
function printReport() {
    window.print();
}

// Add print button to header if needed
document.addEventListener('DOMContentLoaded', function() {
    const headerActions = document.querySelector('.d-flex.gap-2.align-items-center');
    if (headerActions) {
        const printBtn = document.createElement('button');
        printBtn.className = 'btn btn-outline-secondary';
        printBtn.innerHTML = '<i class="fas fa-print me-2"></i>Print';
        printBtn.onclick = printReport;
        headerActions.appendChild(printBtn);
    }
});
</script>

<style>
@media print {
    .btn, .modal, .d-flex.gap-2 {
        display: none !important;
    }
    
    .card {
        break-inside: avoid;
        page-break-inside: avoid;
    }
    
    .card-body {
        padding: 1rem !important;
    }
}

.progress {
    background-color: #e9ecef;
}

.progress-bar {
    transition: width 0.6s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
}

.badge {
    font-size: 0.75em;
}
</style>
@endsection
