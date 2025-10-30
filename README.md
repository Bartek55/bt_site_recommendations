# BT Site Recommendations

AI-powered WordPress plugin that analyzes your site to provide **Page Speed** and **SEO** optimization recommendations.

## Features

- **AI-Powered Analysis**: Uses GPT-5 (OpenAI) or Claude Sonnet 4.5 (Anthropic) for intelligent recommendations
- **Page Speed Optimization**: 
  - Identifies render-blocking resources
  - Detects slow database queries affecting load time
  - Recommends caching and optimization strategies
  - Analyzes Core Web Vitals impact (LCP, FID, CLS)
  - Image optimization for faster loading
- **SEO Recommendations**: 
  - Missing or improper meta tags detection
  - Alt text generation for images
  - Schema markup opportunities
  - URL structure analysis
  - Content accessibility improvements
- **Plugin Recommendations**: Suggests specific WordPress plugins to improve performance and SEO
- **Code Analysis**: Scans active theme and plugin files for security vulnerabilities, deprecated functions, and performance issues
- **Database Analysis**: Examines schema optimization, query performance, unused tables, and security concerns
- **Image Optimization**: Analyzes images for size optimization, format conversion (WebP), missing alt text, and unused files
- **Multi-Hosting Support**: Compatible with Pantheon, WP Engine, and standard WordPress hosting
- **Granular Permissions**: Control what the AI can access with checkboxes for each permission type
- **Batch Processing**: Optimize multiple images at once with progress tracking

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- OpenAI API key (for GPT-5) or Anthropic API key (for Claude Sonnet 4.5)

## Installation

1. Upload the plugin files to `/wp-content/plugins/bt_site_recommendations/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to **BT Site Recommendations** in the admin menu
4. Enter your API key(s) in the Welcome tab
5. Configure permissions as needed
6. Start analyzing your site!

## Usage

### Welcome Tab
- Select your preferred AI provider (OpenAI or Anthropic)
- Enter your API key(s)
- Test the connection
- Configure permissions for what the AI can access
- View auto-detected hosting environment

### Code Tab
- Click "Run Code Analysis" to scan your active theme and plugins
- **Page Speed Score**: View estimated Core Web Vitals impact
- Review security issues, performance recommendations, and **SEO issues**
- **Performance Issues**: See how each issue affects page loading speed
- **SEO Issues**: Understand SEO impact of code problems
- **Recommended Plugins**: Get suggestions for caching, minification, and SEO plugins
- Each issue includes file path, line number, and suggested fix
- Download the report for later reference

### Database Tab
- Click "Run Database Analysis" to examine your database
- Review performance optimization and security recommendations
- Apply safe automated fixes with one click
- Export detailed reports

### Images Tab
- Click "Scan Images" to analyze your media library
- **Statistics Dashboard**: View total images, size, and optimization opportunities
- **Page Speed Impact**: See estimated loading speed improvements
- **SEO Impact**: Understand how alt text improves SEO
- **Lazy Loading Detection**: Check if lazy loading is implemented
- Select images for batch optimization
- Convert formats (JPEG/PNG to WebP) with estimated savings
- Add SEO-optimized alt text
- **Recommended Plugins**: Get suggestions for image optimization tools
- Track before/after size comparisons

## API Keys

### OpenAI (GPT-5)
Get your API key from: https://platform.openai.com/api-keys

### Anthropic (Claude Sonnet 4.5)
Get your API key from: https://console.anthropic.com/

## Permissions

The plugin requests the following permissions (all enabled by default):
- **Read theme files**: Analyze your active theme's code
- **Read active plugin files**: Analyze your active plugins
- **Read wp-config.php**: Detect hosting and database configuration
- **Access database structure**: Examine table schemas and indexes
- **Query database content**: Analyze data for optimization opportunities
- **Read image metadata**: Get information about uploaded images
- **Access image files**: Process and optimize images

You can disable any permission you're not comfortable with.

## Hosting Compatibility

### Auto-Detected Hosting Types
- **Pantheon**: Optimized for Pantheon's environment and restrictions
- **WP Engine**: Compatible with WP Engine's infrastructure
- **Standard**: Works with any standard WordPress hosting

The plugin auto-detects your hosting environment but allows manual override if needed.

## Security

- All API communications are encrypted
- API keys are stored securely in WordPress options
- Nonce verification for all AJAX requests
- Capability checks ensure only administrators can use the plugin
- Code samples sent to AI are sanitized to remove sensitive data

## Support

For support, please visit: https://your-website.com/support

## Changelog

### 1.0.0
- Initial release
- AI-powered code, database, and image analysis
- Support for OpenAI GPT-5 and Anthropic Claude Sonnet 4.5
- Multi-hosting environment support
- Batch image optimization

## License

This plugin is licensed under the GPL v2 or later.

