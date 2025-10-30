<?php
/**
 * AI-powered recommendations engine.
 *
 * @since      1.0.0
 * @package    BT_Site_Recommendations
 */
class BT_Site_Recommendations_AI {

    /**
     * Generate AI-powered recommendations based on analysis data.
     *
     * @since    1.0.0
     * @param    array    $page_speed_data
     * @param    array    $seo_data
     * @return   array
     */
    public function generate_recommendations($page_speed_data, $seo_data) {
        $recommendations = array(
            'page_speed' => $this->generate_page_speed_recommendations($page_speed_data),
            'seo' => $this->generate_seo_recommendations($seo_data)
        );

        return $recommendations;
    }

    /**
     * Generate Page Speed recommendations.
     *
     * @since    1.0.0
     * @param    array    $data
     * @return   array
     */
    private function generate_page_speed_recommendations($data) {
        $recommendations = array();

        if (isset($data['error'])) {
            $recommendations[] = array(
                'priority' => 'high',
                'category' => 'Page Speed',
                'title' => __('Unable to analyze page speed', 'bt-site-recommendations'),
                'description' => sprintf(__('Error: %s', 'bt-site-recommendations'), $data['error']),
                'impact' => 'high'
            );
            return $recommendations;
        }

        // Load time recommendations
        if (isset($data['load_time'])) {
            if ($data['load_time'] > 3000) {
                $recommendations[] = array(
                    'priority' => 'high',
                    'category' => 'Page Speed',
                    'title' => __('Reduce Page Load Time', 'bt-site-recommendations'),
                    'description' => sprintf(
                        __('Your page load time is %dms. Pages should load in under 3 seconds for optimal user experience. Consider implementing caching, CDN, and optimizing server response time.', 'bt-site-recommendations'),
                        $data['load_time']
                    ),
                    'impact' => 'high',
                    'actions' => array(
                        __('Enable browser caching', 'bt-site-recommendations'),
                        __('Use a Content Delivery Network (CDN)', 'bt-site-recommendations'),
                        __('Optimize server response time', 'bt-site-recommendations'),
                        __('Upgrade hosting if necessary', 'bt-site-recommendations')
                    )
                );
            } elseif ($data['load_time'] > 1500) {
                $recommendations[] = array(
                    'priority' => 'medium',
                    'category' => 'Page Speed',
                    'title' => __('Optimize Page Load Time', 'bt-site-recommendations'),
                    'description' => sprintf(
                        __('Your page load time is %dms. Good, but there\'s room for improvement. Aim for under 1.5 seconds.', 'bt-site-recommendations'),
                        $data['load_time']
                    ),
                    'impact' => 'medium',
                    'actions' => array(
                        __('Further optimize images', 'bt-site-recommendations'),
                        __('Minimize HTTP requests', 'bt-site-recommendations')
                    )
                );
            }
        }

        // Compression recommendations
        if (isset($data['has_compression']) && !$data['has_compression']) {
            $recommendations[] = array(
                'priority' => 'high',
                'category' => 'Page Speed',
                'title' => __('Enable GZIP Compression', 'bt-site-recommendations'),
                'description' => __('Your site is not using compression. Enabling GZIP compression can reduce page size by 50-70%.', 'bt-site-recommendations'),
                'impact' => 'high',
                'actions' => array(
                    __('Enable GZIP compression in your server configuration', 'bt-site-recommendations'),
                    __('Or use a WordPress caching plugin that enables compression', 'bt-site-recommendations')
                )
            );
        }

        // Caching recommendations
        if (isset($data['has_caching']) && !$data['has_caching']) {
            $recommendations[] = array(
                'priority' => 'high',
                'category' => 'Page Speed',
                'title' => __('Implement Browser Caching', 'bt-site-recommendations'),
                'description' => __('Your site is not using browser caching headers. This forces browsers to download all resources on every visit.', 'bt-site-recommendations'),
                'impact' => 'high',
                'actions' => array(
                    __('Set appropriate cache-control headers', 'bt-site-recommendations'),
                    __('Use a caching plugin like WP Super Cache or W3 Total Cache', 'bt-site-recommendations')
                )
            );
        }

        // Minification recommendations
        if (isset($data['has_minification']) && !$data['has_minification']) {
            $recommendations[] = array(
                'priority' => 'medium',
                'category' => 'Page Speed',
                'title' => __('Minify HTML, CSS, and JavaScript', 'bt-site-recommendations'),
                'description' => __('Your files appear to be unminified. Minification removes unnecessary characters and can reduce file sizes by 20-30%.', 'bt-site-recommendations'),
                'impact' => 'medium',
                'actions' => array(
                    __('Use a minification plugin like Autoptimize', 'bt-site-recommendations'),
                    __('Enable minification in your caching plugin', 'bt-site-recommendations')
                )
            );
        }

        // Images recommendations
        if (isset($data['images_count']) && $data['images_count'] > 20) {
            $recommendations[] = array(
                'priority' => 'medium',
                'category' => 'Page Speed',
                'title' => __('Optimize Images', 'bt-site-recommendations'),
                'description' => sprintf(
                    __('Your page contains %d images. Consider lazy loading and image optimization to improve load times.', 'bt-site-recommendations'),
                    $data['images_count']
                ),
                'impact' => 'medium',
                'actions' => array(
                    __('Implement lazy loading for images', 'bt-site-recommendations'),
                    __('Use modern image formats (WebP)', 'bt-site-recommendations'),
                    __('Compress images without losing quality', 'bt-site-recommendations')
                )
            );
        }

        // Scripts recommendations
        if (isset($data['scripts_count']) && $data['scripts_count'] > 10) {
            $recommendations[] = array(
                'priority' => 'medium',
                'category' => 'Page Speed',
                'title' => __('Reduce JavaScript Files', 'bt-site-recommendations'),
                'description' => sprintf(
                    __('Your page loads %d JavaScript files. Consider combining and deferring scripts.', 'bt-site-recommendations'),
                    $data['scripts_count']
                ),
                'impact' => 'medium',
                'actions' => array(
                    __('Combine JavaScript files where possible', 'bt-site-recommendations'),
                    __('Defer non-critical JavaScript', 'bt-site-recommendations'),
                    __('Remove unused JavaScript libraries', 'bt-site-recommendations')
                )
            );
        }

        // Page size recommendations
        if (isset($data['page_size']) && $data['page_size'] > 1048576) { // > 1MB
            $recommendations[] = array(
                'priority' => 'medium',
                'category' => 'Page Speed',
                'title' => __('Reduce Page Size', 'bt-site-recommendations'),
                'description' => sprintf(
                    __('Your page size is %s. Large page sizes slow down loading, especially on mobile networks.', 'bt-site-recommendations'),
                    size_format($data['page_size'])
                ),
                'impact' => 'medium',
                'actions' => array(
                    __('Optimize and compress images', 'bt-site-recommendations'),
                    __('Remove unnecessary content', 'bt-site-recommendations'),
                    __('Lazy load media files', 'bt-site-recommendations')
                )
            );
        }

        return $recommendations;
    }

    /**
     * Generate SEO recommendations.
     *
     * @since    1.0.0
     * @param    array    $data
     * @return   array
     */
    private function generate_seo_recommendations($data) {
        $recommendations = array();

        if (isset($data['error'])) {
            $recommendations[] = array(
                'priority' => 'high',
                'category' => 'SEO',
                'title' => __('Unable to analyze SEO', 'bt-site-recommendations'),
                'description' => sprintf(__('Error: %s', 'bt-site-recommendations'), $data['error']),
                'impact' => 'high'
            );
            return $recommendations;
        }

        // Title tag recommendations
        if (!$data['has_title']) {
            $recommendations[] = array(
                'priority' => 'critical',
                'category' => 'SEO',
                'title' => __('Add Title Tag', 'bt-site-recommendations'),
                'description' => __('Your page is missing a title tag. This is crucial for SEO and appears in search results.', 'bt-site-recommendations'),
                'impact' => 'high',
                'actions' => array(
                    __('Add a unique, descriptive title tag to your homepage', 'bt-site-recommendations'),
                    __('Keep it between 50-60 characters', 'bt-site-recommendations')
                )
            );
        } elseif ($data['title_length'] > 60) {
            $recommendations[] = array(
                'priority' => 'medium',
                'category' => 'SEO',
                'title' => __('Shorten Title Tag', 'bt-site-recommendations'),
                'description' => sprintf(
                    __('Your title tag is %d characters. Google typically displays 50-60 characters in search results.', 'bt-site-recommendations'),
                    $data['title_length']
                ),
                'impact' => 'medium',
                'actions' => array(
                    __('Reduce title length to 50-60 characters', 'bt-site-recommendations')
                )
            );
        } elseif ($data['title_length'] < 30) {
            $recommendations[] = array(
                'priority' => 'low',
                'category' => 'SEO',
                'title' => __('Expand Title Tag', 'bt-site-recommendations'),
                'description' => sprintf(
                    __('Your title tag is %d characters. Consider making it more descriptive (50-60 characters).', 'bt-site-recommendations'),
                    $data['title_length']
                ),
                'impact' => 'low',
                'actions' => array(
                    __('Add more descriptive keywords to your title', 'bt-site-recommendations')
                )
            );
        }

        // Meta description recommendations
        if (!$data['has_meta_description']) {
            $recommendations[] = array(
                'priority' => 'high',
                'category' => 'SEO',
                'title' => __('Add Meta Description', 'bt-site-recommendations'),
                'description' => __('Your page is missing a meta description. This appears in search results and affects click-through rates.', 'bt-site-recommendations'),
                'impact' => 'high',
                'actions' => array(
                    __('Add a compelling meta description', 'bt-site-recommendations'),
                    __('Keep it between 150-160 characters', 'bt-site-recommendations'),
                    __('Include your target keywords naturally', 'bt-site-recommendations')
                )
            );
        } elseif ($data['meta_description_length'] > 160) {
            $recommendations[] = array(
                'priority' => 'medium',
                'category' => 'SEO',
                'title' => __('Shorten Meta Description', 'bt-site-recommendations'),
                'description' => sprintf(
                    __('Your meta description is %d characters. Google typically displays 150-160 characters.', 'bt-site-recommendations'),
                    $data['meta_description_length']
                ),
                'impact' => 'medium',
                'actions' => array(
                    __('Reduce meta description to 150-160 characters', 'bt-site-recommendations')
                )
            );
        }

        // H1 tag recommendations
        if ($data['h1_count'] == 0) {
            $recommendations[] = array(
                'priority' => 'high',
                'category' => 'SEO',
                'title' => __('Add H1 Heading', 'bt-site-recommendations'),
                'description' => __('Your page is missing an H1 tag. This is important for SEO and content hierarchy.', 'bt-site-recommendations'),
                'impact' => 'high',
                'actions' => array(
                    __('Add one H1 tag with your main keyword', 'bt-site-recommendations')
                )
            );
        } elseif ($data['h1_count'] > 1) {
            $recommendations[] = array(
                'priority' => 'medium',
                'category' => 'SEO',
                'title' => __('Multiple H1 Tags', 'bt-site-recommendations'),
                'description' => sprintf(
                    __('Your page has %d H1 tags. Best practice is to use only one H1 per page.', 'bt-site-recommendations'),
                    $data['h1_count']
                ),
                'impact' => 'medium',
                'actions' => array(
                    __('Use only one H1 tag per page', 'bt-site-recommendations'),
                    __('Use H2-H6 for subheadings', 'bt-site-recommendations')
                )
            );
        }

        // Image alt text recommendations
        if ($data['images_without_alt'] > 0) {
            $recommendations[] = array(
                'priority' => 'medium',
                'category' => 'SEO',
                'title' => __('Add Alt Text to Images', 'bt-site-recommendations'),
                'description' => sprintf(
                    __('%d out of %d images are missing alt text. Alt text is important for accessibility and SEO.', 'bt-site-recommendations'),
                    $data['images_without_alt'],
                    $data['total_images']
                ),
                'impact' => 'medium',
                'actions' => array(
                    __('Add descriptive alt text to all images', 'bt-site-recommendations'),
                    __('Include relevant keywords where appropriate', 'bt-site-recommendations')
                )
            );
        }

        // Canonical URL recommendations
        if (!$data['has_canonical']) {
            $recommendations[] = array(
                'priority' => 'medium',
                'category' => 'SEO',
                'title' => __('Add Canonical URL', 'bt-site-recommendations'),
                'description' => __('Your page is missing a canonical URL. This helps prevent duplicate content issues.', 'bt-site-recommendations'),
                'impact' => 'medium',
                'actions' => array(
                    __('Add canonical link tag to your pages', 'bt-site-recommendations'),
                    __('Use an SEO plugin like Yoast SEO', 'bt-site-recommendations')
                )
            );
        }

        // Open Graph recommendations
        if (!$data['has_og_tags']) {
            $recommendations[] = array(
                'priority' => 'low',
                'category' => 'SEO',
                'title' => __('Add Open Graph Tags', 'bt-site-recommendations'),
                'description' => __('Your page is missing Open Graph tags. These improve how your content appears when shared on social media.', 'bt-site-recommendations'),
                'impact' => 'low',
                'actions' => array(
                    __('Add Open Graph tags for social media optimization', 'bt-site-recommendations'),
                    __('Include og:title, og:description, og:image, and og:url', 'bt-site-recommendations')
                )
            );
        }

        // Schema markup recommendations
        if (!$data['has_schema']) {
            $recommendations[] = array(
                'priority' => 'low',
                'category' => 'SEO',
                'title' => __('Add Schema Markup', 'bt-site-recommendations'),
                'description' => __('Your site is missing schema markup. This helps search engines understand your content better.', 'bt-site-recommendations'),
                'impact' => 'low',
                'actions' => array(
                    __('Add appropriate schema markup to your pages', 'bt-site-recommendations'),
                    __('Use schema for organization, articles, products, etc.', 'bt-site-recommendations')
                )
            );
        }

        return $recommendations;
    }
}
