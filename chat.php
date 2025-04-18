<?php
// Include session file
require_once 'includes/session.php';

// Require login
requireLogin();

// Get room ID
$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;

// Validate room ID
if ($room_id <= 0) {
    header('Location: index.php');
    exit;
}

// Get room details
$room = getChatRoomById($room_id);

// Check if room exists
if (!$room) {
    header('Location: index.php');
    exit;
}

// Join the room
joinChatRoom($room_id, $_SESSION['user_id']);

// Get users in the room
$users = getUsersInRoom($room_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $room['room_name']; ?> - Chat System</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/chat.js"></script>
</head>
<body>
    <header class="header">
        <div class="container">
            <h1>Chat System</h1>
            <div class="nav">
                <a href="index.php">Back to Rooms</a>
                <span>Welcome, <?php echo $_SESSION['username']; ?></span>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <div class="chat-container">
            <div class="chat-sidebar">
                <div class="room-header">
                    <h2><?php echo $room['room_name']; ?></h2>
                    <p><span id="user-count"><?php echo count($users); ?></span> users in room</p>
                </div>
                
                <h3>Users</h3>
                <ul class="user-list">
                    <?php foreach ($users as $user): ?>
                        <?php
                        $statusClass = 'status-offline';
                        if ($user['status'] === 'online') {
                            $statusClass = 'status-online';
                        } elseif ($user['status'] === 'away') {
                            $statusClass = 'status-away';
                        }
                        ?>
                        <li class="user-item" data-user-id="<?php echo $user['user_id']; ?>">
                            <span class="user-status <?php echo $statusClass; ?>"></span>
                            <span class="user-name"><?php echo $user['username']; ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="chat-main">
                <div class="chat-messages">
                    <!-- Messages will be loaded here via AJAX -->
                </div>
                
                <div class="chat-options">
                    <div class="option-item">
                        <input type="checkbox" id="sound-notification" checked>
                        <label for="sound-notification">Sound Notifications</label>
                    </div>
                    
                    <div class="option-item">
                        <input type="checkbox" id="auto-scroll" checked>
                        <label for="auto-scroll">Auto-scroll</label>
                    </div>
                    
                    <div class="private-message-select">
                        <label for="private-recipient">Send to:</label>
                        <select id="private-recipient">
                            <option value="">Everyone</option>
                            <?php foreach ($users as $user): ?>
                                <?php if ($user['user_id'] != $_SESSION['user_id']): ?>
                                    <option value="<?php echo $user['user_id']; ?>"><?php echo $user['username']; ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <form id="message-form" class="chat-form">
                    <input type="hidden" id="room-id" value="<?php echo $room_id; ?>">
                    <input type="hidden" id="user-id" value="<?php echo $_SESSION['user_id']; ?>">
                    <input type="text" id="message-input" placeholder="Type your message here..." autocomplete="off">
                    <button type="submit" class="btn">Send</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
