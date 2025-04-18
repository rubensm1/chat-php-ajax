<?php
// Include session file
require_once 'includes/session.php';

// Require login
requireLogin();

// Initialize variables
$error = '';
$success = '';

// Get all chat rooms
$rooms = getAllChatRooms();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Rooms - Chat System</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/chat.js"></script>
</head>
<body>
    <header class="header">
        <div class="container">
            <h1>Chat System</h1>
            <div class="nav">
                <span>Welcome, <?php echo $_SESSION['username']; ?></span>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="rooms-container">
            <h2>Available Chat Rooms</h2>
            
            <?php if (empty($rooms)): ?>
                <p>No chat rooms available. Create one below!</p>
            <?php else: ?>
                <ul class="room-list">
                    <?php foreach ($rooms as $room): ?>
                        <li class="room-item">
                            <div class="room-info">
                                <h3><?php echo $room['room_name']; ?></h3>
                                <div class="room-meta">
                                    <span><?php echo $room['user_count']; ?> users</span>
                                    <span>Created: <?php echo formatTimestamp($room['created_at']); ?></span>
                                </div>
                            </div>
                            <a href="chat.php?room_id=<?php echo $room['room_id']; ?>" class="btn">Join Room</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            
            <div class="create-room-form">
                <h3>Create a New Room</h3>
                <form id="create-room-form" method="post">
                    <div class="form-group">
                        <label for="room-name">Room Name:</label>
                        <input type="text" id="room-name" name="room_name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Create Room</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
