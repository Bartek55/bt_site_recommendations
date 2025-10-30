# BT Site Recommendations

AI-powered WordPress plugin that analyzes your site to provide **Page Speed** and **SEO** optimization recommendations.

## Description

BT Site Recommendations is a comprehensive WordPress plugin that helps you optimize your website's performance and search engine visibility. Using intelligent analysis algorithms, it examines your site and provides actionable recommendations to improve:

- **Page Speed**: Load times, compression, caching, minification, and resource optimization
- **SEO**: Meta tags, headings, images, structured data, and more

## Features

### Page Speed Analysis
- ⚡ **Load Time Monitoring** - Measures actual page load times
- 📦 **Compression Detection** - Checks for GZIP/deflate compression
- 🗄️ **Caching Analysis** - Verifies browser caching headers
- 📝 **Minification Check** - Detects if HTML/CSS/JS are minified
- 🖼️ **Resource Counting** - Tracks images, scripts, and stylesheets
- 📊 **Page Size Measurement** - Monitors total page weight

### SEO Analysis
- 📄 **Title Tag Optimization** - Checks presence and length
- 📝 **Meta Description** - Validates meta descriptions
- 🔤 **Heading Structure** - Analyzes H1-H6 usage
- 🖼️ **Image Alt Text** - Identifies missing alt attributes
- 🔗 **Canonical URLs** - Verifies canonical link tags
- 📱 **Open Graph Tags** - Checks social media metadata
- 🏷️ **Schema Markup** - Detects structured data
- 🤖 **Robots Meta** - Checks indexing directives

### AI-Powered Recommendations
- 🎯 **Priority-Based** - Critical, High, Medium, and Low priority issues
- 📋 **Actionable Steps** - Specific actions to resolve each issue
- 📊 **Impact Assessment** - Understand the potential impact of each fix
- 🎨 **Beautiful UI** - Clean, intuitive WordPress admin interface

## Installation

### Method 1: WordPress Admin Panel (Recommended)
1. Download the plugin ZIP file
2. Log in to your WordPress admin panel
3. Navigate to **Plugins > Add New**
4. Click **Upload Plugin** at the top
5. Choose the downloaded ZIP file
6. Click **Install Now**
7. After installation, click **Activate**

### Method 2: Manual Installation
1. Download and extract the plugin
2. Upload the `bt-site-recommendations` folder to `/wp-content/plugins/`
3. Activate the plugin through the **Plugins** menu in WordPress

### Method 3: Git Clone (Development)
```bash
cd /path/to/wordpress/wp-content/plugins/
git clone https://github.com/Bartek55/bt_site_recommendations.git bt-site-recommendations
```
Then activate through WordPress admin.

## Usage

### Running an Analysis

1. Navigate to **Site Recommendations** in your WordPress admin menu
2. Click the **Analyze My Site** button
3. Wait for the analysis to complete (usually takes 5-15 seconds)
4. Review the results in the dashboard

### Understanding Results

#### Metrics Dashboard
The top of the results shows four key metrics:
- **Page Load Time** - How long your homepage takes to load
- **Page Size** - Total size of your homepage in bytes
- **SEO Score** - Overall SEO health (0-100)
- **Issues Found** - Total number of recommendations

#### Recommendations Tabs
Results are organized into three tabs:
- **All Recommendations** - Combined view of all issues
- **Page Speed** - Performance-related recommendations
- **SEO** - Search engine optimization recommendations

#### Priority Levels
- 🔴 **Critical** - Must fix immediately (major SEO/performance issues)
- 🟠 **High** - Important issues that significantly impact your site
- 🟡 **Medium** - Moderate improvements with good ROI
- 🟢 **Low** - Nice-to-have optimizations

### Configuration

Navigate to **Site Recommendations > Settings** to configure:

- **Enable Page Speed Analysis** - Toggle page speed checks
- **Enable SEO Analysis** - Toggle SEO checks
- **Analysis Frequency** - Set how often to auto-analyze (feature coming soon)

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- cURL enabled (for HTTP requests)
- Admin capabilities to access the plugin

## Frequently Asked Questions

### Does this plugin make changes to my site?
No, this plugin only analyzes your site and provides recommendations. You must manually implement the suggested changes or use other plugins/tools to apply them.

### How often should I run an analysis?
We recommend running an analysis:
- After making significant site changes
- After installing/updating themes or plugins
- Monthly for general health checks
- Before major marketing campaigns

### Will this slow down my site?
No, the plugin only runs analysis when you click the "Analyze" button. It doesn't add any frontend code or slow down your site for visitors.

### Can I use this with caching plugins?
Yes! In fact, we often recommend caching plugins based on the analysis results. The plugin works well with popular caching solutions like WP Super Cache, W3 Total Cache, and WP Rocket.

### Is my data shared with external services?
No, all analysis is performed on your server. No data is sent to external APIs or services.

## Roadmap

Future features planned:
- [ ] Scheduled automatic analysis with email reports
- [ ] Historical tracking and trend analysis
- [ ] Integration with Google PageSpeed Insights API
- [ ] Mobile vs Desktop performance comparison
- [ ] Detailed waterfall charts for resource loading
- [ ] One-click fixes for common issues
- [ ] Multi-page analysis (not just homepage)
- [ ] Custom checklist builder
- [ ] Export reports to PDF

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the GNU General Public License v3.0 - see the [LICENSE](LICENSE) file for details.

## Author

Created by [Bartek55](https://github.com/Bartek55)

## Support

For bug reports and feature requests, please use the [GitHub Issues](https://github.com/Bartek55/bt_site_recommendations/issues) page.

## Changelog

### 1.0.0 (2025-10-30)
- Initial release
- Page Speed analysis functionality
- SEO analysis functionality
- AI-powered recommendation engine
- WordPress admin interface
- Priority-based recommendations
- Actionable improvement steps