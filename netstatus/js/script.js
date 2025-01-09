document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('addDeviceModal');
    const editModal = document.getElementById('editDeviceModal');
    const addButtons = document.querySelectorAll('.add-device');
    const emptyCards = document.querySelectorAll('.device-card.empty');
    const editButtons = document.querySelectorAll('.edit-device');
    const removeButtons = document.querySelectorAll('.remove-device');
    const cancelButtons = document.querySelectorAll('.cancel');
    const addDeviceForm = document.getElementById('addDeviceForm');
    const editDeviceForm = document.getElementById('editDeviceForm');
    
    // Sound elements
    const connectedSound = document.getElementById('connectedSound');
    const disconnectedSound = document.getElementById('disconnectedSound');
    const alertSound = document.getElementById('alertSound');
    
    let devices = [];
    let lastStatus = {};
    let countdownIntervals = {};

    // Add device button click
    addButtons.forEach(button => {
        button.addEventListener('click', () => {
            modal.style.display = 'block';
        });
    });

    // Empty card click
    emptyCards.forEach(card => {
        card.addEventListener('click', () => {
            modal.style.display = 'block';
        });
    });

    // Edit device button click
    editButtons.forEach(button => {
        button.addEventListener('click', async () => {
            const deviceId = button.getAttribute('data-id');
            const deviceCard = button.closest('.device-card');
            const deviceName = deviceCard.querySelector('.device-name').textContent;
            const deviceIP = deviceCard.querySelector('.device-ip').textContent;
            const deviceComment = deviceCard.querySelector('.device-comment').textContent;

            document.getElementById('editDeviceId').value = deviceId;
            document.getElementById('editDeviceName').value = deviceName;
            document.getElementById('editDeviceIP').value = deviceIP;
            document.getElementById('editDeviceComment').value = deviceComment;

            editModal.style.display = 'block';
        });
    });

    // Cancel buttons
    cancelButtons.forEach(button => {
        button.addEventListener('click', () => {
            modal.style.display = 'none';
            editModal.style.display = 'none';
        });
    });

    // Close modals when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
        if (e.target === editModal) {
            editModal.style.display = 'none';
        }
    });

    // Add device form submit
    addDeviceForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const deviceName = document.getElementById('deviceName').value;
        const deviceIP = document.getElementById('deviceIP').value;
        const deviceComment = document.getElementById('deviceComment').value;

        try {
            const response = await fetch('api/add_device.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ deviceName, deviceIP, comment: deviceComment })
            });

            const data = await response.json();
            
            if (response.ok && data.success) {
                window.location.reload();
            } else {
                const errorMessage = data.error || 'Failed to add device';
                alert(errorMessage);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Network error: Could not connect to server');
        }
    });

    // Edit device form submit
    editDeviceForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const deviceId = document.getElementById('editDeviceId').value;
        const deviceName = document.getElementById('editDeviceName').value;
        const deviceIP = document.getElementById('editDeviceIP').value;
        const deviceComment = document.getElementById('editDeviceComment').value;

        try {
            const response = await fetch('api/edit_device.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    deviceId, 
                    deviceName, 
                    deviceIP, 
                    deviceComment 
                })
            });

            const data = await response.json();
            
            if (response.ok && data.success) {
                window.location.reload();
            } else {
                const errorMessage = data.error || 'Failed to edit device';
                alert(errorMessage);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Network error: Could not connect to server');
        }
    });

    // Remove device
    removeButtons.forEach(button => {
        button.addEventListener('click', async () => {
            const deviceId = button.getAttribute('data-id');
            if (confirm('Are you sure you want to remove this device?')) {
                try {
                    const response = await fetch('api/remove_device.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ deviceId })
                    });

                    const data = await response.json();
                    
                    if (response.ok && data.success) {
                        window.location.reload();
                    } else {
                        const errorMessage = data.error || 'Failed to remove device';
                        alert(errorMessage);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Network error: Could not connect to server');
                }
            }
        });
    });

    // Format timestamp to time string (HH:mm)
    function formatLastSeen(timestamp) {
        if (timestamp === null) return 'N/A';
        const date = new Date(timestamp * 1000);
        return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false });
    }

    // Check device status
    async function checkDeviceStatus() {
        try {
            const response = await fetch('api/check_status.php');
            const data = await response.json();
            
            let allDisconnected = true;
            let statusChanged = false;

            // Clear existing countdown intervals
            Object.values(countdownIntervals).forEach(interval => clearInterval(interval));
            countdownIntervals = {};

            data.forEach(device => {
                const deviceCard = document.querySelector(`[data-id="${device.id}"]`);
                if (deviceCard) {
                    const statusIndicator = deviceCard.querySelector('.status-indicator');
                    const lastSeen = deviceCard.querySelector('.last-seen');
                    
                    // Update status indicator
                    statusIndicator.textContent = device.status ? window.translations.connected : window.translations.disconnected;
                    statusIndicator.className = 'status-indicator ' + (device.status ? 'connected' : 'disconnected');
                    
                    // Set initial countdown value
                    let nextCheckSeconds = device.next_check_seconds;
                    const lastSeenTime = device.status ? 'now' : formatLastSeen(device.last_seen_time);
                    lastSeen.textContent = `${window.translations.lost_device} ${lastSeenTime} / ${window.translations.next_check} ${nextCheckSeconds} sec.`;
                    
                    // Start countdown for this device
                    countdownIntervals[device.id] = setInterval(() => {
                        nextCheckSeconds = Math.max(0, nextCheckSeconds - 1);
                        lastSeen.textContent = `${window.translations.lost_device} ${lastSeenTime} / ${window.translations.next_check} ${nextCheckSeconds} sec.`;
                        if (nextCheckSeconds === 0) {
                            clearInterval(countdownIntervals[device.id]);
                        }
                    }, 1000);
                    
                    // Check if status changed
                    if (lastStatus[device.id] !== undefined && lastStatus[device.id] !== device.status) {
                        statusChanged = true;
                        if (device.status) {
                            connectedSound.play();
                        } else {
                            disconnectedSound.play();
                        }
                    }
                    
                    lastStatus[device.id] = device.status;
                    
                    if (device.status) {
                        allDisconnected = false;
                    }
                }
            });

            // Play alert sound if all devices are disconnected
            if (allDisconnected && Object.keys(lastStatus).length > 0) {
                alertSound.play();
            }
        } catch (error) {
            console.error('Error checking device status:', error);
        }
    }

    // Language switching
    document.querySelectorAll('.lang-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const lang = this.getAttribute('data-lang');
            try {
                const response = await fetch('index.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=change_language&lang=${lang}`
                });
                const data = await response.json();
                if (data.success) {
                    location.reload();
                }
            } catch (error) {
                console.error('Error changing language:', error);
            }
        });
    });

    // Initial check and start periodic checking
    checkDeviceStatus();
    setInterval(checkDeviceStatus, 10000); // Check every 10 seconds
});
