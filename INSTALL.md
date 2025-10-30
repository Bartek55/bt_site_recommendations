# Installation Guide

## Prerequisites

Before installing BT Site Recommendations, ensure your WordPress site meets these requirements:

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Admin access to your WordPress dashboard
- cURL enabled on your server (standard on most hosting)

## Installation Methods

### Method 1: Direct Upload (Recommended for GitHub Users)

1. **Download the Plugin**
   - Go to https://github.com/Bartek55/bt_site_recommendations
   - Click the green "Code" button
   - Select "Download ZIP"
   - Save the file to your computer

2. **Upload to WordPress**
   - Log in to your WordPress admin dashboard
   - Navigate to **Plugins → Add New**
   - Click **Upload Plugin** at the top of the page
   - Click **Choose File** and select the downloaded ZIP file
   - Click **Install Now**

3. **Activate the Plugin**
   - After installation completes, click **Activate Plugin**
   - You should see a success message

4. **Access the Plugin**
   - Look for **Site Recommendations** in your WordPress admin menu
   - Click it to open the plugin dashboard

### Method 2: Manual FTP Installation

1. **Download and Extract**
   - Download the plugin ZIP from GitHub
   - Extract the ZIP file on your computer
   - You should have a folder named `bt-site-recommendations`

2. **Upload via FTP**
   - Connect to your server using an FTP client (FileZilla, Cyberduck, etc.)
   - Navigate to `/wp-content/plugins/`
   - Upload the entire `bt-site-recommendations` folder

3. **Activate in WordPress**
   - Log in to your WordPress admin dashboard
   - Go to **Plugins → Installed Plugins**
   - Find "BT Site Recommendations" in the list
   - Click **Activate**

### Method 3: Git Clone (For Developers)

1. **SSH into Your Server**
   ```bash
   ssh user@yourserver.com
   ```

2. **Navigate to Plugins Directory**
   ```bash
   cd /path/to/wordpress/wp-content/plugins/
   ```

3. **Clone the Repository**
   ```bash
   git clone https://github.com/Bartek55/bt_site_recommendations.git bt-site-recommendations
   ```

4. **Set Permissions**
   ```bash
   chmod -R 755 bt-site-recommendations
   chown -R www-data:www-data bt-site-recommendations
   ```
   (Adjust user/group as needed for your server)

5. **Activate in WordPress**
   - Log in to WordPress admin
   - Go to **Plugins → Installed Plugins**
   - Activate "BT Site Recommendations"

## First-Time Setup

### 1. Access the Plugin

After activation, you'll find "Site Recommendations" in your WordPress admin menu (left sidebar).

### 2. Configure Settings (Optional)

Navigate to **Site Recommendations → Settings** to configure:

- **Enable Page Speed Analysis**: Check to analyze performance metrics
- **Enable SEO Analysis**: Check to analyze SEO factors
- Both are enabled by default

### 3. Run Your First Analysis

1. Go to **Site Recommendations** main page
2. Click the **Analyze My Site** button
3. Wait 5-15 seconds for the analysis to complete
4. Review your results and recommendations

## Troubleshooting

### Plugin Won't Activate

**Error: "Plugin could not be activated because it triggered a fatal error"**

- Check that your PHP version is 7.4 or higher
- Ensure all plugin files were uploaded correctly
- Check file permissions (files: 644, folders: 755)

### Can't See the Menu Item

- Clear your browser cache
- Try logging out and back into WordPress
- Check that your user role has admin capabilities

### Analysis Button Does Nothing

- Open browser console (F12) to check for JavaScript errors
- Clear browser cache
- Try disabling other plugins temporarily to check for conflicts
- Ensure jQuery is loaded (standard in WordPress)

### "Insufficient Permissions" Error

- You need Administrator role to use this plugin
- Ask your site administrator to grant you proper permissions

### Analysis Takes Too Long

- This is normal for large sites or slow servers
- The plugin needs to fetch and analyze your homepage
- Typical analysis takes 5-15 seconds
- If it times out, your server may have strict timeout limits

## Updating the Plugin

### From GitHub (Manual Update)

1. Download the latest version from GitHub
2. Deactivate the current plugin (don't delete it yet)
3. Delete the old plugin folder via FTP or hosting file manager
4. Upload the new version using any installation method above
5. Activate the plugin

### From Git (For Developers)

```bash
cd /path/to/wordpress/wp-content/plugins/bt-site-recommendations/
git pull origin main
```

## Uninstallation

### Complete Removal

1. **Deactivate the Plugin**
   - Go to **Plugins → Installed Plugins**
   - Click **Deactivate** under BT Site Recommendations

2. **Delete the Plugin**
   - After deactivation, click **Delete**
   - Confirm the deletion

3. **Manual Cleanup (Optional)**
   
   The plugin stores minimal data. If you want to remove everything:
   
   ```sql
   DELETE FROM wp_options WHERE option_name LIKE 'bt_site_recommendations%';
   ```

## Support

If you encounter issues:

1. Check the [FAQ section](README.md#frequently-asked-questions)
2. Review [GitHub Issues](https://github.com/Bartek55/bt_site_recommendations/issues)
3. Create a new issue with:
   - WordPress version
   - PHP version
   - Error messages (if any)
   - Steps to reproduce the problem

## Next Steps

After installation:

1. Run your first analysis
2. Review the recommendations
3. Implement high-priority fixes
4. Re-run analysis to track improvements
5. Schedule regular monthly analyses

Enjoy optimizing your WordPress site! 🚀
