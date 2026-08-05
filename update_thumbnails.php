<?php
$videos = App\Models\VideoShort::all();
foreach ($videos as $video) {
    if (strpos($video->thumbnail_url, 'placehold.co') !== false || empty($video->thumbnail_url)) {
        $url = 'https://www.tiktok.com/oembed?url=' . urlencode($video->video_url);
        try {
            $response = file_get_contents($url);
            $data = json_decode($response, true);
            if (isset($data['thumbnail_url'])) {
                $video->thumbnail_url = $data['thumbnail_url'];
                $video->save();
                echo "Updated video " . $video->id . " thumbnail.\n";
            }
        } catch (\Exception $e) {
            echo "Failed to update video " . $video->id . " - " . $e->getMessage() . "\n";
        }
    }
}
echo "Done.\n";
