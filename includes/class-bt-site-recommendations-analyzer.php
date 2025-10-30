<?php
/**
 * Site analyzer functionality.
 *
 * @since      1.0.0
 * @package    BT_Site_Recommendations
 */
class BT_Site_Recommendations_Analyzer {

    /**
     * Analyze page speed metrics.
     *
     * @since    1.0.0
     * @param    string    $url    The URL to analyze
     * @return   array
     */
    public function analyze_page_speed($url) {
        $metrics = array();

        // Analyze response time
        $start_time = microtime(true);
        $response = wp_remote_get($url, array('timeout' => 30));
        $end_time = microtime(true);
        
        if (!is_wp_error($response)) {
            $load_time = round(($end_time - $start_time) * 1000, 2);
            $metrics['load_time'] = $load_time;
            $metrics['status_code'] = wp_remote_retrieve_response_code($response);
            
            $body = wp_remote_retrieve_body($response);
            $metrics['page_size'] = strlen($body);
            
            // Analyze HTML structure
            $metrics['images_count'] = substr_count($body, '<img');
            $metrics['scripts_count'] = substr_count($body, '<script');
            $metrics['stylesheets_count'] = substr_count($body, '<link') + substr_count($body, '<style');
            
            // Check for optimization opportunities
            $metrics['has_compression'] = $this->check_compression($response);
            $metrics['has_caching'] = $this->check_caching($response);
            $metrics['has_minification'] = $this->check_minification($body);
        } else {
            $metrics['error'] = $response->get_error_message();
        }

        return $metrics;
    }

    /**
     * Analyze SEO metrics.
     *
     * @since    1.0.0
     * @return   array
     */
    public function analyze_seo() {
        $metrics = array();

        // Get homepage content
        $url = home_url();
        $response = wp_remote_get($url);
        
        if (!is_wp_error($response)) {
            $body = wp_remote_retrieve_body($response);
            
            // Check title tag
            preg_match('/<title>(.*?)<\/title>/is', $body, $title_matches);
            $metrics['has_title'] = !empty($title_matches[1]);
            $metrics['title'] = $title_matches[1] ?? '';
            $metrics['title_length'] = strlen($metrics['title']);
            
            // Check meta description
            preg_match('/<meta\s+name=["\']description["\']\s+content=["\'](.*?)["\']/is', $body, $desc_matches);
            $metrics['has_meta_description'] = !empty($desc_matches[1]);
            $metrics['meta_description'] = $desc_matches[1] ?? '';
            $metrics['meta_description_length'] = strlen($metrics['meta_description']);
            
            // Check headings
            $metrics['h1_count'] = substr_count($body, '<h1');
            $metrics['h2_count'] = substr_count($body, '<h2');
            
            // Check images alt text
            preg_match_all('/<img[^>]*>/i', $body, $img_matches);
            $images_without_alt = 0;
            if (!empty($img_matches[0])) {
                foreach ($img_matches[0] as $img) {
                    if (!preg_match('/alt=["\'][^"\']*["\']/i', $img)) {
                        $images_without_alt++;
                    }
                }
            }
            $metrics['images_without_alt'] = $images_without_alt;
            $metrics['total_images'] = count($img_matches[0]);
            
            // Check for robots meta tag
            $metrics['has_robots_meta'] = preg_match('/<meta\s+name=["\']robots["\']/is', $body) > 0;
            
            // Check for canonical URL
            $metrics['has_canonical'] = preg_match('/<link\s+rel=["\']canonical["\']/is', $body) > 0;
            
            // Check for Open Graph tags
            $metrics['has_og_tags'] = preg_match('/<meta\s+property=["\']og:/is', $body) > 0;
            
            // Check for schema markup
            $metrics['has_schema'] = preg_match('/"@type":/is', $body) > 0 || preg_match('/itemtype=/is', $body) > 0;
        } else {
            $metrics['error'] = $response->get_error_message();
        }

        return $metrics;
    }

    /**
     * Check if compression is enabled.
     *
     * @since    1.0.0
     * @param    array    $response
     * @return   bool
     */
    private function check_compression($response) {
        $headers = wp_remote_retrieve_headers($response);
        return isset($headers['content-encoding']) && 
               (strpos($headers['content-encoding'], 'gzip') !== false || 
                strpos($headers['content-encoding'], 'deflate') !== false);
    }

    /**
     * Check if caching headers are present.
     *
     * @since    1.0.0
     * @param    array    $response
     * @return   bool
     */
    private function check_caching($response) {
        $headers = wp_remote_retrieve_headers($response);
        return isset($headers['cache-control']) || isset($headers['expires']);
    }

    /**
     * Check if content appears to be minified.
     *
     * @since    1.0.0
     * @param    string    $body
     * @return   bool
     */
    private function check_minification($body) {
        // Simple check: minified HTML typically has fewer line breaks
        $lines = substr_count($body, "\n");
        $size = strlen($body);
        
        // If less than 1 line break per 500 characters, likely minified
        return $size > 0 && ($lines / $size) < 0.002;
    }
}
