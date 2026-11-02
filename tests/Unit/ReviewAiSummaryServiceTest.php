<?php

namespace Tests\Unit;

use App\Models\Quan;
use App\Models\QuanDanhGia;
use App\Models\User;
use App\Services\ReviewAiSummaryService;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ReviewAiSummaryServiceTest extends TestCase
{
    public function test_smart_fallback_summary_calculation()
    {
        $service = new ReviewAiSummaryService();

        $quan = new Quan();
        $quan->ten_quan = 'Quán Nướng Chill 88';
        $quan->loai_hinh_kinh_doanh = 'Quán nướng';

        $r1 = new QuanDanhGia();
        $r1->so_sao = 5;
        $r1->binh_luan = 'Thịt nướng ướp rất vừa miệng, nước sốt đậm đà, nhân viên phục vụ nhiệt tình!';

        $r2 = new QuanDanhGia();
        $r2->so_sao = 4;
        $r2->binh_luan = 'Món ăn ngon, giá hợp lý, nhưng giờ cao điểm phải chờ món hơi lâu.';

        $r3 = new QuanDanhGia();
        $r3->so_sao = 2;
        $r3->binh_luan = 'Bãi gửi xe máy hơi chật và chật chội, lúc ra lấy xe chờ hơi lâu.';

        $reviews = new Collection([$r1, $r2, $r3]);

        $summary = $service->generateSmartFallbackSummary($quan, $reviews);

        $this->assertIsArray($summary);
        $this->assertArrayHasKey('tong_quan', $summary);
        $this->assertArrayHasKey('ti_le_hai_long', $summary);
        $this->assertArrayHasKey('diem_khen', $summary);
        $this->assertArrayHasKey('diem_can_khac_phuc', $summary);
        $this->assertArrayHasKey('top_mon_an', $summary);
        $this->assertArrayHasKey('khuyen_nghi_chuyen_gia', $summary);

        $this->assertNotEmpty($summary['diem_khen']);
        $this->assertNotEmpty($summary['diem_can_khac_phuc']);
        $this->assertNotEmpty($summary['khuyen_nghi_chuyen_gia']);
    }

    public function test_suggest_reply_fallback_generates_courteous_reply()
    {
        $service = new ReviewAiSummaryService();

        $quan = new Quan();
        $quan->ten_quan = 'Lẩu Bò Ba Toa';

        $user = new User();
        $user->ho_ten = 'Nguyễn Văn A';

        // 5 sao
        $r5 = new QuanDanhGia();
        $r5->so_sao = 5;
        $r5->binh_luan = 'Lẩu cực kỳ ngon!';
        $r5->setRelation('nguoiDung', $user);

        $reply5 = $service->suggestReply($quan, $r5);
        $this->assertTrue($reply5['success']);
        $this->assertStringContainsString('cảm ơn', mb_strtolower($reply5['reply']));

        // 1 sao
        $r1 = new QuanDanhGia();
        $r1->so_sao = 1;
        $r1->binh_luan = 'Quán phục vụ quá chậm.';
        $r1->setRelation('nguoiDung', $user);

        $reply1 = $service->suggestReply($quan, $r1);
        $this->assertTrue($reply1['success']);
        $this->assertStringContainsString('xin lỗi', mb_strtolower($reply1['reply']));
    }

    public function test_staff_complaint_is_not_treated_as_praise_and_has_rich_recommendations()
    {
        $service = new ReviewAiSummaryService();

        $quan = new Quan();
        $quan->ten_quan = 'Quán Cơm Tấm Sài Gòn';

        $r1 = new QuanDanhGia();
        $r1->so_sao = 2;
        $r1->binh_luan = 'Cơm sườn ngon nhưng nhân viên khó chịu, gọi tính tiền thì mặt nặng mày nhẹ.';

        $r2 = new QuanDanhGia();
        $r2->so_sao = 3;
        $r2->binh_luan = 'Đồ ăn ổn nhưng nhân viên thái độ chưa tốt.';

        $reviews = new Collection([$r1, $r2]);

        $summary = $service->generateSmartFallbackSummary($quan, $reviews);

        // 1. Phải có trong điểm cần khắc phục
        $allChe = implode(' ', $summary['diem_can_khac_phuc']);
        $this->assertStringContainsString('nhân viên', mb_strtolower($allChe));

        // 2. Tuyệt đối KHÔNG được khen nhân viên niềm nở
        $allKhen = implode(' ', $summary['diem_khen']);
        $this->assertStringNotContainsString('niềm nở', mb_strtolower($allKhen));
        $this->assertStringNotContainsString('chu đáo', mb_strtolower($allKhen));

        // 3. Đề xuất hành động phải có từ 3 đến 4 đề xuất thực tế, có đề xuất đào tạo/chấn chỉnh nhân viên
        $this->assertGreaterThanOrEqual(3, count($summary['khuyen_nghi_chuyen_gia']));
        $allKhuyenNghi = implode(' ', $summary['khuyen_nghi_chuyen_gia']);
        $this->assertStringContainsString('nhân viên', mb_strtolower($allKhuyenNghi));

        // 4. Tổng quan phải cảnh báo vấn đề nhân viên
        $this->assertStringContainsString('nhân viên', mb_strtolower($summary['tong_quan']));
    }

    public function test_suggest_reply_returns_multiple_diverse_suggestions()
    {
        $service = new ReviewAiSummaryService();

        $quan = new Quan();
        $quan->ten_quan = 'Quán Nướng Bò Tơ';

        $user = new User();
        $user->ho_ten = 'Lê Văn C';

        $danhGia = new QuanDanhGia();
        $danhGia->setRelation('nguoiDung', $user);
        $danhGia->so_sao = 5;
        $danhGia->binh_luan = 'Bò nướng tảng rất mềm và ngọt, sốt phô mai trứng muối ngon xuất sắc!';

        $result = $service->suggestReply($quan, $danhGia);

        $this->assertTrue($result['success']);
        $this->assertNotEmpty($result['reply']);
        $this->assertIsArray($result['suggestions']);
        $this->assertGreaterThanOrEqual(6, count($result['suggestions']));

        $tones = array_column($result['suggestions'], 'tone');
        $this->assertContains('lich_su', $tones);
        $this->assertContains('than_thien', $tones);
        $this->assertContains('tri_an', $tones);
        $this->assertContains('ngan_gon', $tones);
        $this->assertContains('chuyen_sau', $tones);
        $this->assertContains('vui_ve', $tones);
    }
}
