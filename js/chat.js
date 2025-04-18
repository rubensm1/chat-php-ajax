// Chat System JavaScript

$(document).ready(function() {
    // Variables
    let lastMessageTime = null;
    let messageSound = new Audio('sounds/message.mp3');
    let autoScroll = true;
    let currentRoom = $('#room-id').val();
    let privateRecipient = null;
    
    // Initialize
    if (currentRoom) {
        // Load initial messages
        loadMessages();
        
        // Set up polling for new messages
        setInterval(checkNewMessages, 3000);
        
        // Set up auto-scroll
        $('#auto-scroll').change(function() {
            autoScroll = $(this).is(':checked');
            if (autoScroll) {
                scrollToBottom();
            }
        });
        
        // Set up sound notifications
        $('#sound-notification').change(function() {
            // Nothing to do here, we'll check this when messages arrive
        });
        
        // Set up private messaging
        $('#private-recipient').change(function() {
            privateRecipient = $(this).val();
            if (privateRecipient) {
                $('#message-input').attr('placeholder', 'Private message to ' + $('#private-recipient option:selected').text());
            } else {
                $('#message-input').attr('placeholder', 'Type your message here...');
            }
        });
        
        // Handle message form submission
        $('#message-form').submit(function(e) {
            e.preventDefault();
            sendMessage();
        });
        
        // Handle user clicks for private messaging
        $(document).on('click', '.user-item', function() {
            const userId = $(this).data('user-id');
            $('#private-recipient').val(userId).change();
        });
    }
    
    // Create room form submission
    $('#create-room-form').submit(function(e) {
        e.preventDefault();
        createRoom();
    });
    
    // Functions
    
    // Load messages for the current room
    function loadMessages() {
        $.ajax({
            url: 'ajax/get_messages.php',
            type: 'GET',
            data: { room_id: currentRoom },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    displayMessages(response.messages, true);
                    scrollToBottom();
                    
                    // Update last message time
                    if (response.messages.length > 0) {
                        lastMessageTime = response.messages[response.messages.length - 1].sent_at;
                    }
                }
            }
        });
    }
    
    // Check for new messages
    function checkNewMessages() {
        if (!lastMessageTime) return;
        
        $.ajax({
            url: 'ajax/get_new_messages.php',
            type: 'GET',
            data: { 
                room_id: currentRoom,
                since: lastMessageTime
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.messages.length > 0) {
                    displayMessages(response.messages, false);
                    
                    // Play sound if enabled
                    if ($('#sound-notification').is(':checked')) {
                        messageSound.play();
                    }
                    
                    // Auto-scroll if enabled
                    if (autoScroll) {
                        scrollToBottom();
                    }
                    
                    // Update last message time
                    lastMessageTime = response.messages[response.messages.length - 1].sent_at;
                    
                    // Update user list
                    updateUserList();
                }
            }
        });
    }
    
    // Display messages in the chat area
    function displayMessages(messages, clear = false) {
        const $messagesContainer = $('.chat-messages');
        const currentUserId = $('#user-id').val();
        
        if (clear) {
            $messagesContainer.empty();
        }
        
        $.each(messages, function(i, message) {
            let messageClass = 'message-incoming';
            
            if (message.user_id == currentUserId) {
                messageClass = 'message-outgoing';
            }
            
            if (message.is_private == 1) {
                messageClass += ' message-private';
            }
            
            let privateLabel = '';
            if (message.is_private == 1) {
                if (message.user_id == currentUserId) {
                    privateLabel = ' (Private to ' + message.recipient_username + ')';
                } else {
                    privateLabel = ' (Private)';
                }
            }
            
            const $message = $(`
                <div class="message ${messageClass}">
                    <div class="message-header">
                        <span class="message-sender">${message.username}${privateLabel}</span>
                        <span class="message-time">${formatTimestamp(message.sent_at)}</span>
                    </div>
                    <div class="message-content">${escapeHtml(message.message)}</div>
                </div>
            `);
            
            $messagesContainer.append($message);
        });
    }
    
    // Send a message
    function sendMessage() {
        const $messageInput = $('#message-input');
        const message = $messageInput.val().trim();
        
        if (message === '') return;
        
        $.ajax({
            url: 'ajax/send_message.php',
            type: 'POST',
            data: { 
                room_id: currentRoom,
                message: message,
                is_private: privateRecipient ? 1 : 0,
                recipient_id: privateRecipient
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $messageInput.val('');
                    checkNewMessages();
                }
            }
        });
    }
    
    // Create a new chat room
    function createRoom() {
        const roomName = $('#room-name').val().trim();
        
        if (roomName === '') return;
        
        $.ajax({
            url: 'ajax/create_room.php',
            type: 'POST',
            data: { room_name: roomName },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    window.location.href = 'chat.php?room_id=' + response.room_id;
                }
            }
        });
    }
    
    // Update the user list
    function updateUserList() {
        $.ajax({
            url: 'ajax/get_users_in_room.php',
            type: 'GET',
            data: { room_id: currentRoom },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    displayUsers(response.users);
                }
            }
        });
    }
    
    // Display users in the sidebar
    function displayUsers(users) {
        const $userList = $('.user-list');
        const $privateRecipient = $('#private-recipient');
        const currentRecipient = $privateRecipient.val();
        
        $userList.empty();
        $privateRecipient.empty();
        $privateRecipient.append('<option value="">Everyone</option>');
        
        $.each(users, function(i, user) {
            let statusClass = 'status-offline';
            
            if (user.status === 'online') {
                statusClass = 'status-online';
            } else if (user.status === 'away') {
                statusClass = 'status-away';
            }
            
            const $userItem = $(`
                <li class="user-item" data-user-id="${user.user_id}">
                    <span class="user-status ${statusClass}"></span>
                    <span class="user-name">${user.username}</span>
                </li>
            `);
            
            $userList.append($userItem);
            
            // Add to private recipient dropdown
            if (user.user_id != $('#user-id').val()) {
                $privateRecipient.append(`<option value="${user.user_id}">${user.username}</option>`);
            }
        });
        
        // Restore selected recipient
        if (currentRecipient) {
            $privateRecipient.val(currentRecipient);
        }
    }
    
    // Scroll to the bottom of the chat messages
    function scrollToBottom() {
        const $messagesContainer = $('.chat-messages');
        $messagesContainer.scrollTop($messagesContainer[0].scrollHeight);
    }
    
    // Format timestamp for display
    function formatTimestamp(timestamp) {
        const date = new Date(timestamp);
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');
        
        return `${hours}:${minutes}`;
    }
    
    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
