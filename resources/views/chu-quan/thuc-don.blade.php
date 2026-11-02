@extends('layouts.app')

@section('title', 'Quản lý thực đơn - ' . $quan->ten_quan)

@section('content')
<main class="bg-gray-50/50 min-h-screen pb-20 pt-24 md:pt-28">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Quản lý thực đơn</h1>
            <p class="text-gray-500 mt-2">Cập nhật danh mục và món ăn cho quán <span class="font-bold text-primary">{{ $quan->ten_quan }}</span></p>
        </div>

        <form id="thuc-don-form" method="POST" action="{{ route('chu-quan.quan.menu.update', ['slug' => $quan->slug]) }}" onsubmit="event.preventDefault(); submitMenuForm();">
            @csrf
            
            <div class="bg-white p-6 rounded-xl shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-gray-200">
                <div class="flex items-center justify-between mb-6 border-b border-gray-100 pb-4">
                    <div>
                        <h2 class="text-xl font-black text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[24px]">restaurant_menu</span>
                            Xây dựng thực đơn
                        </h2>
                    </div>
                    <button type="button" onclick="addCategory()" class="bg-primary/10 text-primary px-4 py-2 rounded-lg font-bold text-sm hover:bg-primary/20 transition-all flex items-center gap-1">
                        <span class="material-symbols-outlined text-[18px]">add_circle</span> Thêm danh mục
                    </button>
                </div>

                <div id="menu-categories-container" class="space-y-6 min-h-[200px]">
                    <div id="empty-menu-state" class="flex flex-col items-center justify-center h-48 text-gray-400">
                        <span class="material-symbols-outlined text-5xl mb-2">menu_book</span>
                        <p class="text-sm font-medium">Chưa có danh mục nào. Hãy thêm danh mục đầu tiên!</p>
                    </div>
                </div>

                <input type="hidden" id="menu_data" name="menu_data" value="[]" />
                <p id="form-error-general" class="text-sm text-red-600 hidden mt-4 font-bold"></p>

                <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-100">
                    <button id="submit-btn" type="submit" class="bg-primary text-white px-8 py-3.5 rounded-xl font-bold text-[16px] shadow-lg hover:shadow-xl hover:bg-primary/90 active:scale-[0.98] transition-all flex items-center gap-2 cursor-pointer">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">save</span>
                        Lưu thực đơn
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- MODAL XÁC NHẬN CHUNG HIỆN Ở GIỮA MÀN HÌNH (THAY THẾ CONFIRM NATIVE) --}}
    <div id="owner-confirm-modal" class="fixed inset-0 z-[9999] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 hidden" aria-modal="true" role="dialog">
        <div id="owner-confirm-box" class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl text-center space-y-4 transform transition-all scale-95 duration-200">
            <div id="owner-confirm-icon-wrap" class="w-14 h-14 rounded-2xl mx-auto flex items-center justify-center bg-red-100 text-red-600" style="background-color: #fee2e2; color: #dc2626;">
                <span id="owner-confirm-icon" class="material-symbols-outlined text-3xl">delete</span>
            </div>
            <div class="space-y-1.5">
                <h3 id="owner-confirm-title" class="text-base font-bold text-gray-900">Xác Nhận Thao Tác</h3>
                <div id="owner-confirm-message" class="text-xs text-gray-600 leading-relaxed"></div>
            </div>
            <div class="pt-2 flex items-center justify-center gap-2.5">
                <button type="button" onclick="closeOwnerConfirmModal()" class="flex-1 py-2.5 px-4 rounded-xl border border-gray-200 bg-white text-gray-700 font-bold text-xs hover:bg-gray-50 active:scale-95 transition cursor-pointer">
                    Hủy bỏ
                </button>
                <button type="button" id="owner-confirm-submit-btn" onclick="handleOwnerConfirmSubmit()"
                        class="flex-1 py-2.5 px-4 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs shadow-md shadow-red-500/25 transition flex items-center justify-center gap-1.5 cursor-pointer active:scale-95"
                        style="background-color: #dc2626 !important; color: #ffffff !important;">
                    <span id="owner-confirm-btn-icon" class="material-symbols-outlined text-base" style="color: #ffffff !important;">delete</span>
                    <span id="owner-confirm-btn-text" style="color: #ffffff !important; font-weight: 700;">Xác Nhận Xóa</span>
                </button>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
@php
    $r2MenuImageBase = config('filesystems.disks.r2.url') ?: (
        config('filesystems.disks.r2.endpoint') && config('filesystems.disks.r2.bucket')
            ? config('filesystems.disks.r2.endpoint').'/'.config('filesystems.disks.r2.bucket')
            : ''
    );
    $menuInitialData = [
        'imageBase' => rtrim($r2MenuImageBase, '/'),
        'categories' => $quan->danhMucMenu,
    ];
@endphp
<script type="application/json" id="menu-initial-data">@json($menuInitialData)</script>
<script>
    let menuCategories = [];
    let categoryCounter = 0;
    let itemCounter = 0;
    const menuInitialData = JSON.parse(document.getElementById('menu-initial-data').textContent);
    const r2MenuImageBase = menuInitialData.imageBase;

    function resolveMenuImageUrl(image) {
        if (!image) return '';
        if (image.startsWith('http://') || image.startsWith('https://') || image.startsWith('/')) return image;
        return r2MenuImageBase ? `${r2MenuImageBase}/${image.replace(/^\/+/, '')}` : '';
    }

    // Load existing data
    const initialData = menuInitialData.categories;
    
    document.addEventListener('DOMContentLoaded', () => {
        if (initialData && initialData.length > 0) {
            document.getElementById('empty-menu-state')?.classList.add('hidden');
            initialData.forEach(cat => {
                const catId = ++categoryCounter;
                const catObj = { id: catId, db_id: cat.id, name: cat.ten_danh_muc, items: [] };
                
                renderCategoryHtml(catId, catObj.name);
                
                if (cat.mon_an && cat.mon_an.length > 0) {
                    cat.mon_an.forEach(item => {
                        const itemId = ++itemCounter;
                        catObj.items.push({ id: itemId, db_id: item.id, tmp_id: itemId, name: item.ten_mon, price: item.gia, description: item.mo_ta || '', shopeefood_url: item.shopeefood_url || '', image: item.hinh_anh || '' });
                        renderItemHtml(catId, itemId, item.ten_mon, item.gia, item.mo_ta || '', item.hinh_anh_url || item.hinh_anh, item.shopeefood_url || '');
                    });
                }
                menuCategories.push(catObj);
            });
        }
    });

    function escapeAttribute(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function renderCategoryHtml(catId, name = '') {
        const container = document.getElementById('menu-categories-container');
        const html = `
            <div id="category-box-${catId}" class="border border-gray-200 rounded-xl overflow-hidden bg-gray-50/30">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <input type="text" placeholder="Tên danh mục (vd: Khai vị, Món chính - tối đa 100 chữ)..." maxlength="100" value="${escapeAttribute(name)}" oninput="updateCategoryName(${catId}, this.value)" class="bg-transparent font-bold text-gray-800 outline-none w-2/3" required />
                    <button type="button" onclick="removeCategory(${catId})" class="text-red-500 hover:text-red-600"><span class="material-symbols-outlined">delete</span></button>
                </div>
                <div class="p-4 space-y-3" id="category-items-${catId}">
                </div>
                <div class="px-4 py-3 border-t border-gray-100">
                    <button type="button" onclick="addMenuItem(${catId})" class="text-sm font-bold text-primary flex items-center gap-1 hover:underline">
                        <span class="material-symbols-outlined text-[16px]">add</span> Thêm món vào danh mục này
                    </button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    function renderItemHtml(catId, itemId, name = '', price = '', desc = '', image = '', shopeefoodUrl = '') {
        const container = document.getElementById(`category-items-${catId}`);
        const imagePreview = resolveMenuImageUrl(image);
        const imgDisplay = image ? `<img src="${imagePreview}" class="w-full h-full object-cover rounded" />` : `<span class="material-symbols-outlined text-gray-400">image</span>`;
        const html = `
            <div id="item-box-${itemId}" class="flex gap-3 items-start bg-white p-3 rounded-lg border border-gray-100 shadow-sm relative group">
                <button type="button" onclick="removeMenuItem(${catId}, ${itemId})" class="absolute -right-2 -top-2 bg-red-100 text-red-600 rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-sm"><span class="material-symbols-outlined text-[14px]">close</span></button>
                
                <div class="relative w-16 h-16 shrink-0 bg-gray-50 border border-gray-200 rounded cursor-pointer overflow-hidden flex items-center justify-center hover:bg-gray-100" onclick="document.getElementById('item_image_${itemId}').click()">
                    <div id="preview_container_${itemId}" class="w-full h-full flex items-center justify-center">
                        ${imgDisplay}
                    </div>
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                        <span class="material-symbols-outlined text-white text-[20px]">edit</span>
                    </div>
                    <input type="file" id="item_image_${itemId}" name="item_image_${itemId}" accept="image/*" class="hidden" onchange="previewMenuImage(this, ${itemId})" />
                </div>

                <div class="flex-1 space-y-2">
                    <div class="flex gap-2">
                        <input type="text" placeholder="Tên món ăn (tối đa 100 chữ)..." maxlength="100" value="${escapeAttribute(name)}" oninput="updateItem(${catId}, ${itemId}, 'name', this.value)" class="flex-1 border border-gray-200 rounded px-2 py-1.5 text-sm outline-none focus:border-primary" required />
                        <input type="number" placeholder="Giá (VNĐ)" min="0" max="999999999" value="${escapeAttribute(price)}" oninput="updateItem(${catId}, ${itemId}, 'price', this.value)" class="w-28 border border-gray-200 rounded px-2 py-1.5 text-sm outline-none focus:border-primary" />
                    </div>
                    <input type="text" placeholder="Giới thiệu ngắn món ăn (tối đa 500 chữ)..." maxlength="500" value="${escapeAttribute(desc)}" oninput="updateItem(${catId}, ${itemId}, 'description', this.value)" class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm outline-none focus:border-primary text-gray-500" />
                    <input type="url" placeholder="Link ShopeeFood của món (https://shopeefood.vn/...)" pattern="https?:\\/\\/(.*shopeefood\\.vn|.*shopee\\.vn)(\\/.*)?" title="Đường dẫn ShopeeFood phải thuộc tên miền shopeefood.vn hoặc shopee.vn (ví dụ: https://shopeefood.vn/...)" value="${escapeAttribute(shopeefoodUrl)}" oninput="updateItem(${catId}, ${itemId}, 'shopeefood_url', this.value)" class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm outline-none focus:border-primary text-gray-500" />
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    function addCategory() {
        document.getElementById('empty-menu-state')?.classList.add('hidden');
        
        const catId = ++categoryCounter;
        const catObj = { id: catId, name: '', items: [] };
        menuCategories.push(catObj);
        
        renderCategoryHtml(catId);
        addMenuItem(catId); // Auto add first item
    }

    let ownerConfirmCallback = null;
    let isOwnerConfirmSubmitting = false;

    function openOwnerConfirmModal(options) {
        if (typeof window.showConfirmDialog === 'function') {
            window.showConfirmDialog({
                title: options.title || 'Xác nhận thao tác',
                message: options.message || 'Bạn có chắc chắn muốn thực hiện hành động này?',
                icon: options.icon || 'delete',
                confirmText: options.confirmText || 'Xác nhận',
                confirmIcon: options.confirmIcon || options.icon || 'delete',
                cancelText: options.cancelText || 'Hủy bỏ',
                type: options.type || 'danger',
                onConfirm: options.onConfirm,
                onCancel: options.onCancel
            });
            return;
        }

        const modal = document.getElementById('owner-confirm-modal');
        const box = document.getElementById('owner-confirm-box');
        const titleEl = document.getElementById('owner-confirm-title');
        const msgEl = document.getElementById('owner-confirm-message');
        const iconEl = document.getElementById('owner-confirm-icon');
        const iconWrap = document.getElementById('owner-confirm-icon-wrap');
        const submitBtn = document.getElementById('owner-confirm-submit-btn');
        const btnText = document.getElementById('owner-confirm-btn-text');
        const btnIcon = document.getElementById('owner-confirm-btn-icon');

        if (!modal) return;

        ownerConfirmCallback = options.onConfirm || null;
        titleEl.textContent = options.title || 'Xác nhận thao tác';
        msgEl.innerHTML = options.message || 'Bạn có chắc chắn muốn thực hiện hành động này?';
        iconEl.textContent = options.icon || 'delete';
        btnText.textContent = options.confirmText || 'Xác nhận';
        btnIcon.textContent = options.confirmIcon || 'delete';

        modal.classList.remove('hidden');
        setTimeout(() => {
            box.classList.remove('scale-95');
            box.classList.add('scale-100');
        }, 10);
    }

    function closeOwnerConfirmModal() {
        const modal = document.getElementById('owner-confirm-modal');
        const box = document.getElementById('owner-confirm-box');
        ownerConfirmCallback = null;
        if (!modal) return;

        box.classList.remove('scale-100');
        box.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 150);
    }

    function handleOwnerConfirmSubmit() {
        if (isOwnerConfirmSubmitting) return;
        if (typeof ownerConfirmCallback === 'function') {
            isOwnerConfirmSubmitting = true;
            const cb = ownerConfirmCallback;
            ownerConfirmCallback = null;
            closeOwnerConfirmModal();
            try {
                cb();
            } finally {
                setTimeout(() => {
                    isOwnerConfirmSubmitting = false;
                }, 400);
            }
        } else {
            closeOwnerConfirmModal();
        }
    }

    function removeCategory(catId) {
        const cat = menuCategories.find(c => c.id === catId);
        const catName = cat && cat.name ? cat.name : 'danh mục này';
        openOwnerConfirmModal({
            title: 'Xác Nhận Xóa Danh Mục',
            message: `Bạn có chắc chắn muốn xóa danh mục "<strong>${escapeAttribute(catName)}</strong>" không?<br><span class="text-xs text-red-500 font-medium">Tất cả món ăn thuộc danh mục này sẽ bị xóa khỏi danh sách.</span>`,
            icon: 'delete',
            iconClass: 'bg-red-100 text-red-600',
            confirmText: 'Xác Nhận Xóa',
            confirmIcon: 'delete',
            confirmClass: 'owner-confirm-btn-danger',
            onConfirm: () => {
                menuCategories = menuCategories.filter(c => c.id !== catId);
                document.getElementById(`category-box-${catId}`)?.remove();
                if (menuCategories.length === 0) {
                    document.getElementById('empty-menu-state')?.classList.remove('hidden');
                }
                if (typeof window.showToast === 'function') {
                    window.showToast({
                        title: 'Thông báo',
                        message: 'Đã xóa danh mục khỏi thực đơn.',
                        type: 'info'
                    });
                }
            }
        });
    }

    function updateCategoryName(catId, val) {
        const cat = menuCategories.find(c => c.id === catId);
        if (cat) {
            cat.name = String(val || '').slice(0, 100);
        }
    }

    function addMenuItem(catId) {
        const cat = menuCategories.find(c => c.id === catId);
        if (!cat) return;
        
        const itemId = ++itemCounter;
        cat.items.push({ id: itemId, tmp_id: itemId, name: '', price: '', description: '', shopeefood_url: '', image: '' });
        renderItemHtml(catId, itemId);
    }

    function previewMenuImage(input, itemId) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (!file.type.startsWith('image/')) {
                if (typeof window.showAlertDialog === 'function') {
                    window.showAlertDialog({
                        title: 'Lỗi định dạng ảnh',
                        message: 'File tải lên phải là hình ảnh (JPEG, PNG, JPG, WEBP).',
                        type: 'error'
                    });
                } else if (typeof window.showToast === 'function') {
                    window.showToast({
                        title: 'Lỗi tải ảnh',
                        message: 'File tải lên phải là hình ảnh (JPEG, PNG, JPG, WEBP).',
                        type: 'error'
                    });
                }
                input.value = '';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                if (typeof window.showAlertDialog === 'function') {
                    window.showAlertDialog({
                        title: 'Ảnh quá lớn',
                        message: 'Dung lượng ảnh món ăn tối đa là 5MB. Vui lòng chọn ảnh có kích thước nhỏ hơn.',
                        type: 'warning'
                    });
                } else if (typeof window.showToast === 'function') {
                    window.showToast({
                        title: 'Lỗi dung lượng',
                        message: 'Dung lượng ảnh món ăn tối đa là 5MB.',
                        type: 'error'
                    });
                }
                input.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                const container = document.getElementById(`preview_container_${itemId}`);
                container.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover rounded" />`;
            }
            reader.readAsDataURL(file);
        }
    }

    function removeMenuItem(catId, itemId) {
        const cat = menuCategories.find(c => c.id === catId);
        let itemName = '';
        if (cat) {
            const item = cat.items.find(i => i.id === itemId);
            if (item && item.name) itemName = item.name;
        }
        openOwnerConfirmModal({
            title: 'Xác Nhận Xóa Món Ăn',
            message: itemName ? `Bạn có chắc chắn muốn xóa món "<strong>${escapeAttribute(itemName)}</strong>" này không?` : 'Bạn có chắc chắn muốn xóa món ăn này không?',
            icon: 'delete',
            iconClass: 'bg-red-100 text-red-600',
            confirmText: 'Xác Nhận Xóa',
            confirmIcon: 'delete',
            confirmClass: 'owner-confirm-btn-danger',
            onConfirm: () => {
                if (cat) {
                    cat.items = cat.items.filter(i => i.id !== itemId);
                    document.getElementById(`item-box-${itemId}`)?.remove();
                    if (typeof window.showToast === 'function') {
                        window.showToast({
                            title: 'Thông báo',
                            message: 'Đã xóa món ăn khỏi danh mục.',
                            type: 'info'
                        });
                    }
                }
            }
        });
    }

    function updateItem(catId, itemId, field, val) {
        const cat = menuCategories.find(c => c.id === catId);
        if (cat) {
            const item = cat.items.find(i => i.id === itemId);
            if (item) {
                if (field === 'name') {
                    val = String(val || '').slice(0, 100);
                } else if (field === 'description') {
                    val = String(val || '').slice(0, 500);
                }
                item[field] = val;
            }
        }
    }

    function isValidShopeeFoodUrl(url) {
        if (!url || !url.trim()) return true;
        const trimmed = url.trim();
        const pattern = /^https?:\/\/(?:[a-zA-Z0-9-]+\.)*(?:shopeefood\.vn|shopee\.vn)(\/.*)?$/i;
        return pattern.test(trimmed);
    }

    async function submitMenuForm() {
        const form = document.getElementById('thuc-don-form');
        
        if (!form.reportValidity()) return;

        // Kiểm tra giới hạn ký tự và ràng buộc link ShopeeFood phía Client
        for (const cat of menuCategories) {
            if ((cat.name || '').trim().length > 100) {
                const msg = `Tên danh mục "${cat.name.slice(0, 20)}..." vượt quá giới hạn 100 chữ.`;
                showErrorGeneral(msg);
                return;
            }
            for (const item of (cat.items || [])) {
                if ((item.name || '').trim().length > 100) {
                    const msg = `Tên món "${item.name.slice(0, 20)}..." vượt quá giới hạn 100 chữ.`;
                    showErrorGeneral(msg);
                    return;
                }
                if ((item.description || '').trim().length > 500) {
                    const msg = `Giới thiệu ngắn món "${item.name || ''}" vượt quá giới hạn 500 chữ.`;
                    showErrorGeneral(msg);
                    return;
                }
                if (item.shopeefood_url && item.shopeefood_url.trim()) {
                    if (!isValidShopeeFoodUrl(item.shopeefood_url)) {
                        const msg = `Đường dẫn ShopeeFood của món "${item.name || 'này'}" không hợp lệ. Vui lòng nhập liên kết hợp lệ từ shopeefood.vn hoặc shopee.vn.`;
                        showErrorGeneral(msg);
                        return;
                    }
                }
            }
        }

        document.getElementById('menu_data').value = JSON.stringify(menuCategories);

        const formData = new FormData(form);
        const btn = document.getElementById('submit-btn');
        const errGen = document.getElementById('form-error-general');

        errGen.classList.add('hidden');
        const originalBtnText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span> Đang lưu...';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: formData
            });

            const isJsonResponse = (response.headers.get('content-type') || '').includes('application/json');
            const data = isJsonResponse ? await response.json() : null;

            if (!response.ok) {
                let errorMsg = 'Đã có lỗi xảy ra. Vui lòng kiểm tra lại.';
                if (data && data.errors) {
                    const firstErrKey = Object.keys(data.errors)[0];
                    errorMsg = data.errors[firstErrKey][0];
                } else if (data && data.message) {
                    errorMsg = data.message;
                }
                showErrorGeneral(errorMsg);
                btn.disabled = false;
                btn.innerHTML = originalBtnText;
                return;
            }

            btn.innerHTML = '<span class="material-symbols-outlined text-[20px]" style="font-variation-settings: \'FILL\' 1;">check_circle</span> Hoàn tất!';
            if (typeof window.showToast === 'function') {
                window.showToast({
                    title: 'Thành công',
                    message: (data && data.message) ? data.message : 'Cập nhật thực đơn thành công!',
                    type: 'success'
                });
            }
            setTimeout(() => {
                window.location.href = data.redirect_to || '/';
            }, 1000);
        } catch (e) {
            showErrorGeneral('Lỗi kết nối hệ thống. Vui lòng thử lại sau.');
            btn.disabled = false;
            btn.innerHTML = originalBtnText;
        }
    }

    function showErrorGeneral(errorMsg) {
        const errGen = document.getElementById('form-error-general');
        if (errGen) {
            errGen.textContent = errorMsg;
            errGen.classList.remove('hidden');
            errGen.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        if (typeof window.showAlertDialog === 'function') {
            window.showAlertDialog({
                title: 'Lỗi thực đơn',
                message: errorMsg,
                type: 'error'
            });
        } else if (typeof window.showToast === 'function') {
            window.showToast({
                title: 'Lỗi thực đơn',
                message: errorMsg,
                type: 'error'
            });
        }
    }

    // Realtime detection: when admin rejects or deletes this restaurant, alert owner and redirect to /chu-quan
    window.addEventListener('quanmoi:quan-status-updated', function(e) {
        const notif = e.detail;
        if (!notif) return;

        const currentQuanId = '{{ $quan->id }}';
        const currentQuanSlug = '{{ $quan->slug }}';

        if (String(notif.quan_id) === currentQuanId || notif.slug === currentQuanSlug) {
            const isRemoved = ['rejected', 'deleted', 'force_deleted', 'khong_duyet'].includes(notif.action) ||
                              ['bi_khoa', 'tu_choi'].includes(notif.status);

            if (isRemoved) {
                const noteInfo = notif.note ? `<br><br><span class="text-xs text-rose-600 font-semibold">Ghi chú từ BQT: ${notif.note}</span>` : '';
                if (typeof window.showAlertDialog === 'function') {
                    window.showAlertDialog({
                        title: 'Cơ sở ẩm thực đã bị gỡ',
                        message: `Cơ sở ẩm thực "${notif.title || '{{ $quan->ten_quan }}'}" vừa bị Ban quản trị từ chối duyệt hoặc xóa khỏi hệ thống.${noteInfo}<br><br>Hệ thống sẽ chuyển bạn về trang quản lý cửa hàng.`,
                        type: 'error',
                        onOk: () => {
                            window.location.href = '{{ route('chu-quan.quan.index') }}';
                        }
                    });
                } else {
                    alert('Cơ sở ẩm thực đã bị Ban quản trị từ chối duyệt hoặc xóa khỏi hệ thống.');
                    window.location.href = '{{ route('chu-quan.quan.index') }}';
                }

                setTimeout(() => {
                    window.location.href = '{{ route('chu-quan.quan.index') }}';
                }, 2500);
            }
        }
    });
</script>
@endpush
