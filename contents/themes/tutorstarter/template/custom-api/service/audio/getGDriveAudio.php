<?php
require_once realpath(__DIR__ . '/../google-api/vendor/autoload.php');
function getAudioGDrive(WP_REST_Request $request) {
    $fileId = $request->get_param('id');
    if (!$fileId) {
        return new WP_REST_Response(['error' => 'Missing file ID'], 400);
    }

    // Setup Google Client
    $client = new Google_Client();
    $client->setAuthConfig(__DIR__ . '/credentials.json');
    $client->addScope(Google_Service_Drive::DRIVE_READONLY);

    $service = new Google_Service_Drive($client);

    try {
        // Lấy metadata để biết MIME type
        $file = $service->files->get($fileId, ['fields' => 'name, mimeType']);
        
        // Lấy nội dung file
        $response = $service->files->get($fileId, ['alt' => 'media']);
        $content = $response->getBody()->getContents();

        return new WP_REST_Response($content, 200, [
            'Content-Type' => $file->getMimeType(),
            'Content-Disposition' => 'inline; filename="' . $file->getName() . '"'
        ]);
    } catch (Exception $e) {
        return new WP_REST_Response(['error' => 'Failed to fetch file: ' . $e->getMessage()], 500);
    }
}
