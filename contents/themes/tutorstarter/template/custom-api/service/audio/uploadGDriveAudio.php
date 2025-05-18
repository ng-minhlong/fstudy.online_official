<?php

require_once realpath(__DIR__ . '/../google-api/vendor/autoload.php');

function uploadAudioGDrive(WP_REST_Request $request) {
    $files = $request->get_file_params();

    if (!isset($files['file'])) {
        return new WP_REST_Response(['error' => 'No file uploaded.'], 400);
    }

    $file = $files['file'];
    $tmp_name = $file['tmp_name'];
    $filename = basename($file['name']);
    $mime_type = mime_content_type($tmp_name);

    $upload_dir = wp_upload_dir();
    $target_path = $upload_dir['basedir'] . '/gdrive_uploads';
    if (!file_exists($target_path)) {
        mkdir($target_path, 0775, true);
    }

    $saved_path = $target_path . '/' . $filename;
    move_uploaded_file($tmp_name, $saved_path);

    // Google Drive credentials
    $client = new Google_Client();
    $client->setAuthConfig(__DIR__ . '/credentials.json'); // File JSON chứa key dịch vụ
    $client->addScope(Google_Service_Drive::DRIVE);

    $service = new Google_Service_Drive($client);

    $fileMetadata = new Google_Service_Drive_DriveFile([
        'name' => $filename,
        'parents' => ['1St-zffvWUc0OSZk6dGQTHhPJOP2WBQUx'] // Folder ID
    ]);

    $content = file_get_contents($saved_path);
    $driveFile = $service->files->create($fileMetadata, [
        'data' => $content,
        'mimeType' => $mime_type,
        'uploadType' => 'multipart',
        'fields' => 'id'
    ]);

    // Share file publicly
    $permission = new Google_Service_Drive_Permission([
        'type' => 'anyone',
        'role' => 'reader'
    ]);
    $service->permissions->create($driveFile->id, $permission);

    $link = "https://drive.google.com/uc?export=download&id=" . $driveFile->id;

    $response = new WP_REST_Response(['link' => $link], 200);
    
    // Thêm filter tạm thời cho request này
    add_filter('rest_pre_serve_request', function($served, $response, $request, $server) {
        echo json_encode($response->get_data(), JSON_UNESCAPED_SLASHES);
        return true;
    }, 10, 4);
    
    return $response;


}
