@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ $client->name }} - Monthly Report</h1>
            <p class="text-muted mb-0">
                {{ $date->format('F Y') }} • {{ $client->company ?? 'Individual Client' }}
            </p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <input type="month" class="form-control" id="monthSelector" value="{{ $month }}" onchange="changeMonth()">
            <button type="button" class="btn btn-outline-primary" onclick="exportReport()">
                <i class="fas fa-download me-2"></i>Export
            </button>
            <a href="{{ route('reports.monthly') }}?month={{ $month }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>All Clients
            </a>
            <a href="{{ route('clients.show', $client) }}" class="btn btn-outline-info">
                <i class="fas fa-user me-2"></i>Client Profile
            </a>
        </div>
    </div>

    <!-- Statistics Overview -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-block mb-2">
                        <i class="fas fa-calendar-check text-primary"></i>
                    </div>
                    <h4 class="mb-1">{{ $stats['total_updates'] }}</h4>
                    <small class="text-muted">Total Updates</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-block mb-2">
                        <i class="fas fa-calendar-day text-success"></i>
                    </div>
                    <h4 class="mb-1">{{ $stats['contact_days'] }}</h4>
                    <small class="text-muted">Contact Days</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="bg-info bg-opacity-10 rounded-circle p-3 d-inline-block mb-2">
                        <i class="fas fa-chart-line text-info"></i>
                    </div>
                    <h4 class="mb-1">{{ $stats['average_updates_per_contact'] }}</h4>
                    <small class="text-muted">Avg/Contact</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3 d-inline-block mb-2">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                    </div>
                    <h4 class="mb-1">
                        {{ $updates->pluck('contact_date')->unique()->count() > 0 
                            ? round(($date->daysInMonth - $stats['contact_days']) / $date->daysInMonth * 100, 0) 
                            : 100 }}%
                    </h4>
                    <small class="text-muted">No Contact Days</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="bg-secondary bg-opacity-10 rounded-circle p-3 d-inline-block mb-2">
                        <i class="fas fa-percentage text-secondary"></i>
                    </div>
                    <h4 class="mb-1">{{ round($stats['contact_days'] / $date->daysInMonth * 100, 0) }}%</h4>
                    <small class="text-muted">Coverage Rate</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="bg-danger bg-opacity-10 rounded-circle p-3 d-inline-block mb-2">
                        <i class="fas fa-clock text-danger"></i>
                    </div>
                    <h4 class="mb-1">
                        {{ $client->last_contact_date 
                            ? $client->last_contact_date->diffInDays(now()) 
                            : 'N/A' }}
                    </h4>
                    <small class="text-muted">Days Since Last</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar View -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-alt text-primary me-2"></i>
                            Contact Calendar
                        </h5>
                        <div class="d-flex gap-2">
                            <div class="d-flex align-items-center">
                                <div class="bg-success rounded me-1" style="width: 12px; height: 12px;"></div>
                                <small>Has Contact</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded me-1" style="width: 12px; height: 12px;"></div>
                                <small>No Contact</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="bg-warning rounded me-1" style="width: 12px; height: 12px;"></div>
                                <small>Today</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Calendar Grid -->
                    <div class="calendar-grid">
                        <!-- Weekday Headers -->
                        <div class="calendar-weekdays">
                            <div class="calendar-weekday">Sun</div>
                            <div class="calendar-weekday">Mon</div>
                            <div class="calendar-weekday">Tue</div>
                            <div class="calendar-weekday">Wed</div>
                            <div class="calendar-weekday">Thu</div>
                            <div class="calendar-weekday">Fri</div>
                            <div class="calendar-weekday">Sat</div>
                        </div>
                        
                        <!-- Calendar Days -->
                        <div class="calendar-days">
                            @php
                                $firstDayOfMonth = $date->copy()->startOfMonth()->dayOfWeek;
                                $daysInMonth = $date->daysInMonth;
                            @endphp
                            
                            <!-- Empty cells for days before month starts -->
                            @for($i = 0; $i < $firstDayOfMonth; $i++)
                                <div class="calendar-day empty"></div>
                            @endfor
                            
                            <!-- Days of the month -->
                            @for($day = 1; $day <= $daysInMonth; $day++)
                                @php
                                    $currentDate = $date->copy()->day($day);
                                    $dayData = $calendar[$day] ?? null;
                                    $hasContact = $dayData && $dayData['has_contact'];
                                    $isToday = $dayData && $dayData['is_today'];
                                    $isWeekend = $dayData && $dayData['is_weekend'];
                                @endphp
                                
                                <div class="calendar-day {{ $hasContact ? 'has-contact' : 'no-contact' }} {{ $isToday ? 'today' : '' }} {{ $isWeekend ? 'weekend' : '' }}"
                                     onclick="showDayDetails({{ $day }})"
                                     data-bs-toggle="tooltip" 
                                     title="{{ $dayData ? ($dayData['has_contact'] ? $dayData['updates']->count() . ' contacts' : 'No contact') : 'No contact data' }}">
                                    <div class="calendar-day-number">{{ $day }}</div>
                                    @if($hasContact)
                                        <div class="calendar-indicator">
                                            <i class="fas fa-check-circle text-success"></i>
                                        </div>
                                    @endif
                                    @if($dayData && $dayData['updates']->count() > 1)
                                        <div class="calendar-multiple-indicator">
                                            {{ $dayData['updates']->count() }}
                                        </div>
                                    @endif
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Types Breakdown -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-chart-pie text-info me-2"></i>
                        Update Types
                    </h5>
                    
                    @if($stats['updates_by_type']->count() > 0)
                        <div class="mb-3">
                            @foreach($stats['updates_by_type'] as $type => $count)
                                @php
                                    $percentage = round(($count / $stats['total_updates']) * 100, 1);
                                    $color = match($type) {
                                        'call' => 'info',
                                        'email' => 'success',
                                        'meeting' => 'warning',
                                        'note' => 'secondary',
                                        default => 'primary'
                                    };
                                @endphp
                                
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <div>
                                            <span class="badge bg-{{ $color }} me-2">{{ ucfirst($type) }}</span>
                                            <small class="text-muted">{{ $count }} updates</small>
                                        </div>
                                        <small class="text-muted">{{ $percentage }}%</small>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-{{ $color }}" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-chart-pie fa-2x mb-2"></i>
                            <div>No updates this month</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Contact Frequency -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-chart-line text-success me-2"></i>
                        Contact Frequency
                    </h5>
                    
                    <div class="text-center mb-3">
                        <h3 class="mb-1">{{ $stats['average_updates_per_contact'] }}</h3>
                        <small class="text-muted">Average updates per contact day</small>
                    </div>
                    
                    <div class="progress mb-3" style="height: 20px;">
                        <div class="progress-bar bg-success d-flex align-items-center justify-content-center" 
                             style="width: {{ min($stats['average_updates_per_contact'] * 25, 100) }}%">
                            <small>{{ $stats['average_updates_per_contact'] }}/day</small>
                        </div>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="bg-light rounded p-2">
                                <h6 class="mb-0">{{ $stats['contact_days'] }}</h6>
                                <small class="text-muted">Active Days</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded p-2">
                                <h6 class="mb-0">{{ $date->daysInMonth - $stats['contact_days'] }}</h6>
                                <small class="text-muted">Inactive Days</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Updates List -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list text-primary me-2"></i>
                            All Updates ({{ $updates->count() }})
                        </h5>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" onchange="filterUpdates(this.value)">
                                <option value="all">All Types</option>
                                <option value="call">Calls Only</option>
                                <option value="email">Emails Only</option>
                                <option value="meeting">Meetings Only</option>
                                <option value="note">Notes Only</option>
                            </select>
                            <select class="form-select form-select-sm" onchange="sortUpdates(this.value)">
                                <option value="date-desc">Newest First</option>
                                <option value="date-asc">Oldest First</option>
                                <option value="type">By Type</option>
                            </select>
                        </div>
                    </div>
                    
                    @if($updates->count() > 0)
                        <div class="updates-list" id="updatesList">
                            @foreach($updates as $update)
                                <div class="update-item mb-3 p-3 border rounded" data-type="{{ $update->update_type }}" data-date="{{ $update->contact_date->format('Y-m-d') }}">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="bg-{{ $update->update_type === 'call' ? 'info' : ($update->update_type === 'email' ? 'success' : ($update->update_type === 'meeting' ? 'warning' : 'secondary')) }} bg-opacity-10 rounded-circle p-3">
                                                <i class="fas fa-{{ $update->update_type === 'call' ? 'phone' : ($update->update_type === 'email' ? 'envelope' : ($update->update_type === 'meeting' ? 'users' : 'edit')) }} text-{{ $update->update_type === 'call' ? 'info' : ($update->update_type === 'email' ? 'success' : ($update->update_type === 'meeting' ? 'warning' : 'secondary')) }}"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <span class="badge bg-{{ $update->update_type === 'call' ? 'info' : ($update->update_type === 'email' ? 'success' : ($update->update_type === 'meeting' ? 'warning' : 'secondary')) }} me-2">
                                                        {{ ucfirst($update->update_type) }}
                                                    </span>
                                                    <strong>{{ $update->contact_date->format('M d, Y') }}</strong>
                                                    <small class="text-muted ms-2">{{ $update->user->name }}</small>
                                                </div>
                                                <small class="text-muted">{{ $update->created_at->format('H:i') }}</small>
                                            </div>
                                            <p class="mb-2">{{ $update->update_content }}</p>
                                            @if($update->notes)
                                                <div class="bg-light rounded p-2 small text-muted">
                                                    <i class="fas fa-sticky-note me-1"></i>{{ $update->notes }}
                                                </div>
                                            @endif
                                            @if($update->next_followup_date)
                                                <div class="mt-2">
                                                    <small class="text-info">
                                                        <i class="fas fa-calendar-alt me-1"></i>
                                                        Next follow-up: {{ $update->next_followup_date->format('M d, Y') }}
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                            <div>No updates recorded for {{ $date->format('F Y') }}</div>
                            <small>This client had no follow-up activity during this period.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Day Details Modal -->
    <div class="modal fade" id="dayDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Day Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="dayDetailsContent">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Change month
function changeMonth() {
    const month = document.getElementById('monthSelector').value;
    window.location.href = `{{ route('reports.client.monthly', $client) }}?month=${month}`;
}

// Show day details
function showDayDetails(day) {
    const month = document.getElementById('monthSelector').value;
    
    fetch(`/api/client/{{ $client->id }}/day-details?month=${month}&day=${day}`, {
        headers: {
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        const modal = new bootstrap.Modal(document.getElementById('dayDetailsModal'));
        const content = document.getElementById('dayDetailsContent');
        
        if (data.updates && data.updates.length > 0) {
            content.innerHTML = `
                <h6>${data.date}</h6>
                <div class="mb-3">
                    <span class="badge bg-success">${data.updates.length} contacts</span>
                </div>
                <div class="timeline">
                    ${data.updates.map(update => `
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="bg-${update.update_type === 'call' ? 'info' : (update.update_type === 'email' ? 'success' : (update.update_type === 'meeting' ? 'warning' : 'secondary'))} bg-opacity-10 rounded-circle p-2">
                                        <i class="fas fa-${update.update_type === 'call' ? 'phone' : (update.update_type === 'email' ? 'envelope' : (update.update_type === 'meeting' ? 'users' : 'edit'))} text-${update.update_type === 'call' ? 'info' : (update.update_type === 'email' ? 'success' : (update.update_type === 'meeting' ? 'warning' : 'secondary'))} small"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="mb-1">
                                        <span class="badge bg-${update.update_type === 'call' ? 'info' : (update.update_type === 'email' ? 'success' : (update.update_type === 'meeting' ? 'warning' : 'secondary'))} me-2">
                                            ${update.update_type}
                                        </span>
                                        <small class="text-muted">${update.user.name} • ${update.created_at}</small>
                                    </div>
                                    <p class="mb-0">${update.update_content}</p>
                                    ${update.notes ? `<div class="bg-light rounded p-2 small text-muted mt-2">${update.notes}</div>` : ''}
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        } else {
            content.innerHTML = `
                <h6>${data.date}</h6>
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-phone-slash fa-2x mb-2"></i>
                    <div>No contact recorded</div>
                </div>
            `;
        }
        
        modal.show();
    })
    .catch(error => {
        console.error('Error fetching day details:', error);
    });
}

// Filter updates
function filterUpdates(type) {
    const items = document.querySelectorAll('.update-item');
    
    items.forEach(item => {
        if (type === 'all' || item.dataset.type === type) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

// Sort updates
function sortUpdates(sortBy) {
    const container = document.getElementById('updatesList');
    const items = Array.from(container.querySelectorAll('.update-item'));
    
    items.sort((a, b) => {
        switch(sortBy) {
            case 'date-desc':
                return new Date(b.dataset.date) - new Date(a.dataset.date);
            case 'date-asc':
                return new Date(a.dataset.date) - new Date(b.dataset.date);
            case 'type':
                return a.dataset.type.localeCompare(b.dataset.type);
            default:
                return 0;
        }
    });
    
    items.forEach(item => container.appendChild(item));
}

// Export report
function exportReport() {
    const month = document.getElementById('monthSelector').value;
    window.open(`{{ route('reports.export') }}?format=pdf&client_id={{ $client->id }}&month=${month}`, '_blank');
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + E for export
    if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
        e.preventDefault();
        exportReport();
    }
    
    // Ctrl/Cmd + P for print
    if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
        e.preventDefault();
        window.print();
    }
});
</script>

<style>
.calendar-grid {
    font-family: monospace;
}

.calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
    margin-bottom: 1px;
}

.calendar-weekday {
    text-align: center;
    font-weight: bold;
    font-size: 0.8rem;
    padding: 0.5rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
}

.calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
}

.calendar-day {
    aspect-ratio: 1;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    padding: 0.25rem;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    min-height: 60px;
}

.calendar-day:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.calendar-day.has-contact {
    background-color: #d1e7dd;
    border-color: #badbcc;
}

.calendar-day.no-contact {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.calendar-day.today {
    border-color: #ffc107;
    border-width: 2px;
}

.calendar-day.weekend {
    background-color: #f1f3f4;
}

.calendar-day.empty {
    border: none;
    cursor: default;
}

.calendar-day.empty:hover {
    transform: none;
    box-shadow: none;
}

.calendar-day-number {
    font-weight: bold;
    font-size: 0.9rem;
}

.calendar-indicator {
    position: absolute;
    top: 2px;
    right: 2px;
    font-size: 0.7rem;
}

.calendar-multiple-indicator {
    position: absolute;
    bottom: 2px;
    right: 2px;
    background-color: #0d6efd;
    color: white;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.6rem;
    font-weight: bold;
}

.update-item {
    transition: all 0.2s ease;
}

.update-item:hover {
    background-color: #f8f9fa;
    transform: translateY(-1px);
}

@media print {
    .btn, .modal, .form-select {
        display: none !important;
    }
    
    .calendar-day {
        break-inside: avoid;
    }
    
    .card {
        break-inside: avoid;
    }
}
</style>
@endsection
