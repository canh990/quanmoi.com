<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoShort extends Model
{
    use HasFactory;

    protected $fillable = [
        'quan_id',
        'tieu_de',
        'video_id',
        'video_url',
        'thumbnail_url',
        'nguoi_dang',
        'avatar_nguoi_dang',
        'luot_xem',
        'luot_thich',
        'trang_thai',
    ];

    protected $appends = [
        'clean_video_url',
        'has_clean_video',
        'effective_thumbnail',
        'embed_url',
        'watch_url',
    ];

    public function quan()
    {
        return $this->belongsTo(Quan::class, 'quan_id');
    }

    /**
     * Bóc tách TikTok Video ID từ URL hoặc chuỗi ID
     */
    public static function extractTikTokVideoId(?string $urlOrId): ?string
    {
        if (empty($urlOrId)) {
            return null;
        }

        $trimmed = trim($urlOrId);

        // Trường hợp người dùng nhập trực tiếp ID (18-20 số)
        if (preg_match('/^\d{15,25}$/', $trimmed)) {
            return $trimmed;
        }

        // Trường hợp link chuẩn: /video/1234567890123456789
        if (preg_match('/\/video\/(\d+)/i', $trimmed, $matches)) {
            return $matches[1];
        }

        // Trường hợp link cũ: /v/1234567890123456789
        if (preg_match('/\/v\/(\d+)/i', $trimmed, $matches)) {
            return $matches[1];
        }

        // Trường hợp link rút gọn vt.tiktok.com hoặc vm.tiktok.com
        if (preg_match('/(vt|vm)\.tiktok\.com\/([A-Za-z0-9]+)/i', $trimmed)) {
            try {
                $headers = @get_headers($trimmed, 1);
                $location = $headers['Location'] ?? $headers['location'] ?? null;
                if (is_array($location)) {
                    $location = end($location);
                }
                if ($location && preg_match('/\/video\/(\d+)/i', $location, $m)) {
                    return $m[1];
                }
            } catch (\Throwable $e) {
                // bỏ qua lỗi nếu không kết nối được
            }
        }

        return null;
    }

    /**
     * Lấy dữ liệu video từ TikWM API (bao gồm link video sạch không logo/watermark)
     */
    public static function fetchTikTokData(string $urlOrId): ?array
    {
        $videoId = self::extractTikTokVideoId($urlOrId);
        $lookupUrl = str_starts_with($urlOrId, 'http')
            ? $urlOrId
            : ($videoId ? "https://www.tiktok.com/@tiktok/video/{$videoId}" : null);

        if (! $lookupUrl) {
            return null;
        }

        try {
            $apiEndpoint = 'https://www.tikwm.com/api/?url=' . urlencode($lookupUrl);
            $ch = curl_init($apiEndpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            $raw = curl_exec($ch);
            curl_close($ch);

            $json = json_decode($raw, true);
            if ($json && ($json['code'] ?? -1) === 0 && ! empty($json['data'])) {
                $d = $json['data'];
                return [
                    'video_id' => $d['id'] ?? $videoId,
                    'title' => $d['title'] ?? null,
                    'author_name' => '@' . ($d['author']['unique_id'] ?? 'reviewer'),
                    'author_avatar' => $d['author']['avatar'] ?? null,
                    'cover_url' => $d['cover'] ?? null,
                    'play_url' => $d['play'] ?? null,
                    'duration' => $d['duration'] ?? 0,
                    'views' => $d['play_count'] ?? 0,
                    'likes' => $d['digg_count'] ?? 0,
                ];
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('TikWM fetch failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Tải file video MP4 sạch (không logo/nút TikTok) về lưu trữ cục bộ
     */
    public static function downloadPlayVideo(string $playUrl, string $videoId): ?string
    {
        if (empty($playUrl) || empty($videoId)) {
            return null;
        }

        try {
            $folder = storage_path('app/public/videos/shorts');
            if (! is_dir($folder)) {
                mkdir($folder, 0755, true);
            }

            $destination = $folder . DIRECTORY_SEPARATOR . "{$videoId}.mp4";

            // Nếu file đã tồn tại và dung lượng > 100KB, dùng luôn
            if (file_exists($destination) && filesize($destination) > 100000) {
                return "/storage/videos/shorts/{$videoId}.mp4";
            }

            $fp = fopen($destination, 'w+');
            $ch = curl_init($playUrl);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            curl_setopt($ch, CURLOPT_REFERER, 'https://www.tiktok.com/');
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            fclose($fp);

            if ($httpCode === 200 && file_exists($destination) && filesize($destination) > 100000) {
                return "/storage/videos/shorts/{$videoId}.mp4";
            }

            // Nếu tải lỗi, xóa file rỗng
            if (file_exists($destination) && filesize($destination) <= 100000) {
                @unlink($destination);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Video download failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Tải file video MP4 sạch (không watermark) - alias cho downloadPlayVideo
     */
    public static function downloadCleanVideo(string $playUrl, string $videoId): ?string
    {
        return self::downloadPlayVideo($playUrl, $videoId);
    }

    /**
     * Tải thumbnail về lưu trữ cục bộ
     */
    public static function downloadCoverImage(string $coverUrl, string $videoId): ?string
    {
        if (empty($coverUrl) || empty($videoId)) {
            return null;
        }

        try {
            $folder = storage_path('app/public/videos/thumbnails');
            if (! is_dir($folder)) {
                mkdir($folder, 0755, true);
            }

            $destination = $folder . DIRECTORY_SEPARATOR . "{$videoId}.jpg";
            if (file_exists($destination) && filesize($destination) > 1000) {
                return "/storage/videos/thumbnails/{$videoId}.jpg";
            }

            $fp = fopen($destination, 'w+');
            $ch = curl_init($coverUrl);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            curl_setopt($ch, CURLOPT_REFERER, 'https://www.tiktok.com/');
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            fclose($fp);

            if ($httpCode === 200 && file_exists($destination) && filesize($destination) > 1000) {
                return "/storage/videos/thumbnails/{$videoId}.jpg";
            }
        } catch (\Throwable $e) {
            // bỏ qua lỗi nếu không tải được ảnh bìa
        }

        return null;
    }

    /**
     * Tải avatar người đăng về lưu trữ cục bộ
     */
    public static function downloadAvatarImage(?string $avatarUrl, string $videoId): ?string
    {
        if (empty($avatarUrl) || empty($videoId)) {
            return null;
        }

        try {
            $folder = storage_path('app/public/videos/avatars');
            if (! is_dir($folder)) {
                mkdir($folder, 0755, true);
            }

            $destination = $folder . DIRECTORY_SEPARATOR . "{$videoId}.jpg";
            if (file_exists($destination) && filesize($destination) > 500) {
                return "/storage/videos/avatars/{$videoId}.jpg";
            }

            $fp = fopen($destination, 'w+');
            $ch = curl_init($avatarUrl);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            fclose($fp);

            if ($httpCode === 200 && file_exists($destination) && filesize($destination) > 500) {
                return "/storage/videos/avatars/{$videoId}.jpg";
            }
        } catch (\Throwable $e) {
            // bỏ qua lỗi nếu không tải được avatar
        }

        return null;
    }

    /**
     * Đường dẫn video MP4 sạch (nếu có sẵn local hoặc link trực tiếp)
     */
    public function getCleanVideoUrlAttribute(): ?string
    {
        // 1. Kiểm tra nếu file local theo video_id tồn tại và dung lượng hợp lệ (>100KB)
        if (! empty($this->video_id)) {
            $localShort = storage_path("app/public/videos/shorts/{$this->video_id}.mp4");
            if (file_exists($localShort) && filesize($localShort) > 100000) {
                return "/storage/videos/shorts/{$this->video_id}.mp4";
            }
        }

        // 2. Kiểm tra nếu video_url đã là file cục bộ /storage/ và file đó có tồn tại
        if (! empty($this->video_url) && (str_starts_with($this->video_url, '/storage/') || str_starts_with($this->video_url, 'storage/'))) {
            $relativePath = preg_replace('#^/?storage/#', '', $this->video_url);
            $fullPath = storage_path("app/public/{$relativePath}");
            if (file_exists($fullPath) && filesize($fullPath) > 100000) {
                return '/' . ltrim($this->video_url, '/');
            }
        }

        // 3. Nếu video_url là một link MP4 trực tiếp
        if (! empty($this->video_url) && (str_ends_with(strtolower(parse_url($this->video_url, PHP_URL_PATH) ?? ''), '.mp4') || str_contains($this->video_url, 'mime_type=video_mp4'))) {
            return $this->video_url;
        }

        return null;
    }

    /**
     * Kiểm tra xem video này có sẵn video MP4 sạch không
     */
    public function getHasCleanVideoAttribute(): bool
    {
        return ! empty($this->clean_video_url);
    }

    /**
     * Ảnh thumbnail tối ưu (ưu tiên file local, sau đó đến thumbnail_url)
     */
    public function getEffectiveThumbnailAttribute(): string
    {
        // 1. Nếu có thumbnail_url cụ thể (ảnh upload tùy chỉnh hoặc link)
        if (! empty($this->thumbnail_url) && ! str_contains($this->thumbnail_url, 'placehold.co')) {
            if (str_starts_with($this->thumbnail_url, '/storage/') || str_starts_with($this->thumbnail_url, 'storage/')) {
                $rel = preg_replace('#^/?storage/#', '', $this->thumbnail_url);
                if (file_exists(storage_path("app/public/{$rel}"))) {
                    return '/' . ltrim($this->thumbnail_url, '/');
                }
            } else {
                return $this->thumbnail_url;
            }
        }

        // 2. Kiểm tra nếu có file thumbnail trong storage theo video_id
        if (! empty($this->video_id)) {
            $candidates = [
                storage_path("app/public/videos/thumbnails/{$this->video_id}.jpg"),
                storage_path("app/public/videos/thumbnails/{$this->video_id}.webp"),
                storage_path("app/public/videos/thumbnails/{$this->video_id}.png"),
                storage_path("app/public/videos/thumbnails/thumb_{$this->video_id}.jpg"),
                storage_path("app/public/videos/thumbnails/thumb_{$this->video_id}.png"),
                storage_path("app/public/videos/thumbnails/{$this->video_id}_custom.jpg"),
                storage_path("app/public/videos/thumbnails/{$this->video_id}_custom.png"),
            ];
            foreach ($candidates as $cand) {
                if (file_exists($cand) && filesize($cand) > 1000) {
                    return "/storage/videos/thumbnails/" . basename($cand);
                }
            }
        }

        return $this->quan?->anh_bia_url ?: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=600&q=80';
    }

    /**
     * URL nhúng iframe TikTok chuẩn
     */
    public function getEmbedUrlAttribute(): string
    {
        return "https://www.tiktok.com/embed/v2/{$this->video_id}";
    }

    /**
     * URL xem gốc trên TikTok
     */
    public function getWatchUrlAttribute(): string
    {
        if (! empty($this->video_url) && str_contains($this->video_url, 'tiktok.com')) {
            return $this->video_url;
        }

        $user = ltrim($this->nguoi_dang ?? 'tiktok', '@');
        return "https://www.tiktok.com/@{$user}/video/{$this->video_id}";
    }

    /**
     * Scope lọc video đã duyệt
     */
    public function scopeDaDuyet($query)
    {
        return $query->where('trang_thai', 'da_duyet');
    }

    /**
     * Scope lọc video chờ duyệt
     */
    public function scopeChoDuyet($query)
    {
        return $query->where('trang_thai', 'cho_duyet');
    }
}
