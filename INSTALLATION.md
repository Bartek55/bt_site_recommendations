# BT Site Recommendations - Installation Guide

## Quick Start

### 1. Installation

**Option A: Upload via WordPress Admin**
1. Log in to your WordPress admin panel
2. Navigate to **Plugins > Add New**
3. Click **Upload Plugin**
4. Choose the `bt_site_recommendations` folder (zipped)
5. Click **Install Now**
6. Activate the plugin

**Option B: Manual Installation**
1. Upload the `bt_site_recommendations` folder to `/wp-content/plugins/`
2. Log in to WordPress admin
3. Navigate to **Plugins**
4. Find "BT Site Recommendations" and click **Activate**

### 2. Initial Configuration

After activation:

1. Go to **Site Recommendations** in the WordPress admin menu
2. You'll see the **Welcome** tab by default

### 3. Configure AI Provider

#### Get API Keys:

**For OpenAI (GPT-5):**
- Visit: https://platform.openai.com/api-keys
- Create a new API key
- Copy the key (starts with `sk-...`)

**For Anthropic (Claude Sonnet 4.5):**
- Visit: https://console.anthropic.com/
- Create a new API key
- Copy the key (starts with `sk-ant-...`)

#### Enter API Keys:

1. In the **Welcome** tab, scroll to "AI Provider Configuration"
2. Select your preferred **Default AI Provider**
3. Paste your API key in the appropriate field
4. Click **Test Connection** to verify
5. Click **Save Settings**

### 4. Configure Permissions

By default, all permissions are enabled for comprehensive analysis. You can adjust:

- **Code Analysis:**
  - Read active theme files
  - Read active plugin files
  - Read wp-config.php

- **Database Analysis:**
  - Access database structure
  - Query database content

- **Image Analysis:**
  - Read image metadata
  - Access and optimize image files

Uncheck any permissions you're not comfortable with, then click **Save Settings**.

### 5. Review Hosting Environment

The plugin auto-detects your hosting environment:
- Pantheon
- WP Engine
- Pressable
- GoDaddy
- Standard WordPress Hosting

If auto-detection is incorrect, use the **Manual Override** dropdown to select the correct hosting type.

## Using the Plugin

### Code Analysis

1. Click the **Code Analysis** tab
2. Click **Run Code Analysis**
3. Wait for analysis to complete (may take 1-2 minutes)
4. Review results:
   - Security Issues
   - Deprecated Functions
   - Performance Recommendations
   - Code Quality Suggestions
5. Download or copy the report for reference

### Database Analysis

1. Click the **Database** tab
2. Click **Run Database Analysis**
3. Wait for analysis to complete
4. Review:
   - Performance & Optimization suggestions
   - Security & Data Integrity issues
5. Click **Apply Safe Fixes** to automatically:
   - Delete expired transients
   - Remove orphaned metadata
   - Optimize tables

### Image Optimization

1. Click the **Images** tab
2. Click **Scan Images**
3. Review statistics and recommendations
4. **Format Conversion:**
   - Select images to convert to WebP
   - Click **Convert Selected to WebP**
5. **Alt Text:**
   - Select images missing alt text
   - Click **Add Alt Text to Selected**
6. Monitor the batch progress

## Caching

Analysis results are cached for 24 hours by default to save API costs.

To force a fresh analysis:
- Use the **Force Re-analyze (Clear Cache)** button on any tab
- Or click **Clear Analysis Cache** in the Welcome tab

## Hosting-Specific Notes

### Pantheon
- File modifications may be restricted on Test/Live environments
- Database CREATE/DROP operations are restricted
- Analysis runs in read-only mode when needed

### WP Engine
- Full optimization capabilities available
- Object caching is automatically detected

### Standard Hosting
- All features available
- Ensure sufficient PHP memory (256MB+ recommended)

## Troubleshooting

### "Connection Failed" Error
- Verify your API key is correct
- Check your server can make outbound HTTPS requests
- Ensure you have an active API subscription

### "Permission Denied" Error
- Only administrators can use this plugin
- Check you're logged in as an admin

### Analysis Takes Too Long
- Large sites may take several minutes
- Increase PHP `max_execution_time` if needed
- Consider analyzing during low-traffic periods

### Images Not Converting to WebP
- Check if GD or Imagick with WebP support is installed
- Contact your hosting provider if needed

## Requirements

- **WordPress:** 5.0 or higher
- **PHP:** 7.4 or higher
- **PHP Extensions:** GD or Imagick (for image optimization)
- **API Key:** OpenAI or Anthropic account

## Security

- API keys are stored securely in WordPress options
- Only administrators can access the plugin
- Code samples sent to AI are sanitized
- No sensitive data (passwords, keys) is sent to AI
- All AJAX requests use WordPress nonces

## Support

For issues or questions:
- Review the main README.md
- Check hosting capabilities in the Welcome tab
- Verify API key is valid and has sufficient credits

## Uninstallation

If you need to remove the plugin:

1. Deactivate the plugin from **Plugins** page
2. Delete the plugin
3. All settings and cached data will be removed automatically

## What's Next?

After installation:
1. Run your first code analysis
2. Review database optimization suggestions
3. Optimize your images
4. Schedule regular reviews (monthly recommended)

Enjoy optimizing your WordPress site with AI-powered recommendations!

