<?php

namespace Database\Seeders;

use App\Models\DanhMucMenu;
use App\Models\DanhMucQuan;
use App\Models\MonTrongMenu;
use App\Models\Quan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ShopeeFoodQuanSeeder extends Seeder
{
    /**
     * Seed danh sách các quán ăn nổi bật có liên kết ShopeeFood chính thức
     * kèm thực đơn món ăn có đường dẫn đặt hàng ShopeeFood chi tiết.
     */
    public function run(): void
    {
        // 1. Tìm hoặc lấy chủ quán mặc định
        $owner = User::where('email', 'chuquan@quanmoi.com')->first()
            ?? User::where('email', 'admin@quanmoi.com')->first()
            ?? User::first()
            ?? User::factory()->create([
                'ho_ten' => 'Chủ Quán ShopeeFood',
                'email' => 'chuquan.shopee@quanmoi.com',
                'so_dien_thoai' => '0912345678',
                'da_xac_thuc' => true,
            ]);

        // 2. Tra cứu danh mục quán sẵn có theo slug
        $categoriesBySlug = DanhMucQuan::pluck('id', 'slug')->all();

        // 3. Danh sách 15 quán ăn nổi tiếng có mặt trên ShopeeFood tại Hà Nội, TP.HCM & Đà Nẵng
        $venues = [
            // ==========================================
            // TP. HỒ CHÍ MINH
            // ==========================================
            [
                'ten_quan' => 'Cơm Tấm Phúc Lộc Thọ - Đinh Tiên Hoàng',
                'loai_hinh_kinh_doanh' => 'Nhà hàng',
                'category_slug' => 'quan-an-binh-dan',
                'dia_chi_chi_tiet' => '236 Đinh Tiên Hoàng',
                'tinh_thanh_id' => '79',
                'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
                'quan_huyen_id' => '760',
                'ten_quan_huyen' => 'Quận 1',
                'phuong_xa_id' => '26740',
                'ten_phuong_xa' => 'Phường Đa Kao',
                'vi_do' => 10.7905,
                'kinh_do' => 106.6975,
                'gia_nho_nhat' => 39000,
                'gia_lon_nhat' => 89000,
                'gio_mo_cua' => '06:00',
                'gio_dong_cua' => '22:00',
                'shopeefood_url' => 'https://shopeefood.vn/ho-chi-minh/com-tam-phuc-loc-tho-dinh-tien-hoang',
                'anh_bia' => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?auto=format&fit=crop&w=800&q=80',
                'mo_ta' => 'Thương hiệu cơm tấm truyền thống chuẩn vị Sài Gòn với sườn nướng than hoa thơm lừng mềm mọng, bì chả nhà làm đậm đà, nước mắm kẹo bí truyền.',
                'menus' => [
                    [
                        'ten_danh_muc' => 'Cơm Tấm Bán Chạy',
                        'dishes' => [
                            [
                                'ten_mon' => 'Cơm Tấm Sườn Cốt Lết Nướng',
                                'mo_ta' => 'Miếng sườn cốt lết dày dặn ướp gia vị đậm đà nướng than hồng thơm nức, ăn kèm mỡ hành tóp mỡ giòn rụm.',
                                'gia' => 46000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ho-chi-minh/com-tam-phuc-loc-tho-dinh-tien-hoang#suon-nuong',
                            ],
                            [
                                'ten_mon' => 'Cơm Tấm Sườn Bì Chả Đặc Biệt',
                                'mo_ta' => 'Suất đặc biệt gồm sườn nướng tảng, chả trứng hấp béo ngậy, bì thăn trộn thính thơm và trứng ốp la lòng đào.',
                                'gia' => 69000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ho-chi-minh/com-tam-phuc-loc-tho-dinh-tien-hoang#suon-bi-cha',
                            ],
                            [
                                'ten_mon' => 'Cơm Tấm Đùi Gà Nướng Mật Ong',
                                'mo_ta' => 'Góc đùi gà nướng sốt mật ong hoa cà phê da óng ả giòn dai, thịt bên trong mềm ngọt thấm vị.',
                                'gia' => 52000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ho-chi-minh/com-tam-phuc-loc-tho-dinh-tien-hoang#ga-nuong',
                            ],
                        ],
                    ],
                    [
                        'ten_danh_muc' => 'Canh & Đồ Uống',
                        'dishes' => [
                            [
                                'ten_mon' => 'Canh Khổ Qua Nhồi Thịt',
                                'mo_ta' => 'Khổ qua tươi nhồi thịt thăn nấm mèo thanh mát, giải nhiệt ngày hè cực tốt.',
                                'gia' => 22000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ho-chi-minh/com-tam-phuc-loc-tho-dinh-tien-hoang#canh-kho-qua',
                            ],
                            [
                                'ten_mon' => 'Trà Tắc Hạt Chia Mát Lạnh',
                                'mo_ta' => 'Trà lài ủ lạnh kết hợp nước cốt tắc tươi và hạt chia hữu cơ thanh mát giải khát.',
                                'gia' => 18000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ho-chi-minh/com-tam-phuc-loc-tho-dinh-tien-hoang#tra-tac',
                            ],
                        ],
                    ],
                ],
            ],

            [
                'ten_quan' => 'Trà Sữa Phúc Long - Ngô Đức Kế',
                'loai_hinh_kinh_doanh' => 'Cà phê & Trà',
                'category_slug' => 'ca-phe-tra',
                'dia_chi_chi_tiet' => '42 Ngô Đức Kế',
                'tinh_thanh_id' => '79',
                'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
                'quan_huyen_id' => '760',
                'ten_quan_huyen' => 'Quận 1',
                'phuong_xa_id' => '26734',
                'ten_phuong_xa' => 'Phường Bến Nghé',
                'vi_do' => 10.7735,
                'kinh_do' => 106.7042,
                'gia_nho_nhat' => 40000,
                'gia_lon_nhat' => 85000,
                'gio_mo_cua' => '07:00',
                'gio_dong_cua' => '22:30',
                'shopeefood_url' => 'https://shopeefood.vn/ho-chi-minh/phuc-long-coffee-tea-ngo-duc-ke',
                'anh_bia' => 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=800&q=80',
                'mo_ta' => 'Thương hiệu trà và cà phê đậm vị hàng đầu Việt Nam từ năm 1968, nổi tiếng với trà sữa truyền thống thơm nồng hương trà Bảo Lộc.',
                'menus' => [
                    [
                        'ten_danh_muc' => 'Trà & Trà Sữa Nổi Tiếng',
                        'dishes' => [
                            [
                                'ten_mon' => 'Trà Sữa Phúc Long Đậm Vị',
                                'mo_ta' => 'Hương vị trà đen ủ men truyền thống hòa quyện cùng sữa thơm béo ngậy, chuẩn gu người sành trà.',
                                'gia' => 55000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1544145945-f90425340c7e?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ho-chi-minh/phuc-long-coffee-tea-ngo-duc-ke#tra-sua-phuc-long',
                            ],
                            [
                                'ten_mon' => 'Trà Đào Cam Sả Đặc Biệt',
                                'mo_ta' => 'Vị trà thanh mát hòa cùng hương cam vàng chua ngọt dịu và tinh dầu sả tươi, kèm miếng đào giòn ngọt.',
                                'gia' => 58000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ho-chi-minh/phuc-long-coffee-tea-ngo-duc-ke#tra-dao-cam-sa',
                            ],
                            [
                                'ten_mon' => 'Trà Ô Long Mãng Cầu Tươi',
                                'mo_ta' => 'Nền trà Ô Long thanh tao kết hợp cùng thịt mãng cầu xiêm tươi chín mọng chua ngọt lạ miệng.',
                                'gia' => 62000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1497935586351-b67a49e012bf?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ho-chi-minh/phuc-long-coffee-tea-ngo-duc-ke#tra-o-long-mang-cau',
                            ],
                        ],
                    ],
                    [
                        'ten_danh_muc' => 'Cà Phê & Bánh Ngọt',
                        'dishes' => [
                            [
                                'ten_mon' => 'Cà Phê Sữa Đá Sài Gòn',
                                'mo_ta' => 'Cà phê rang mộc pha phin nhỏ giọt truyền thống, đượm vị đắng nồng quyện sữa đặc thơm béo.',
                                'gia' => 45000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ho-chi-minh/phuc-long-coffee-tea-ngo-duc-ke#ca-phe-sua-da',
                            ],
                            [
                                'ten_mon' => 'Bánh Croissant Bơ Tỏi Phô Mai',
                                'mo_ta' => 'Bánh sừng bò nướng giòn nhiều lớp, đẫm sốt bơ tỏi thơm lừng và nhân phô mai kéo sợi ngậy béo.',
                                'gia' => 38000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ho-chi-minh/phuc-long-coffee-tea-ngo-duc-ke#croissant-bo-toi',
                            ],
                        ],
                    ],
                ],
            ],

            [
                'ten_quan' => 'Bánh Mì Huỳnh Hoa - Lê Thị Riêng',
                'loai_hinh_kinh_doanh' => 'Ăn vặt',
                'category_slug' => 'do-an-vat',
                'dia_chi_chi_tiet' => '26 Lê Thị Riêng',
                'tinh_thanh_id' => '79',
                'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
                'quan_huyen_id' => '760',
                'ten_quan_huyen' => 'Quận 1',
                'phuong_xa_id' => '26737',
                'ten_phuong_xa' => 'Phường Bến Thành',
                'vi_do' => 10.7718,
                'kinh_do' => 106.6925,
                'gia_nho_nhat' => 65000,
                'gia_lon_nhat' => 95000,
                'gio_mo_cua' => '11:00',
                'gio_dong_cua' => '21:30',
                'shopeefood_url' => 'https://shopeefood.vn/ho-chi-minh/banh-mi-huynh-hoa-le-thi-rieng',
                'anh_bia' => 'https://images.unsplash.com/photo-1509722747041-616f39b57569?auto=format&fit=crop&w=800&q=80',
                'mo_ta' => 'Thương hiệu bánh mì đắt khách bậc nhất Sài Gòn với biệt danh "bánh mì ô môi", ngập tràn 8 loại thịt nguội thượng hạng và pate tươi béo mịn.',
                'menus' => [
                    [
                        'ten_danh_muc' => 'Bánh Mì & Món Kèm',
                        'dishes' => [
                            [
                                'ten_mon' => 'Bánh Mì Pate Đặc Biệt Siêu Thịt Nguội',
                                'mo_ta' => 'Ổ bánh mì vỏ giòn rụm nặng gần nửa ký ngập tràn giò thủ, chả lụa, xá xíu, dăm bông, bơ tươi và pate gia truyền.',
                                'gia' => 68000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1509722747041-616f39b57569?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ho-chi-minh/banh-mi-huynh-hoa-le-thi-rieng#banh-mi-dac-biet',
                            ],
                            [
                                'ten_mon' => 'Hộp Pate Tươi Truyền Thống Huỳnh Hoa (250g)',
                                'mo_ta' => 'Pate gan heo tươi làm mới mỗi ngày, béo ngậy thơm nức mùi tiêu sọ và hành phi, đóng hộp tiện bảo quản.',
                                'gia' => 75000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1529042410759-befb1204b468?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ho-chi-minh/banh-mi-huynh-hoa-le-thi-rieng#pate-tuoi-250g',
                            ],
                            [
                                'ten_mon' => 'Bánh Mì Bơ Tỏi Phô Mai Chà Bông',
                                'mo_ta' => 'Bánh mì nướng sốt bơ tỏi thơm lừng rắc ngập chà bông heo xé sợi dai ngọt cay nhẹ.',
                                'gia' => 45000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ho-chi-minh/banh-mi-huynh-hoa-le-thi-rieng#banh-mi-cha-bong',
                            ],
                        ],
                    ],
                ],
            ],

            [
                'ten_quan' => 'Haidilao Hotpot - Bitexco Financial Tower',
                'loai_hinh_kinh_doanh' => 'Lẩu & Nướng',
                'category_slug' => 'lau-nuong',
                'dia_chi_chi_tiet' => 'Tầng 2 Bitexco, 2 Hải Triều',
                'tinh_thanh_id' => '79',
                'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
                'quan_huyen_id' => '760',
                'ten_quan_huyen' => 'Quận 1',
                'phuong_xa_id' => '26734',
                'ten_phuong_xa' => 'Phường Bến Nghé',
                'vi_do' => 10.7715,
                'kinh_do' => 106.7045,
                'gia_nho_nhat' => 150000,
                'gia_lon_nhat' => 450000,
                'gio_mo_cua' => '09:00',
                'gio_dong_cua' => '23:30',
                'shopeefood_url' => 'https://shopeefood.vn/ho-chi-minh/haidilao-hotpot-bitexco',
                'anh_bia' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=800&q=80',
                'mo_ta' => 'Thương hiệu lẩu Trung Hoa trứ danh toàn cầu với dịch vụ chu đáo, nhận giao tận nhà set lẩu mini đầy đủ nồi lẩu điện mini và nguyên liệu hảo hạng.',
                'menus' => [
                    [
                        'ten_danh_muc' => 'Set Lẩu Giao Tận Nhà (Hi Delivery)',
                        'dishes' => [
                            [
                                'ten_mon' => 'Set Lẩu Mini Cà Chua Bò Mỹ',
                                'mo_ta' => 'Gói nước lẩu cà chua chua ngọt đậm đà, kèm 200g ba chỉ bò Mỹ thượng hạng, viên thả lẩu và rau nấm.',
                                'gia' => 280000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ho-chi-minh/haidilao-hotpot-bitexco#set-lau-ca-chua',
                            ],
                            [
                                'ten_mon' => 'Set Lẩu Nấm Dưỡng Sinh Kèm Tôm Phỉ Thúy',
                                'mo_ta' => 'Nước dùng nấm thanh ngọt bồi bổ sức khỏe, thịt thăn bò úc, tôm viên phỉ thúy tươi xay nhuyễn và mì tươi.',
                                'gia' => 320000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ho-chi-minh/haidilao-hotpot-bitexco#set-lau-nam',
                            ],
                            [
                                'ten_mon' => 'Tôm Viên Phỉ Thúy Tươi (Phần Giao Hàng)',
                                'mo_ta' => 'Thịt tôm sú tươi băm nhuyễn đóng túi bắt bông kem, nhúng lẩu giòn ngọt tự nhiên.',
                                'gia' => 95000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1615141982883-c7ad0e69fd62?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ho-chi-minh/haidilao-hotpot-bitexco#tom-phi-thuy',
                            ],
                        ],
                    ],
                ],
            ],

            [
                'ten_quan' => 'Chè Thái Ý Phương - Nguyễn Tri Phương',
                'loai_hinh_kinh_doanh' => 'Ăn vặt',
                'category_slug' => 'do-an-vat',
                'dia_chi_chi_tiet' => '380 Nguyễn Tri Phương',
                'tinh_thanh_id' => '79',
                'ten_tinh_thanh' => 'Thành phố Hồ Chí Minh',
                'quan_huyen_id' => '771',
                'ten_quan_huyen' => 'Quận 10',
                'phuong_xa_id' => '26857',
                'ten_phuong_xa' => 'Phường 4',
                'vi_do' => 10.7620,
                'kinh_do' => 106.6690,
                'gia_nho_nhat' => 25000,
                'gia_lon_nhat' => 60000,
                'gio_mo_cua' => '08:00',
                'gio_dong_cua' => '23:00',
                'shopeefood_url' => 'https://shopeefood.vn/ho-chi-minh/che-thai-y-phuong-nguyen-tri-phuong',
                'anh_bia' => 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?auto=format&fit=crop&w=800&q=80',
                'mo_ta' => 'Quán chè Thái nổi danh hơn 20 năm tại Sài Gòn, đặc trưng với múi sầu riêng tươi dày cơm, nước cốt dừa béo ngậy và các loại thạch nhiệt đới tươi mát.',
                'menus' => [
                    [
                        'ten_danh_muc' => 'Chè Thái & Tráng Miệng Đặc Sắc',
                        'dishes' => [
                            [
                                'ten_mon' => 'Chè Thái Sầu Riêng Đệ Nhất',
                                'mo_ta' => 'Ly chè đầy đặn với cơm sầu riêng tươi thơm phức, thạch dừa sợi, hạt lựu, mít ngọt và nước cốt dừa béo ngậy.',
                                'gia' => 40000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ho-chi-minh/che-thai-y-phuong-nguyen-tri-phuong#che-thai-sau-rieng',
                            ],
                            [
                                'ten_mon' => 'Chè Khúc Bạch Hạnh Nhân Thanh Mát',
                                'mo_ta' => 'Từng viên khúc bạch sữa tươi mềm dẻo, nước đường phèn hoa cúc thanh tao rắc ngập hạnh nhân lát rang giòn.',
                                'gia' => 38000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ho-chi-minh/che-thai-y-phuong-nguyen-tri-phuong#che-khuc-bach',
                            ],
                            [
                                'ten_mon' => 'Bánh Flan Caramen Cốt Dừa',
                                'mo_ta' => 'Flan trứng mềm mịn tan trong miệng quyện vị đắng nhẹ của sốt caramen và cốt dừa thơm ngát.',
                                'gia' => 25000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1587314168485-3236d6710814?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ho-chi-minh/che-thai-y-phuong-nguyen-tri-phuong#banh-flan',
                            ],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // HÀ NỘI
            // ==========================================
            [
                'ten_quan' => 'Phở Thìn Lò Đúc 13 Lò Đúc',
                'loai_hinh_kinh_doanh' => 'Nhà hàng',
                'category_slug' => 'nha-hang',
                'dia_chi_chi_tiet' => '13 Lò Đúc',
                'tinh_thanh_id' => '01',
                'ten_tinh_thanh' => 'Thành phố Hà Nội',
                'quan_huyen_id' => '004',
                'ten_quan_huyen' => 'Quận Hai Bà Trưng',
                'phuong_xa_id' => '00157',
                'ten_phuong_xa' => 'Phường Phạm Đình Hổ',
                'vi_do' => 21.0185,
                'kinh_do' => 105.8562,
                'gia_nho_nhat' => 65000,
                'gia_lon_nhat' => 95000,
                'gio_mo_cua' => '06:00',
                'gio_dong_cua' => '21:00',
                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/pho-thin-13-lo-duc',
                'anh_bia' => 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?auto=format&fit=crop&w=800&q=80',
                'mo_ta' => 'Huyền thoại phở bò tái lăn Hà Nội ra đời từ năm 1979. Thịt bò xào lăn lửa lớn ngập hành hoa xanh mướt và nước béo thơm lừng nức tiếng.',
                'menus' => [
                    [
                        'ten_danh_muc' => 'Phở Bò Truyền Thống',
                        'dishes' => [
                            [
                                'ten_mon' => 'Phở Tái Lăn Hà Nội Đặc Biệt',
                                'mo_ta' => 'Bò bắp thái mỏng xào lửa to cùng tỏi đập dập thơm lừng, ngập tràn hành hoa và nước ninh xương bò đậm đà.',
                                'gia' => 75000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/pho-thin-13-lo-duc#pho-tai-lan',
                            ],
                            [
                                'ten_mon' => 'Phở Bò Tái Nạm Gầu Giòn',
                                'mo_ta' => 'Tô phở thập cẩm gồm thịt bò tái mềm thơm, gầu bò thái mỏng giòn sần sật béo ngậy.',
                                'gia' => 85000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/pho-thin-13-lo-duc#pho-tai-nam-gau',
                            ],
                            [
                                'ten_mon' => 'Trứng Gà Chần Nước Béo & Quẩy Giòn',
                                'mo_ta' => 'Bộ đôi không thể thiếu: 2 quả trứng gà ta chần lòng đào béo ngậy kèm quẩy giòn vàng ruộm.',
                                'gia' => 20000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/pho-thin-13-lo-duc#trung-chan-quay',
                            ],
                        ],
                    ],
                ],
            ],

            [
                'ten_quan' => 'Bún Chả Hương Liên - Lê Văn Hưu',
                'loai_hinh_kinh_doanh' => 'Nhà hàng',
                'category_slug' => 'nha-hang',
                'dia_chi_chi_tiet' => '24 Lê Văn Hưu',
                'tinh_thanh_id' => '01',
                'ten_tinh_thanh' => 'Thành phố Hà Nội',
                'quan_huyen_id' => '002',
                'ten_quan_huyen' => 'Quận Hoàn Kiếm',
                'phuong_xa_id' => '00055',
                'ten_phuong_xa' => 'Phường Phan Chu Trinh',
                'vi_do' => 21.0188,
                'kinh_do' => 105.8532,
                'gia_nho_nhat' => 50000,
                'gia_lon_nhat' => 110000,
                'gio_mo_cua' => '08:00',
                'gio_dong_cua' => '20:30',
                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/bun-cha-huong-lien-le-van-huu',
                'anh_bia' => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?auto=format&fit=crop&w=800&q=80',
                'mo_ta' => 'Địa chỉ bún chả nổi tiếng thế giới từng vinh dự đón Tổng thống Mỹ Barack Obama và đầu bếp Anthony Bourdain ghé thăm thưởng thức năm 2016.',
                'menus' => [
                    [
                        'ten_danh_muc' => 'Bún Chả & Nem Giòn Rụm',
                        'dishes' => [
                            [
                                'ten_mon' => 'Suất Bún Chả Đặc Biệt Combo Obama',
                                'mo_ta' => 'Chả miếng nướng xém cạnh, chả băm thơm mềm bọc lá chuối nướng than, kèm nem hải sản và đĩa bún tươi.',
                                'gia' => 70000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/bun-cha-huong-lien-le-van-huu#combo-obama',
                            ],
                            [
                                'ten_mon' => 'Nem Cua Bể Hải Phòng Giòn Rụm (2 chiếc)',
                                'mo_ta' => 'Nem vuông vỏ rế giòn rụm ngập nhân thịt cua bể tươi, mộc nhĩ, thịt heo xay và miến rong.',
                                'gia' => 40000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1617093727343-374698b1b08d?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/bun-cha-huong-lien-le-van-huu#nem-cua-be',
                            ],
                            [
                                'ten_mon' => 'Nước Sấu Ngâm Đường Phèn Hà Nội',
                                'mo_ta' => 'Quả sấu tươi ngâm đường phèn gừng non chua ngọt giòn sần sật, giải ngấy hoàn hảo.',
                                'gia' => 15000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1600271886742-f049cd451bba?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/bun-cha-huong-lien-le-van-huu#nuoc-sau',
                            ],
                        ],
                    ],
                ],
            ],

            [
                'ten_quan' => 'Trà Sữa ToCoToCo - Hàng Bông',
                'loai_hinh_kinh_doanh' => 'Trà sữa & Nước ép',
                'category_slug' => 'tra-sua-nuoc-ep',
                'dia_chi_chi_tiet' => '54 Hàng Bông',
                'tinh_thanh_id' => '01',
                'ten_tinh_thanh' => 'Thành phố Hà Nội',
                'quan_huyen_id' => '002',
                'ten_quan_huyen' => 'Quận Hoàn Kiếm',
                'phuong_xa_id' => '00046',
                'ten_phuong_xa' => 'Phường Hàng Gai',
                'vi_do' => 21.0312,
                'kinh_do' => 105.8488,
                'gia_nho_nhat' => 32000,
                'gia_lon_nhat' => 68000,
                'gio_mo_cua' => '08:00',
                'gio_dong_cua' => '22:30',
                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/tra-sua-tocotoco-54-hang-bong',
                'anh_bia' => 'https://images.unsplash.com/photo-1558857563-b37cf5a4b7f7?auto=format&fit=crop&w=800&q=80',
                'mo_ta' => 'Thương hiệu trà sữa tiên phong dùng nguồn nông sản Việt Nam: búp trà Ô Long Mộc Châu, trân châu sắn tươi dẻo dai bùi thơm.',
                'menus' => [
                    [
                        'ten_danh_muc' => 'Trà Sữa Đậm Đà Topping',
                        'dishes' => [
                            [
                                'ten_mon' => 'Trà Sữa Ba Anh Em (Size L)',
                                'mo_ta' => 'Trà sữa truyền thống kết hợp bộ 3 topping đỉnh cao: trân châu sợi dai dẻo, pudding trứng mềm béo và thạch sương sáo.',
                                'gia' => 45000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1558857563-b37cf5a4b7f7?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/tra-sua-tocotoco-54-hang-bong#ba-anh-em',
                            ],
                            [
                                'ten_mon' => 'Trà Sữa Panda Đường Đen',
                                'mo_ta' => 'Trân châu sợi óng ánh nấu cùng đường đen mật mía tự nhiên, quyện lớp sữa béo ngậy thơm ngon.',
                                'gia' => 48000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1544145945-f90425340c7e?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/tra-sua-tocotoco-54-hang-bong#panda-duong-den',
                            ],
                            [
                                'ten_mon' => 'Ô Long Kem Phô Mai Macchiato',
                                'mo_ta' => 'Trà Ô Long thanh nhẹ phủ lớp màng kem cheese phô mai New Zealand sánh mịn hơi mặn béo ngậy.',
                                'gia' => 42000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/tra-sua-tocotoco-54-hang-bong#o-long-kem-cheese',
                            ],
                        ],
                    ],
                ],
            ],

            [
                'ten_quan' => 'Gà Rán Jollibee - Vincom Bà Triệu',
                'loai_hinh_kinh_doanh' => 'Ăn vặt',
                'category_slug' => 'do-an-vat',
                'dia_chi_chi_tiet' => 'Tầng 5 Vincom Center, 191 Bà Triệu',
                'tinh_thanh_id' => '01',
                'ten_tinh_thanh' => 'Thành phố Hà Nội',
                'quan_huyen_id' => '004',
                'ten_quan_huyen' => 'Quận Hai Bà Trưng',
                'phuong_xa_id' => '00160',
                'ten_phuong_xa' => 'Phường Lê Đại Hành',
                'vi_do' => 21.0118,
                'kinh_do' => 105.8495,
                'gia_nho_nhat' => 35000,
                'gia_lon_nhat' => 175000,
                'gio_mo_cua' => '09:00',
                'gio_dong_cua' => '22:00',
                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/jollibee-vincom-ba-trieu',
                'anh_bia' => 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=800&q=80',
                'mo_ta' => 'Chuỗi thức ăn nhanh được yêu thích hàng đầu với gà giòn vui vẻ giòn tan bên ngoài mọng nước bên trong, cùng sốt ớt cay ngọt đặc quyền.',
                'menus' => [
                    [
                        'ten_danh_muc' => 'Gà Giòn Vui Vẻ & Mì Ý',
                        'dishes' => [
                            [
                                'ten_mon' => 'Combo 2 Miếng Gà Giòn Cay Vui Vẻ',
                                'mo_ta' => '2 miếng gà góc đùi tẩm bột chiên vàng ươm giòn rụm rưới sốt cay nồng đặc trưng của Jollibee.',
                                'gia' => 76000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/jollibee-vincom-ba-trieu#ga-gion-2-mieng',
                            ],
                            [
                                'ten_mon' => 'Mì Ý Sốt Bò Bằm Jollibee Thượng Hạng',
                                'mo_ta' => 'Sợi mì Ý dai ngon ngập sốt cà chua thịt bò bằm ngọt dịu chuẩn khẩu vị gia đình, phủ xúc xích lát và phô mai.',
                                'gia' => 45000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/jollibee-vincom-ba-trieu#mi-y-bo-bam',
                            ],
                            [
                                'ten_mon' => 'Khoai Tây Lắc Phô Mai Béo Thơm',
                                'mo_ta' => 'Khoai tây cắt que chiên giòn lắc đều cùng bột phô mai cheddar thơm lừng béo ngậy.',
                                'gia' => 32000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/jollibee-vincom-ba-trieu#khoai-lac-pho-mai',
                            ],
                        ],
                    ],
                ],
            ],

            [
                'ten_quan' => 'Bún Đậu Mắm Tôm Cô Bông - Đặng Văn Ngữ',
                'loai_hinh_kinh_doanh' => 'Quán ăn bình dân',
                'category_slug' => 'quan-an-binh-dan',
                'dia_chi_chi_tiet' => '45 Đặng Văn Ngữ',
                'tinh_thanh_id' => '01',
                'ten_tinh_thanh' => 'Thành phố Hà Nội',
                'quan_huyen_id' => '006',
                'ten_quan_huyen' => 'Quận Đống Đa',
                'phuong_xa_id' => '00214',
                'ten_phuong_xa' => 'Phường Trung Tự',
                'vi_do' => 21.0102,
                'kinh_do' => 105.8325,
                'gia_nho_nhat' => 35000,
                'gia_lon_nhat' => 85000,
                'gio_mo_cua' => '09:30',
                'gio_dong_cua' => '21:30',
                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/bun-dau-mam-tom-co-bong-dang-van-ngu',
                'anh_bia' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=800&q=80',
                'mo_ta' => 'Quán bún đậu nổi danh đất Đống Đa với chả cốm làng Mễ Trì thơm dẻo, nem rán vỏ rế giòn rụm và chén mắm tôm Thanh Hóa đánh bông bọt.',
                'menus' => [
                    [
                        'ten_danh_muc' => 'Bún Đậu Mẹt Truyền Thống',
                        'dishes' => [
                            [
                                'ten_mon' => 'Mẹt Bún Đậu Thập Cẩm Đặc Biệt',
                                'mo_ta' => 'Mẹt đầy đặn gồm đậu mơ chiên phồng lướt ván, chả cốm nóng giòn, nem rán, dồi sụn nướng, thịt chân giò luộc và bún lá.',
                                'gia' => 60000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/bun-dau-mam-tom-co-bong-dang-van-ngu#bun-dau-thap-cam',
                            ],
                            [
                                'ten_mon' => 'Đĩa Dồi Sụn Nướng Than Hoa',
                                'mo_ta' => 'Dồi sụn non nhồi thơm nức lá mơ, sả băm nướng trên than hoa chín vàng thơm giòn sần sật.',
                                'gia' => 45000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/bun-dau-mam-tom-co-bong-dang-van-ngu#doi-sun-nuong',
                            ],
                            [
                                'ten_mon' => 'Nước Mơ Chùa Hương Chua Ngọt',
                                'mo_ta' => 'Mơ vàng ngâm đường phèn thanh khiết, thêm đá viên xua tan cái nóng hè oi ả.',
                                'gia' => 15000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/bun-dau-mam-tom-co-bong-dang-van-ngu#nuoc-mo',
                            ],
                        ],
                    ],
                ],
            ],

            [
                'ten_quan' => 'Pizza 4Ps - Phan Kế Bính',
                'loai_hinh_kinh_doanh' => 'Nhà hàng',
                'category_slug' => 'nha-hang',
                'dia_chi_chi_tiet' => '5 Phan Kế Bính',
                'tinh_thanh_id' => '01',
                'ten_tinh_thanh' => 'Thành phố Hà Nội',
                'quan_huyen_id' => '001',
                'ten_quan_huyen' => 'Quận Ba Đình',
                'phuong_xa_id' => '00010',
                'ten_phuong_xa' => 'Phường Cống Vị',
                'vi_do' => 21.0345,
                'kinh_do' => 105.8115,
                'gia_nho_nhat' => 120000,
                'gia_lon_nhat' => 380000,
                'gio_mo_cua' => '10:00',
                'gio_dong_cua' => '22:00',
                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/pizza-4ps-phan-ke-binh',
                'anh_bia' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=800&q=80',
                'mo_ta' => 'Chuỗi pizza lò củi phong cách Nhật - Ý nổi danh với các loại phô mai tự làm thủ công tại trang trại Đơn Dương, Đà Lạt.',
                'menus' => [
                    [
                        'ten_danh_muc' => 'Pizza Nướng Củi & Mì Ý',
                        'dishes' => [
                            [
                                'ten_mon' => 'Pizza 4 Loại Phô Mai Kèm Mật Ong Rừng (4 Cheese)',
                                'mo_ta' => 'Bánh pizza nướng lò củi rưới mật ong hoa rừng ngọt dịu hòa quyện cùng 4 loại phô mai tươi trứ danh: Camembert, Mozzarella, Gorgonzola, Raclette.',
                                'gia' => 265000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/pizza-4ps-phan-ke-binh#4-cheese-pizza',
                            ],
                            [
                                'ten_mon' => 'Mì Ý Cua Sốt Kem Cà Chua Cay Nhẹ',
                                'mo_ta' => 'Thịt cua tươi xào sốt kem cà chua mịn màng béo ngậy, kèm thịt cua nguyên miếng và gia vị ớt bột cay nhẹ.',
                                'gia' => 245000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1621996346565-e3d5d6281724?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/pizza-4ps-phan-ke-binh#crab-pasta',
                            ],
                            [
                                'ten_mon' => 'Salad Phô Mai Burrata Tươi Hoa Quả Mùa Hè',
                                'mo_ta' => 'Quả phô mai Burrata tươi mềm béo ngập tràn kem tươi bên trong, ăn kèm sốt balsamic và trái cây nhiệt đới.',
                                'gia' => 195000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/pizza-4ps-phan-ke-binh#burrata-salad',
                            ],
                        ],
                    ],
                ],
            ],

            [
                'ten_quan' => 'The Coffee House - Bà Triệu',
                'loai_hinh_kinh_doanh' => 'Cà phê & Trà',
                'category_slug' => 'ca-phe-tra',
                'dia_chi_chi_tiet' => '56A Bà Triệu',
                'tinh_thanh_id' => '01',
                'ten_tinh_thanh' => 'Thành phố Hà Nội',
                'quan_huyen_id' => '002',
                'ten_quan_huyen' => 'Quận Hoàn Kiếm',
                'phuong_xa_id' => '00052',
                'ten_phuong_xa' => 'Phường Hàng Bài',
                'vi_do' => 21.0225,
                'kinh_do' => 105.8510,
                'gia_nho_nhat' => 35000,
                'gia_lon_nhat' => 75000,
                'gio_mo_cua' => '07:00',
                'gio_dong_cua' => '22:30',
                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/the-coffee-house-ba-trieu',
                'anh_bia' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&w=800&q=80',
                'mo_ta' => 'Ngôi nhà cà phê ấm cúng mang đến trải nghiệm đồ uống sáng tạo, từ Trà đào cam sả trứ danh đến cà phê Robusta Cầu Đất nguyên chất.',
                'menus' => [
                    [
                        'ten_danh_muc' => 'Thức Uống Yêu Thích',
                        'dishes' => [
                            [
                                'ten_mon' => 'Trà Đào Cam Sả Signature (Size L)',
                                'mo_ta' => 'Vị trà đen nồng nàn quyện cùng hương sả đập dập thơm dịu, cam vàng tươi mọng nước và từng lát đào giòn ngọt.',
                                'gia' => 59000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/the-coffee-house-ba-trieu#tra-dao-cam-sa',
                            ],
                            [
                                'ten_mon' => 'Bạc Xỉu Đá Ba Tầng Béo Ngậy',
                                'mo_ta' => 'Hương vị cà phê ngọt ngào thân quen với gấp đôi sữa béo, hòa cùng một chút cà phê nguyên chất tạo nên 3 tầng màu đẹp mắt.',
                                'gia' => 39000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/the-coffee-house-ba-trieu#bac-xiu-da',
                            ],
                            [
                                'ten_mon' => 'Bánh Mousse Chanh Dây Phô Mai',
                                'mo_ta' => 'Lớp bánh xốp mịn hòa quyện lớp kem cheese béo thơm và thạch sốt chanh dây chua chua ngọt ngọt.',
                                'gia' => 35000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1565958011703-44f9829ba187?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/ha-noi/the-coffee-house-ba-trieu#mousse-chanh-day',
                            ],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // ĐÀ NẴNG
            // ==========================================
            [
                'ten_quan' => 'Mì Quảng Bà Mua - Núi Thành',
                'loai_hinh_kinh_doanh' => 'Nhà hàng',
                'category_slug' => 'nha-hang',
                'dia_chi_chi_tiet' => '235 Núi Thành',
                'tinh_thanh_id' => '48',
                'ten_tinh_thanh' => 'Thành phố Đà Nẵng',
                'quan_huyen_id' => '492',
                'ten_quan_huyen' => 'Quận Hải Châu',
                'phuong_xa_id' => '20245',
                'ten_phuong_xa' => 'Phường Hòa Cường Bắc',
                'vi_do' => 16.0425,
                'kinh_do' => 108.2190,
                'gia_nho_nhat' => 35000,
                'gia_lon_nhat' => 75000,
                'gio_mo_cua' => '06:30',
                'gio_dong_cua' => '21:30',
                'shopeefood_url' => 'https://shopeefood.vn/da-nang/mi-quang-ba-mua-235-nui-thanh',
                'anh_bia' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=800&q=80',
                'mo_ta' => 'Thương hiệu Mì Quảng nức tiếng miền Trung với sợi mì cán thủ công dẻo dai, nước nhưỡng ninh đậm đà ăn kèm rau sống búp chuối tươi.',
                'menus' => [
                    [
                        'ten_danh_muc' => 'Mì Quảng Chuẩn Vị Xứ Quảng',
                        'dishes' => [
                            [
                                'ten_mon' => 'Mì Quảng Gà Ta Thả Vườn',
                                'mo_ta' => 'Gà ta da vàng óng giòn sần sật, thịt ngọt mềm kho củ nén đượm vị quê nhà, rắc đậu phộng rang giòn thơm phức.',
                                'gia' => 45000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/da-nang/mi-quang-ba-mua-235-nui-thanh#mi-quang-ga',
                            ],
                            [
                                'ten_mon' => 'Mì Quảng Tôm Thịt Trứng Cút',
                                'mo_ta' => 'Tô mì truyền thống với tôm đất rim săn, thịt ba chỉ đậm đà và trứng cút bùi ngậy cùng bánh tráng mè nướng.',
                                'gia' => 40000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1552611052-33e04de081de?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/da-nang/mi-quang-ba-mua-235-nui-thanh#mi-quang-tom-thit',
                            ],
                            [
                                'ten_mon' => 'Mì Quảng Ếch Đồng Om Chuối Đậu',
                                'mo_ta' => 'Ếch đồng tươi xào xém nghệ tươi và sả ớt, nước sốt vàng ươm thơm lừng chấm bánh tráng giòn rụm.',
                                'gia' => 55000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/da-nang/mi-quang-ba-mua-235-nui-thanh#mi-quang-ech',
                            ],
                        ],
                    ],
                ],
            ],

            [
                'ten_quan' => 'Trà Sữa Gong Cha - Nguyễn Văn Linh',
                'loai_hinh_kinh_doanh' => 'Trà sữa & Nước ép',
                'category_slug' => 'tra-sua-nuoc-ep',
                'dia_chi_chi_tiet' => '225 Nguyễn Văn Linh',
                'tinh_thanh_id' => '48',
                'ten_tinh_thanh' => 'Thành phố Đà Nẵng',
                'quan_huyen_id' => '493',
                'ten_quan_huyen' => 'Quận Thanh Khê',
                'phuong_xa_id' => '20256',
                'ten_phuong_xa' => 'Phường Vĩnh Trung',
                'vi_do' => 16.0618,
                'kinh_do' => 108.2095,
                'gia_nho_nhat' => 38000,
                'gia_lon_nhat' => 78000,
                'gio_mo_cua' => '08:30',
                'gio_dong_cua' => '22:30',
                'shopeefood_url' => 'https://shopeefood.vn/da-nang/gong-cha-nguyen-van-linh',
                'anh_bia' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=800&q=80',
                'mo_ta' => 'Thương hiệu trà cung đình Đài Loan nổi tiếng với lớp váng sữa Milk Foam bồng bềnh béo mặn gây nghiện và trà lá Alisan thơm lừng.',
                'menus' => [
                    [
                        'ten_danh_muc' => 'Trà Sữa & Milk Foam Trứ Danh',
                        'dishes' => [
                            [
                                'ten_mon' => 'Trà Alisan Milk Foam Trứ Danh',
                                'mo_ta' => 'Vị trà Alisan thơm ngát thanh tao từ vùng núi cao Alishan, phủ lớp bọt sữa Milk Foam mằn mặn béo ngậy độc quyền.',
                                'gia' => 57000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1544145945-f90425340c7e?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/da-nang/gong-cha-nguyen-van-linh#alisan-milk-foam',
                            ],
                            [
                                'ten_mon' => 'Trà Sữa Oolong Trân Châu Đen',
                                'mo_ta' => 'Nền trà Oolong đậm đà hòa cùng sữa thơm và hạt trân châu đường đen Đài Loan dai dẻo chuẩn vị.',
                                'gia' => 52000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1558857563-b37cf5a4b7f7?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/da-nang/gong-cha-nguyen-van-linh#oolong-tran-chau',
                            ],
                            [
                                'ten_mon' => 'Xoài Đá Xay Trân Châu Trắng',
                                'mo_ta' => 'Thịt xoài tươi xay đá mịn màng mát lạnh, thêm topping trân châu trắng ngọc trai giòn sần sật.',
                                'gia' => 65000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1600271886742-f049cd451bba?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/da-nang/gong-cha-nguyen-van-linh#xoai-da-xay',
                            ],
                        ],
                    ],
                ],
            ],

            [
                'ten_quan' => 'Bánh Tráng Cuốn Thịt Heo Đại Lộc Nhỏ',
                'loai_hinh_kinh_doanh' => 'Nhà hàng',
                'category_slug' => 'nha-hang',
                'dia_chi_chi_tiet' => '97 Trưng Nữ Vương',
                'tinh_thanh_id' => '48',
                'ten_tinh_thanh' => 'Thành phố Đà Nẵng',
                'quan_huyen_id' => '492',
                'ten_quan_huyen' => 'Quận Hải Châu',
                'phuong_xa_id' => '20239',
                'ten_phuong_xa' => 'Phường Bình Hiên',
                'vi_do' => 16.0592,
                'kinh_do' => 108.2198,
                'gia_nho_nhat' => 45000,
                'gia_lon_nhat' => 120000,
                'gio_mo_cua' => '09:00',
                'gio_dong_cua' => '21:30',
                'shopeefood_url' => 'https://shopeefood.vn/da-nang/banh-trang-thit-heo-dai-loc-nho',
                'anh_bia' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=800&q=80',
                'mo_ta' => 'Đặc sản Đà Nẵng trứ danh với đĩa thịt heo luộc hai đầu da thái mỏng tang, cuốn kèm bánh tráng Đại Lộc dẻo dai và chén mắm nêm cá cơm đậm đà cay xé lưỡi.',
                'menus' => [
                    [
                        'ten_danh_muc' => 'Bánh Tráng Cuốn & Món Ngon Xứ Quảng',
                        'dishes' => [
                            [
                                'ten_mon' => 'Phần Bánh Tráng Cuốn Thịt Heo Hai Đầu Da',
                                'mo_ta' => 'Đĩa thịt heo luộc chuẩn hai đầu mỡ bì trong veo, dĩa rau rừng bạt ngàn 10 loại, bánh ướt mềm và mắm nêm pha sẵn.',
                                'gia' => 75000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/da-nang/banh-trang-thit-heo-dai-loc-nho#thit-heo-hai-dau-da',
                            ],
                            [
                                'ten_mon' => 'Phần Thịt Ba Chỉ Quay Giòn Bì Cuốn Bánh Tráng',
                                'mo_ta' => 'Thịt ba rọi tẩm ướp ngũ vị quay da nổ giòn rụm bên ngoài mềm mọng nước bên trong, cuốn rau sống chấm mắm chua cay.',
                                'gia' => 85000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/da-nang/banh-trang-thit-heo-dai-loc-nho#thit-quay-gion-bi',
                            ],
                            [
                                'ten_mon' => 'Chai Mắm Nêm Pha Sẵn Chuẩn Vị Đại Lộc (300ml)',
                                'mo_ta' => 'Mắm nêm cá cơm nguyên chất ủ chín thơm lừng, giã cùng ớt xiêm xanh, tỏi Lý Sơn và dứa gai ngọt.',
                                'gia' => 35000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1529042410759-befb1204b468?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/da-nang/banh-trang-thit-heo-dai-loc-nho#mam-nem-300ml',
                            ],
                        ],
                    ],
                ],
            ],

            [
                'ten_quan' => 'Cộng Cà Phê - Bạch Đằng Sông Hàn',
                'loai_hinh_kinh_doanh' => 'Cà phê & Trà',
                'category_slug' => 'ca-phe-tra',
                'dia_chi_chi_tiet' => '96 Bạch Đằng',
                'tinh_thanh_id' => '48',
                'ten_tinh_thanh' => 'Thành phố Đà Nẵng',
                'quan_huyen_id' => '492',
                'ten_quan_huyen' => 'Quận Hải Châu',
                'phuong_xa_id' => '20230',
                'ten_phuong_xa' => 'Phường Hải Châu 1',
                'vi_do' => 16.0675,
                'kinh_do' => 108.2240,
                'gia_nho_nhat' => 35000,
                'gia_lon_nhat' => 75000,
                'gio_mo_cua' => '07:00',
                'gio_dong_cua' => '23:00',
                'shopeefood_url' => 'https://shopeefood.vn/da-nang/cong-ca-phe-bach-dang',
                'anh_bia' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&w=800&q=80',
                'mo_ta' => 'Chuỗi cà phê thời bao cấp độc đáo đậm chất văn hóa Việt Nam, nổi tiếng với Cà phê cốt dừa đá xay béo ngậy và không gian hoài niệm.',
                'menus' => [
                    [
                        'ten_danh_muc' => 'Cà Phê Cốt Dừa & Đồ Uống Đặc Biệt',
                        'dishes' => [
                            [
                                'ten_mon' => 'Cà Phê Cốt Dừa Đá Xay',
                                'mo_ta' => 'Món đồ uống biểu tượng của Cộng: Cốt dừa béo ngậy xay đá mịn tuyết hòa cùng cà phê phin đậm đắng.',
                                'gia' => 59000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/da-nang/cong-ca-phe-bach-dang#ca-phe-cot-dua',
                            ],
                            [
                                'ten_mon' => 'Cốt Dừa Cacao Đá Xay',
                                'mo_ta' => 'Cacao nguyên chất đắng thơm hòa quyện cốt dừa sánh mịn mát lạnh, ngọt ngào thích hợp cho mọi lứa tuổi.',
                                'gia' => 55000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/da-nang/cong-ca-phe-bach-dang#cot-dua-cacao',
                            ],
                            [
                                'ten_mon' => 'Trà Cam Quế Thảo Mộc Ấm Áp',
                                'mo_ta' => 'Trà đen hảo hạng ủ cùng cam sấy lát và thanh quế tự nhiên, mùi hương thơm ngát thư giãn tinh thần.',
                                'gia' => 45000,
                                'hinh_anh' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=600&q=80',
                                'shopeefood_url' => 'https://shopeefood.vn/da-nang/cong-ca-phe-bach-dang#tra-cam-que',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $venueCount = 0;
        $dishCount = 0;

        foreach ($venues as $venueData) {
            $menus = $venueData['menus'] ?? [];
            $categorySlug = $venueData['category_slug'] ?? null;
            unset($venueData['menus'], $venueData['category_slug']);

            $slug = Str::slug($venueData['ten_quan']);
            $danhMucId = $categorySlug && isset($categoriesBySlug[$categorySlug])
                ? $categoriesBySlug[$categorySlug]
                : null;

            // Tạo hoặc cập nhật thông tin quán
            $quan = Quan::updateOrCreate(
                ['slug' => $slug],
                array_merge($venueData, [
                    'chu_quan_id' => $owner->id,
                    'danh_muc_id' => $danhMucId,
                    'so_dien_thoai' => '09' . rand(10000000, 99999999),
                    'email' => $slug . '@quanmoi.vn',
                    'trang_thai' => 'da_duyet',
                    'is_noi_bat' => true,
                    'is_xac_thuc' => true,
                    'luot_xem' => rand(1500, 15000),
                ])
            );

            $venueCount++;

            // Tạo danh mục menu và các món ăn kèm link ShopeeFood
            foreach ($menus as $catIndex => $menuCatData) {
                $danhMuc = DanhMucMenu::firstOrCreate(
                    [
                        'quan_id' => $quan->id,
                        'ten_danh_muc' => $menuCatData['ten_danh_muc'],
                    ],
                    [
                        'thu_tu' => $catIndex + 1,
                    ]
                );

                foreach ($menuCatData['dishes'] as $dish) {
                    MonTrongMenu::updateOrCreate(
                        [
                            'danh_muc_id' => $danhMuc->id,
                            'ten_mon' => $dish['ten_mon'],
                        ],
                        [
                            'mo_ta' => $dish['mo_ta'] ?? null,
                            'gia' => $dish['gia'] ?? 50000,
                            'hinh_anh' => $dish['hinh_anh'] ?? null,
                            'shopeefood_url' => $dish['shopeefood_url'] ?? $quan->shopeefood_url,
                            'con_hang' => true,
                        ]
                    );
                    $dishCount++;
                }
            }

            // Đồng bộ sang Elasticsearch / Scout nếu có
            if (method_exists($quan, 'searchable')) {
                try {
                    $quan->searchable();
                } catch (\Throwable $e) {
                    // Bỏ qua nếu Elasticsearch chưa khởi động hoặc gặp lỗi kết nối
                }
            }
        }

        // Cập nhật thêm một số quán sẵn có nếu quán chưa có link ShopeeFood
        $existingQuansWithoutShopee = Quan::whereNull('shopeefood_url')
            ->orWhere('shopeefood_url', '')
            ->take(10)
            ->get();

        foreach ($existingQuansWithoutShopee as $eq) {
            $citySlug = match ((string) $eq->tinh_thanh_id) {
                '79' => 'ho-chi-minh',
                '48' => 'da-nang',
                default => 'ha-noi',
            };
            $eq->shopeefood_url = "https://shopeefood.vn/{$citySlug}/" . Str::slug($eq->ten_quan);
            $eq->save();

            // Gán link shopeefood cho các món ăn đầu tiên của quán
            $firstMenuCategory = $eq->danhMucMenu()->with('monAn')->first();
            if ($firstMenuCategory) {
                foreach ($firstMenuCategory->monAn as $mon) {
                    if (empty($mon->shopeefood_url)) {
                        $mon->shopeefood_url = $eq->shopeefood_url . '#' . Str::slug($mon->ten_mon);
                        $mon->save();
                    }
                }
            }

            if (method_exists($eq, 'searchable')) {
                try {
                    $eq->searchable();
                } catch (\Throwable $e) {
                    // Bỏ qua
                }
            }
        }

        if ($this->command) {
            $this->command->info("Đã seed thành công {$venueCount} quán ăn nổi bật có ShopeeFood và {$dishCount} món ăn kèm link ShopeeFood.");
            if ($existingQuansWithoutShopee->isNotEmpty()) {
                $this->command->info("Đã bổ sung liên kết ShopeeFood cho {$existingQuansWithoutShopee->count()} quán có sẵn trong CSDL.");
            }
        }
    }
}

