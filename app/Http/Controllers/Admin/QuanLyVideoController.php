<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quan;
use App\Models\VideoShort;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuanLyVideoController extends Controller
{
    /**
     * Kiểm tra quyền quản trị video
     */
    protected function authorizeAccess(): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(401, 'Vui lòng đăng nhập.');
        }

        if (! $user->isAdmin() && ! $user->hasPermissionTo('quan_ly_video_tiktok')) {
            abort(403, 'Bạn không có quyền quản lý video TikTok review.');
        }
    }

    /**
     * Danh sách video shorts / TikTok review
     */
    public function index(Request $request)
    {
        $this->authorizeAccess();

        $query = VideoShort::with('quan:id,ten_quan,slug,dia_chi_chi_tiet,ten_quan_huyen,ten_tinh_thanh');

        // Tìm kiếm theo từ khóa
        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('tieu_de', 'like', "%{$keyword}%")
                    ->orWhere('video_id', 'like', "%{$keyword}%")
                    ->orWhere('nguoi_dang', 'like', "%{$keyword}%")
                    ->orWhereHas('quan', fn ($r) => $r->where('ten_quan', 'like', "%{$keyword}%"));
            });
        }

        // Lọc theo trạng thái
        if ($request->filled('trang_thai') && $request->trang_thai !== 'all') {
            $query->where('trang_thai', $request->trang_thai);
        }

        // Lọc theo quán ăn
        if ($request->filled('quan_id')) {
            $query->where('quan_id', $request->quan_id);
        }

        // Sắp xếp
        switch ($request->input('sort', 'moi_nhat')) {
            case 'cu_nhat':
                $query->orderBy('created_at', 'asc');
                break;
            case 'nhieu_view':
                $query->orderBy('luot_xem', 'desc');
                break;
            case 'nhieu_like':
                $query->orderBy('luot_thich', 'desc');
                break;
            default: // moi_nhat
                $query->orderBy('created_at', 'desc');
                break;
        }

        $videos = $query->paginate(12)->withQueryString();

        // Thống kê nhanh
        $totalVideos = VideoShort::count();
        $activeVideos = VideoShort::where('trang_thai', 'da_duyet')->count();
        $pendingVideos = VideoShort::where('trang_thai', 'cho_duyet')->count();
        $hiddenVideos = VideoShort::where('trang_thai', 'an')->count();
        $totalViews = VideoShort::sum('luot_xem');

        // Danh sách quán ăn đã duyệt để chọn liên kết
        $danhSachQuan = Quan::select('id', 'ten_quan', 'dia_chi_chi_tiet', 'ten_quan_huyen')
            ->where('trang_thai', 'da_duyet')
            ->orderBy('ten_quan')
            ->get();

        return view('admin.video-shorts.index', compact(
            'videos',
            'totalVideos',
            'activeVideos',
            'pendingVideos',
            'hiddenVideos',
            'totalViews',
            'danhSachQuan'
        ));
    }

    /**
     * AJAX Lấy thông tin video TikTok (tự động điền tiêu đề, tác giả, thumbnail, và kiểm tra stream sạch)
     */
    public function ajaxFetchTikTokInfo(Request $request)
    {
        $this->authorizeAccess();

        $request->validate([
            'url' => 'required|string',
        ]);

        $input = trim($request->url);
        $videoId = VideoShort::extractTikTokVideoId($input);

        if (! $videoId) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy ID video từ đường dẫn đã nhập.',
            ], 422);
        }

        $data = VideoShort::fetchTikTokData($input);

        if (! $data) {
            return response()->json([
                'success' => true,
                'message' => 'Đã nhận diện Video ID: ' . $videoId . ' (Chế độ nhúng iframe)',
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
            'message' => 'Đã bóc tách dữ liệu video thành công!',
            'data' => array_merge($data, [
                'has_clean_stream' => ! empty($data['play_url']),
            ]),
        ]);
    }

    /**
     * Thêm mới video TikTok review (Chặn admin tự thêm - chỉ duyệt video do quán gửi)
     */
    public function store(Request $request)
    {
        $this->authorizeAccess();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Admin chỉ có quyền phê duyệt video do quán gửi lên, không được tự ý thêm video.',
            ], 403);
        }

        return redirect()->route('admin.video-shorts.index')
            ->with('error', 'Admin chỉ có quyền phê duyệt video do quán gửi lên.');
    }

    /**
     * Đồng bộ / Tải video MP4 sạch cho một video đã có
     */
    public function syncClean(string $id)
    {
        $this->authorizeAccess();

        $video = VideoShort::findOrFail($id);

        $tiktokData = VideoShort::fetchTikTokData($video->video_id);
        if (! $tiktokData || empty($tiktokData['play_url'])) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể kết nối đến TikTok hoặc video này là riêng tư/đã bị xóa.',
            ], 422);
        }

        $downloaded = VideoShort::downloadPlayVideo($tiktokData['play_url'], $video->video_id);
        if (! $downloaded) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải file video MP4. Vui lòng thử lại sau.',
            ], 500);
        }

        $video->video_url = $downloaded;

        // Cập nhật thêm thumbnail nếu chưa có
        if (empty($video->thumbnail_url) && ! empty($tiktokData['cover_url'])) {
            $thumb = VideoShort::downloadCoverImage($tiktokData['cover_url'], $video->video_id);
            $video->thumbnail_url = $thumb ?: substr(strtok($tiktokData['cover_url'], '?'), 0, 250);
        }

        // Cập nhật thêm avatar nếu chưa có
        if (empty($video->avatar_nguoi_dang) && ! empty($tiktokData['author_avatar'])) {
            $avatar = VideoShort::downloadAvatarImage($tiktokData['author_avatar'], $video->video_id);
            $video->avatar_nguoi_dang = $avatar ?: substr(strtok($tiktokData['author_avatar'], '?'), 0, 250);
        }

        if (! empty($tiktokData['views']) && $video->luot_xem == 0) {
            $video->luot_xem = $tiktokData['views'];
        }
        if (! empty($tiktokData['likes']) && $video->luot_thich == 0) {
            $video->luot_thich = $tiktokData['likes'];
        }

        $video->save();

        return response()->json([
            'success' => true,
            'message' => 'Đã tải và chuyển đổi thành video MP4 sạch thành công!',
            'clean_url' => $video->clean_video_url,
        ]);
    }

    /**
     * Cập nhật video TikTok review
     */
    public function update(Request $request, string $id)
    {
        $this->authorizeAccess();
        set_time_limit(180);

        $video = VideoShort::findOrFail($id);

        $validated = $request->validate([
            'tieu_de' => 'required|string|max:1000',
            'video_input' => 'nullable|string|max:1000',
            'video_file' => 'nullable|file|mimes:mp4,mov,webm|max:102400',
            'quan_id' => 'nullable|uuid|exists:quan,id',
            'nguoi_dang' => 'nullable|string|max:100',
            'thumbnail_url' => 'nullable|string|max:2000',
            'thumbnail_file' => 'nullable|image|max:10240',
            'avatar_nguoi_dang' => 'nullable|string|max:2000',
            'luot_xem' => 'nullable|integer|min:0',
            'luot_thich' => 'nullable|integer|min:0',
            'trang_thai' => 'required|in:da_duyet,cho_duyet,an',
            'tai_video_sach' => 'nullable|boolean',
        ]);

        // 1. Nếu tải file video mới từ máy
        if ($request->hasFile('video_file')) {
            $file = $request->file('video_file');
            $uniqueId = 'local_' . time() . '_' . substr(bin2hex(random_bytes(4)), 0, 8);
            $filename = "{$uniqueId}." . $file->getClientOriginalExtension();
            $file->storeAs('videos/shorts', $filename, 'public');

            $video->video_id = $uniqueId;
            $video->video_url = "/storage/videos/shorts/{$filename}";
        }
        // 2. Nếu có link TikTok mới hoặc cần đồng bộ file sạch
        else {
            $targetVideoId = $video->video_id;
            $localFile = storage_path("app/public/videos/shorts/{$targetVideoId}.mp4");
            $hasLocalFile = file_exists($localFile) && filesize($localFile) > 100000;

            if (! empty($validated['video_input'])) {
                $newId = VideoShort::extractTikTokVideoId($validated['video_input']);
                if ($newId) {
                    $targetVideoId = $newId;
                    $video->video_id = $newId;
                    if ($newId !== $video->getOriginal('video_id')) {
                        $hasLocalFile = false; // Đổi sang video khác thì tải mới
                    }
                }
            }

            // Tự động tải video sạch nếu chưa có file local
            if (! $hasLocalFile && ! empty($targetVideoId)) {
                $fetchUrl = ! empty($validated['video_input']) ? $validated['video_input'] : $video->watch_url;
                $tiktokData = VideoShort::fetchTikTokData($fetchUrl);
                if ($tiktokData) {
                    if (! empty($tiktokData['play_url'])) {
                        $downloaded = VideoShort::downloadPlayVideo($tiktokData['play_url'], $targetVideoId);
                        if ($downloaded) {
                            $video->video_url = $downloaded;
                        }
                    }
                    if (! empty($tiktokData['cover_url'])) {
                        $localThumb = VideoShort::downloadCoverImage($tiktokData['cover_url'], $targetVideoId);
                        if ($localThumb) {
                            $video->thumbnail_url = $localThumb;
                        }
                    }
                    if (! empty($tiktokData['author_avatar']) && empty($video->avatar_nguoi_dang)) {
                        $localAvatar = VideoShort::downloadAvatarImage($tiktokData['author_avatar'], $targetVideoId);
                        if ($localAvatar) {
                            $video->avatar_nguoi_dang = $localAvatar;
                        }
                    }
                    if (! empty($tiktokData['views'])) {
                        $video->luot_xem = (int) $tiktokData['views'];
                    }
                    if (! empty($tiktokData['likes'])) {
                        $video->luot_thich = (int) $tiktokData['likes'];
                    }
                }
            }
        }

        // Thumbnail file upload
        if ($request->hasFile('thumbnail_file')) {
            $tFile = $request->file('thumbnail_file');
            $tName = "thumb_{$video->video_id}." . $tFile->getClientOriginalExtension();
            $tFile->storeAs('videos/thumbnails', $tName, 'public');
            $video->thumbnail_url = "/storage/videos/thumbnails/{$tName}";
        } elseif (isset($validated['thumbnail_url'])) {
            $video->thumbnail_url = $validated['thumbnail_url'] ? substr($validated['thumbnail_url'], 0, 2000) : null;
        }

        // Nếu video vẫn chưa có thumbnail_url thì tìm trong storage
        if (empty($video->thumbnail_url) && ! empty($video->video_id)) {
            $candidates = [
                storage_path("app/public/videos/thumbnails/{$video->video_id}.jpg"),
                storage_path("app/public/videos/thumbnails/{$video->video_id}.webp"),
                storage_path("app/public/videos/thumbnails/{$video->video_id}.png"),
                storage_path("app/public/videos/thumbnails/thumb_{$video->video_id}.jpg"),
                storage_path("app/public/videos/thumbnails/thumb_{$video->video_id}.png"),
            ];
            foreach ($candidates as $cand) {
                if (file_exists($cand) && filesize($cand) > 1000) {
                    $video->thumbnail_url = "/storage/videos/thumbnails/" . basename($cand);
                    break;
                }
            }
        }

        $nguoiDang = trim($validated['nguoi_dang'] ?? $video->nguoi_dang);
        if ($nguoiDang && ! str_starts_with($nguoiDang, '@')) {
            $nguoiDang = '@' . $nguoiDang;
        }

        $video->tieu_de = mb_substr($validated['tieu_de'], 0, 1000);
        $video->quan_id = $validated['quan_id'] ?? null;
        $video->nguoi_dang = mb_substr($nguoiDang, 0, 100);
        if (isset($validated['avatar_nguoi_dang'])) {
            $video->avatar_nguoi_dang = $validated['avatar_nguoi_dang'] ? substr($validated['avatar_nguoi_dang'], 0, 2000) : null;
        }
        if (! empty($validated['luot_xem'])) {
            $video->luot_xem = (int) $validated['luot_xem'];
        }
        if (! empty($validated['luot_thich'])) {
            $video->luot_thich = (int) $validated['luot_thich'];
        }
        $video->trang_thai = $validated['trang_thai'];
        $video->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật video TikTok thành công!',
                'data' => $video,
            ]);
        }

        return redirect()->route('admin.video-shorts.index')->with('success', 'Cập nhật video TikTok thành công!');
    }

    /**
     * Bật / Tắt trạng thái hiển thị video
     */
    public function toggleStatus(string $id)
    {
        $this->authorizeAccess();

        $video = VideoShort::findOrFail($id);

        if ($video->trang_thai === 'da_duyet') {
            $video->trang_thai = 'an';
            $msg = 'Đã ẩn video khỏi trang chủ';
        } else {
            $video->trang_thai = 'da_duyet';
            $msg = 'Đã duyệt & hiển thị video ra trang chủ';
        }

        $video->save();

        return response()->json([
            'success' => true,
            'trang_thai' => $video->trang_thai,
            'message' => $msg,
        ]);
    }

    /**
     * Xóa video TikTok
     */
    public function destroy(string $id)
    {
        $this->authorizeAccess();

        $video = VideoShort::findOrFail($id);
        
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

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa video thành công.',
        ]);
    }

    /**
     * Phê duyệt video review do quán gửi lên
     */
    public function approve(Request $request, string $id)
    {
        $this->authorizeAccess();

        $video = VideoShort::with(['quan.chuQuan'])->findOrFail($id);
        $video->trang_thai = 'da_duyet';
        $video->save();

        // Gửi thông báo đến chủ quán
        try {
            $owner = $video->quan?->chuQuan ?? \App\Models\NguoiDung::find($video->nguoi_dung_id ?? null);
            if ($owner) {
                $owner->notify(new \App\Notifications\VideoStatusNotification([
                    'id' => $video->id,
                    'tieu_de' => $video->tieu_de,
                    'quan_id' => $video->quan_id,
                    'ten_quan' => $video->quan?->ten_quan ?? 'Quán',
                    'quan_slug' => $video->quan?->slug ?? '',
                ], 'video_approved'));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Lỗi gửi thông báo video approve: ' . $e->getMessage());
        }

        // Ghi nhận Audit Log
        try {
            \App\Models\AdminAuditLog::create([
                'actor_id' => auth()->id(),
                'action' => 'video.approved',
                'target_type' => 'App\Models\VideoShort',
                'target_id' => (string) $video->id,
                'metadata' => [
                    'tieu_de' => $video->tieu_de,
                    'ten_quan' => $video->quan?->ten_quan ?? '',
                    'video_id' => $video->video_id,
                ],
                'ip_address' => $request->ip(),
            ]);
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'trang_thai' => 'da_duyet',
            'message' => 'Đã phê duyệt video review thành công! Video đã hiển thị công khai.',
        ]);
    }

    /**
     * Từ chối video review - Gỡ video khỏi quán và thông báo cho chủ quán kèm lý do
     */
    public function reject(Request $request, string $id)
    {
        $this->authorizeAccess();

        $reason = trim($request->input('reason', 'Nội dung video review chưa đạt tiêu chuẩn cộng đồng hoặc không phù hợp.'));
        $video = VideoShort::with(['quan.chuQuan'])->findOrFail($id);

        $videoData = [
            'id' => $video->id,
            'tieu_de' => $video->tieu_de,
            'quan_id' => $video->quan_id,
            'ten_quan' => $video->quan?->ten_quan ?? 'Quán',
            'quan_slug' => $video->quan?->slug ?? '',
        ];

        // Gửi thông báo cho chủ quán trước khi xóa
        try {
            $owner = $video->quan?->chuQuan ?? \App\Models\NguoiDung::find($video->nguoi_dung_id ?? null);
            if ($owner) {
                $owner->notify(new \App\Notifications\VideoStatusNotification($videoData, 'video_rejected', $reason));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Lỗi gửi thông báo video reject: ' . $e->getMessage());
        }

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

        // Xóa hoàn toàn video khỏi hệ thống để không còn hiển thị bên quán
        $video->delete();

        // Ghi nhận Audit Log
        try {
            \App\Models\AdminAuditLog::create([
                'actor_id' => auth()->id(),
                'action' => 'video.rejected',
                'target_type' => 'App\Models\VideoShort',
                'target_id' => (string) $videoData['id'],
                'metadata' => [
                    'tieu_de' => $videoData['tieu_de'],
                    'ten_quan' => $videoData['ten_quan'],
                    'reason' => $reason,
                ],
                'ip_address' => $request->ip(),
            ]);
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'message' => 'Đã từ chối và gỡ bỏ video review thành công. Đã gửi thông báo lý do đến chủ quán.',
        ]);
    }
}
