// Chat Notification System - Only for Admin
class ChatNotificationManager {
    constructor() {
        this.container = null;
        this.notifications = new Map();
        this.soundEnabled = true;
        this.audioUnlocked = false;
        this.preloadedAudio = null;
        this.echoChannel = null;
        this.pushNotificationEnabled = false;
        this.init();
        this.unlockAudio();
        this.requestNotificationPermission();
    }

    requestNotificationPermission() {
        // Check if browser supports notifications
        if (!('Notification' in window)) {
            console.log('⚠️ Browser does not support notifications');
            return;
        }

        // Request permission if not already granted
        if (Notification.permission === 'default') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    this.pushNotificationEnabled = true;
                    console.log('✅ Push notification permission granted');
                    // Show test notification
                    this.showTestNotification();
                } else {
                    console.log('❌ Push notification permission denied');
                }
            });
        } else if (Notification.permission === 'granted') {
            this.pushNotificationEnabled = true;
            console.log('✅ Push notification already enabled');
        } else {
            console.log('❌ Push notification permission denied');
        }
    }

    showTestNotification() {
        const notification = new Notification('Chat Notification Aktif', {
            body: 'Anda akan menerima notifikasi chat di sini',
            icon: '/favicon.ico',
            badge: '/favicon.ico',
            tag: 'test-notification',
            requireInteraction: false,
            silent: false
        });

        setTimeout(() => notification.close(), 3000);
    }

    init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.initContainer());
        } else {
            this.initContainer();
        }
    }

    unlockAudio() {
        // Preload audio
        this.preloadedAudio = new Audio('/sounds/42289.mp3');
        this.preloadedAudio.volume = 0.5;
        this.preloadedAudio.load();
        
        // Unlock audio on first user interaction
        const unlockAudioContext = () => {
            if (this.audioUnlocked) return;
            
            // Try to play and pause the preloaded audio to unlock
            this.preloadedAudio.play()
                .then(() => {
                    this.preloadedAudio.pause();
                    this.preloadedAudio.currentTime = 0;
                    this.audioUnlocked = true;
                    console.log('🔊 Audio unlocked - notifications will play sound');
                })
                .catch(() => {
                    // Still locked, will retry on next interaction
                });
        };

        // Listen for multiple types of interactions
        const events = ['click', 'touchstart', 'keydown', 'scroll', 'mousemove'];
        events.forEach(eventType => {
            document.addEventListener(eventType, unlockAudioContext, { once: true, passive: true });
        });
        
        console.log('🎵 Waiting for user interaction to unlock audio... (click, scroll, or move mouse)');
    }

    initContainer() {
        // Create container
        this.container = document.createElement('div');
        this.container.className = 'chat-notification-container';
        document.body.appendChild(this.container);

        // Setup Echo listener for incoming messages
        this.setupEchoListener();

        console.log('💬 Chat Notification Manager initialized');
    }

    setupEchoListener() {
        // Wait for Echo to be available
        if (!window.Echo) {
            console.log('⏳ Waiting for Echo to initialize...');
            setTimeout(() => this.setupEchoListener(), 100);
            return;
        }

        const userId = window.userId;
        if (!userId) {
            console.error('❌ User ID not found');
            return;
        }

        console.log('🔌 Setting up Echo listener for notifications on user:', userId);
        
        this.echoChannel = window.Echo.private(`chat.${userId}`)
            .subscribed(() => {
                console.log('✅ Subscribed to notification channel: chat.' + userId);
                console.log('📍 Waiting for MessageSent events on this channel...');
            })
            .listenToAll((event, data) => {
                console.log('🎯 ALL EVENTS RECEIVED ON NOTIFICATION CHANNEL:', event, data);
            })
            .listen('.MessageSent', (e) => {
                console.log('📩 New message received for notification:', e);
                
                // Show notification if:
                // 1. Not on chat page at all, OR
                // 2. On chat page but viewing different conversation
                const onChatPage = window.location.pathname.includes('/chat');
                const viewingThisUser = window.selectedUserId && window.selectedUserId == e.sender_id;
                
                console.log('🔍 Check notification display:', {
                    onChatPage,
                    viewingThisUser,
                    selectedUserId: window.selectedUserId,
                    senderId: e.sender_id,
                    shouldShow: !onChatPage || (onChatPage && !viewingThisUser)
                });
                
                // Show if not on chat page OR on chat but not viewing this user
                if (!onChatPage || !viewingThisUser) {
                    const senderName = e.sender ? e.sender.name : 'User';
                    console.log('✅ Showing notification for:', senderName);
                    this.show({
                        id: e.id,
                        sender: senderName,
                        message: e.message,
                        time: new Date(e.created_at).toLocaleTimeString('id-ID', { 
                            hour: '2-digit', 
                            minute: '2-digit' 
                        }),
                        senderId: e.sender_id
                    });
                } else {
                    console.log('⏭️ Skip notification - already viewing this conversation');
                }
            })
            .error((error) => {
                console.error('❌ Echo notification subscription error:', error);
            });
    }

    show(data) {
        const { id, sender, message, time, senderId } = data;
        
        // Don't show if notification already exists
        if (this.notifications.has(id)) {
            return;
        }

        // Show browser push notification (works even when tab not focused)
        this.showPushNotification(sender, message, senderId);

        // Create in-page notification element
        const notification = document.createElement('div');
        notification.className = 'chat-notification';
        notification.dataset.id = id;
        notification.dataset.senderId = senderId;
        
        const initials = this.getInitials(sender);
        const displayTime = time || new Date().toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });

        notification.innerHTML = `
            <div class="chat-notification-icon">
                ${initials}
            </div>
            <div class="chat-notification-content">
                <div class="chat-notification-header">
                    <span class="chat-notification-sender">${this.escapeHtml(sender)}</span>
                    <span class="chat-notification-time">${displayTime}</span>
                </div>
                <div class="chat-notification-message">${this.escapeHtml(message)}</div>
            </div>
            <button class="chat-notification-close" title="Tutup">
                <i class="fas fa-times"></i>
            </button>
        `;

        // Click to open chat
        notification.addEventListener('click', (e) => {
            if (!e.target.closest('.chat-notification-close')) {
                this.openChat(senderId, sender);
                this.remove(id);
            }
        });

        // Close button
        const closeBtn = notification.querySelector('.chat-notification-close');
        closeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.remove(id);
        });

        // Add to container
        this.container.appendChild(notification);
        this.notifications.set(id, notification);

        // Play sound (in-page audio, still needs interaction)
        if (this.soundEnabled && this.audioUnlocked) {
            this.playNotificationSound();
        }

        // Auto remove after 10 seconds
        setTimeout(() => {
            this.remove(id);
        }, 10000);

        console.log('📬 Notification shown:', { id, sender, message });
    }

    showPushNotification(sender, message, senderId) {
        if (!this.pushNotificationEnabled || Notification.permission !== 'granted') {
            console.log('⚠️ Push notification not enabled');
            return;
        }

        try {
            const notification = new Notification(`💬 ${sender}`, {
                body: message.length > 100 ? message.substring(0, 100) + '...' : message,
                icon: '/favicon.ico',
                badge: '/favicon.ico',
                tag: `chat-${senderId}`,
                requireInteraction: false,
                silent: false, // This allows sound to play!
                vibrate: [200, 100, 200],
                data: {
                    senderId: senderId,
                    sender: sender
                }
            });

            // Click notification to open chat
            notification.onclick = (e) => {
                e.preventDefault();
                window.focus();
                this.openChat(senderId, sender);
                notification.close();
            };

            // Auto close after 10 seconds
            setTimeout(() => notification.close(), 10000);

            console.log('🔔 Push notification shown');
        } catch (error) {
            console.error('❌ Failed to show push notification:', error);
        }
    }

    remove(id) {
        const notification = this.notifications.get(id);
        if (!notification) return;

        notification.classList.add('closing');
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
            this.notifications.delete(id);
        }, 300);
    }

    openChat(userId, userName) {
        console.log('🔗 Opening chat with:', userName, userId);
        
        // Redirect to admin chat page with selected user
        window.location.href = `/dashboard/admin/chat?user=${userId}`;
    }

    playNotificationSound() {
        if (!this.audioUnlocked) {
            console.log('🔇 Audio locked - please click/scroll on page first');
            return;
        }

        try {
            // Use preloaded audio for faster playback
            if (this.preloadedAudio) {
                this.preloadedAudio.currentTime = 0;
                this.preloadedAudio.play()
                    .then(() => {
                        console.log('🔔 Notification sound played');
                    })
                    .catch(e => {
                        console.log('🔇 Audio play failed:', e.message);
                    });
            } else {
                // Fallback to creating new audio
                const audio = new Audio('/sounds/42289.mp3');
                audio.volume = 0.5;
                audio.play().catch(() => {});
            }
        } catch (error) {
            console.log('🔇 Cannot play notification sound:', error);
        }
    }

    getInitials(name) {
        return name
            .split(' ')
            .map(n => n[0])
            .join('')
            .toUpperCase()
            .substring(0, 2);
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    clear() {
        this.notifications.forEach((_, id) => this.remove(id));
    }
}

// Initialize notification manager for admin only
console.log('📦 Chat notification script loaded');
console.log('👤 isAdmin:', window.isAdmin);
console.log('🆔 userId:', window.userId);

if (window.isAdmin) {
    window.chatNotifications = new ChatNotificationManager();
    console.log('✅ Chat notifications enabled for admin');
    
    // Test function - call this from console to test notification
    window.testNotification = function() {
        window.chatNotifications.show({
            id: 999,
            sender: 'Test User',
            message: 'This is a test notification',
            time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
            senderId: 'test-123'
        });
        console.log('🧪 Test notification triggered!');
    };
    console.log('💡 Test notification dengan: testNotification()');
} else {
    console.log('⚠️ Chat notifications disabled - not admin');
}
