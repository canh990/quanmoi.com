<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\BlogCommentLike;
use App\Models\BlogReaction;
use App\Models\User;
use App\Models\VaiTro;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BlogReactionTestSeeder extends Seeder
{
    public function run(): void
    {
        $roleUser = VaiTro::where('ten', 'nguoi_dung')->first();
        $roleId = $roleUser ? $roleUser->id : null;

        // 1. Tạo danh sách 30 tài khoản người dùng ẩm thực thực tế nếu chưa có
        $sampleUsers = [
            ['ho_ten' => 'Linh Chi Foodie', 'email' => 'linhchi.foodie@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Hoàng Nam FoodReview', 'email' => 'nam.hoang@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Mai Phương Bếp Nhà', 'email' => 'maiphuong.cook@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Lê Tuấn Khám Phá', 'email' => 'tuan.khampha@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Thảo My La Cà', 'email' => 'thaomy.laca@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Đức Trọng Đi Ăn', 'email' => 'trong.dian@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Khánh Linh Sài Gòn', 'email' => 'khanhlinh.sg@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Bích Ngọc Quán Quen', 'email' => 'bichngoc.qq@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Anh Quân Vlogger', 'email' => 'anhquan.vlog@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Thanh Trúc Mê Món Cay', 'email' => 'thanhtruc.cay@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Minh Triết Ăn Uống', 'email' => 'triet.anuong@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Hương Giang Tín Đồ Trà', 'email' => 'huonggiang.tra@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Quốc Bảo Săn Quán', 'email' => 'quocbao.sanquan@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Diệu Huyền Đậm Đà', 'email' => 'dieuhuyen.damda@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Tấn Phát Ăn Vặt', 'email' => 'tanphat.anvat@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1513956589380-bad6acb9b9d4?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Thu Hà Hà Nội', 'email' => 'thuha.hanoi@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1488426862026-3ee34a7d66df?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Văn Hùng Hải Phòng', 'email' => 'vanhung.hp@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Ngọc Diệp Thích Ngọt', 'email' => 'ngocdiep.ngot@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1502685104226-ee32379fefbe?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Hải Đăng Bữa Tối', 'email' => 'haidang.buatoi@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1463453091185-61582044d556?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Phương Thảo Đam Mê', 'email' => 'phuongthao.dm@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1548142813-c348350df52b?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Bảo Trâm Foodaholic', 'email' => 'baotram.food@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Việt Anh Phố Cổ', 'email' => 'vietanh.phoco@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Thùy Dung Healthy', 'email' => 'thuydung.healthy@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Quang Huy Món Ngon', 'email' => 'quanghuy.food@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Hà My Cuối Tuần', 'email' => 'hamy.weekend@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Gia Bảo Ăn Đêm', 'email' => 'giabao.andem@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Thục Trinh Foodie', 'email' => 'thuctrinh.eat@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Minh Khang Reviewer', 'email' => 'minhkhang.rev@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Hồng Hạnh Món Bắc', 'email' => 'honghanh.monbac@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=150&q=80'],
            ['ho_ten' => 'Duy Mạnh Quán Xưa', 'email' => 'duymanh.quanxua@gmail.com', 'avatar' => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?auto=format&fit=crop&w=150&q=80'],
        ];

        $users = [];
        foreach ($sampleUsers as $item) {
            $user = User::firstOrCreate(
                ['email' => $item['email']],
                [
                    'id' => (string) Str::uuid(),
                    'ho_ten' => $item['ho_ten'],
                    'mat_khau' => Hash::make('123456'),
                    'anh_dai_dien' => $item['avatar'],
                    'vai_tro_id' => $roleId,
                    'da_xac_thuc' => true,
                    'trang_thai' => 'hoat_dong',
                ]
            );
            $users[] = $user;
        }

        // Lấy thêm các user khác trong DB (loại trừ tài khoản của tác giả)
        $authorUserIds = User::where('email', 'like', '%nhi%')->pluck('id')->toArray();
        $dbUsers = User::whereNotIn('id', $authorUserIds)->get();
        if ($dbUsers->count() > 0) {
            $users = $dbUsers->all();
        }

        // 2. Danh sách bài viết của tôi (Nguyễn Thị Nhi) + các bài gần nhất
        $myBlogs = Blog::whereIn('user_id', $authorUserIds)->get();
        if ($myBlogs->isEmpty()) {
            $myBlogs = Blog::latest('id')->take(5)->get();
        } else {
            // Thêm cả các bài mới nhất để chắc chắn phủ sóng
            $latest = Blog::latest('id')->take(5)->get();
            $myBlogs = $myBlogs->merge($latest)->unique('id');
        }

        // Bể cảm xúc bài viết phong phú (26 cảm xúc -> nhiều người tương tác, cuộn mỏi tay!)
        $blogReactionPool = [
            'love', 'love', 'love', 'love', 'love', 'love', 'love', 'love', // 8 love
            'drool', 'drool', 'drool', 'drool', 'drool', 'drool', 'drool', // 7 drool
            'fire', 'fire', 'fire', 'fire', 'fire',                        // 5 fire
            'like', 'like', 'like', 'like',                                // 4 like
            'cry', 'cry',                                                  // 2 cry
            'angry',                                                       // 1 angry
        ];

        // Mẫu bình luận ẩm thực chân thực
        $sampleCommentTexts = [
            'Món này nhìn cuốn hút quá! Mình vừa lưu địa chỉ lại rồi, cuối tuần dẫn gia đình ghé ăn thử liền. Cảm ơn bài viết rất chi tiết!',
            'Quán này nằm ngay gần cơ quan mình luôn, buổi trưa hay ra đây ăn. Đồ ăn ra nhanh, nóng hổi, phục vụ cũng rất niềm nở nha!',
            'Nước chấm và gia vị ở đây pha chế chuẩn vị thật sự, ăn một lần là nhớ mãi. Giá cả lại rất vừa túi tiền sinh viên.',
            'Hình ảnh chụp đẹp và góc chụp rất có tâm! Đọc xong mà bụng đói cồn cào luôn á. Bạn có review thêm quán nào ở khu vực này nữa không?',
            'Mình chấm quán này 9.5/10 điểm! Nước dùng thanh ngọt tự nhiên không bị gắt bột ngọt, quẩy giòn tan ăn kèm siêu đã.',
        ];

        $sampleReplies = [
            'Đúng rồi bạn ơi, nhớ ghé sớm tầm trước 12h trưa nhé không là đông kín bàn luôn á!',
            'Chuẩn luôn nè, mình đi 3 lần rồi lần nào cũng thấy ưng ý như lần đầu!',
        ];

        foreach ($myBlogs as $blog) {
            // Xóa reactions cũ trên bài này để nạp mới đầy đặn
            BlogReaction::where('blog_id', $blog->id)->delete();

            $shuffled = collect($users)->shuffle();
            foreach ($blogReactionPool as $idx => $rxType) {
                if (!isset($shuffled[$idx])) break;
                $u = $shuffled[$idx];

                BlogReaction::create([
                    'blog_id' => $blog->id,
                    'user_id' => $u->id,
                    'reaction' => $rxType,
                    'created_at' => now()->subMinutes(rand(10, 2880)),
                    'updated_at' => now(),
                ]);
            }

            // Đảm bảo bài viết có 3-4 bình luận + phản hồi
            $existingComments = BlogComment::where('blog_id', $blog->id)->whereNull('parent_id')->get();
            if ($existingComments->count() < 3) {
                foreach ($sampleCommentTexts as $cIdx => $text) {
                    if ($cIdx >= 3) break;
                    $commentUser = $users[($cIdx * 3) % count($users)];
                    $newC = BlogComment::create([
                        'blog_id' => $blog->id,
                        'user_id' => $commentUser->id,
                        'content' => $text,
                        'created_at' => now()->subHours(rand(2, 48)),
                        'updated_at' => now()->subHours(rand(2, 48)),
                    ]);

                    // Thêm 1 phản hồi trả lời cho bình luận đầu
                    if ($cIdx === 0 && isset($sampleReplies[0])) {
                        $replyUser = $users[($cIdx * 3 + 1) % count($users)];
                        BlogComment::create([
                            'blog_id' => $blog->id,
                            'parent_id' => $newC->id,
                            'user_id' => $replyUser->id,
                            'content' => $sampleReplies[0],
                            'created_at' => now()->subHours(rand(1, 12)),
                            'updated_at' => now()->subHours(rand(1, 12)),
                        ]);
                    }
                }
            }

            // Nạp 15-18 cảm xúc cho TỪNG bình luận và phản hồi
            $allBlogComments = BlogComment::where('blog_id', $blog->id)->get();
            $commentReactionPool = [
                'drool', 'drool', 'drool', 'drool', 'drool', 'drool', // 6 drool
                'love', 'love', 'love', 'love',                       // 4 love
                'fire', 'fire', 'fire',                               // 3 fire
                'like', 'like', 'like',                               // 3 like
                'cry',                                                // 1 cry
            ]; // 17 lượt thả cảm xúc -> danh sách dài tha hồ cuộn!

            foreach ($allBlogComments as $comment) {
                BlogCommentLike::where('comment_id', $comment->id)->delete();
                $shuffledForComment = collect($users)->shuffle();

                foreach ($commentReactionPool as $cIdx => $cRxType) {
                    if (!isset($shuffledForComment[$cIdx])) break;
                    $cu = $shuffledForComment[$cIdx];

                    BlogCommentLike::create([
                        'comment_id' => $comment->id,
                        'user_id' => $cu->id,
                        'reaction' => $cRxType,
                        'created_at' => now()->subMinutes(rand(5, 1200)),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
