<?php
/**
 * Basic functionality tests for BT Site Recommendations
 * 
 * This is a simple test file to verify core functionality.
 * Run with: php tests/test-basic.php
 */

// Mock WordPress functions for testing
if (!function_exists('__')) {
    function __($text, $domain = 'default') {
        return $text;
    }
}

if (!function_exists('_e')) {
    function _e($text, $domain = 'default') {
        echo $text;
    }
}

if (!function_exists('esc_html')) {
    function esc_html($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('size_format')) {
    function size_format($bytes) {
        if ($bytes === 0) return '0 Bytes';
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }
}

echo "BT Site Recommendations - Basic Tests\n";
echo "=====================================\n\n";

// Test 1: Check if main classes can be loaded
echo "Test 1: Loading core classes...\n";

define('BT_SITE_RECOMMENDATIONS_PATH', dirname(__DIR__) . '/');

try {
    require_once BT_SITE_RECOMMENDATIONS_PATH . 'includes/class-bt-site-recommendations-analyzer.php';
    echo "✓ Analyzer class loaded successfully\n";
} catch (Exception $e) {
    echo "✗ Failed to load Analyzer class: " . $e->getMessage() . "\n";
}

try {
    require_once BT_SITE_RECOMMENDATIONS_PATH . 'includes/class-bt-site-recommendations-ai.php';
    echo "✓ AI class loaded successfully\n";
} catch (Exception $e) {
    echo "✗ Failed to load AI class: " . $e->getMessage() . "\n";
}

// Test 2: Test AI recommendation generation
echo "\nTest 2: Testing AI recommendation generation...\n";

$ai = new BT_Site_Recommendations_AI();

// Test with sample data
$test_page_speed = array(
    'load_time' => 3500,
    'page_size' => 1500000,
    'has_compression' => false,
    'has_caching' => false,
    'has_minification' => false,
    'images_count' => 25,
    'scripts_count' => 15
);

$test_seo = array(
    'has_title' => true,
    'title' => 'Test Site - Welcome',
    'title_length' => 20,
    'has_meta_description' => false,
    'meta_description' => '',
    'meta_description_length' => 0,
    'h1_count' => 0,
    'h2_count' => 5,
    'images_without_alt' => 10,
    'total_images' => 25,
    'has_robots_meta' => false,
    'has_canonical' => false,
    'has_og_tags' => false,
    'has_schema' => false
);

$recommendations = $ai->generate_recommendations($test_page_speed, $test_seo);

if (isset($recommendations['page_speed']) && is_array($recommendations['page_speed'])) {
    echo "✓ Page Speed recommendations generated: " . count($recommendations['page_speed']) . " items\n";
    
    // Check priority levels
    $priorities = array_unique(array_column($recommendations['page_speed'], 'priority'));
    echo "  - Priority levels found: " . implode(', ', $priorities) . "\n";
} else {
    echo "✗ Failed to generate Page Speed recommendations\n";
}

if (isset($recommendations['seo']) && is_array($recommendations['seo'])) {
    echo "✓ SEO recommendations generated: " . count($recommendations['seo']) . " items\n";
    
    // Check priority levels
    $priorities = array_unique(array_column($recommendations['seo'], 'priority'));
    echo "  - Priority levels found: " . implode(', ', $priorities) . "\n";
} else {
    echo "✗ Failed to generate SEO recommendations\n";
}

// Test 3: Verify recommendation structure
echo "\nTest 3: Verifying recommendation structure...\n";

$all_recommendations = array_merge(
    $recommendations['page_speed'] ?? [],
    $recommendations['seo'] ?? []
);

$valid_structure = true;
foreach ($all_recommendations as $rec) {
    if (!isset($rec['priority']) || !isset($rec['category']) || 
        !isset($rec['title']) || !isset($rec['description'])) {
        $valid_structure = false;
        break;
    }
}

if ($valid_structure && count($all_recommendations) > 0) {
    echo "✓ All recommendations have valid structure\n";
    echo "  - Total recommendations: " . count($all_recommendations) . "\n";
} else {
    echo "✗ Some recommendations have invalid structure\n";
}

// Test 4: Display sample recommendation
echo "\nTest 4: Sample recommendation output...\n";
if (count($all_recommendations) > 0) {
    $sample = $all_recommendations[0];
    echo "---\n";
    echo "Priority: " . $sample['priority'] . "\n";
    echo "Category: " . $sample['category'] . "\n";
    echo "Title: " . $sample['title'] . "\n";
    echo "Description: " . $sample['description'] . "\n";
    if (isset($sample['actions']) && is_array($sample['actions'])) {
        echo "Actions:\n";
        foreach ($sample['actions'] as $action) {
            echo "  - " . $action . "\n";
        }
    }
    echo "---\n";
    echo "✓ Sample recommendation displayed successfully\n";
}

// Summary
echo "\n=====================================\n";
echo "Tests completed!\n";
echo "Check the output above for any failures.\n";
