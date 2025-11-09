<?php
require_once '../includes/validate.php';

$content_file = '../admin/content_config.json';
$gallery_image_dir = '../admin/image/gallery/';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filename'])) {
    $filename_to_delete = $_POST['filename'];
    $file_path = $gallery_image_dir . $filename_to_delete;

    $response = ['success' => false, 'message' => 'An unknown error occurred.'];

    if (file_exists($file_path)) {
        // Read content_config.json
        $content_data = json_decode(file_get_contents($content_file), true);

        // Remove image from content_config.json
        $updated_images = [];
        $found = false;
        foreach ($content_data['gallery']['images'] as $image) {
            if ($image['filename'] !== $filename_to_delete) {
                $updated_images[] = $image;
            } else {
                $found = true;
            }
        }
        $content_data['gallery']['images'] = $updated_images;

        if ($found) {
            // Write updated content_config.json back
            if (file_put_contents($content_file, json_encode($content_data, JSON_PRETTY_PRINT))) {
                // Delete the actual image file
                if (unlink($file_path)) {
                    $response = ['success' => true, 'message' => 'Image and entry deleted successfully.'];
                } else {
                    $response = ['success' => false, 'message' => 'Failed to delete image file from server.'];
                }
            } else {
                $response = ['success' => false, 'message' => 'Failed to update content_config.json.'];
            }
        } else {
            $response = ['success' => false, 'message' => 'Image not found in content_config.json.'];
        }
    } else {
        $response = ['success' => false, 'message' => 'Image file not found on server.'];
    }
} else {
    $response = ['success' => false, 'message' => 'Invalid request.'];
}

echo json_encode($response);
?>