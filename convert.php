<?php
session_start();
header('Content-Type: application/json');

// Read and decode the JSON data from the request
$data = json_decode(file_get_contents('php://input'), true);

// Validate CSRF token
if (empty($data['csrf_token']) || $data['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token.']);
    exit;
}

// Check if the JSON data is valid
if ($data === null) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON.']);
    exit;
}

// Extract and sanitize URL and format
$url = filter_var($data['url'] ?? null, FILTER_SANITIZE_URL);
$format = $data['format'] ?? 'mp3'; // Default to mp3 if format is not provided

// Validate URL
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid URL provided.']);
    exit;
}

// Extract video ID from the URL
$videoId = extractVideoId($url);

if ($videoId) {
    // Fetch video information
    $infoCommand = escapeshellcmd("yt-dlp.exe --print-json \"$url\"");
    $infoJson = shell_exec($infoCommand);
    $info = json_decode($infoJson, true);

    if (!$info || !isset($info['title'])) {
        echo json_encode(['success' => false, 'error' => 'Failed to fetch video info.']);
        exit;
    }

    // Sanitize the title for file naming
    $title = preg_replace('/[^a-zA-Z0-9-_ ]/', '', $info['title']);
    $title = str_replace(' ', '_', $title); // Replace spaces with underscores

    // Directory for storing converted files
    $outputDir = __DIR__ . '/downloads/';
    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0777, true);
    }

    // Determine output file extension and command based on format
    $uniqueId = uniqid();
    if ($format === 'mp4') {
        $outputFile = $outputDir . $uniqueId . "_" . $title . ".mp4";

        $command = escapeshellcmd("yt-dlp.exe -f bestvideo+bestaudio --merge-output-format mp4 --output \"$outputFile\" \"$url\"");

    } else {
        $outputFile = $outputDir . $uniqueId . "_" . $title . ".mp3";
        $command = escapeshellcmd("yt-dlp.exe -x --audio-format mp3 --audio-quality 0 --output \"$outputFile\" \"$url\"");
    }
    
    // Execute the command
    exec($command, $output, $returnCode);
    
    // Check if the output file exists
    if (file_exists($outputFile) && $returnCode === 0) {
        $downloadUrl = 'downloads/' . basename($outputFile);
        echo json_encode(['success' => true, 'downloadUrl' => $downloadUrl]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Conversion failed.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid YouTube URL.']);
}

// Function to extract video ID from a YouTube URL
function extractVideoId($url) {
    preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches);
    return $matches[1] ?? null;
}
?>
