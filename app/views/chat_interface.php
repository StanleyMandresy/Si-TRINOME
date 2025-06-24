<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - Ticket #<?= htmlspecialchars($ticketId) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
        }

        .chat-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            height: calc(100vh - 40px);
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            text-align: center;
            position: relative;
        }

        .profile-switcher {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .profile-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.3s;
        }

        .profile-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .chat-header h2 {
            margin-bottom: 5px;
        }

        .chat-status {
            font-size: 14px;
            opacity: 0.9;
        }

        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #f8f9fa;
        }

        .message {
            margin-bottom: 15px;
            display: flex;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message.client {
            justify-content: flex-end;
        }

        .message.agent {
            justify-content: flex-start;
        }

        .message-content {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 18px;
            position: relative;
        }

        .message.client .message-content {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom-right-radius: 4px;
        }

        .message.agent .message-content {
            background: #e9ecef;
            color: #333;
            border-bottom-left-radius: 4px;
        }

        .message-time {
            font-size: 11px;
            opacity: 0.7;
            margin-top: 5px;
        }

        .message.client .message-time {
            text-align: right;
        }

        .message.agent .message-time {
            text-align: left;
        }

        .chat-input {
            padding: 20px;
            background: white;
            border-top: 1px solid #e9ecef;
            border-radius: 0 0 10px 10px;
        }

        .input-group {
            display: flex;
            gap: 10px;
        }

        #messageInput {
            flex: 1;
            padding: 12px 16px;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s;
        }

        #messageInput:focus {
            border-color: #667eea;
        }

        #sendButton {
            padding: 12px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            transition: transform 0.2s;
        }

        #sendButton:hover {
            transform: translateY(-2px);
        }

        #sendButton:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .typing-indicator {
            display: none;
            padding: 10px;
            font-style: italic;
            color: #666;
            text-align: center;
        }

        .unread-badge {
            background: #ff4757;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            margin-left: 10px;
        }

        .connection-status {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 8px 12px;
            border-radius: 20px;
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        .connection-status.online {
            background: #2ed573;
        }

        .connection-status.offline {
            background: #ff4757;
        }

        .no-messages {
            text-align: center;
            color: #666;
            font-style: italic;
            margin-top: 50px;
        }

        @media (max-width: 768px) {
            .chat-container {
                margin: 10px;
                height: calc(100vh - 20px);
                border-radius: 0;
            }

            .chat-header {
                border-radius: 0;
            }

            .chat-input {
                border-radius: 0;
            }

            .message-content {
                max-width: 85%;
            }
        }
    </style>
</head>
<body>
    <div class="connection-status online" id="connectionStatus">
        En ligne
    </div>

    <div class="chat-container">
        <div class="chat-header">
            <div class="profile-switcher">
                <button class="profile-btn" id="switchProfile">Passer en <?= $userType === 'client' ? 'Agent' : 'Client' ?></button>
            </div>
            <h2>Support Chat</h2>
            <div class="chat-status">
                Ticket #<?= htmlspecialchars($ticketId) ?> - 
                <span id="currentProfile"><?= $userType === 'client' ? 'Client' : 'Agent' ?></span>
                <span id="unreadBadge" class="unread-badge" style="display: none;">0</span>
            </div>
        </div>

        <div class="chat-messages" id="chatMessages">
            <?php if (empty($messages)): ?>
                <div class="no-messages">
                    Aucun message pour le moment. Démarrez la conversation !
                </div>
            <?php else: ?>
                <?php foreach ($messages as $message): ?>
                    <div class="message <?= !empty($message['commentaire_client']) ? 'client' : 'agent' ?>" 
                         data-message-id="<?= $message['id'] ?>">
                        <div class="message-content">
                            <?= htmlspecialchars($message['commentaire_client'] ?: $message['commentaire_agent']) ?>
                            <div class="message-time">
                                <?= date('H:i', strtotime($message['timestamp'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="typing-indicator" id="typingIndicator">
            L'autre utilisateur est en train d'écrire...
        </div>

        <div class="chat-input">
            <div class="input-group">
                <input type="text" 
                       id="messageInput" 
                       placeholder="Tapez votre message..." 
                       maxlength="500">
                <button id="sendButton">Envoyer</button>
            </div>
        </div>
    </div>

    <script>
    class ChatManager {
        constructor() {
            this.ticketId = <?= json_encode($ticketId) ?>;
            this.userType = <?= json_encode($userType) ?>;
            this.lastMessageId = this.getLastMessageId();
            this.pollingInterval = null;
            this.isTyping = false;
            this.typingTimer = null;
            
            this.initializeElements();
            this.bindEvents();
            this.startPolling();
            this.scrollToBottom();
            this.markMessagesAsRead();
        }

        initializeElements() {
            this.messageInput = document.getElementById('messageInput');
            this.sendButton = document.getElementById('sendButton');
            this.chatMessages = document.getElementById('chatMessages');
            this.typingIndicator = document.getElementById('typingIndicator');
            this.connectionStatus = document.getElementById('connectionStatus');
            this.unreadBadge = document.getElementById('unreadBadge');
            this.switchProfileBtn = document.getElementById('switchProfile');
            this.currentProfileSpan = document.getElementById('currentProfile');
        }

        bindEvents() {
            this.sendButton.addEventListener('click', () => this.sendMessage());
            this.messageInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.sendMessage();
                }
            });

            this.switchProfileBtn.addEventListener('click', () => this.switchProfile());

            window.addEventListener('focus', () => {
                this.markMessagesAsRead();
            });
        }

        switchProfile() {
            this.userType = this.userType === 'client' ? 'agent' : 'client';
            this.currentProfileSpan.textContent = this.userType === 'client' ? 'Client' : 'Agent';
            this.switchProfileBtn.textContent = `Passer en ${this.userType === 'client' ? 'Agent' : 'Client'}`;
            
            // Recharger les messages avec le nouveau profil
            this.lastMessageId = 0;
            this.chatMessages.innerHTML = '<div class="no-messages">Chargement des messages...</div>';
            this.pollForNewMessages();
        }

        async sendMessage() {
            const message = this.messageInput.value.trim();
            if (!message) return;

            this.sendButton.disabled = true;
            this.messageInput.disabled = true;

            try {
                const response = await fetch('/chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        ticket_id: this.ticketId,
                        message: message,
                        user_type: this.userType
                    })
                });

                const result = await response.json();

                if (result.success) {
                    this.messageInput.value = '';
                    // Ajouter le message localement pour un feedback immédiat
                    this.addMessageToChat(message, this.userType, new Date().toISOString());
                    this.scrollToBottom();
                } else {
                    throw new Error(result.error || 'Erreur lors de l\'envoi');
                }
            } catch (error) {
                console.error('Erreur:', error);
                this.showConnectionError();
            } finally {
                this.sendButton.disabled = false;
                this.messageInput.disabled = false;
                this.messageInput.focus();
            }
        }

        async pollForNewMessages() {
            try {
                const response = await fetch(`/chat/messages?ticket_id=${this.ticketId}&last_message_id=${this.lastMessageId}`);
                const result = await response.json();

                if (result.success) {
                    if (result.messages.length > 0) {
                        result.messages.forEach(message => {
                            const messageText = message.commentaire_client || message.commentaire_agent;
                            const messageType = message.commentaire_client ? 'client' : 'agent';
                            
                            const existingMsg = document.querySelector(`[data-message-id="${message.id}"]`);
                            if (!existingMsg) {
                                this.addMessageToChat(messageText, messageType, message.timestamp, message.id);
                                this.updateUnreadCount();
                            }
                            
                            this.lastMessageId = Math.max(this.lastMessageId, message.id);
                        });
                        
                        this.scrollToBottom();
                    }
                }

                this.setConnectionStatus(true);
            } catch (error) {
                console.error('Erreur de polling:', error);
                this.setConnectionStatus(false);
            }
        }

        addMessageToChat(message, userType, timestamp, messageId = null) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${userType}`;
            if (messageId) messageDiv.setAttribute('data-message-id', messageId);

            const timeStr = new Date(timestamp).toLocaleTimeString('fr-FR', {
                hour: '2-digit',
                minute: '2-digit'
            });

            messageDiv.innerHTML = `
                <div class="message-content">
                    ${this.escapeHtml(message)}
                    <div class="message-time">${timeStr}</div>
                </div>
            `;

            const noMessages = this.chatMessages.querySelector('.no-messages');
            if (noMessages) {
                noMessages.remove();
            }

            this.chatMessages.appendChild(messageDiv);
        }

        async markMessagesAsRead() {
            try {
                await fetch('/chat/mark-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        ticket_id: this.ticketId,
                        user_type: this.userType
                    })
                });
                
                this.updateUnreadCount();
            } catch (error) {
                console.error('Erreur marquage lu:', error);
            }
        }

        async updateUnreadCount() {
            try {
                const response = await fetch(`/chat/unread-count?ticket_id=${this.ticketId}&user_type=${this.userType}`);
                const result = await response.json();
                
                if (result.success) {
                    const count = result.unread_count;
                    if (count > 0) {
                        this.unreadBadge.textContent = count;
                        this.unreadBadge.style.display = 'inline';
                    } else {
                        this.unreadBadge.style.display = 'none';
                    }
                }
            } catch (error) {
                console.error('Erreur comptage non lus:', error);
            }
        }

        startPolling() {
            this.pollingInterval = setInterval(() => {
                this.pollForNewMessages();
            }, 2000);
        }

        stopPolling() {
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
            }
        }

        setConnectionStatus(isOnline) {
            this.connectionStatus.textContent = isOnline ? 'En ligne' : 'Hors ligne';
            this.connectionStatus.className = `connection-status ${isOnline ? 'online' : 'offline'}`;
        }

        showConnectionError() {
            this.setConnectionStatus(false);
            setTimeout(() => {
                this.setConnectionStatus(true);
            }, 3000);
        }

        scrollToBottom() {
            this.chatMessages.scrollTop = this.chatMessages.scrollHeight;
        }

        getLastMessageId() {
            const messages = document.querySelectorAll('[data-message-id]');
            let maxId = 0;
            messages.forEach(msg => {
                const id = parseInt(msg.getAttribute('data-message-id'));
                if (id > maxId) maxId = id;
            });
            return maxId;
        }

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    }

    // Initialisation
    document.addEventListener('DOMContentLoaded', () => {
        window.chatManager = new ChatManager();
    });

    window.addEventListener('beforeunload', () => {
        if (window.chatManager) {
            window.chatManager.stopPolling();
        }
    });
    </script>
</body>
</html>