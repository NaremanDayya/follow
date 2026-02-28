@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 h-100">
    <div class="row h-100">
        <!-- Chat Sidebar -->
        <div class="col-md-3 border-end">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">{{ $client->name }}</h5>
                <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
            
            <!-- Client Info -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-4 d-inline-block">
                            <i class="fas fa-user fa-2x text-primary"></i>
                        </div>
                    </div>
                    <h6 class="text-center">{{ $client->name }}</h6>
                    @if($client->company)
                        <p class="text-muted small text-center">{{ $client->company }}</p>
                    @endif
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">Status</small>
                        <span class="badge bg-{{ $client->status === 'active' ? 'success' : ($client->status === 'inactive' ? 'danger' : 'warning') }}">
                            {{ ucfirst($client->status) }}
                        </span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">Last Contact</small>
                        <small>{{ $client->last_contact_date?->format('M d') ?: 'Never' }}</small>
                    </div>
                    
                    @if($client->assignedEmployee)
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">Assigned</small>
                            <small>{{ $client->assignedEmployee->name }}</small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-3">Quick Stats</h6>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="bg-primary bg-opacity-10 rounded p-2 mb-2">
                                <h6 class="mb-0">{{ $client->total_updates_count }}</h6>
                                <small class="text-muted">Updates</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-success bg-opacity-10 rounded p-2 mb-2">
                                <h6 class="mb-0">{{ $messages->count() }}</h6>
                                <small class="text-muted">Messages</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="col-md-9 d-flex flex-column">
            <!-- Chat Header -->
            <div class="border-bottom pb-3 mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Chat with {{ $client->name }}</h4>
                        <small class="text-muted">
                            @if($client->unread_count > 0)
                                <span class="badge bg-danger me-2">{{ $client->unread_count }} unread</span>
                            @endif
                            {{ $messages->count() }} total messages
                        </small>
                    </div>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="markAllAsRead()">
                            <i class="fas fa-check-double me-1"></i>Mark All Read
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshMessages()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Messages Container -->
            <div class="flex-grow-1 overflow-auto" id="messagesContainer" style="max-height: 500px;">
                @if($messages->count() > 0)
                    <div class="messages-list">
                        @foreach($messages as $message)
                            <div class="message-item mb-3" data-message-id="{{ $message->id }}">
                                <div class="d-flex {{ $message->sender_id === auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                                    <div class="max-width-70">
                                        <!-- Message Header -->
                                        <div class="d-flex align-items-center mb-1 {{ $message->sender_id === auth()->id() ? 'justify-content-end' : '' }}">
                                            <small class="text-muted">{{ $message->sender->name }}</small>
                                            <small class="text-muted ms-2">{{ $message->created_at->format('M d, H:i') }}</small>
                                            @if($message->message_type !== 'text')
                                                <span class="badge bg-secondary ms-2">{{ ucfirst($message->message_type) }}</span>
                                            @endif
                                        </div>
                                        
                                        <!-- Message Bubble -->
                                        <div class="rounded p-3 position-relative {{ $message->sender_id === auth()->id() ? 'bg-primary text-white' : ($message->message_type === 'system' ? 'bg-warning text-dark' : 'bg-light') }}">
                                            {{ $message->message }}
                                            
                                            <!-- Delete button for own messages -->
                                            @if($message->sender_id === auth()->id() || auth()->user()->isAdmin())
                                                <button type="button" class="btn btn-sm btn-link position-absolute top-0 end-0 p-1" 
                                                        onclick="deleteMessage({{ $message->id }})"
                                                        style="font-size: 0.75rem;">
                                                    <i class="fas fa-times text-{{ $message->sender_id === auth()->id() ? 'white' : 'secondary' }}"></i>
                                                </button>
                                            @endif
                                        </div>
                                        
                                        <!-- Read status -->
                                        @if($message->sender_id === auth()->id())
                                            <div class="text-end mt-1">
                                                <small class="text-muted">
                                                    @if($message->is_read)
                                                        <i class="fas fa-check-double text-info"></i> Read
                                                    @else
                                                        <i class="fas fa-check"></i> Sent
                                                    @endif
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
                        <i class="fas fa-comment fa-3x mb-3"></i>
                        <div>No messages yet</div>
                        <small>Start the conversation below</small>
                    </div>
                @endif
            </div>

            <!-- Message Input -->
            <div class="border-top pt-3 mt-3">
                <form id="messageForm">
                    @csrf
                    <div class="input-group">
                        <input type="text" class="form-control" id="messageInput" 
                               placeholder="Type your message..." maxlength="2000" required>
                        <button type="submit" class="btn btn-primary" id="sendButton">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <small class="text-muted">
                            <span id="charCount">0</span>/2000 characters
                        </small>
                        <small class="text-muted">
                            Press Enter to send, Shift+Enter for new line
                        </small>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Typing Indicator -->
<div id="typingIndicator" class="position-fixed bottom-0 start-50 translate-middle-x bg-light border rounded-pill px-3 py-2 d-none" style="z-index: 1000;">
    <small class="text-muted">
        <i class="fas fa-ellipsis-h"></i> Someone is typing...
    </small>
</div>

<script>
// Chat functionality
let clientId = {{ $client->id }};
let lastMessageId = {{ $messages->last()?->id ?? 0 }};
let isTyping = false;
let typingTimer;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Scroll to bottom
    scrollToBottom();
    
    // Focus input
    document.getElementById('messageInput').focus();
    
    // Start real-time updates
    startRealTimeUpdates();
    
    // Character counter
    document.getElementById('messageInput').addEventListener('input', updateCharCount);
    
    // Typing indicator
    document.getElementById('messageInput').addEventListener('input', handleTyping);
});

// Send message
document.getElementById('messageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    
    if (!message) return;
    
    const sendButton = document.getElementById('sendButton');
    const originalHtml = sendButton.innerHTML;
    
    sendButton.disabled = true;
    sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    fetch(`{{ route('clients.chat.store', $client) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Add message to chat
            addMessageToChat(data.message);
            
            // Clear input
            input.value = '';
            updateCharCount();
            
            // Update unread count
            updateUnreadCount(data.unread_count);
            
            // Scroll to bottom
            scrollToBottom();
        } else {
            showAlert('error', data.error || 'Failed to send message');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while sending the message');
    })
    .finally(() => {
        sendButton.disabled = false;
        sendButton.innerHTML = originalHtml;
        input.focus();
    });
});

// Add message to chat
function addMessageToChat(message) {
    const container = document.getElementById('messagesContainer');
    const messagesList = container.querySelector('.messages-list') || createMessagesList();
    
    const messageHtml = `
        <div class="message-item mb-3" data-message-id="${message.id}">
            <div class="d-flex justify-content-end">
                <div class="max-width-70">
                    <div class="d-flex align-items-center mb-1 justify-content-end">
                        <small class="text-muted">${message.sender.name}</small>
                        <small class="text-muted ms-2">${new Date(message.created_at).toLocaleString()}</small>
                    </div>
                    <div class="rounded p-3 bg-primary text-white position-relative">
                        ${message.message}
                    </div>
                    <div class="text-end mt-1">
                        <small class="text-muted">
                            <i class="fas fa-check"></i> Sent
                        </small>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    messagesList.insertAdjacentHTML('beforeend', messageHtml);
    lastMessageId = message.id;
}

// Create messages list if it doesn't exist
function createMessagesList() {
    const container = document.getElementById('messagesContainer');
    const messagesList = document.createElement('div');
    messagesList.className = 'messages-list';
    container.appendChild(messagesList);
    return messagesList;
}

// Real-time updates
function startRealTimeUpdates() {
    setInterval(() => {
        fetch(`{{ route('clients.chat.recent', $client) }}?last_message_id=${lastMessageId}&limit=10`, {
            headers: {
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(message => {
                    if (message.id > lastMessageId) {
                        addReceivedMessage(message);
                        lastMessageId = message.id;
                    }
                });
                scrollToBottom();
            }
        })
        .catch(error => console.error('Error fetching messages:', error));
    }, 5000); // Check every 5 seconds
}

// Add received message
function addReceivedMessage(message) {
    const container = document.getElementById('messagesContainer');
    const messagesList = container.querySelector('.messages-list') || createMessagesList();
    
    const messageHtml = `
        <div class="message-item mb-3" data-message-id="${message.id}">
            <div class="d-flex justify-content-start">
                <div class="max-width-70">
                    <div class="d-flex align-items-center mb-1">
                        <small class="text-muted">${message.sender.name}</small>
                        <small class="text-muted ms-2">${new Date(message.created_at).toLocaleString()}</small>
                        ${message.message_type !== 'text' ? `<span class="badge bg-secondary ms-2">${message.message_type}</span>` : ''}
                    </div>
                    <div class="rounded p-3 ${message.message_type === 'system' ? 'bg-warning text-dark' : 'bg-light'}">
                        ${message.message}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    messagesList.insertAdjacentHTML('beforeend', messageHtml);
    
    // Update unread count
    if (message.sender_id !== {{ auth()->id() }}) {
        const currentCount = parseInt(document.querySelector('.badge.bg-danger')?.textContent || '0');
        updateUnreadCount(currentCount + 1);
    }
}

// Delete message
function deleteMessage(messageId) {
    if (!confirm('Are you sure you want to delete this message?')) {
        return;
    }
    
    fetch(`{{ route('clients.chat.delete', [$client, ':message']) }}`.replace(':message', messageId), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove message from DOM
            const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
            if (messageElement) {
                messageElement.remove();
            }
            showAlert('success', 'Message deleted successfully');
        } else {
            showAlert('error', data.error || 'Failed to delete message');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while deleting the message');
    });
}

// Mark all as read
function markAllAsRead() {
    fetch(`{{ route('clients.chat.mark-read', $client) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateUnreadCount(0);
            showAlert('success', 'All messages marked as read');
        }
    })
    .catch(error => console.error('Error marking messages as read:', error));
}

// Refresh messages
function refreshMessages() {
    location.reload();
}

// Update character count
function updateCharCount() {
    const input = document.getElementById('messageInput');
    const count = input.value.length;
    document.getElementById('charCount').textContent = count;
}

// Handle typing indicator
function handleTyping() {
    if (!isTyping) {
        isTyping = true;
        // Broadcast typing start (implement with WebSockets/Pusher)
    }
    
    clearTimeout(typingTimer);
    typingTimer = setTimeout(() => {
        isTyping = false;
        // Broadcast typing stop (implement with WebSockets/Pusher)
    }, 1000);
}

// Update unread count
function updateUnreadCount(count) {
    const badge = document.querySelector('.badge.bg-danger');
    if (badge) {
        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    }
}

// Scroll to bottom
function scrollToBottom() {
    const container = document.getElementById('messagesContainer');
    container.scrollTop = container.scrollHeight;
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

// Handle Enter key in message input
document.getElementById('messageInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        document.getElementById('messageForm').dispatchEvent(new Event('submit'));
    }
});
</script>

<style>
.max-width-70 {
    max-width: 70%;
}

.messages-list {
    min-height: 100%;
}

#messagesContainer {
    scroll-behavior: smooth;
}

.message-item {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endsection
