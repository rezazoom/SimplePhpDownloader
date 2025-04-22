# SimplePHPUploader

A secure and user-friendly PHP file downloader with password protection and file management capabilities.

## Features

- 🔒 Password protection to secure your uploader
- 📥 Download files directly to your server via URL
- 🗑️ File management with delete functionality
- 📊 Human-readable file size display
- 🎨 Modern and responsive Bootstrap UI
- 🔐 Secure file handling and input validation
- 📁 Organized file storage in dedicated downloads directory

## Installation

1. Upload `upload.php` to your web server
2. The script will automatically create a `downloads` directory
3. Change the default password in the code (default: "admin123")

## Usage

1. Access the uploader through your web browser
2. Enter the password to access the interface
3. Enter the file URL and desired filename
4. Click "Download" to save the file to your server
5. View, download, or delete files from the interface
6. Logout when finished

## Security Notes

- ⚠️ Always change the default password
- ⚠️ Delete the script after use to prevent unauthorized access
- ⚠️ The script is designed for temporary use only
- ⚠️ For production use, consider implementing additional security measures

## Requirements

- PHP 7.0 or higher
- Web server with write permissions
- SSL support for HTTPS downloads

## License

This project is open source and available for personal and commercial use.

## Credits

Original concept and development by Reza Esmaeili
