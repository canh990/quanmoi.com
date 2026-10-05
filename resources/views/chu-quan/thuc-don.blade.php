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
                        renderItemHtml(catId, itemId, item.ten_mon, item.gia, item.mo_ta || '', item.hinh_anh, item.shopeefood_url || '');
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
                    <input type="text" placeholder="Tên danh mục (vd: Khai vị, Món chính)..." value="${escapeAttribute(name)}" oninput="updateCategoryName(${catId}, this.value)" class="bg-transparent font-bold text-gray-800 outline-none w-2/3" required />
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
        const imagePreview = image ? (image.startsWith('http') ? image : `${r2MenuImageBase}/${image.replace(/^\/+/, '')}`) : '';
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
                        <input type="text" placeholder="Tên món ăn..." value="${escapeAttribute(name)}" oninput="updateItem(${catId}, ${itemId}, 'name', this.value)" class="flex-1 border border-gray-200 rounded px-2 py-1.5 text-sm outline-none focus:border-primary" required />
                        <input type="number" placeholder="Giá (VNĐ)" value="${escapeAttribute(price)}" oninput="updateItem(${catId}, ${itemId}, 'price', this.value)" class="w-28 border border-gray-200 rounded px-2 py-1.5 text-sm outline-none focus:border-primary" />
                    </div>
                    <input type="text" placeholder="Mô tả ngắn (tùy chọn)..." value="${escapeAttribute(desc)}" oninput="updateItem(${catId}, ${itemId}, 'description', this.value)" class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm outline-none focus:border-primary text-gray-500" />
                    <input type="url" placeholder="Link ShopeeFood của món (tùy chọn)..." value="${escapeAttribute(shopeefoodUrl)}" oninput="updateItem(${catId}, ${itemId}, 'shopeefood_url', this.value)" class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm outline-none focus:border-primary text-gray-500" />
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

    function removeCategory(catId) {
        if (!confirm('Bạn có chắc muốn xóa danh mục này?')) return;
        menuCategories = menuCategories.filter(c => c.id !== catId);
        document.getElementById(`category-box-${catId}`).remove();
        if (menuCategories.length === 0) {
            document.getElementById('empty-menu-state')?.classList.remove('hidden');
        }
    }

    function updateCategoryName(catId, val) {
        const cat = menuCategories.find(c => c.id === catId);
        if (cat) cat.name = val;
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
            const reader = new FileReader();
            reader.onload = function(e) {
                const container = document.getElementById(`preview_container_${itemId}`);
                container.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover rounded" />`;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeMenuItem(catId, itemId) {
        const cat = menuCategories.find(c => c.id === catId);
        if (cat) {
            cat.items = cat.items.filter(i => i.id !== itemId);
            document.getElementById(`item-box-${itemId}`).remove();
        }
    }

    function updateItem(catId, itemId, field, val) {
        const cat = menuCategories.find(c => c.id === catId);
        if (cat) {
            const item = cat.items.find(i => i.id === itemId);
            if (item) item[field] = val;
        }
    }

    async function submitMenuForm() {
        const form = document.getElementById('thuc-don-form');
        
        if (!form.reportValidity()) return;

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
                if (data && data.errors) {
                    const firstErrKey = Object.keys(data.errors)[0];
                    errGen.textContent = data.errors[firstErrKey][0];
                } else {
                    errGen.textContent = (data && data.message) ? data.message : 'Đã có lỗi xảy ra. Vui lòng kiểm tra lại.';
                }
                errGen.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = originalBtnText;
                return;
            }

            btn.innerHTML = '<span class="material-symbols-outlined text-[20px]" style="font-variation-settings: \'FILL\' 1;">check_circle</span> Hoàn tất!';
            setTimeout(() => {
                window.location.href = data.redirect_to || '/';
            }, 1000);
        } catch (e) {
            errGen.textContent = 'Lỗi kết nối hệ thống.';
            errGen.classList.remove('hidden');
            btn.disabled = false;
            btn.innerHTML = originalBtnText;
        }
    }
</script>
@endpush
