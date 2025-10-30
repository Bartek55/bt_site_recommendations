<?php
/**
 * AI Provider Manager for BT Site Recommendations
 * Handles communication with OpenAI (GPT-5) and Anthropic (Claude Sonnet 4.5)
 */

if (!defined('ABSPATH')) {
    exit;
}

class BT_Site_Recommendations_AI_Provider_Manager {
    
    private $openai_api_url = 'https://api.openai.com/v1/chat/completions';
    private $anthropic_api_url = 'https://api.anthropic.com/v1/messages';
    
    /**
     * Test API connection for a provider
     */
    public function test_connection($provider, $api_key) {
        if ($provider === 'openai') {
            return $this->test_openai_connection($api_key);
        } elseif ($provider === 'anthropic') {
            return $this->test_anthropic_connection($api_key);
        }
        
        return array(
            'success' => false,
            'message' => __('Invalid provider.', 'bt-site-recommendations')
        );
    }
    
    /**
     * Test OpenAI connection
     */
    private function test_openai_connection($api_key) {
        $response = wp_remote_post($this->openai_api_url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode(array(
                'model' => 'gpt-4', // Use gpt-4 for testing, will use gpt-5 when available
                'messages' => array(
                    array('role' => 'user', 'content' => 'Hello')
                ),
                'max_tokens' => 10
            )),
            'timeout' => 30,
        ));
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => $response->get_error_message()
            );
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        $status_code = wp_remote_retrieve_response_code($response);
        
        if ($status_code === 200 && isset($body['choices'])) {
            return array(
                'success' => true,
                'message' => __('OpenAI connection successful!', 'bt-site-recommendations')
            );
        }
        
        $error_message = isset($body['error']['message']) ? $body['error']['message'] : __('Unknown error', 'bt-site-recommendations');
        
        return array(
            'success' => false,
            'message' => $error_message
        );
    }
    
    /**
     * Test Anthropic connection
     */
    private function test_anthropic_connection($api_key) {
        $response = wp_remote_post($this->anthropic_api_url, array(
            'headers' => array(
                'x-api-key' => $api_key,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ),
            'body' => json_encode(array(
                'model' => 'claude-sonnet-4-5', // Will use Claude Sonnet 4.5 when available
                'messages' => array(
                    array('role' => 'user', 'content' => 'Hello')
                ),
                'max_tokens' => 10
            )),
            'timeout' => 30,
        ));
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => $response->get_error_message()
            );
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        $status_code = wp_remote_retrieve_response_code($response);
        
        if ($status_code === 200 && isset($body['content'])) {
            return array(
                'success' => true,
                'message' => __('Anthropic connection successful!', 'bt-site-recommendations')
            );
        }
        
        $error_message = isset($body['error']['message']) ? $body['error']['message'] : __('Unknown error', 'bt-site-recommendations');
        
        return array(
            'success' => false,
            'message' => $error_message
        );
    }
    
    /**
     * Send analysis request to AI provider
     */
    public function analyze($data, $analysis_type, $provider = null) {
        if (empty($provider)) {
            $provider = BT_Site_Recommendations_Settings::get_default_ai_provider();
        }
        
        if (empty($provider)) {
            return array(
                'success' => false,
                'message' => __('No AI provider configured. Please set up an API key first.', 'bt-site-recommendations')
            );
        }
        
        // Format the prompt based on analysis type
        $prompt = $this->build_prompt($data, $analysis_type);
        
        if ($provider === 'openai') {
            return $this->send_openai_request($prompt);
        } elseif ($provider === 'anthropic') {
            return $this->send_anthropic_request($prompt);
        }
        
        return array(
            'success' => false,
            'message' => __('Invalid provider.', 'bt-site-recommendations')
        );
    }
    
    /**
     * Build prompt based on analysis type
     */
    private function build_prompt($data, $analysis_type) {
        $prompts = array(
            'code' => $this->build_code_prompt($data),
            'database' => $this->build_database_prompt($data),
            'images' => $this->build_images_prompt($data),
        );
        
        return isset($prompts[$analysis_type]) ? $prompts[$analysis_type] : '';
    }
    
    /**
     * Build code analysis prompt
     */
    private function build_code_prompt($data) {
        $prompt = "You are a WordPress security, performance, and SEO expert. Analyze the following code from a WordPress site and provide detailed recommendations.\n\n";
        $prompt .= "Focus on:\n";
        $prompt .= "1. Security vulnerabilities (SQL injection, XSS, CSRF, insecure file operations)\n";
        $prompt .= "2. Deprecated WordPress functions\n";
        $prompt .= "3. PAGE SPEED & PERFORMANCE issues:\n";
        $prompt .= "   - Render-blocking JavaScript and CSS\n";
        $prompt .= "   - Inefficient database queries that slow page load\n";
        $prompt .= "   - Heavy loops and excessive processing\n";
        $prompt .= "   - Missing caching mechanisms\n";
        $prompt .= "   - Unoptimized asset loading\n";
        $prompt .= "   - Excessive HTTP requests\n";
        $prompt .= "4. SEO issues in code:\n";
        $prompt .= "   - Missing or improper meta tags\n";
        $prompt .= "   - Poor URL structure\n";
        $prompt .= "   - Missing schema markup opportunities\n";
        $prompt .= "   - Slow loading scripts affecting Core Web Vitals\n";
        $prompt .= "5. Code quality and best practices\n";
        $prompt .= "6. Recommend specific WordPress plugins that could help with:\n";
        $prompt .= "   - Caching (WP Rocket, W3 Total Cache, etc.)\n";
        $prompt .= "   - Lazy loading (if not implemented)\n";
        $prompt .= "   - Minification and concatenation\n";
        $prompt .= "   - SEO optimization (Yoast SEO, Rank Math, etc.)\n";
        $prompt .= "   - Performance monitoring\n\n";
        
        $prompt .= "Active Theme: " . $data['theme']['name'] . "\n";
        $prompt .= "Theme Files Analyzed: " . count($data['theme']['files']) . "\n\n";
        
        $prompt .= "Active Plugins:\n";
        foreach ($data['plugins'] as $plugin) {
            $prompt .= "- " . $plugin['name'] . " (Files: " . count($plugin['files']) . ")\n";
        }
        
        $prompt .= "\n\nCode Samples (truncated for analysis):\n\n";
        
        // Include code samples (limit size)
        $char_limit = 50000; // Limit to avoid token limits
        $current_chars = 0;
        
        foreach ($data['theme']['files'] as $file) {
            if ($current_chars >= $char_limit) break;
            $sample = substr($file['content'], 0, 2000);
            $prompt .= "File: " . $file['path'] . "\n";
            $prompt .= "```php\n" . $sample . "\n```\n\n";
            $current_chars += strlen($sample);
        }
        
        foreach ($data['plugins'] as $plugin) {
            if ($current_chars >= $char_limit) break;
            foreach ($plugin['files'] as $file) {
                if ($current_chars >= $char_limit) break;
                $sample = substr($file['content'], 0, 2000);
                $prompt .= "File: " . $file['path'] . "\n";
                $prompt .= "```php\n" . $sample . "\n```\n\n";
                $current_chars += strlen($sample);
            }
        }
        
        $prompt .= "\n\nProvide your analysis in JSON format with this structure:\n";
        $prompt .= "{\n";
        $prompt .= "  \"security_issues\": [{\"file\": \"path\", \"line\": number, \"severity\": \"critical|high|medium|low\", \"issue\": \"description\", \"fix\": \"suggestion\"}],\n";
        $prompt .= "  \"deprecated_functions\": [{\"file\": \"path\", \"line\": number, \"function\": \"name\", \"replacement\": \"suggestion\"}],\n";
        $prompt .= "  \"performance_issues\": [{\"file\": \"path\", \"line\": number, \"issue\": \"description\", \"fix\": \"suggestion\", \"impact\": \"page_speed_impact_description\"}],\n";
        $prompt .= "  \"seo_issues\": [{\"file\": \"path\", \"line\": number, \"issue\": \"description\", \"fix\": \"suggestion\", \"seo_impact\": \"how_it_affects_seo\"}],\n";
        $prompt .= "  \"code_quality\": [{\"file\": \"path\", \"line\": number, \"issue\": \"description\", \"fix\": \"suggestion\"}],\n";
        $prompt .= "  \"recommended_plugins\": [{\"name\": \"Plugin Name\", \"purpose\": \"what_it_does\", \"benefit\": \"speed_or_seo_benefit\", \"url\": \"wordpress_org_url\"}],\n";
        $prompt .= "  \"page_speed_score\": \"estimated Core Web Vitals impact summary\",\n";
        $prompt .= "  \"summary\": \"overall assessment with focus on page speed and SEO improvements\"\n";
        $prompt .= "}\n";
        
        return $prompt;
    }
    
    /**
     * Build database analysis prompt
     */
    private function build_database_prompt($data) {
        $prompt = "You are a WordPress database expert. Analyze the following database information and provide optimization recommendations.\n\n";
        $prompt .= "Focus on:\n";
        $prompt .= "1. Schema optimization (indexes, data types, relationships)\n";
        $prompt .= "2. Query performance (slow queries, inefficient queries)\n";
        $prompt .= "3. Data cleanup (unused tables, orphaned records, old transients)\n";
        $prompt .= "4. Security issues (exposed data, privilege concerns)\n";
        $prompt .= "5. Data integrity (missing foreign keys, validation)\n\n";
        
        $prompt .= "Database Information:\n";
        $prompt .= "Total Tables: " . count($data['tables']) . "\n";
        $prompt .= "Database Size: " . $data['total_size'] . "\n";
        $prompt .= "WordPress Version: " . $data['wp_version'] . "\n\n";
        
        $prompt .= "Table Details:\n";
        foreach ($data['tables'] as $table) {
            $prompt .= "- " . $table['name'] . " (Rows: " . $table['rows'] . ", Size: " . $table['size'] . ")\n";
            if (!empty($table['indexes'])) {
                $prompt .= "  Indexes: " . implode(', ', $table['indexes']) . "\n";
            }
        }
        
        if (!empty($data['slow_queries'])) {
            $prompt .= "\n\nSlow Queries Detected:\n";
            foreach ($data['slow_queries'] as $query) {
                $prompt .= "- Query: " . substr($query['query'], 0, 200) . "...\n";
                $prompt .= "  Time: " . $query['time'] . "s\n";
            }
        }
        
        $prompt .= "\n\nProvide your analysis in JSON format with this structure:\n";
        $prompt .= "{\n";
        $prompt .= "  \"schema_optimization\": [{\"table\": \"name\", \"issue\": \"description\", \"recommendation\": \"suggestion\", \"severity\": \"critical|warning|info\"}],\n";
        $prompt .= "  \"query_performance\": [{\"query\": \"excerpt\", \"issue\": \"description\", \"recommendation\": \"suggestion\"}],\n";
        $prompt .= "  \"data_cleanup\": [{\"type\": \"unused_tables|orphaned_records|transients\", \"items\": [\"list\"], \"recommendation\": \"suggestion\"}],\n";
        $prompt .= "  \"security_issues\": [{\"issue\": \"description\", \"severity\": \"critical|high|medium|low\", \"recommendation\": \"suggestion\"}],\n";
        $prompt .= "  \"data_integrity\": [{\"table\": \"name\", \"issue\": \"description\", \"recommendation\": \"suggestion\"}],\n";
        $prompt .= "  \"summary\": \"overall assessment\"\n";
        $prompt .= "}\n";
        
        return $prompt;
    }
    
    /**
     * Build images analysis prompt
     */
    private function build_images_prompt($data) {
        $prompt = "You are a WordPress media optimization, page speed, and SEO expert. Analyze the following image data and provide recommendations.\n\n";
        $prompt .= "Focus on PAGE SPEED and SEO:\n";
        $prompt .= "1. File size optimization for faster loading (Core Web Vitals - LCP)\n";
        $prompt .= "2. Format recommendations (WebP, AVIF) for reduced bandwidth\n";
        $prompt .= "3. Missing alt text for SEO and accessibility\n";
        $prompt .= "4. Unused images that slow down site\n";
        $prompt .= "5. Image dimensions vs actual usage (serving oversized images)\n";
        $prompt .= "6. Lazy loading opportunities\n";
        $prompt .= "7. Responsive image recommendations (srcset)\n";
        $prompt .= "8. CDN usage for images\n";
        $prompt .= "9. Recommend specific WordPress plugins for:\n";
        $prompt .= "   - Image optimization (Smush, ShortPixel, Imagify, etc.)\n";
        $prompt .= "   - Lazy loading (if not implemented)\n";
        $prompt .= "   - CDN integration\n";
        $prompt .= "   - WebP conversion if server doesn't support it\n\n";
        
        $prompt .= "Image Library Statistics:\n";
        $prompt .= "Total Images: " . $data['total_images'] . "\n";
        $prompt .= "Total Size: " . $data['total_size'] . "\n";
        $prompt .= "Average Size: " . $data['average_size'] . "\n";
        $prompt .= "Formats: " . implode(', ', array_keys($data['formats'])) . "\n\n";
        
        $prompt .= "Sample Images:\n";
        $sample_count = 0;
        foreach ($data['images'] as $image) {
            if ($sample_count >= 50) break; // Limit samples
            $prompt .= "- " . $image['filename'] . "\n";
            $prompt .= "  Size: " . $image['size'] . ", Format: " . $image['format'] . ", Dimensions: " . $image['width'] . "x" . $image['height'] . "\n";
            $prompt .= "  Alt Text: " . ($image['alt_text'] ? 'Yes' : 'No') . ", Used in: " . $image['usage_count'] . " places\n";
            $sample_count++;
        }
        
        $prompt .= "\n\nProvide your analysis in JSON format with this structure:\n";
        $prompt .= "{\n";
        $prompt .= "  \"size_optimization\": [{\"image_id\": number, \"filename\": \"name\", \"current_size\": \"size\", \"potential_savings\": \"estimate\", \"recommendation\": \"suggestion\", \"page_speed_impact\": \"LCP improvement estimate\"}],\n";
        $prompt .= "  \"format_conversion\": [{\"image_id\": number, \"filename\": \"name\", \"current_format\": \"format\", \"recommended_format\": \"format\", \"estimated_savings\": \"percentage\", \"page_speed_impact\": \"loading time improvement\"}],\n";
        $prompt .= "  \"missing_alt_text\": [{\"image_id\": number, \"filename\": \"name\", \"suggested_alt_text\": \"SEO optimized text\", \"seo_impact\": \"how_it_helps_seo\"}],\n";
        $prompt .= "  \"unused_images\": [{\"image_id\": number, \"filename\": \"name\", \"last_used\": \"date or never\", \"can_remove\": boolean}],\n";
        $prompt .= "  \"dimension_issues\": [{\"image_id\": number, \"filename\": \"name\", \"issue\": \"description\", \"recommendation\": \"suggestion\"}],\n";
        $prompt .= "  \"lazy_loading\": {\"implemented\": boolean, \"recommendation\": \"if not implemented, how to add it\"},\n";
        $prompt .= "  \"recommended_plugins\": [{\"name\": \"Plugin Name\", \"purpose\": \"what_it_does\", \"benefit\": \"page_speed_or_seo_benefit\", \"url\": \"wordpress_org_url\"}],\n";
        $prompt .= "  \"page_speed_improvement\": \"estimated total page speed improvement from all optimizations\",\n";
        $prompt .= "  \"seo_improvement\": \"estimated SEO benefit from alt text and optimization\",\n";
        $prompt .= "  \"summary\": \"overall assessment with total potential savings and page speed/SEO impact\"\n";
        $prompt .= "}\n";
        
        return $prompt;
    }
    
    /**
     * Send request to OpenAI
     */
    private function send_openai_request($prompt) {
        $api_key = BT_Site_Recommendations_Settings::get_api_keys()['openai'];
        
        if (empty($api_key)) {
            return array(
                'success' => false,
                'message' => __('OpenAI API key not configured.', 'bt-site-recommendations')
            );
        }
        
        $response = wp_remote_post($this->openai_api_url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode(array(
                'model' => 'gpt-4-turbo-preview', // Use gpt-5 when available
                'messages' => array(
                    array(
                        'role' => 'system',
                        'content' => 'You are an expert WordPress developer and consultant. Provide detailed, actionable recommendations in JSON format as requested.'
                    ),
                    array(
                        'role' => 'user',
                        'content' => $prompt
                    )
                ),
                'temperature' => 0.3,
                'max_tokens' => 4000,
            )),
            'timeout' => 60,
        ));
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => $response->get_error_message()
            );
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        $status_code = wp_remote_retrieve_response_code($response);
        
        if ($status_code !== 200) {
            $error_message = isset($body['error']['message']) ? $body['error']['message'] : __('Unknown error', 'bt-site-recommendations');
            return array(
                'success' => false,
                'message' => $error_message
            );
        }
        
        if (!isset($body['choices'][0]['message']['content'])) {
            return array(
                'success' => false,
                'message' => __('Invalid response from OpenAI.', 'bt-site-recommendations')
            );
        }
        
        $content = $body['choices'][0]['message']['content'];
        
        // Try to parse JSON from the response
        $analysis = $this->parse_ai_response($content);
        
        return array(
            'success' => true,
            'data' => $analysis,
            'raw_response' => $content
        );
    }
    
    /**
     * Send request to Anthropic
     */
    private function send_anthropic_request($prompt) {
        $api_key = BT_Site_Recommendations_Settings::get_api_keys()['anthropic'];
        
        if (empty($api_key)) {
            return array(
                'success' => false,
                'message' => __('Anthropic API key not configured.', 'bt-site-recommendations')
            );
        }
        
        $response = wp_remote_post($this->anthropic_api_url, array(
            'headers' => array(
                'x-api-key' => $api_key,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ),
            'body' => json_encode(array(
                'model' => 'claude-3-5-sonnet-20241022', // Will use claude-sonnet-4-5 when available
                'max_tokens' => 4000,
                'messages' => array(
                    array(
                        'role' => 'user',
                        'content' => $prompt
                    )
                )
            )),
            'timeout' => 60,
        ));
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => $response->get_error_message()
            );
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        $status_code = wp_remote_retrieve_response_code($response);
        
        if ($status_code !== 200) {
            $error_message = isset($body['error']['message']) ? $body['error']['message'] : __('Unknown error', 'bt-site-recommendations');
            return array(
                'success' => false,
                'message' => $error_message
            );
        }
        
        if (!isset($body['content'][0]['text'])) {
            return array(
                'success' => false,
                'message' => __('Invalid response from Anthropic.', 'bt-site-recommendations')
            );
        }
        
        $content = $body['content'][0]['text'];
        
        // Try to parse JSON from the response
        $analysis = $this->parse_ai_response($content);
        
        return array(
            'success' => true,
            'data' => $analysis,
            'raw_response' => $content
        );
    }
    
    /**
     * Parse AI response and extract JSON
     */
    private function parse_ai_response($content) {
        // Try to find JSON in the response
        if (preg_match('/\{[\s\S]*\}/', $content, $matches)) {
            $json = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $json;
            }
        }
        
        // If no JSON found, return the raw content
        return array(
            'raw_content' => $content,
            'error' => 'Could not parse JSON from AI response'
        );
    }
}

