# NetStatus 1.0 (beta)

A web-based application for monitoring device connectivity status using PING.

## Features

- Monitor up to 9 devices simultaneously
- Real-time status updates every 10 seconds
- Sound notifications for connection changes
- Easy device management (add/remove)
- Responsive grid layout
- Dark mode interface
- Mobile-friendly design
- Multi-language support (English/Russian)
- Automatic reconnection attempts
- Status history logging

## Requirements

- XAMPP (or other web server with PHP and MySQL)
- PHP 7.0 or higher
- MySQL 5.6 or higher
- Modern web browser with JavaScript enabled

## Installation

### Quick Install (XAMPP)

1. Install XAMPP if you haven't already
2. Copy all files to your XAMPP's htdocs directory (e.g., `C:\xampp\htdocs\netstatus`)
3. Start Apache and MySQL in XAMPP Control Panel
4. Open your web browser and navigate to `http://localhost/netstatus/setup/install.php`
5. Follow the installation wizard:
   - System requirements will be checked automatically
   - Database will be configured automatically with default XAMPP settings
   - After installation, you can log in with:

### Manual Installation

1. Set up your web server and ensure PHP and MySQL are installed
2. Copy all files to your web server directory
3. Navigate to the setup directory in your web browser
4. Follow the installation wizard:
   - Verify system requirements
   - Configure database connection
   - Installation will complete automatically
5. Log in with the default credentials and change the password

## Sound Files

Place the following sound files in the `sounds` directory:
- `connected.mp3`: Plays when a device connects
- `disconnected.mp3`: Plays when a device disconnects
- `alert.mp3`: Plays when all devices are disconnected

## Directory Structure

```
netstatus/
├── api/
│   ├── add_device.php
│   ├── check_status.php
│   └── remove_device.php
├── css/
│   └── style.css
├── js/
│   └── script.js
├── setup/
│   ├── css/
│   │   └── style.css
│   ├── templates/
│   │   ├── requirements.php
│   │   ├── database.php
│   │   ├── complete.php
│   │   └── error.php
│   └── install.php
├── sounds/
│   └── README.txt
├── config.php
├── index.php
└── README.md
```

## Usage

1. Open the application in your web browser
2. Log in using your administrator credentials
3. Click "ADD DEVICE" to add a new device
4. Enter the device name and IP address
5. The application will automatically start monitoring the device
6. To remove a device, hover over its card and click "Remove Device"

## Support

For support or bug reports, please create an "issue" in the project repository.

## License

This software is developed for Vedenskaya CRB. All rights reserved.
