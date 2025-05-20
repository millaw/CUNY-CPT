# CUNY Letters CPT Plugin
A WordPress plugin that creates a custom post type for managing letters organized by office with custom URL structure and breadcrumbs.

```
cuny-cpt/
├── cuny-cpt.php           # Main plugin file
├── includes/
│   ├── class-post-type.php        # Handles CPT registration
│   ├── class-taxonomy.php         # Handles taxonomy registration
│   ├── class-permalinks.php       # Manages custom permalinks
│   ├── class-breadcrumbs.php      # Handles breadcrumb functionality
│   ├── cpt-register.php           # Handles additional CPT functionality
│   └── class-admin.php            # Enhances admin interface
└── uninstall.php                  # Cleanup on uninstall
```

## Features

- Custom Post Type for Letters
- Office taxonomy for categorization
- Custom URL structure: cuny.edu/letters/office-name/letter-title
- Automatic breadcrumb navigation
- Enhanced admin interface with custom columns and filters
- Scalable and maintainable architecture

## Installation

### Method 1: WordPress Admin Dashboard

- Download the plugin ZIP file from GitHub releases
- In your WordPress admin, go to Plugins → Add New
- Click "Upload Plugin" and select the ZIP file
- Click "Install Now" and then "Activate Plugin"
- Go to Settings → Permalinks and click "Save Changes" to flush rewrite rules

### Method 2: Manual Installation via FTP/SFTP

- Download the plugin ZIP file from GitHub
- Extract the ZIP file to your computer
- Upload the cuny-letters-cpt folder to your wp-content/plugins directory
- In your WordPress admin, go to Plugins → Installed Plugins
- Find "CUNY CPT & Office Extension" and click "Activate"
- Go to Settings → Permalinks and click "Save Changes" to flush rewrite rules

## Usage

### Creating Offices

- Navigate to CPT → Offices in your WordPress admin
- Click "Add New Office"
- Enter the office name and optional description
- Click "Add New Office"

### Displaying Post

Letters will automatically appear with the following URL structure:
`yourdomain.com/letters/office-name/letter-title`

Breadcrumbs will automatically appear on single letter pages in the format:
`Home > Letters > Office Name > Letter Title`

### Shortcode (Optional)

To display a list of letters in a specific office:
```
[cuny_letters office="office-slug" posts_per_page="5"]
```

### Parameters:

- `office` (string) - The slug of the office to filter by
- `posts_per_page` (int) - Number of letters to display (default: 10)

## Customization

## Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher

## Changelog

2.3.0

- Initial release with all core functionality

## License

GPL-2.0+
