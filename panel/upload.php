<?php
include_once('../includes/config.php');

// Define allowed file formats and maximum file size (in bytes)
$allowed_formats = array("docx", "doc", "pdf", "mp3", "mp4", "wav", "avi", "mov", "zip", "xlsx","jpg", "png", "jpeg", "gif", "svg", "webp");
$max_file_size = 20000 * 1024 * 1024; // 20GB

// Check if a file is uploaded
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {

    // Get file info
    $file_name = $_FILES['file']['name'];
    $file_size = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_type = mime_content_type($file_tmp); // Get MIME type

    // Check if file type is allowed
    if (!in_array($file_type, $allowed_formats)) {
        echo json_encode(["error" => "Invalid file format. Only JPEG, PNG, and GIF are allowed."]);
        exit;
    }

    // Check if file size is within the allowed limit
    if ($file_size > $max_file_size) {
        echo json_encode(["error" => "File size exceeds the allowed limit of 5MB."]);
        exit;
    }

    // Proceed with the upload if everything is valid
    $upload = base::Upload($_FILES['file']);
    $images = array("path" => $upload);
    echo json_encode($images);
} else {
    echo json_encode(["error" => "No file uploaded or upload error occurred."]);
}
?>