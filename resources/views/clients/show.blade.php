@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ $client->name }}</h1>
            <p class="text-muted mb-0">
                @if($client->company){{ $client->company }} • @endif
                Client since {{ $client->created_at->format('M d, Y') }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Clients
            </a>
            @if(auth()->user()->canEditClient($client))
                <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Edit Client
                </a>
            @endif
            <a href="{{ route('clients.chat', $client) }}" class="btn btn-success position-relative">
                <i class="fas fa-comment me-2"></i>Chat
                @if($client->unread_count > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ $client->unread_count }}
                    </span>
                @endif
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Client Information -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-user text-primary me-2"></i>Client Information
                    </h5>
                    
                    <div class="mb-3">
                        <label class="text-muted small">Status</label>
                        <div>
                            <span class="badge bg-{{ $client->status === 'active' ? 'success' : ($client->status === 'inactive' ? 'danger' : 'warning') }}">
                                {{ ucfirst($client->status) }}
                            </span>
                            @switch($client->late_status)
                                @case('active')
                                    <span class="badge bg-success ms-1">Active</span>
                                    @break
                                @case('near_late')
                                    <span class="badge bg-warning ms-1">Near Late</span>
                                    @break
                                @case('late')
                                    <span class="badge bg-danger ms-1">Late</span>
                                    @break
                            @endswitch
                        </div>
                    </div>

                    @if($client->assignedEmployee)
                        <div class="mb-3">
                            <label class="text-muted small">Assigned Employee</label>
                            <div class="d-flex align-items-center">
                                <div class="bg-secondary bg-opacity-10 rounded-circle p-2 me-2">
                                    <i class="fas fa-user-tie text-secondary small"></i>
                                </div>
                                <span>{{ $client->assignedEmployee->name }}</span>
                            </div>
                        </div>
                    @endif

                    @if($client->email)
                        <div class="mb-3">
                            <label class="text-muted small">Email</label>
                            <div>
                                <a href="mailto:{{ $client->email }}" class="text-decoration-none">
                                    <i class="fas fa-envelope me-2"></i>{{ $client->email }}
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($client->phone)
                        <div class="mb-3">
                            <label class="text-muted small">Phone</label>
                            <div>
                                <a href="tel:{{ $client->phone }}" class="text-decoration-none">
                                    <i class="fas fa-phone me-2"></i>{{ $client->phone }}
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($client->address)
                        <div class="mb-3">
                            <label class="text-muted small">Address</label>
                            <div>
                                <i class="fas fa-map-marker-alt me-2"></i>{{ $client->address }}
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="text-muted small">Created By</label>
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                <i class="fas fa-user text-primary small"></i>
                            </div>
                            <span>{{ $client->createdBy->name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-chart-bar text-info me-2"></i>Quick Stats
                    </h5>
                    
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="bg-primary bg-opacity-10 rounded p-3">
                                <h4 class="mb-1">{{ $client->total_updates_count }}</h4>
                                <small class="text-muted">Total Updates</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="bg-success bg-opacity-10 rounded p-3">
                                <h4 class="mb-1">{{ $client->unread_count }}</h4>
                                <small class="text-muted">Unread Messages</small>
                            </div>
                        </div>
                    </div>

                    @if($client->last_contact_date)
                        <div class="mb-3">
                            <label class="text-muted small">Last Contact</label>
                            <div>
                                <i class="fas fa-calendar me-2"></i>{{ $client->last_contact_date->format('M d, Y') }}
                                <div class="text-muted small">{{ $client->last_contact_date->diffForHumans() }}</div>
                            </div>
                        </div>
                    @endif

                    @if($client->next_followup_date)
                        <div class="mb-3">
                            <label class="text-muted small">Next Follow-up</label>
                            <div>
                                <i class="fas fa-clock me-2"></i>{{ $client->next_followup_date->format('M d, Y') }}
                                <div class="text-muted small">{{ $client->next_followup_date->diffForHumans() }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-bolt text-warning me-2"></i>Quick Actions
                    </h5>
                    
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#quickUpdateModal">
                            <i class="fas fa-plus me-2"></i>Add Quick Update
                        </button>
                        <a href="{{ route('clients.updates.create', $client) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>Detailed Update
                        </a>
                        <a href="{{ route('clients.chat', $client) }}" class="btn btn-success">
                            <i class="fas fa-comment me-2"></i>Open Chat
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Updates & Chat -->
        <div class="col-md-8">
            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4" id="clientTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="updates-tab" data-bs-toggle="tab" data-bs-target="#updates" type="button" role="tab">
                        <i class="fas fa-history me-2"></i>Recent Updates
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="chat-tab" data-bs-toggle="tab" data-bs-target="#chat" type="button" role="tab">
                        <i class="fas fa-comment me-2"></i>Chat Messages
                        @if($client->unread_count > 0)
                            <span class="badge bg-danger ms-1">{{ $client->unread_count }}</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="timeline-tab" data-bs-toggle="tab" data-bs-target="#timeline" type="button" role="tab">
                        <i class="fas fa-stream me-2"></i>Timeline
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="clientTabsContent">
                <!-- Updates Tab -->
                <div class="tab-pane fade show active" id="updates" role="tabpanel">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0">Follow-up Updates</h5>
                                <a href="{{ route('clients.updates.index', $client) }}" class="btn btn-sm btn-outline-primary">
                                    View All
                                </a>
                            </div>

                            @if($client->updates->count() > 0)
                                <div class="timeline">
                                    @foreach($client->updates->take(5) as $update)
                                        <div class="timeline-item mb-4">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                                        <i class="fas fa-{{ $update->update_type === 'call' ? 'phone' : ($update->update_type === 'email' ? 'envelope' : ($update->update_type === 'meeting' ? 'users' : 'edit')) }} text-primary small"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <span class="badge bg-{{ $update->update_type === 'call' ? 'info' : ($update->update_type === 'email' ? 'success' : ($update->update_type === 'meeting' ? 'warning' : 'secondary')) }} me-2">
                                                                {{ ucfirst($update->update_type) }}
                                                            </span>
                                                            <small class="text-muted">{{ $update->contact_date->format('M d, Y') }}</small>
                                                        </div>
                                                        <small class="text-muted">{{ $update->user->name }}</small>
                                                    </div>
                                                    <p class="mb-2">{{ $update->update_content }}</p>
                                                    @if($update->notes)
                                                        <div class="bg-light rounded p-2 small text-muted">
                                                            {{ $update->notes }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5 text-muted">
                                    <i class="fas fa-history fa-3x mb-3"></i>
                                    <div>No updates yet</div>
                                    <small>Add your first follow-up update to get started</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Chat Tab -->
                <div class="tab-pane fade" id="chat" role="tabpanel">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0">Chat Messages</h5>
                                <a href="{{ route('clients.chat', $client) }}" class="btn btn-sm btn-success">
                                    Open Full Chat
                                </a>
                            </div>

                            @if($client->chatMessages->count() > 0)
                                <div class="chat-messages" style="max-height: 400px; overflow-y: auto;">
                                    @foreach($client->chatMessages->take(10) as $message)
                                        <div class="chat-message mb-3">
                                            <div class="d-flex {{ $message->sender_id === auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                                                <div class="max-width-70">
                                                    <div class="d-flex align-items-center mb-1 {{ $message->sender_id === auth()->id() ? 'justify-content-end' : '' }}">
                                                        <small class="text-muted">{{ $message->sender->name }}</small>
                                                        <small class="text-muted ms-2">{{ $message->created_at->format('H:i') }}</small>
                                                    </div>
                                                    <div class="rounded p-2 {{ $message->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-light' }}">
                                                        {{ $message->message }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5 text-muted">
                                    <i class="fas fa-comment fa-3x mb-3"></i>
                                    <div>No messages yet</div>
                                    <small>Start a conversation with this client</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Timeline Tab -->
                <div class="tab-pane fade" id="timeline" role="tabpanel">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="mb-4">Complete Timeline</h5>
                            
                            <div class="timeline">
                                <!-- Client Creation -->
                                <div class="timeline-item mb-4">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="bg-success bg-opacity-10 rounded-circle p-2">
                                                <i class="fas fa-user-plus text-success small"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <span class="badge bg-success me-2">Created</span>
                                                    <small class="text-muted">{{ $client->created_at->format('M d, Y H:i') }}</small>
                                                </div>
                                                <small class="text-muted">{{ $client->createdBy->name }}</small>
                                            </div>
                                            <p class="mb-0">Client created and added to the system</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- All Updates and Messages -->
                                @foreach($client->updates->merge($client->chatMessages)->sortByDesc('created_at') as $item)
                                    @if($item instanceof \App\Models\ClientUpdate)
                                        <div class="timeline-item mb-4">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                                        <i class="fas fa-{{ $item->update_type === 'call' ? 'phone' : ($item->update_type === 'email' ? 'envelope' : ($item->update_type === 'meeting' ? 'users' : 'edit')) }} text-primary small"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <span class="badge bg-{{ $item->update_type === 'call' ? 'info' : ($item->update_type === 'email' ? 'success' : ($item->update_type === 'meeting' ? 'warning' : 'secondary')) }} me-2">
                                                                {{ ucfirst($item->update_type) }} Update
                                                            </span>
                                                            <small class="text-muted">{{ $item->created_at->format('M d, Y H:i') }}</small>
                                                        </div>
                                                        <small class="text-muted">{{ $item->user->name }}</small>
                                                    </div>
                                                    <p class="mb-2">{{ $item->update_content }}</p>
                                                    @if($item->notes)
                                                        <div class="bg-light rounded p-2 small text-muted">
                                                            {{ $item->notes }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="timeline-item mb-4">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-info bg-opacity-10 rounded-circle p-2">
                                                        <i class="fas fa-comment text-info small"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <span class="badge bg-info me-2">Chat Message</span>
                                                            <small class="text-muted">{{ $item->created_at->format('M d, Y H:i') }}</small>
                                                        </div>
                                                        <small class="text-muted">{{ $item->sender->name }}</small>
                                                    </div>
                                                    <p class="mb-0">{{ $item->message }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Update Modal -->
<div class="modal fade" id="quickUpdateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Update</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickUpdateForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="quick_update_content" class="form-label">Update Content *</label>
                        <textarea class="form-control" id="quick_update_content" name="update_content" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="quick_update_type" class="form-label">Update Type *</label>
                        <select class="form-select" id="quick_update_type" name="update_type" required>
                            <option value="">Select Type</option>
                            <option value="call">Call</option>
                            <option value="email">Email</option>
                            <option value="meeting">Meeting</option>
                            <option value="note">Note</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quick_next_followup_date" class="form-label">Next Follow-up Date</label>
                        <input type="date" class="form-control" id="quick_next_followup_date" name="next_followup_date" min="{{ now()->format('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Quick Update Form
document.getElementById('quickUpdateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';
    
    fetch(`{{ route('clients.quick-update', $client) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal and reset form
            bootstrap.Modal.getInstance(document.getElementById('quickUpdateModal')).hide();
            this.reset();
            
            // Show success message
            showAlert('success', data.message);
            
            // Reload page after a short delay to show the new update
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('error', data.error || 'Failed to add update');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while adding the update');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Helper function to show alerts
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
    alertDiv.style.zIndex = '9999';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 5000);
}
</script>
@endsection
