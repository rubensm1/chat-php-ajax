<?php
// Include database connection
require_once 'db_connect.php';

/**
 * User Functions
 */

// Register a new user
function registerUser($username, $password, $email) {
    global $pdo;
    
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (:username, :password, :email)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $pdo->lastInsertId();
    } catch(PDOException $e) {
        return false;
    }
}

// Authenticate a user
function loginUser($username, $password) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        if($stmt->rowCount() == 1) {
            $user = $stmt->fetch();
            
            if(password_verify($password, $user['password'])) {
                // Update user status to online
                updateUserStatus($user['user_id'], 'online');
                return $user;
            }
        }
        
        return false;
    } catch(PDOException $e) {
        return false;
    }
}

// Update user status (online, offline, away)
function updateUserStatus($user_id, $status) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET status = :status WHERE user_id = :user_id");
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return true;
    } catch(PDOException $e) {
        return false;
    }
}

// Get user by ID
function getUserById($user_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        if($stmt->rowCount() == 1) {
            return $stmt->fetch();
        }
        
        return false;
    } catch(PDOException $e) {
        return false;
    }
}

/**
 * Chat Room Functions
 */

// Create a new chat room
function createChatRoom($room_name, $user_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO chat_rooms (room_name, created_by) VALUES (:room_name, :created_by)");
        $stmt->bindParam(':room_name', $room_name);
        $stmt->bindParam(':created_by', $user_id);
        $stmt->execute();
        
        $room_id = $pdo->lastInsertId();
        
        // Add creator as a participant
        joinChatRoom($room_id, $user_id);
        
        return $room_id;
    } catch(PDOException $e) {
        return false;
    }
}

// Get all chat rooms
function getAllChatRooms() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT cr.*, COUNT(rp.user_id) as user_count 
                               FROM chat_rooms cr 
                               LEFT JOIN room_participants rp ON cr.room_id = rp.room_id 
                               GROUP BY cr.room_id 
                               ORDER BY cr.created_at DESC");
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        return [];
    }
}

// Get chat room by ID
function getChatRoomById($room_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM chat_rooms WHERE room_id = :room_id");
        $stmt->bindParam(':room_id', $room_id);
        $stmt->execute();
        
        if($stmt->rowCount() == 1) {
            return $stmt->fetch();
        }
        
        return false;
    } catch(PDOException $e) {
        return false;
    }
}

// Join a chat room
function joinChatRoom($room_id, $user_id) {
    global $pdo;
    
    try {
        // Check if user is already in the room
        $check = $pdo->prepare("SELECT * FROM room_participants WHERE room_id = :room_id AND user_id = :user_id");
        $check->bindParam(':room_id', $room_id);
        $check->bindParam(':user_id', $user_id);
        $check->execute();
        
        if($check->rowCount() == 0) {
            $stmt = $pdo->prepare("INSERT INTO room_participants (room_id, user_id) VALUES (:room_id, :user_id)");
            $stmt->bindParam(':room_id', $room_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
        }
        
        return true;
    } catch(PDOException $e) {
        return false;
    }
}

// Leave a chat room
function leaveChatRoom($room_id, $user_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("DELETE FROM room_participants WHERE room_id = :room_id AND user_id = :user_id");
        $stmt->bindParam(':room_id', $room_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return true;
    } catch(PDOException $e) {
        return false;
    }
}

// Get users in a chat room
function getUsersInRoom($room_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT u.* FROM users u 
                               JOIN room_participants rp ON u.user_id = rp.user_id 
                               WHERE rp.room_id = :room_id");
        $stmt->bindParam(':room_id', $room_id);
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        return [];
    }
}

/**
 * Message Functions
 */

// Send a message
function sendMessage($room_id, $user_id, $message, $is_private = false, $recipient_id = null) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO messages (room_id, user_id, message, is_private, recipient_id) 
                               VALUES (:room_id, :user_id, :message, :is_private, :recipient_id)");
        $stmt->bindParam(':room_id', $room_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':is_private', $is_private, PDO::PARAM_BOOL);
        $stmt->bindParam(':recipient_id', $recipient_id);
        $stmt->execute();
        
        return $pdo->lastInsertId();
    } catch(PDOException $e) {
        return false;
    }
}

// Get messages for a room
function getMessages($room_id, $limit = 50) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT m.*, u.username FROM messages m 
                               JOIN users u ON m.user_id = u.user_id 
                               WHERE m.room_id = :room_id AND (m.is_private = 0 OR m.recipient_id = :user_id OR m.user_id = :user_id) 
                               ORDER BY m.sent_at DESC 
                               LIMIT :limit");
        $stmt->bindParam(':room_id', $room_id);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $messages = $stmt->fetchAll();
        // Reverse to get chronological order
        return array_reverse($messages);
    } catch(PDOException $e) {
        return [];
    }
}

// Get new messages since a specific time
function getNewMessages($room_id, $since_timestamp) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT m.*, u.username FROM messages m 
                               JOIN users u ON m.user_id = u.user_id 
                               WHERE m.room_id = :room_id 
                               AND m.sent_at > :since_timestamp 
                               AND (m.is_private = 0 OR m.recipient_id = :user_id OR m.user_id = :user_id) 
                               ORDER BY m.sent_at ASC");
        $stmt->bindParam(':room_id', $room_id);
        $stmt->bindParam(':since_timestamp', $since_timestamp);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        return [];
    }
}

/**
 * Utility Functions
 */

// Sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Format timestamp for display
function formatTimestamp($timestamp) {
    $date = new DateTime($timestamp);
    $now = new DateTime();
    $diff = $now->diff($date);
    
    if ($diff->d == 0) {
        return $date->format('H:i'); // Today, show only time
    } elseif ($diff->d == 1) {
        return 'Yesterday ' . $date->format('H:i');
    } else {
        return $date->format('M j, H:i'); // Show date and time
    }
}
?>
