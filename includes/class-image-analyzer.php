<?php
/**
 * Image Analyzer for BT Site Recommendations
 * Analyzes images for optimization opportunities
 */

if (!defined('ABSPATH')) {
    exit;
}

class BT_Site_Recommendations_Image_Analyzer {
    
    private $image_data = array();
    
    /**
     * Run image analysis
     */
    public function analyze() {
        // Check permissions
        if (!BT_Site_Recommendations_Settings::has_permission('read_image_metadata') &&
            !BT_Site_Recommendations_Settings::has_permission('access_image_files')) {
            return array(
                'success' => false,
                'message' => __('Permission to access images is not granted.', 'bt-site-recommendations')
            );
        }
        
        // Collect image data
        $data = $this->collect_image_data();
        
        if (empty($data['images'])) {
            return array(
                'success' => true,
                'data' => array(
                    'summary' => 'No images found in media library',
                    'total_images' => 0,
                    'total_size' => '0 B',
                ),
                'message' => __('No images found to analyze.', 'bt-site-recommendations')
            );
        }
        
        // Send to AI for analysis
        $ai_manager = new BT_Site_Recommendations_AI_Provider_Manager();
        $result = $ai_manager->analyze($data, 'images');
        
        if (!$result['success']) {
            return $result;
        }
        
        // Enhance with local analysis
        $enhanced_data = $this->enhance_with_local_analysis($result['data'], $data);
        
        return array(
            'success' => true,
            'data' => $enhanced_data,
            'message' => __('Image analysis completed successfully.', 'bt-site-recommendations')
        );
    }
    
    /**
     * Collect image data from media library
     */
    private function collect_image_data() {
        $data = array(
            'images' => array(),
            'total_images' => 0,
            'total_size' => 0,
            'average_size' => 0,
            'formats' => array(),
        );
        
        // Query all image attachments
        $args = array(
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'post_status' => 'inherit',
            'posts_per_page' => -1,
        );
        
        $images = get_posts($args);
        
        if (empty($images)) {
            return $data;
        }
        
        foreach ($images as $image) {
            $image_data = $this->get_image_data($image);
            
            if ($image_data) {
                $data['images'][] = $image_data;
                $data['total_size'] += $image_data['size_bytes'];
                
                // Count formats
                $format = $image_data['format'];
                if (!isset($data['formats'][$format])) {
                    $data['formats'][$format] = 0;
                }
                $data['formats'][$format]++;
            }
        }
        
        $data['total_images'] = count($data['images']);
        $data['average_size'] = $data['total_images'] > 0 ? 
            $this->format_size($data['total_size'] / $data['total_images']) : '0 B';
        $data['total_size'] = $this->format_size($data['total_size']);
        
        return $data;
    }
    
    /**
     * Get individual image data
     */
    private function get_image_data($image) {
        $image_id = $image->ID;
        $file_path = get_attached_file($image_id);
        
        if (!file_exists($file_path)) {
            return null;
        }
        
        $metadata = wp_get_attachment_metadata($image_id);
        $file_size = filesize($file_path);
        
        // Get image dimensions
        $width = isset($metadata['width']) ? $metadata['width'] : 0;
        $height = isset($metadata['height']) ? $metadata['height'] : 0;
        
        // Get alt text
        $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true);
        
        // Get file format
        $file_info = pathinfo($file_path);
        $format = strtolower($file_info['extension']);
        
        // Check usage (where this image is used)
        $usage_count = $this->get_image_usage_count($image_id);
        
        return array(
            'id' => $image_id,
            'filename' => basename($file_path),
            'path' => $file_path,
            'url' => wp_get_attachment_url($image_id),
            'size_bytes' => $file_size,
            'size' => $this->format_size($file_size),
            'width' => $width,
            'height' => $height,
            'format' => $format,
            'alt_text' => !empty($alt_text),
            'alt_text_value' => $alt_text,
            'usage_count' => $usage_count,
            'uploaded' => $image->post_date,
            'title' => $image->post_title,
        );
    }
    
    /**
     * Get image usage count
     */
    private function get_image_usage_count($image_id) {
        global $wpdb;
        
        // Check featured images
        $featured_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->postmeta} 
            WHERE meta_key = '_thumbnail_id' AND meta_value = %d",
            $image_id
        ));
        
        // Check content (simplified - would need more complex query for actual usage)
        $content_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts} 
            WHERE post_content LIKE %s AND post_status = 'publish'",
            '%wp-image-' . $image_id . '%'
        ));
        
        return intval($featured_count) + intval($content_count);
    }
    
    /**
     * Format size in human-readable format
     */
    private function format_size($bytes) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * Enhance with local analysis
     */
    private function enhance_with_local_analysis($ai_data, $raw_data) {
        $local_checks = $this->run_local_checks($raw_data);
        
        // Merge local checks with AI analysis
        foreach ($local_checks as $category => $items) {
            if (isset($ai_data[$category])) {
                $ai_data[$category] = array_merge($ai_data[$category], $items);
            } else {
                $ai_data[$category] = $items;
            }
        }
        
        // Add statistics
        $ai_data['statistics'] = $this->get_statistics($raw_data);
        
        return $ai_data;
    }
    
    /**
     * Run local image checks
     */
    private function run_local_checks($data) {
        $checks = array(
            'size_optimization' => array(),
            'format_conversion' => array(),
            'missing_alt_text' => array(),
            'unused_images' => array(),
        );
        
        foreach ($data['images'] as $image) {
            // Check for large file sizes
            if ($image['size_bytes'] > 500000) { // > 500KB
                $checks['size_optimization'][] = array(
                    'image_id' => $image['id'],
                    'filename' => $image['filename'],
                    'current_size' => $image['size'],
                    'potential_savings' => 'Up to 50-70% with compression',
                    'recommendation' => 'Compress this image to reduce file size',
                );
            }
            
            // Check for format conversion opportunities
            if (in_array($image['format'], array('jpg', 'jpeg', 'png'))) {
                $estimated_savings = ($image['format'] === 'png') ? '60-80%' : '20-30%';
                $checks['format_conversion'][] = array(
                    'image_id' => $image['id'],
                    'filename' => $image['filename'],
                    'current_format' => $image['format'],
                    'recommended_format' => 'webp',
                    'estimated_savings' => $estimated_savings,
                );
            }
            
            // Check for missing alt text
            if (!$image['alt_text']) {
                $checks['missing_alt_text'][] = array(
                    'image_id' => $image['id'],
                    'filename' => $image['filename'],
                    'suggested_alt_text' => $this->suggest_alt_text($image),
                );
            }
            
            // Check for unused images
            if ($image['usage_count'] === 0) {
                // Check if image is old (more than 6 months)
                $uploaded_time = strtotime($image['uploaded']);
                $six_months_ago = strtotime('-6 months');
                
                if ($uploaded_time < $six_months_ago) {
                    $checks['unused_images'][] = array(
                        'image_id' => $image['id'],
                        'filename' => $image['filename'],
                        'last_used' => 'never',
                        'can_remove' => true,
                    );
                }
            }
        }
        
        return $checks;
    }
    
    /**
     * Suggest alt text based on filename and title
     */
    private function suggest_alt_text($image) {
        $title = $image['title'];
        $filename = pathinfo($image['filename'], PATHINFO_FILENAME);
        
        // Clean up filename
        $filename = str_replace(array('-', '_'), ' ', $filename);
        $filename = preg_replace('/\d+/', '', $filename); // Remove numbers
        $filename = ucwords(trim($filename));
        
        // Use title if available, otherwise use cleaned filename
        return !empty($title) ? $title : $filename;
    }
    
    /**
     * Get image statistics
     */
    public function get_statistics($data = null) {
        if ($data === null) {
            $data = $this->collect_image_data();
        }
        
        $stats = array(
            'total_images' => $data['total_images'],
            'total_size' => $data['total_size'],
            'average_size' => $data['average_size'],
            'formats' => $data['formats'],
        );
        
        // Calculate potential savings
        $potential_savings = 0;
        $webp_candidates = 0;
        $missing_alt = 0;
        $unused = 0;
        
        foreach ($data['images'] as $image) {
            // Estimate savings from compression
            if ($image['size_bytes'] > 500000) {
                $potential_savings += $image['size_bytes'] * 0.6; // Estimate 60% savings
            }
            
            // Count WebP conversion candidates
            if (in_array($image['format'], array('jpg', 'jpeg', 'png'))) {
                $webp_candidates++;
            }
            
            // Count missing alt text
            if (!$image['alt_text']) {
                $missing_alt++;
            }
            
            // Count unused images
            if ($image['usage_count'] === 0) {
                $unused++;
            }
        }
        
        $stats['potential_savings'] = $this->format_size($potential_savings);
        $stats['webp_candidates'] = $webp_candidates;
        $stats['missing_alt_text'] = $missing_alt;
        $stats['unused_images'] = $unused;
        
        return $stats;
    }
    
    /**
     * Get detailed image list for display
     */
    public function get_image_list($filters = array()) {
        $data = $this->collect_image_data();
        $images = $data['images'];
        
        // Apply filters
        if (!empty($filters['format'])) {
            $images = array_filter($images, function($img) use ($filters) {
                return $img['format'] === $filters['format'];
            });
        }
        
        if (!empty($filters['missing_alt'])) {
            $images = array_filter($images, function($img) {
                return !$img['alt_text'];
            });
        }
        
        if (!empty($filters['unused'])) {
            $images = array_filter($images, function($img) {
                return $img['usage_count'] === 0;
            });
        }
        
        if (!empty($filters['large'])) {
            $images = array_filter($images, function($img) {
                return $img['size_bytes'] > 500000;
            });
        }
        
        return array_values($images);
    }
}

