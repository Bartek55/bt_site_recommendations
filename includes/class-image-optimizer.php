<?php
/**
 * Image Optimizer for BT Site Recommendations
 * Handles batch image optimization and format conversion
 */

if (!defined('ABSPATH')) {
    exit;
}

class BT_Site_Recommendations_Image_Optimizer {
    
    /**
     * Optimize batch of images
     */
    public function optimize_batch($image_ids, $action) {
        if (!BT_Site_Recommendations_Settings::has_permission('access_image_files')) {
            return array(
                'success' => false,
                'message' => __('Permission to modify images is not granted.', 'bt-site-recommendations')
            );
        }
        
        $results = array(
            'success' => true,
            'processed' => 0,
            'failed' => 0,
            'details' => array(),
        );
        
        foreach ($image_ids as $image_id) {
            $result = $this->optimize_single_image($image_id, $action);
            
            if ($result['success']) {
                $results['processed']++;
                $results['details'][] = array(
                    'image_id' => $image_id,
                    'status' => 'success',
                    'message' => $result['message'],
                    'savings' => isset($result['savings']) ? $result['savings'] : null,
                );
            } else {
                $results['failed']++;
                $results['details'][] = array(
                    'image_id' => $image_id,
                    'status' => 'failed',
                    'message' => $result['message'],
                );
            }
        }
        
        if ($results['failed'] > 0) {
            $results['success'] = false;
        }
        
        $results['message'] = sprintf(
            __('Processed %d images, %d failed.', 'bt-site-recommendations'),
            $results['processed'],
            $results['failed']
        );
        
        return $results;
    }
    
    /**
     * Optimize single image
     */
    private function optimize_single_image($image_id, $action) {
        switch ($action) {
            case 'compress':
                return $this->compress_image($image_id);
                
            case 'convert_webp':
                return $this->convert_to_webp($image_id);
                
            case 'add_alt_text':
                return $this->add_alt_text($image_id);
                
            case 'resize':
                return $this->resize_image($image_id);
                
            default:
                return array(
                    'success' => false,
                    'message' => __('Invalid action.', 'bt-site-recommendations')
                );
        }
    }
    
    /**
     * Compress image
     */
    private function compress_image($image_id) {
        $file_path = get_attached_file($image_id);
        
        if (!file_exists($file_path)) {
            return array(
                'success' => false,
                'message' => __('Image file not found.', 'bt-site-recommendations')
            );
        }
        
        // Get original file size
        $original_size = filesize($file_path);
        
        // Create backup
        $backup_path = $file_path . '.backup';
        if (!copy($file_path, $backup_path)) {
            return array(
                'success' => false,
                'message' => __('Failed to create backup.', 'bt-site-recommendations')
            );
        }
        
        // Get image type
        $image_type = wp_check_filetype($file_path);
        $mime_type = $image_type['type'];
        
        // Load image
        $image = wp_get_image_editor($file_path);
        
        if (is_wp_error($image)) {
            // Restore from backup
            copy($backup_path, $file_path);
            unlink($backup_path);
            
            return array(
                'success' => false,
                'message' => $image->get_error_message()
            );
        }
        
        // Set compression quality
        $quality = 82; // Good balance between quality and size
        
        if (method_exists($image, 'set_quality')) {
            $image->set_quality($quality);
        }
        
        // Save compressed image
        $saved = $image->save($file_path);
        
        if (is_wp_error($saved)) {
            // Restore from backup
            copy($backup_path, $file_path);
            unlink($backup_path);
            
            return array(
                'success' => false,
                'message' => $saved->get_error_message()
            );
        }
        
        // Get new file size
        $new_size = filesize($file_path);
        $savings = $original_size - $new_size;
        $savings_percent = ($original_size > 0) ? round(($savings / $original_size) * 100, 2) : 0;
        
        // Remove backup
        unlink($backup_path);
        
        return array(
            'success' => true,
            'message' => sprintf(
                __('Compressed successfully. Saved %s (%s%%).', 'bt-site-recommendations'),
                $this->format_size($savings),
                $savings_percent
            ),
            'savings' => $savings,
        );
    }
    
    /**
     * Convert image to WebP format
     */
    private function convert_to_webp($image_id) {
        // Check if GD or Imagick supports WebP
        if (!$this->webp_supported()) {
            return array(
                'success' => false,
                'message' => __('WebP conversion is not supported on this server.', 'bt-site-recommendations')
            );
        }
        
        $file_path = get_attached_file($image_id);
        
        if (!file_exists($file_path)) {
            return array(
                'success' => false,
                'message' => __('Image file not found.', 'bt-site-recommendations')
            );
        }
        
        // Get original file size
        $original_size = filesize($file_path);
        
        // Get image type
        $image_info = pathinfo($file_path);
        $extension = strtolower($image_info['extension']);
        
        // Skip if already WebP
        if ($extension === 'webp') {
            return array(
                'success' => false,
                'message' => __('Image is already in WebP format.', 'bt-site-recommendations')
            );
        }
        
        // Create backup of original
        $backup_path = $file_path . '.backup';
        copy($file_path, $backup_path);
        
        // Load image
        $image = wp_get_image_editor($file_path);
        
        if (is_wp_error($image)) {
            unlink($backup_path);
            return array(
                'success' => false,
                'message' => $image->get_error_message()
            );
        }
        
        // Generate new WebP filename
        $webp_path = $image_info['dirname'] . '/' . $image_info['filename'] . '.webp';
        
        // Set quality and format
        if (method_exists($image, 'set_quality')) {
            $image->set_quality(85);
        }
        
        // Save as WebP
        $saved = $image->save($webp_path, 'image/webp');
        
        if (is_wp_error($saved)) {
            unlink($backup_path);
            return array(
                'success' => false,
                'message' => $saved->get_error_message()
            );
        }
        
        // Get new file size
        $new_size = filesize($webp_path);
        $savings = $original_size - $new_size;
        $savings_percent = ($original_size > 0) ? round(($savings / $original_size) * 100, 2) : 0;
        
        // Update attachment metadata
        update_attached_file($image_id, $webp_path);
        
        // Update post mime type
        wp_update_post(array(
            'ID' => $image_id,
            'post_mime_type' => 'image/webp',
        ));
        
        // Generate new metadata
        $metadata = wp_generate_attachment_metadata($image_id, $webp_path);
        wp_update_attachment_metadata($image_id, $metadata);
        
        // Keep backup for rollback option
        update_post_meta($image_id, '_bt_sr_original_backup', $backup_path);
        
        return array(
            'success' => true,
            'message' => sprintf(
                __('Converted to WebP. Saved %s (%s%%).', 'bt-site-recommendations'),
                $this->format_size($savings),
                $savings_percent
            ),
            'savings' => $savings,
        );
    }
    
    /**
     * Add alt text to image
     */
    private function add_alt_text($image_id) {
        // Get suggested alt text
        $image = get_post($image_id);
        
        if (!$image) {
            return array(
                'success' => false,
                'message' => __('Image not found.', 'bt-site-recommendations')
            );
        }
        
        // Check if alt text already exists
        $existing_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
        
        if (!empty($existing_alt)) {
            return array(
                'success' => false,
                'message' => __('Image already has alt text.', 'bt-site-recommendations')
            );
        }
        
        // Generate alt text from title or filename
        $alt_text = $image->post_title;
        
        if (empty($alt_text)) {
            $file_path = get_attached_file($image_id);
            $filename = pathinfo($file_path, PATHINFO_FILENAME);
            $alt_text = str_replace(array('-', '_'), ' ', $filename);
            $alt_text = preg_replace('/\d+/', '', $alt_text);
            $alt_text = ucwords(trim($alt_text));
        }
        
        // Update alt text
        update_post_meta($image_id, '_wp_attachment_image_alt', sanitize_text_field($alt_text));
        
        return array(
            'success' => true,
            'message' => sprintf(
                __('Added alt text: "%s"', 'bt-site-recommendations'),
                $alt_text
            ),
        );
    }
    
    /**
     * Resize oversized image
     */
    private function resize_image($image_id, $max_width = 2048, $max_height = 2048) {
        $file_path = get_attached_file($image_id);
        
        if (!file_exists($file_path)) {
            return array(
                'success' => false,
                'message' => __('Image file not found.', 'bt-site-recommendations')
            );
        }
        
        // Get image dimensions
        $image_info = getimagesize($file_path);
        
        if (!$image_info) {
            return array(
                'success' => false,
                'message' => __('Could not get image dimensions.', 'bt-site-recommendations')
            );
        }
        
        $width = $image_info[0];
        $height = $image_info[1];
        
        // Check if resize is needed
        if ($width <= $max_width && $height <= $max_height) {
            return array(
                'success' => false,
                'message' => __('Image does not need resizing.', 'bt-site-recommendations')
            );
        }
        
        // Get original file size
        $original_size = filesize($file_path);
        
        // Create backup
        $backup_path = $file_path . '.backup';
        copy($file_path, $backup_path);
        
        // Load image
        $image = wp_get_image_editor($file_path);
        
        if (is_wp_error($image)) {
            unlink($backup_path);
            return array(
                'success' => false,
                'message' => $image->get_error_message()
            );
        }
        
        // Resize
        $image->resize($max_width, $max_height, false);
        
        // Save
        $saved = $image->save($file_path);
        
        if (is_wp_error($saved)) {
            // Restore from backup
            copy($backup_path, $file_path);
            unlink($backup_path);
            
            return array(
                'success' => false,
                'message' => $saved->get_error_message()
            );
        }
        
        // Get new file size
        $new_size = filesize($file_path);
        $savings = $original_size - $new_size;
        
        // Update metadata
        $metadata = wp_generate_attachment_metadata($image_id, $file_path);
        wp_update_attachment_metadata($image_id, $metadata);
        
        // Remove backup
        unlink($backup_path);
        
        return array(
            'success' => true,
            'message' => sprintf(
                __('Resized to %dx%d. Saved %s.', 'bt-site-recommendations'),
                $metadata['width'],
                $metadata['height'],
                $this->format_size($savings)
            ),
            'savings' => $savings,
        );
    }
    
    /**
     * Check if WebP is supported
     */
    private function webp_supported() {
        // Check GD
        if (function_exists('imagewebp')) {
            return true;
        }
        
        // Check Imagick
        if (class_exists('Imagick')) {
            $imagick = new Imagick();
            $formats = $imagick->queryFormats('WEBP');
            return !empty($formats);
        }
        
        return false;
    }
    
    /**
     * Restore original image from backup
     */
    public function restore_original($image_id) {
        $backup_path = get_post_meta($image_id, '_bt_sr_original_backup', true);
        
        if (empty($backup_path) || !file_exists($backup_path)) {
            return array(
                'success' => false,
                'message' => __('No backup found for this image.', 'bt-site-recommendations')
            );
        }
        
        $current_path = get_attached_file($image_id);
        
        // Replace with backup
        if (copy($backup_path, $current_path)) {
            // Update metadata
            $metadata = wp_generate_attachment_metadata($image_id, $current_path);
            wp_update_attachment_metadata($image_id, $metadata);
            
            // Update mime type
            $filetype = wp_check_filetype($current_path);
            wp_update_post(array(
                'ID' => $image_id,
                'post_mime_type' => $filetype['type'],
            ));
            
            // Remove backup
            unlink($backup_path);
            delete_post_meta($image_id, '_bt_sr_original_backup');
            
            return array(
                'success' => true,
                'message' => __('Original image restored successfully.', 'bt-site-recommendations')
            );
        }
        
        return array(
            'success' => false,
            'message' => __('Failed to restore original image.', 'bt-site-recommendations')
        );
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
     * Get optimization statistics
     */
    public function get_statistics() {
        global $wpdb;
        
        // Count images with backups (optimized images)
        $optimized_count = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->postmeta} 
            WHERE meta_key = '_bt_sr_original_backup'"
        );
        
        return array(
            'optimized_images' => intval($optimized_count),
        );
    }
}

