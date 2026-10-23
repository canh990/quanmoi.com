<?php

namespace App\Http\Controllers\ChuQuan;

use App\Http\Controllers\Controller;
use App\Models\Quan;
use App\Models\VideoShort;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class VideoController extends Controller
{
    /**
     * Lấy quán thuộc sở hữu của chủ quán (hoặc admin)
     */
    protected function getQuan(string $slug): Quan
    {
        $user = Auth::user();
        if (! $user) {
            abort(401, 'Vui lòng đăng nhập.');
        }

        $query = Quan::where('slug', $slug);
        if (! $user->isAdmin()) {
            $query->where('chu_quan_id', $user->id);
        }

        return $query->firstOrFail();
    }

    /**
     * AJAX Lấy thông tin video TikTok để chủ quán xem trước khi đăng
     */
    public function ajaxFetchTikTokInfo(Request $request, string $slug)
    {
        $quan = $this->getQuan($slug);

        $request->validate([
            'url' => 'required|string',
        ]);

        $input = trim($request->url);
        $videoId = VideoShort::extractTikTokVideoId($input);

        if (! $videoId) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy ID video từ link TikTok bạn đã nhập.',
            ], 422);
        }

        $data = VideoShort::fetchTikTokData($input);

        if (! $data) {
            return response()->json([
                'success' => true,
                'message' => 'Đã nhận diện Video ID: ' . $videoId,
                'data' => [
                    'video_id' => $videoId,
                    'title' => '',
                    'author_name' => '@tiktok_creator',
                    'author_avatar' => null,
                    'cover_url' => null,
                    'has_clean_stream' => false,
                    'views' => 0,
                    'likes' => 0,
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã tải thông tin video thành công!',
            'data' => array_merge($data, [
                'has_clean_stream' => ! empty($data['play_url']),
            ]),
        ]);
    }

    /**
     * Chủ quán gửi video review TikTok mới (chờ Admin duyệt)
     */
    public function store(Request $request, string $slug)
    {
        $quan = $this->getQuan($slug);
        set_time_limit(180);

        $validated = $request->validate([
            'video_input' => 'nullable|string|max:1000',
            'video_file' => 'nullable|file|mimes:mp4,mov,webm|max:102400',
            'tieu_de' => 'nullable|string|max:1000',
            'thumbnail_file' => 'nullable|image|max:10240',
        ], [
            'video_file.mimes' => 'File video phải có định dạng .mp4, .mov hoặc .webm.',
            'video_file.max' => 'Dung lượng video tối đa là 100MB.',
            'thumbnail_file.image' => 'Ảnh bìa phải là định dạng hình ảnh.',
        ]);

        if (empty($validated['video_input']) && ! $request->hasFile('video_file')) {
            return back()->withInput()->with('error', 'Vui lòng dán link video TikTok hoặc chọn file video MP4 để tải lên.');
        }

        $videoId = null;
        $videoUrl = null;
        $thumbnailUrl = null;
        $tieuDe = trim($validated['tieu_de'] ?? '');
        $user = Auth::user();
        $nguoiDang = '@' . ($user->ho_ten ?: $user->name ?: 'chuquan');
        $avatarNguoiDang = $user->anh_dai_dien_url ?? null;
        $luotXem = 0;
        $luotThich = 0;
        $isCleanDownloaded = false;

        // 1. Trường hợp tải file video trực tiếp từ máy
        if ($request->hasFile('video_file')) {
            $file = $request->file('video_file');
            $uniqueId = 'local_' . time() . '_' . substr(bin2hex(random_bytes(4)), 0, 8);
            $filename = "{$uniqueId}." . $file->getClientOriginalExtension();
            $file->storeAs('videos/shorts', $filename, 'public');

            $videoId = $uniqueId;
            $videoUrl = "/storage/videos/shorts/{$filename}";
            $isCleanDownloaded = true;

            if (empty($tieuDe)) {
                $tieuDe = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            }
        }
        // 2. Trường hợp dán link TikTok
        elseif (! empty($validated['video_input'])) {
            $input = trim($validated['video_input']);
            $videoId = VideoShort::extractTikTokVideoId($input);

            if (! $videoId) {
                return back()->withInput()->with('error', 'Không thể nhận diện Video ID từ đường dẫn TikTok. Vui lòng kiểm tra lại link video.');
            }

            // Kiểm tra xem video này đã được đăng cho quán này chưa
            $existing = VideoShort::where('video_id', $videoId)->where('quan_id', $quan->id)->first();
            if ($existing) {
                return back()->withInput()->with('error', 'Video TikTok này đã được đăng ký cho quán của bạn trước đó rồi.');
            }

            $tiktokData = VideoShort::fetchTikTokData($input);

            if ($tiktokData) {
                if (empty($tieuDe)) {
                    $tieuDe = $tiktokData['title'] ?? ('Review ' . $quan->ten_quan . ' #' . $videoId);
                }
                $nguoiDang = ! empty($tiktokData['author_name']) ? '@' . ltrim($tiktokData['author_name'], '@') : $nguoiDang;
                $luotXem = (int) ($tiktokData['views'] ?? 0);
                $luotThich = (int) ($tiktokData['likes'] ?? 0);

                // Tải video sạch không watermark
                if (! empty($tiktokData['play_url'])) {
                    $savedPath = VideoShort::downloadPlayVideo($tiktokData['play_url'], $videoId);
                    if ($savedPath) {
                        $videoUrl = $savedPath;
                        $isCleanDownloaded = true;
                    }
                }

                // Tải ảnh thumbnail
                if (! empty($tiktokData['cover_url'])) {
                    $savedThumb = VideoShort::downloadCoverImage($tiktokData['cover_url'], $videoId);
                    $thumbnailUrl = $savedThumb ?: $tiktokData['cover_url'];
                }

                // Tải ảnh đại diện tác giả
                if (! empty($tiktokData['author_avatar'])) {
                    $savedAvatar = VideoShort::downloadAvatarImage($tiktokData['author_avatar'], $videoId);
                    $avatarNguoiDang = $savedAvatar ?: $tiktokData['author_avatar'];
                }
            } else {
                if (empty($tieuDe)) {
                    $tieuDe = 'Review ' . $quan->ten_quan . ' #' . $videoId;
                }
            }
        }

        // Tải ảnh bìa tùy chỉnh (nếu có)
        if ($request->hasFile('thumbnail_file')) {
            $thumbFile = $request->file('thumbnail_file');
            $thumbName = "{$videoId}_custom." . $thumbFile->getClientOriginalExtension();
            $thumbFile->storeAs('videos/thumbnails', $thumbName, 'public');
            $thumbnailUrl = "/storage/videos/thumbnails/{$thumbName}";
        }

        // Nếu chưa có thumbnail_url mà trong storage đã có thumbnail của video_id này, tận dụng luôn
        if (empty($thumbnailUrl) && ! empty($videoId)) {
            $candidates = [
                storage_path("app/public/videos/thumbnails/{$videoId}.jpg"),
                storage_path("app/public/videos/thumbnails/{$videoId}.webp"),
                storage_path("app/public/videos/thumbnails/{$videoId}.png"),
            ];
            foreach ($candidates as $cand) {
                if (file_exists($cand) && filesize($cand) > 1000) {
                    $thumbnailUrl = "/storage/videos/thumbnails/" . basename($cand);
                    break;
                }
            }
        }

        // Tạo video ở trạng thái "cho_duyet" (chờ Admin duyệt)
        $video = VideoShort::create([
            'quan_id' => $quan->id,
            'tieu_de' => Str::limit($tieuDe, 250, '...'),
            'video_id' => $videoId,
            'video_url' => $videoUrl,
            'thumbnail_url' => $thumbnailUrl,
            'nguoi_dang' => Str::limit($nguoiDang, 95, ''),
            'avatar_nguoi_dang' => $avatarNguoiDang,
            'luot_xem' => $luotXem,
            'luot_thich' => $luotThich,
            'trang_thai' => 'cho_duyet', // Chờ admin phê duyệt trước khi khách thấy
        ]);

        // Ghi nhận yêu cầu gửi duyệt video tới Admin
        try {
            \App\Models\AdminAuditLog::create([
                'actor_id' => auth()->id(),
                'action' => 'video.submitted',
                'target_type' => 'App\Models\VideoShort',
                'target_id' => (string) $video->id,
                'metadata' => [
                    'tieu_de' => $video->tieu_de,
                    'ten_quan' => $quan->ten_quan,
                    'video_id' => $video->video_id,
                ],
                'ip_address' => $request->ip(),
            ]);
        } catch (\Throwable $e) {}

        $successMsg = 'Đã gửi video review thành công! Video đang ở trạng thái 🟡 "Chờ duyệt" và sẽ hiển thị công khai cho khách hàng ngay sau khi Ban quản trị (Admin) phê duyệt.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'data' => $video,
            ]);
        }

        return back()->with('success', $successMsg);
    }

    /**
     * Xóa video của quán
     */
    public function destroy(Request $request, string $slug, string $id)
    {
        $quan = $this->getQuan($slug);

        $video = VideoShort::where('id', $id)
            ->where('quan_id', $quan->id)
            ->firstOrFail();

        // Xóa file local nếu có (chỉ xóa khi không còn video nào khác dùng chung video_id này)
        if (! empty($video->video_id)) {
            $countSame = VideoShort::where('video_id', $video->video_id)->where('id', '!=', $video->id)->count();
            if ($countSame === 0) {
                $localVideo = storage_path("app/public/videos/shorts/{$video->video_id}.mp4");
                if (file_exists($localVideo)) {
                    @unlink($localVideo);
                }
                $localThumb = storage_path("app/public/videos/thumbnails/{$video->video_id}.jpg");
                if (file_exists($localThumb)) {
                    @unlink($localThumb);
                }
            }
        }

        $video->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa video khỏi quán thành công.',
            ]);
        }

        return back()->with('success', 'Đã xóa video thành công.');
    }
}
