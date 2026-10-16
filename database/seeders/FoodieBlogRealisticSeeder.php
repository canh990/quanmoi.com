<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\User;
use Illuminate\Support\Str;

class FoodieBlogRealisticSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (!$user) return;

        // Create categories
        $catCafe = BlogCategory::firstOrCreate(
            ['slug' => 'ca-phe-quan-dep'],
            ['name' => 'Cà phê & Quán đẹp', 'description' => 'Khám phá quán cà phê sống ảo, yên tĩnh học bài, làm việc', 'status' => true]
        );
        $catBreakfast = BlogCategory::firstOrCreate(
            ['slug' => 'goi-y-an-sang'],
            ['name' => 'Gợi ý ăn sáng', 'description' => 'Món ngon ăn sáng năng lượng, giá rẻ bình dân', 'status' => true]
        );
        $catBbq = BlogCategory::firstOrCreate(
            ['slug' => 'lau-nuong-tu-tap'],
            ['name' => 'Lẩu & Nướng tụ tập', 'description' => 'Các kèo buffet lẩu nướng cuối tuần cho nhóm bạn bè', 'status' => true]
        );
        $catRooftop = BlogCategory::firstOrCreate(
            ['slug' => 'rooftop-hen-ho'],
            ['name' => 'Rooftop & Hẹn hò', 'description' => 'Quán view đẹp, chill lãng mạn ngắm hoàng hôn và thành phố', 'status' => true]
        );

        // Tags
        $tagCafeQ1 = BlogTag::firstOrCreate(['slug' => 'caphe-q1'], ['name' => 'Cà phê Q1']);
        $tagYenTinh = BlogTag::firstOrCreate(['slug' => 'yen-tinh'], ['name' => 'Yên tĩnh']);
        $tagHocBai = BlogTag::firstOrCreate(['slug' => 'hoc-bai'], ['name' => 'Học bài']);
        $tagAnSang = BlogTag::firstOrCreate(['slug' => 'an-sang'], ['name' => 'Ăn sáng']);
        $tagDuoi50k = BlogTag::firstOrCreate(['slug' => 'duoi-50k'], ['name' => 'Dưới 50k']);
        $tagLauNuong = BlogTag::firstOrCreate(['slug' => 'lau-nuong'], ['name' => 'Lẩu nướng']);
        $tagCuoiTuan = BlogTag::firstOrCreate(['slug' => 'cuoi-tuan'], ['name' => 'Cuối tuần']);
        $tagHenHo = BlogTag::firstOrCreate(['slug' => 'hen-ho'], ['name' => 'Hẹn hò']);

        $blogsData = [
            [
                'category_id' => $catCafe->id,
                'title' => 'Top 5 quán cà phê yên tĩnh học bài và chạy deadline cực chill tại Quận 1',
                'slug' => 'top-5-quan-ca-phe-yen-tinh-hoc-bai-quan-1',
                'excerpt' => 'Nếu bạn đang tìm quán cà phê yên tĩnh học bài ở Q1 với bàn ghế cao êm ái, wifi mạnh và nhiều ổ cắm sạc thì đây là danh sách cứu cánh cho mùa thi và chạy deadline.',
                'content' => '<h2>1. Giới thiệu không gian học tập lý tưởng tại Quận 1</h2>
<p>Khu vực Quận 1 luôn sôi động, nhưng ẩn mình giữa những con phố rợp bóng mát là các quán cà phê yên tĩnh học bài và làm việc cực kỳ chất lượng. Quán có địa chỉ tại 18 Alexandre De Rhodes, Phường Bến Nghé, Quận 1 và chi nhánh tại 42 Nguyễn Huệ, Quận 1.</p>
<h2>2. Thực đơn và những món nên thử</h2>
<p>Quán phục vụ đa dạng các dòng cà phê thủ công. Các <strong>món nên thử</strong> gồm có: <strong>Cold Brew cam sả</strong> thanh mát, <strong>Bạc xỉu cốt dừa</strong> béo ngậy và bánh <strong>Croissant phô mai trứng muối</strong> thơm lừng vừa ra lò.</p>
<h2>3. Mức giá và chi phí</h2>
<p>Mức giá đồ uống dao động từ <strong>45.000đ - 65.000đ</strong>, hoàn toàn tương xứng với chất lượng không gian trung tâm Quận 1.</p>
<h2>4. Đánh giá ưu điểm và nhược điểm</h2>
<p><strong>Ưu điểm:</strong> Quán mở nhạc jazz không lời nhẹ nhàng, bàn làm việc dài rộng, điều hòa mát rượi, mỗi bàn đều có từ 2-4 ổ cắm điện 3 chấu cực kỳ tiện lợi.</p>
<p><strong>Nhược điểm:</strong> Do quán khá hot nên vào buổi chiều tầm 14h - 17h cuối tuần thường kín bàn nhanh, bạn nên đến sớm để chọn chỗ ngồi gần cửa sổ view công viên.</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&w=800&q=80',
                'view_count' => 842,
                'comment_count' => 3,
                'tags' => [$tagCafeQ1->id, $tagYenTinh->id, $tagHocBai->id],
            ],
            [
                'category_id' => $catBreakfast->id,
                'title' => 'Tổng hợp 7 món ăn sáng ngon nức tiếng tại TP.HCM giá dưới 50k no căng bụng',
                'slug' => 'tong-hop-mon-an-sang-ngon-duoi-50k-sai-gon',
                'excerpt' => 'Điểm danh các món ăn sáng ngon dưới 50k tại Sài Gòn dành cho học sinh, sinh viên và dân công sở: Bánh mì chảo, Cơm tấm sườn bì chả, Phở bò tái lăn.',
                'content' => '<h2>1. Món ăn sáng ngon dưới 50k tại trung tâm thành phố</h2>
<p>Bữa sáng là bữa ăn quan trọng nhất trong ngày. Tại TP.HCM, bạn chỉ cần cầm từ 25.000đ đến 50.000đ là đã có thể thưởng thức những món ăn sáng ngon, nóng hổi và chất lượng đỉnh cao.</p>
<h2>2. Địa chỉ các quán gợi ý</h2>
<p>Địa chỉ quán gợi ý: Bánh mì chảo tại hẻm 213 Nguyễn Thiện Thuật, Quận 3; Cơm tấm sườn nướng tại 128 Nguyễn Đình Chiểu, Quận 3; Phở bò tái lăn tại 45 Đinh Tiên Hoàng, Quận 1.</p>
<h2>3. Món nên thử</h2>
<p>Các món nhất định phải gọi: <strong>Bánh mì chảo xíu mại trứng ốp la</strong>, <strong>Cơm tấm sườn bì chả mỡ hành</strong>, và <strong>Phở bò tái lăn nước dùng trong</strong> đậm đà.</p>
<h2>4. Mức giá bình dân</h2>
<p>Mức giá từ <strong>30.000đ - 48.000đ / phần</strong>, cực kỳ hợp túi tiền cho sinh viên và người đi làm.</p>
<h2>5. Ưu điểm và điểm trừ</h2>
<p><strong>Ưu điểm:</strong> Quán lên món siêu nhanh, đồ ăn nóng sốt, bánh mì giòn rụm, trà đá miễn phí.</p>
<p><strong>Điểm trừ:</strong> Giờ cao điểm từ 7h - 8h sáng khách mua mang về rất đông, chỗ để xe hơi chật hẹp.</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?auto=format&fit=crop&w=800&q=80',
                'view_count' => 1250,
                'comment_count' => 4,
                'tags' => [$tagAnSang->id, $tagDuoi50k->id],
            ],
            [
                'category_id' => $catBbq->id,
                'title' => 'Kèo lẩu nướng cho nhóm bạn cuối tuần: Review buffet nướng ngói và lẩu bò niêu đất',
                'slug' => 'keo-lau-nuong-cho-nhom-ban-cuoi-tuan',
                'excerpt' => 'Tìm quán lẩu nướng cho nhóm bạn cuối tuần vừa ngon, giá cả hợp lý lại có không gian rộng rãi thoáng mát tha hồ nhậu tới bến.',
                'content' => '<h2>1. Tụ tập lẩu nướng xả stress cuối tuần</h2>
<p>Cuối tuần là dịp lý tưởng để hẹn hò nhóm bạn thân ngồi quanh bếp than hồng xèo xèo thịt nướng và nồi lẩu bốc khói nghi ngút. Địa chỉ quán nằm tại 152 Ung Văn Khiêm, Phường 25, Quận Bình Thạnh.</p>
<h2>2. Món nên thử</h2>
<p>Thực đơn tại đây nổi bật với: <strong>Bò nướng tảng sốt trứng muối</strong>, <strong>Nầm heo nướng sa tế</strong>, <strong>Dẻ sườn bò Mỹ</strong> và <strong>Lẩu riêu cua bắp bò sườn sụn</strong> chua thanh kích thích vị giác.</p>
<h2>3. Mức giá phân khúc nhóm</h2>
<p>Mức giá trung bình chỉ từ <strong>149.000đ - 189.000đ / người</strong>, no nê phủ phê.</p>
<h2>4. Ưu và nhược điểm</h2>
<p><strong>Ưu điểm:</strong> Bàn dài rộng rãi đón được đoàn từ 10 - 20 người, không gian mở ngoài trời gió mát, vỉ nướng được thay liên tục.</p>
<p><strong>Nhược điểm:</strong> Mùi khói nướng có thể ám vào quần áo, các tối thứ 6 và thứ 7 nên đặt bàn trước ít nhất 2 tiếng.</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=800&q=80',
                'view_count' => 980,
                'comment_count' => 2,
                'tags' => [$tagLauNuong->id, $tagCuoiTuan->id],
            ],
            [
                'category_id' => $catRooftop->id,
                'title' => 'Quán rooftop view đẹp ngắm hoàng hôn Sài Gòn hẹn hò lãng mạn',
                'slug' => 'quan-rooftop-view-dep-ngam-hoang-hon-sai-gon',
                'excerpt' => 'Trải nghiệm không gian quán rooftop ngắm trọn hoàng hôn và tòa nhà Landmark 81 lung linh về đêm, điểm hẹn hò lý tưởng cho các cặp đôi.',
                'content' => '<h2>1. Điểm hẹn hò lãng mạn trên tầng cao</h2>
<p>Nếu bạn muốn tìm một nơi thư giãn ngắm hoàng hôn buông xuống thành phố, quán rooftop tọa lạc tại Tầng 8, 26 Võ Oanh, Phường 25, Quận Bình Thạnh là lựa chọn số 1.</p>
<h2>2. Đồ uống và món ăn đặc sắc</h2>
<p>Các món nên thử: <strong>Cocktail Saigon Sunset</strong>, <strong>Mocktail dâu tây việt quất</strong>, <strong>Bò Wagyu xiên nướng sốt tiêu đen</strong> và đĩa <strong>Khoai tây chiên phủ dầu nấm Truffle</strong>.</p>
<h2>3. Mức giá</h2>
<p>Mức giá đồ uống từ <strong>75.000đ - 150.000đ</strong>, rất tương xứng với trải nghiệm view 360 độ ngắm trọn thành phố.</p>
<h2>4. Đánh giá</h2>
<p><strong>Ưu điểm:</strong> View triệu đô ngắm Landmark 81 cực chill, có ban nhạc acoustic vào tối thứ 7, nhân viên chu đáo.</p>
<p><strong>Nhược điểm:</strong> Cần đặt trước vị trí bàn cạnh lan can kính để có góc chụp ảnh đẹp nhất.</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=800&q=80',
                'view_count' => 1120,
                'comment_count' => 5,
                'tags' => [$tagHenHo->id, $tagCafeQ1->id],
            ]
        ];

        foreach ($blogsData as $item) {
            $tags = $item['tags'];
            unset($item['tags']);

            $blog = Blog::updateOrCreate(
                ['slug' => $item['slug']],
                array_merge($item, [
                    'user_id' => $user->id,
                    'status' => 'published',
                    'allow_comments' => true,
                    'published_at' => now()->subDays(rand(1, 5)),
                    'reading_time' => 4,
                ])
            );

            $blog->tags()->sync($tags);
        }
    }
}
