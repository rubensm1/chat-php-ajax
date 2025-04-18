# Real-Time Chat System

A complete real-time chat system built with PHP, MySQL, and jQuery.

## Features

1. **User Authentication**
   - User registration with email, username, and password
   - Secure login with password hashing
   - Session management with timeout

2. **Chat Rooms**
   - View list of active chat rooms
   - Join existing chat rooms
   - Create new chat rooms

3. **Real-Time Messaging**
   - Send and receive messages in real-time using AJAX
   - Private messaging between users
   - Message timestamps
   - Sound notifications for new messages
   - Auto-scroll option

4. **User Status**
   - Online/offline status indicators
   - User list in each chat room

## Technical Implementation

### Database Structure

The system uses MySQL with the following tables:
- `users`: Stores user information and authentication details
- `chat_rooms`: Stores information about chat rooms
- `messages`: Stores all messages with room and user references
- `room_participants`: Tracks which users are in which rooms

### Technologies Used

- **Backend**: PHP with PDO for database operations
- **Database**: MySQL
- **Frontend**: HTML, CSS, JavaScript
- **AJAX**: jQuery for asynchronous updates
- **Real-time Updates**: Polling technique with AJAX

## Setup Instructions

### Option 1: Using Docker (Recommended)

1. **Prerequisites**
   - Install [Docker](https://www.docker.com/get-started) and [Docker Compose](https://docs.docker.com/compose/install/)

2. **Run the Application**
   - Clone or download this repository
   - Open a terminal in the project directory
   - Run `docker-compose up -d`
   - Access the chat system at http://localhost:8080
   - Access PHPMyAdmin at http://localhost:8081 (username: root, password: root_password)

3. **Stop the Application**
   - Run `docker-compose down` to stop the containers
   - Use `docker-compose down -v` to also remove the database volume

### Option 2: Manual Setup

1. **Database Setup**
   - Create a MySQL database named `chat_system`
   - Import the `database.sql` file to create the necessary tables

2. **Configuration**
   - Update database connection parameters in `includes/db_connect.php`
   - Ensure the web server has write permissions to the project directory

3. **Web Server**
   - Deploy the files to a PHP-enabled web server (Apache, Nginx, etc.)
   - Access the application through the web server URL

## Usage

1. **Registration/Login**
   - Register a new account or login with existing credentials
   - The system will redirect to the chat rooms page after successful login

2. **Chat Rooms**
   - View available chat rooms on the homepage
   - Join a room by clicking the "Join Room" button
   - Create a new room using the form at the bottom of the page

3. **Chatting**
   - Type messages in the input field and press Enter or click Send
   - Messages appear in real-time for all users in the room
   - To send a private message, select a user from the dropdown
   - Toggle sound notifications and auto-scroll using the checkboxes

## Security Features

- Password hashing using PHP's `password_hash()` function
- Input sanitization to prevent XSS attacks
- Session management with timeout
- PDO prepared statements to prevent SQL injection

## Future Improvements

- WebSocket implementation for true real-time communication
- File sharing capabilities
- User profile customization
- Message editing and deletion
- Read receipts
- Typing indicators
