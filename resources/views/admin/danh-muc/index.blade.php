@extends(request()->ajax() ? 'admin.ajax_layout' : 'admin.layout')

@section('title', 'Quản Lý Danh Mục Quán - Quán Mới Admin')
@section('page-title', 'Quản Lý Danh Mục Quán Ăn')

@section('content')
<div class="space-y-5">
    {{-- Top Metrics & Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3.5">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">category</span>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Tổng Danh Mục</p>
                <p class="text-xl font-black text-slate-800">{{ $stats['total'] }}</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">visibility</span>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Đang Hiển Thị</p>
                <p class="text-xl font-black text-emerald-600">{{ $stats['active_count'] }}</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">hotel_class</span>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Ghim Trang Chủ</p>
                <p class="text-xl font-black text-indigo-600">{{ $stats['featured_count'] }}</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">storefront</span>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Quán Đã Gán</p>
                <p class="text-xl font-black text-slate-800">{{ $stats['mapped_quans'] }} <span class="text-xs font-normal text-slate-400">/ {{ $stats['total_quans'] }}</span></p>
            </div>
        </div>
    </div>

    {{-- Action Toolbar & Search --}}
    <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-2xs flex flex-col md:flex-row items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.danh-muc-quan.index') }}" class="flex-1 w-full md:w-auto min-w-[240px]">
            <div class="admin-search-wrapper w-full shadow-2xs">
                <span class="material-symbols-outlined text-[18px] text-slate-400">search</span>
                <input type="text" name="q" value="{{ $searchQuery }}" placeholder="Tìm theo tên danh mục, slug, mô tả..." class="w-full bg-transparent border-0 p-0 text-xs font-medium outline-none text-slate-800 placeholder:text-slate-400">
                @if(!empty($searchQuery))
                    <a href="{{ route('admin.danh-muc-quan.index') }}" class="text-xs text-slate-400 hover:text-rose-500 transition-colors">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                    </a>
                @endif
            </div>
        </form>

        <div class="flex items-center gap-2 self-end md:self-auto shrink-0">
            <button type="button" onclick="openAddModal()" class="btn-action-primary-glow">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>Thêm Danh Mục Mới</span>
            </button>
        </div>
    </div>

    {{-- Categories Table --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4 w-14 text-center">Thứ tự</th>
                        <th class="py-3 px-4 w-20 text-center">Icon</th>
                        <th class="py-3 px-4">Tên Danh Mục & Mô Tả</th>
                        <th class="py-3 px-4 w-36">Đường Dẫn (Slug)</th>
                        <th class="py-3 px-4 w-28 text-center">Số Quán</th>
                        <th class="py-3 px-4 w-28 text-center">Hiển Thị</th>
                        <th class="py-3 px-4 w-32 text-center">Ghim Trang Chủ</th>
                        <th class="py-3 px-4 w-32 text-center">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($categories as $category)
                        @php
                            $colorInfo = $category->color_info;
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors group" id="row-cat-{{ $category->id }}">
                            {{-- Thứ tự --}}
                            <td class="py-3.5 px-4 text-center font-bold text-slate-500">
                                <span class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center mx-auto text-xs font-semibold text-slate-600">
                                    {{ $category->thu_tu }}
                                </span>
                            </td>

                            {{-- Icon & Màu --}}
                            <td class="py-3.5 px-4 text-center">
                                <div class="w-10 h-10 rounded-xl border flex items-center justify-center mx-auto shadow-xs"
                                     style="background-color: {{ $colorInfo['light'] ?? '#fffbeb' }}; color: {{ $colorInfo['text'] ?? '#b45309' }}; border-color: {{ $colorInfo['border'] ?? '#fde68a' }};">
                                    <span class="material-symbols-outlined text-[20px]">{{ $category->icon ?: 'restaurant' }}</span>
                                </div>
                            </td>

                            {{-- Tên & Mô tả --}}
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-800 text-[13px] hover:text-amber-600 transition-colors">{{ $category->ten_danh_muc }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border"
                                          style="background-color: {{ $colorInfo['light'] ?? '#fffbeb' }}; color: {{ $colorInfo['text'] ?? '#b45309' }}; border-color: {{ $colorInfo['border'] ?? '#fde68a' }};">
                                        {{ $colorInfo['name'] }}
                                    </span>
                                </div>
                                @if($category->mo_ta)
                                    <p class="text-slate-400 text-[11px] mt-0.5 line-clamp-1">{{ $category->mo_ta }}</p>
                                @endif
                            </td>

                            {{-- Slug --}}
                            <td class="py-3.5 px-4">
                                <code class="px-2 py-1 rounded bg-slate-100 text-slate-600 font-mono text-[11px]">/danh-muc/{{ $category->slug }}</code>
                            </td>

                            {{-- Số lượng quán --}}
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('admin.quan.index', ['danh_muc_id' => $category->id]) }}" title="Xem danh sách quán" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 hover:bg-amber-100 text-slate-700 hover:text-amber-800 font-bold text-xs transition-colors">
                                    <span>{{ $category->quans_count }}</span>
                                    <span class="text-[10px] font-normal text-slate-400">quán</span>
                                </a>
                            </td>

                            {{-- Switch Hiển thị --}}
                            <td class="py-3.5 px-4 text-center">
                                <button type="button" 
                                        onclick="toggleStatus('{{ route('admin.danh-muc-quan.toggle-hien-thi', $category->id) }}', this)" 
                                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border transition-colors duration-200 ease-in-out focus:outline-none {{ $category->is_hien_thi ? 'bg-emerald-500 border-emerald-500' : 'bg-slate-200 border-slate-300' }}"
                                        title="{{ $category->is_hien_thi ? 'Đang hiển thị công khai' : 'Đang ẩn' }}">
                                    <span class="sr-only">Bật/tắt hiển thị</span>
                                    <span class="switch-dot pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow-xs transition duration-200 ease-in-out {{ $category->is_hien_thi ? 'translate-x-4' : 'translate-x-0' }}"></span>
                                </button>
                            </td>

                            {{-- Switch Ghim Trang chủ --}}
                            <td class="py-3.5 px-4 text-center">
                                <button type="button" 
                                        onclick="toggleStatus('{{ route('admin.danh-muc-quan.toggle-noi-bat', $category->id) }}', this)" 
                                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border transition-colors duration-200 ease-in-out focus:outline-none {{ $category->is_noi_bat ? 'bg-amber-500 border-amber-500' : 'bg-slate-200 border-slate-300' }}"
                                        title="{{ $category->is_noi_bat ? 'Đang ghim trang chủ' : 'Chưa ghim trang chủ' }}">
                                    <span class="sr-only">Bật/tắt ghim trang chủ</span>
                                    <span class="switch-dot pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow-xs transition duration-200 ease-in-out {{ $category->is_noi_bat ? 'translate-x-4' : 'translate-x-0' }}"></span>
                                </button>
                            </td>

                            {{-- Thao tác --}}
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    {{-- Xem ngoài Web --}}
                                    <a href="{{ route('danh-muc', $category->slug) }}" target="_blank" title="Xem ngoài web" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors">
                                        <span class="material-symbols-outlined text-[17px]">open_in_new</span>
                                    </a>

                                    {{-- Nút Sửa --}}
                                    <button type="button" onclick='openEditModal(@json($category))' title="Chỉnh sửa danh mục" class="w-8 h-8 rounded-lg text-amber-600 hover:text-amber-700 hover:bg-amber-50 flex items-center justify-center transition-colors">
                                        <span class="material-symbols-outlined text-[17px]">edit</span>
                                    </button>

                                    {{-- Nút Xóa --}}
                                    <form method="POST" action="{{ route('admin.danh-muc-quan.destroy', $category->id) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục \'{{ $category->ten_danh_muc }}\'?\nCác quán thuộc danh mục này sẽ được chuyển về Chưa phân loại.')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Xóa danh mục" class="w-8 h-8 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-[17px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">category</span>
                                <p class="text-sm font-medium">Chưa tìm thấy danh mục nào phù hợp.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL THÊM / SỬA DANH MỤC --}}
<div id="categoryModal" class="fixed inset-0 hidden overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4" style="z-index: 99999 !important;">
    <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all duration-150 my-auto">
        {{-- Modal Header --}}
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
            <div class="flex items-center gap-2.5">
                <div id="modalIconBox" class="w-9 h-9 rounded-xl flex items-center justify-center border shadow-xs transition-colors"
                     style="background-color: #fffbeb; color: #b45309; border-color: #fde68a;">
                    <span class="material-symbols-outlined text-[20px]" id="modalIconPreview">restaurant</span>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm md:text-base leading-tight" id="modalTitle">Thêm Danh Mục Mới</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Cấu hình biểu tượng, màu sắc và hiển thị</p>
                </div>
            </div>
            <button type="button" onclick="closeModal()" class="w-8 h-8 rounded-full hover:bg-slate-200/60 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        {{-- Modal Form --}}
        <form id="categoryForm" method="POST" action="{{ route('admin.danh-muc-quan.store') }}" class="p-6 space-y-4">
            @csrf
            <div id="methodContainer"></div>

            {{-- Tên danh mục --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Tên Danh Mục <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="cat_ten_danh_muc" name="ten_danh_muc" required placeholder="VD: Lẩu & Nướng, Cà phê & Trà..." oninput="autoSlug(this.value)" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/10 bg-white text-slate-800 transition-all shadow-2xs">
            </div>

            {{-- Slug --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Đường Dẫn Tĩnh (Slug) <span class="text-slate-400 text-[10px] font-normal">(tự động sinh hoặc tùy chỉnh)</span>
                </label>
                <div class="flex items-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-500 font-mono shadow-2xs">
                    <span class="shrink-0 text-slate-400 select-none">/danh-muc/</span>
                    <input type="text" id="cat_slug" name="slug" placeholder="ca-phe-tra" class="w-full bg-transparent border-0 p-0 text-xs font-mono font-semibold text-slate-800 outline-none">
                </div>
            </div>

            {{-- Mô tả ngắn --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Mô Tả Ngắn / Khẩu Hiệu
                </label>
                <input type="text" id="cat_mo_ta" name="mo_ta" placeholder="VD: Tụ tập bạn bè, chill cuối tuần..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/10 bg-white text-slate-800 transition-all shadow-2xs">
            </div>

            {{-- Chọn Icon --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Biểu Tượng (Icon Material Symbol)
                    </label>
                    <span class="text-[11px] text-slate-400">Chọn 1 biểu tượng</span>
                </div>
                <input type="hidden" id="cat_icon" name="icon" value="restaurant">
                <div class="p-2 bg-slate-50 rounded-2xl border border-slate-200">
                    <div id="iconPicker" class="grid grid-cols-8 gap-1.5" style="display: grid; grid-template-columns: repeat(8, minmax(0, 1fr)); gap: 6px;">
                        @foreach($presetIcons as $pIcon)
                            <button type="button" 
                                    onclick="selectIcon('{{ $pIcon['icon'] }}')" 
                                    data-icon="{{ $pIcon['icon'] }}" 
                                    title="{{ $pIcon['label'] }}" 
                                    class="icon-choice-btn w-full aspect-square rounded-xl flex items-center justify-center text-slate-600 hover:bg-amber-100 hover:text-amber-800 transition-all cursor-pointer border border-transparent">
                                <span class="material-symbols-outlined text-[20px] pointer-events-none">{{ $pIcon['icon'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Chọn Màu sắc --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Màu Sắc Đại Diện
                    </label>
                    <span id="selectedColorName" class="text-[11px] font-bold text-amber-600">Vàng Hổ Phách</span>
                </div>
                <input type="hidden" id="cat_mau_sac" name="mau_sac" value="amber">
                <div class="flex flex-wrap items-center gap-2 p-2 bg-slate-50 rounded-2xl border border-slate-200" id="colorPicker">
                    @foreach($presetColors as $cKey => $cVal)
                        <button type="button" 
                                onclick="selectColor('{{ $cKey }}', '{{ $cVal['name'] }}')" 
                                data-color="{{ $cKey }}" 
                                data-color-name="{{ $cVal['name'] }}"
                                title="{{ $cVal['name'] }}" 
                                style="background-color: {{ $cVal['hex'] }}; width: 28px; height: 28px;" 
                                class="color-choice-btn rounded-full transition-all cursor-pointer shadow-2xs hover:scale-115 border border-black/10">
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Thứ tự & Tuỳ chọn --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Thứ Tự Sắp Xếp
                    </label>
                    <input type="number" id="cat_thu_tu" name="thu_tu" value="0" min="0" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:border-amber-500 bg-white text-slate-800 shadow-2xs">
                </div>

                <div class="flex flex-col justify-center space-y-2 pt-1 sm:pt-3">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" id="cat_is_hien_thi" name="is_hien_thi" value="1" checked class="w-4 h-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500 cursor-pointer">
                        <span class="text-xs font-semibold text-slate-700">Hiển thị công khai</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" id="cat_is_noi_bat" name="is_noi_bat" value="1" class="w-4 h-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500 cursor-pointer">
                        <span class="text-xs font-semibold text-slate-700">Ghim Trang chủ</span>
                    </label>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeModal()" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                    Hủy bỏ
                </button>
                <button type="submit" class="btn-action-primary-glow cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span id="submitBtnText">Lưu Danh Mục</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- JAVASCRIPT XỬ LÝ MODAL & AJAX --}}
<script>
    let isEditing = false;
    const presetColorsMap = @json($presetColors);

    function autoSlug(text) {
        if (!isEditing) {
            const slug = text.toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/[đĐ]/g, 'd')
                .replace(/[^a-z0-9\s-]/g, '')
                .trim()
                .replace(/[\s-]+/g, '-');
            document.getElementById('cat_slug').value = slug;
        }
    }

    function updateIconHighlight(activeIcon, hexColor) {
        document.querySelectorAll('.icon-choice-btn').forEach(btn => {
            if (btn.dataset.icon === activeIcon) {
                btn.style.backgroundColor = hexColor || '#f59e0b';
                btn.style.color = '#ffffff';
                btn.style.borderColor = hexColor || '#d97706';
                btn.classList.add('shadow-xs');
            } else {
                btn.style.backgroundColor = '';
                btn.style.color = '';
                btn.style.borderColor = 'transparent';
                btn.classList.remove('shadow-xs');
            }
        });
    }

    function selectIcon(iconName) {
        document.getElementById('cat_icon').value = iconName;
        const preview = document.getElementById('modalIconPreview');
        if (preview) preview.innerText = iconName;

        const currentColor = document.getElementById('cat_mau_sac')?.value || 'amber';
        const colorMeta = presetColorsMap[currentColor] || presetColorsMap['amber'];
        updateIconHighlight(iconName, colorMeta?.hex || '#f59e0b');
    }

    function selectColor(colorName, displayName) {
        document.getElementById('cat_mau_sac').value = colorName;
        const colorMeta = presetColorsMap[colorName] || presetColorsMap['amber'];

        // Cập nhật nhãn tên màu và màu chữ nhãn
        const colorLabel = document.getElementById('selectedColorName');
        if (colorLabel && colorMeta) {
            colorLabel.innerText = displayName || colorMeta.name || colorName;
            colorLabel.style.color = colorMeta.hex;
        }

        // Cập nhật ô preview icon ở header của modal
        const modalIconBox = document.getElementById('modalIconBox');
        if (modalIconBox && colorMeta) {
            modalIconBox.style.backgroundColor = colorMeta.light;
            modalIconBox.style.color = colorMeta.text || colorMeta.hex;
            modalIconBox.style.borderColor = colorMeta.border;
        }

        // Cập nhật vòng chọn nút màu (ring đổi theo mã hex của chính màu đó)
        document.querySelectorAll('.color-choice-btn').forEach(btn => {
            if (btn.dataset.color === colorName) {
                btn.style.boxShadow = `0 0 0 2.5px #ffffff, 0 0 0 4.5px ${colorMeta.hex}`;
                btn.style.transform = 'scale(1.18)';
            } else {
                btn.style.boxShadow = 'none';
                btn.style.transform = 'scale(1)';
            }
        });

        // Cập nhật màu nút icon đang chọn trong grid icon
        const currentIcon = document.getElementById('cat_icon')?.value || 'restaurant';
        updateIconHighlight(currentIcon, colorMeta.hex);
    }

    function openAddModal() {
        isEditing = false;
        document.getElementById('modalTitle').innerText = 'Thêm Danh Mục Quán Mới';
        document.getElementById('submitBtnText').innerText = 'Tạo Danh Mục';
        document.getElementById('categoryForm').action = "{{ route('admin.danh-muc-quan.store') }}";
        document.getElementById('methodContainer').innerHTML = '';
        
        document.getElementById('cat_ten_danh_muc').value = '';
        document.getElementById('cat_slug').value = '';
        document.getElementById('cat_mo_ta').value = '';
        document.getElementById('cat_thu_tu').value = 0;
        document.getElementById('cat_is_hien_thi').checked = true;
        document.getElementById('cat_is_noi_bat').checked = false;

        selectColor('amber', 'Vàng Hổ Phách');
        selectIcon('restaurant');

        document.getElementById('categoryModal').classList.remove('hidden');
    }

    function openEditModal(category) {
        isEditing = true;
        document.getElementById('modalTitle').innerText = 'Chỉnh Sửa Danh Mục: ' + category.ten_danh_muc;
        document.getElementById('submitBtnText').innerText = 'Lưu Thay Đổi';
        document.getElementById('categoryForm').action = "/admin/danh-muc-quan/" + category.id;
        document.getElementById('methodContainer').innerHTML = '<input type="hidden" name="_method" value="PUT">';

        document.getElementById('cat_ten_danh_muc').value = category.ten_danh_muc;
        document.getElementById('cat_slug').value = category.slug;
        document.getElementById('cat_mo_ta').value = category.mo_ta || '';
        document.getElementById('cat_thu_tu').value = category.thu_tu;
        document.getElementById('cat_is_hien_thi').checked = Boolean(category.is_hien_thi);
        document.getElementById('cat_is_noi_bat').checked = Boolean(category.is_noi_bat);

        selectColor(category.mau_sac || 'amber');
        selectIcon(category.icon || 'restaurant');

        document.getElementById('categoryModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('categoryModal').classList.add('hidden');
    }

    // Toggle status AJAX chuẩn xác 100%
    function toggleStatus(url, buttonEl) {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        buttonEl.disabled = true;

        fetch(url, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        })
        .then(res => res.json())
        .then(data => {
            buttonEl.disabled = false;
            if (data.success) {
                const isNoiBat = url.includes('toggle-noi-bat');
                const isChecked = isNoiBat ? Boolean(data.is_noi_bat) : Boolean(data.is_hien_thi);
                const dot = buttonEl.querySelector('.switch-dot');

                if (isChecked) {
                    buttonEl.classList.remove('bg-slate-200', 'border-slate-300');
                    buttonEl.classList.add(isNoiBat ? 'bg-amber-500' : 'bg-emerald-500', isNoiBat ? 'border-amber-500' : 'border-emerald-500');
                    buttonEl.title = isNoiBat ? 'Đang ghim trang chủ' : 'Đang hiển thị công khai';
                    if (dot) {
                        dot.classList.remove('translate-x-0');
                        dot.classList.add('translate-x-4');
                    }
                } else {
                    buttonEl.classList.remove('bg-amber-500', 'bg-emerald-500', 'border-amber-500', 'border-emerald-500');
                    buttonEl.classList.add('bg-slate-200', 'border-slate-300');
                    buttonEl.title = isNoiBat ? 'Chưa ghim trang chủ' : 'Đang ẩn';
                    if (dot) {
                        dot.classList.remove('translate-x-4');
                        dot.classList.add('translate-x-0');
                    }
                }

                if (typeof showCenterRealtimeAlert === 'function') {
                    showCenterRealtimeAlert('Cập Nhật Danh Mục Ẩm Thực', data.message || 'Trạng thái danh mục đã được đồng bộ vào CSDL.', 'category', 'info');
                } else if (window.showToast) {
                    window.showToast(data.message, 'success');
                }
            }
        })
        .catch(err => {
            buttonEl.disabled = false;
            console.error('Toggle error:', err);
            alert('Có lỗi xảy ra khi cập nhật trạng thái.');
        });
    }

    // Close modal on escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });
</script>
@endsection