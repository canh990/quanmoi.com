{{-- ==================== COVER IMAGE CROPPER & EDITOR MODAL ==================== --}}


<style>
    /* Tùy biến khung cắt Cropper.js sang phong cách Quán Mới */
    .cropper-view-box {
        outline: 2px solid #f59e0b !important;
        outline-color: rgba(245, 158, 11, 0.95) !important;
        border-radius: 4px;
    }
    .cropper-line {
        background-color: #f59e0b !important;
    }
    .cropper-point {
        background-color: #f59e0b !important;
        width: 10px !important;
        height: 10px !important;
        border-radius: 50% !important;
    }
    .cropper-dashed {
        border-color: rgba(255, 255, 255, 0.45) !important;
    }
    .cropper-bg {
        background-image: repeating-linear-gradient(45deg, #1e293b 25%, transparent 25%, transparent 75%, #1e293b 75%, #1e293b), repeating-linear-gradient(45deg, #1e293b 25%, #0f172a 25%, #0f172a 75%, #1e293b 75%, #1e293b) !important;
        background-position: 0 0, 10px 10px !important;
        background-size: 20px 20px !important;
    }
    .filter-btn.active {
        border-color: #f59e0b !important;
        background-color: rgba(245, 158, 11, 0.25) !important;
        color: #fef3c7 !important;
    }
    .ratio-btn.active {
        border-color: #f59e0b !important;
        background-color: #f59e0b !important;
        color: #ffffff !important;
    }
</style>

<!-- Modal Container (Overlay z-[99999]) -->
<div id="blogCoverCropperModal" class="fixed inset-0 z-[99999] hidden overflow-y-auto bg-slate-950/85 backdrop-blur-md p-2 sm:p-4 md:p-6" aria-labelledby="cropper-modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-full items-center justify-center p-0 text-center">
        <div class="relative w-full max-w-4xl bg-slate-900 rounded-3xl shadow-2xl border border-slate-700/80 overflow-hidden flex flex-col text-left text-slate-100 max-h-[96vh]">
            
            <!-- Modal Header -->
            <div class="px-5 py-3.5 sm:px-6 sm:py-4 bg-slate-800/90 border-b border-slate-700/80 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white flex items-center justify-center shadow-md shrink-0">
                        <span class="material-symbols-outlined text-2xl">crop</span>
                    </div>
                    <div>
                        <h3 id="cropper-modal-title" class="text-base sm:text-lg font-extrabold text-white flex items-center gap-2">
                            Chỉnh Sửa & Cắt Ảnh Bìa
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">Chuẩn 21:9</span>
                        </h3>
                        <p class="text-xs text-slate-400 font-medium">Căn chỉnh góc đẹp nhất, xoay lật, thu phóng và lọc màu sắc</p>
                    </div>
                </div>
                
                <button type="button" onclick="closeCoverCropperModal()" class="w-9 h-9 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition-all cursor-pointer border border-slate-700" title="Đóng">
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
            </div>

            <!-- Cropper Work Area -->
            <div class="relative flex-1 bg-slate-950 flex items-center justify-center overflow-hidden p-2 min-h-[300px] sm:min-h-[380px] max-h-[52vh]">
                <div class="w-full h-full flex items-center justify-center">
                    <img id="cover-cropper-image" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" alt="Ảnh chỉnh sửa" style="display: block; max-width: 100%; max-height: 100%;">
                </div>

                <!-- Loading Spinner -->
                <div id="cropper-loading" class="absolute inset-0 bg-slate-950/90 flex flex-col items-center justify-center gap-3 z-10 hidden">
                    <div class="w-9 h-9 border-3 border-amber-500 border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-xs font-semibold text-slate-400">Đang tải ảnh vào trình chỉnh sửa...</p>
                </div>
            </div>

            <!-- Toolbar Controls -->
            <div class="p-3 sm:p-5 bg-slate-900 border-t border-slate-800 space-y-3 shrink-0">
                
                <!-- Row 1: Aspect Ratio Selectors -->
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5 text-xs font-bold text-slate-400 uppercase tracking-wider shrink-0">
                        <span class="material-symbols-outlined text-sm text-amber-400">aspect_ratio</span>
                        <span>Tỷ lệ khung:</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                        <button type="button" onclick="setCropperAspectRatio(21/9, this)" class="ratio-btn active px-3 py-1.5 text-xs font-bold rounded-xl border border-slate-700 bg-slate-800 text-slate-300 hover:border-amber-500 hover:text-amber-400 transition-all flex items-center gap-1 cursor-pointer">
                            <span>21:9</span>
                            <span class="text-[10px] opacity-80">(Bìa Blog)</span>
                        </button>
                        <button type="button" onclick="setCropperAspectRatio(16/9, this)" class="ratio-btn px-3 py-1.5 text-xs font-bold rounded-xl border border-slate-700 bg-slate-800 text-slate-300 hover:border-amber-500 hover:text-amber-400 transition-all cursor-pointer">
                            16:9
                        </button>
                        <button type="button" onclick="setCropperAspectRatio(4/3, this)" class="ratio-btn px-3 py-1.5 text-xs font-bold rounded-xl border border-slate-700 bg-slate-800 text-slate-300 hover:border-amber-500 hover:text-amber-400 transition-all cursor-pointer">
                            4:3
                        </button>
                        <button type="button" onclick="setCropperAspectRatio(1, this)" class="ratio-btn px-3 py-1.5 text-xs font-bold rounded-xl border border-slate-700 bg-slate-800 text-slate-300 hover:border-amber-500 hover:text-amber-400 transition-all cursor-pointer">
                            1:1 (Vuông)
                        </button>
                        <button type="button" onclick="setCropperAspectRatio(NaN, this)" class="ratio-btn px-3 py-1.5 text-xs font-bold rounded-xl border border-slate-700 bg-slate-800 text-slate-300 hover:border-amber-500 hover:text-amber-400 transition-all cursor-pointer">
                            Tự do
                        </button>
                    </div>
                </div>

                <!-- Row 2: Action Tool Buttons & Preset Filters -->
                <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-800/80">
                    <!-- Transform Buttons -->
                    <div class="flex items-center flex-wrap gap-1.5">
                        <button type="button" onclick="rotateCropper(-90)" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition-all cursor-pointer flex items-center justify-center" title="Xoay trái 90°">
                            <span class="material-symbols-outlined text-lg">rotate_left</span>
                        </button>
                        <button type="button" onclick="rotateCropper(90)" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition-all cursor-pointer flex items-center justify-center" title="Xoay phải 90°">
                            <span class="material-symbols-outlined text-lg">rotate_right</span>
                        </button>
                        <button type="button" onclick="flipCropperX()" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition-all cursor-pointer flex items-center justify-center" title="Lật ngang">
                            <span class="material-symbols-outlined text-lg">swap_horiz</span>
                        </button>
                        <button type="button" onclick="flipCropperY()" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition-all cursor-pointer flex items-center justify-center" title="Lật dọc">
                            <span class="material-symbols-outlined text-lg">swap_vert</span>
                        </button>
                        <button type="button" onclick="zoomCropper(-0.1)" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition-all cursor-pointer flex items-center justify-center" title="Thu nhỏ">
                            <span class="material-symbols-outlined text-lg">zoom_out</span>
                        </button>
                        <button type="button" onclick="zoomCropper(0.1)" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition-all cursor-pointer flex items-center justify-center" title="Phóng to">
                            <span class="material-symbols-outlined text-lg">zoom_in</span>
                        </button>
                        <button type="button" onclick="resetCropper()" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 text-xs font-bold transition-all cursor-pointer flex items-center gap-1" title="Khôi phục gốc">
                            <span class="material-symbols-outlined text-base">restart_alt</span>
                            <span class="hidden sm:inline">Đặt lại</span>
                        </button>
                    </div>

                    <!-- Preset Color Filters -->
                    <div class="flex items-center gap-1.5 overflow-x-auto py-1">
                        <span class="text-[11px] font-bold text-slate-500 uppercase mr-1 hidden lg:inline">Bộ lọc:</span>
                        <button type="button" onclick="setCropperFilter('none', this)" class="filter-btn active px-2.5 py-1 text-xs font-bold rounded-lg border border-slate-700 bg-slate-800 text-slate-300 hover:text-white transition-all cursor-pointer">
                            Gốc
                        </button>
                        <button type="button" onclick="setCropperFilter('vibrant', this)" class="filter-btn px-2.5 py-1 text-xs font-bold rounded-lg border border-slate-700 bg-slate-800 text-slate-300 hover:text-white transition-all cursor-pointer" title="Tươi sáng, ngon mắt">
                            ✨ Tươi tắn
                        </button>
                        <button type="button" onclick="setCropperFilter('warm', this)" class="filter-btn px-2.5 py-1 text-xs font-bold rounded-lg border border-slate-700 bg-slate-800 text-slate-300 hover:text-white transition-all cursor-pointer" title="Ấm cúng ẩm thực">
                            🍜 Ấm áp
                        </button>
                        <button type="button" onclick="setCropperFilter('vintage', this)" class="filter-btn px-2.5 py-1 text-xs font-bold rounded-lg border border-slate-700 bg-slate-800 text-slate-300 hover:text-white transition-all cursor-pointer" title="Cổ điển hoài niệm">
                            🎞️ Cổ điển
                        </button>
                        <button type="button" onclick="setCropperFilter('bw', this)" class="filter-btn px-2.5 py-1 text-xs font-bold rounded-lg border border-slate-700 bg-slate-800 text-slate-300 hover:text-white transition-all cursor-pointer" title="Đen trắng nghệ thuật">
                            🖤 Đen trắng
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-5 py-3.5 sm:px-6 sm:py-4 bg-slate-800/90 border-t border-slate-700/80 flex items-center justify-end gap-3 shrink-0">
                <button type="button" onclick="closeCoverCropperModal()" class="px-4 py-2 rounded-xl text-slate-300 hover:text-white hover:bg-slate-700 text-xs font-bold transition-all cursor-pointer">
                    Hủy bỏ
                </button>
                
                <button type="button" onclick="applyCoverCrop()" id="btn-apply-crop" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white text-xs font-extrabold shadow-lg shadow-amber-500/20 transition-all flex items-center justify-center gap-1.5 cursor-pointer active:scale-95">
                    <span class="material-symbols-outlined text-base">check_circle</span>
                    <span>Áp dụng & Lưu ảnh bìa</span>
                </button>
            </div>

        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>

<script>
    // Biến toàn cục quản lý Cropper
    let coverCropper = null;
    let coverCropperContext = 'modal'; // 'modal' hoặc 'page'
    let currentCropperRatio = 21 / 9;
    let currentCropperFilter = 'none';
    let cropperScaleX = 1;
    let cropperScaleY = 1;

    /**
     * Đảm bảo thư viện Cropper.js đã sẵn sàng
     */
    function ensureCropperReady(callback) {
        if (typeof Cropper !== 'undefined') {
            callback();
            return;
        }
        let attempts = 0;
        const interval = setInterval(function() {
            attempts++;
            if (typeof Cropper !== 'undefined') {
                clearInterval(interval);
                callback();
            } else if (attempts > 50) {
                clearInterval(interval);
                console.error('Không thể tải thư viện Cropper.js');
                const loading = document.getElementById('cropper-loading');
                if (loading) loading.classList.add('hidden');
                alert('Thư viện chỉnh sửa ảnh chưa sẵn sàng. Vui lòng tải lại trang.');
            }
        }, 60);
    }

    /**
     * Mở Modal Chỉnh Sửa Ảnh Bìa
     */
    function openCoverImageCropper(imageSrc, context = 'modal') {
        if (!imageSrc) return;
        
        coverCropperContext = context;
        currentCropperFilter = 'none';
        cropperScaleX = 1;
        cropperScaleY = 1;

        const modal = document.getElementById('blogCoverCropperModal');
        const img = document.getElementById('cover-cropper-image');
        const loading = document.getElementById('cropper-loading');

        if (!modal || !img) return;

        // Reset filter UI
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        const defaultFilterBtn = document.querySelector('.filter-btn');
        if (defaultFilterBtn) defaultFilterBtn.classList.add('active');

        // Reset ratio UI
        document.querySelectorAll('.ratio-btn').forEach(b => b.classList.remove('active'));
        const defaultRatioBtn = document.querySelector('.ratio-btn');
        if (defaultRatioBtn) defaultRatioBtn.classList.add('active');
        currentCropperRatio = 21 / 9;

        // Hiển thị modal
        modal.classList.remove('hidden');
        modal.classList.add('block');
        if (loading) loading.classList.remove('hidden');

        // Hủy cropper cũ nếu còn
        if (coverCropper) {
            try { coverCropper.destroy(); } catch(e){}
            coverCropper = null;
        }

        img.onload = null;
        img.onerror = null;

        ensureCropperReady(function() {
            img.onload = function() {
                if (loading) loading.classList.add('hidden');
                try {
                    if (coverCropper) {
                        try { coverCropper.destroy(); } catch(e){}
                        coverCropper = null;
                    }
                    coverCropper = new Cropper(img, {
                        aspectRatio: currentCropperRatio,
                        viewMode: 1,
                        dragMode: 'move',
                        autoCropArea: 0.95,
                        restore: false,
                        guides: true,
                        center: true,
                        highlight: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                        responsive: true,
                        ready: function() {
                            if (loading) loading.classList.add('hidden');
                        }
                    });
                } catch(err) {
                    console.error('Cropper init error:', err);
                    if (loading) loading.classList.add('hidden');
                }
            };

            img.onerror = function() {
                if (loading) loading.classList.add('hidden');
                console.warn('Lỗi khi tải ảnh vào thẻ img trong Cropper');
                if (img.hasAttribute('crossorigin')) {
                    img.removeAttribute('crossorigin');
                    img.src = imageSrc;
                    return;
                }
                alert('Không thể tải ảnh này vào trình chỉnh sửa. Bạn vui lòng chọn file ảnh trực tiếp từ máy tính nhé.');
                closeCoverCropperModal();
            };

            // Đảm bảo không gán crossOrigin nếu là Data URL hoặc Blob URL
            if (imageSrc.startsWith('data:') || imageSrc.startsWith('blob:')) {
                img.removeAttribute('crossorigin');
            } else {
                img.crossOrigin = 'anonymous';
            }

            img.src = imageSrc;
        });
    }

    /**
     * Đóng Modal Cropper
     */
    function closeCoverCropperModal() {
        const modal = document.getElementById('blogCoverCropperModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('block');
        }
        if (coverCropper) {
            try { coverCropper.destroy(); } catch(e){}
            coverCropper = null;
        }
    }

    /**
     * Thay đổi tỷ lệ khung cắt (Aspect Ratio)
     */
    function setCropperAspectRatio(ratio, btn) {
        if (!coverCropper) return;
        currentCropperRatio = ratio;
        coverCropper.setAspectRatio(ratio);

        document.querySelectorAll('.ratio-btn').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');
    }

    /**
     * Xoay ảnh
     */
    function rotateCropper(degree) {
        if (!coverCropper) return;
        coverCropper.rotate(degree);
    }

    /**
     * Lật ngang ảnh
     */
    function flipCropperX() {
        if (!coverCropper) return;
        cropperScaleX = -cropperScaleX;
        coverCropper.scaleX(cropperScaleX);
    }

    /**
     * Lật dọc ảnh
     */
    function flipCropperY() {
        if (!coverCropper) return;
        cropperScaleY = -cropperScaleY;
        coverCropper.scaleY(cropperScaleY);
    }

    /**
     * Thu phóng ảnh
     */
    function zoomCropper(ratio) {
        if (!coverCropper) return;
        coverCropper.zoom(ratio);
    }

    /**
     * Đặt lại ban đầu
     */
    function resetCropper() {
        if (!coverCropper) return;
        coverCropper.reset();
        cropperScaleX = 1;
        cropperScaleY = 1;
        setCropperAspectRatio(21 / 9, document.querySelector('.ratio-btn'));
        setCropperFilter('none', document.querySelector('.filter-btn'));
    }

    /**
     * Áp dụng bộ lọc màu
     */
    function setCropperFilter(filterName, btn) {
        currentCropperFilter = filterName;
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');

        const viewCanvas = document.querySelector('.cropper-view-box img');
        if (viewCanvas) {
            if (filterName === 'vibrant') {
                viewCanvas.style.filter = 'contrast(1.15) saturate(1.25) brightness(1.05)';
            } else if (filterName === 'warm') {
                viewCanvas.style.filter = 'sepia(0.25) saturate(1.15) brightness(1.05)';
            } else if (filterName === 'vintage') {
                viewCanvas.style.filter = 'sepia(0.45) contrast(0.95) brightness(0.98)';
            } else if (filterName === 'bw') {
                viewCanvas.style.filter = 'grayscale(1) contrast(1.2)';
            } else {
                viewCanvas.style.filter = 'none';
            }
        }
    }

    /**
     * Cắt ảnh và Áp dụng vào Form
     */
    function applyCoverCrop() {
        if (!coverCropper) return;

        const btnApply = document.getElementById('btn-apply-crop');
        const originalBtnHtml = btnApply ? btnApply.innerHTML : '';
        if (btnApply) {
            btnApply.disabled = true;
            btnApply.innerHTML = `<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div><span>Đang lưu ảnh...</span>`;
        }

        try {
            const croppedCanvas = coverCropper.getCroppedCanvas({
                maxWidth: 2048,
                maxHeight: 1200,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            if (!croppedCanvas) {
                alert('Không thể xử lý ảnh này. Vui lòng thử lại!');
                if (btnApply) {
                    btnApply.disabled = false;
                    btnApply.innerHTML = originalBtnHtml;
                }
                return;
            }

            // Áp dụng bộ lọc màu lên canvas nếu có
            let finalCanvas = croppedCanvas;
            if (currentCropperFilter && currentCropperFilter !== 'none') {
                const filteredCanvas = document.createElement('canvas');
                filteredCanvas.width = croppedCanvas.width;
                filteredCanvas.height = croppedCanvas.height;
                const ctx = filteredCanvas.getContext('2d');

                if (currentCropperFilter === 'vibrant') {
                    ctx.filter = 'contrast(1.15) saturate(1.25) brightness(1.05)';
                } else if (currentCropperFilter === 'warm') {
                    ctx.filter = 'sepia(0.25) saturate(1.15) brightness(1.05)';
                } else if (currentCropperFilter === 'vintage') {
                    ctx.filter = 'sepia(0.45) contrast(0.95) brightness(0.98)';
                } else if (currentCropperFilter === 'bw') {
                    ctx.filter = 'grayscale(1) contrast(1.2)';
                }
                ctx.drawImage(croppedCanvas, 0, 0);
                finalCanvas = filteredCanvas;
            }

            // Chuyển đổi sang Blob JPEG chất lượng cao
            finalCanvas.toBlob(function(blob) {
                if (!blob) {
                    alert('Lỗi xuất dữ liệu ảnh. Vui lòng thử lại!');
                    if (btnApply) {
                        btnApply.disabled = false;
                        btnApply.innerHTML = originalBtnHtml;
                    }
                    return;
                }

                const fileName = 'cover_edited_' + Date.now() + '.jpg';
                const editedFile = new File([blob], fileName, { type: 'image/jpeg', lastModified: Date.now() });

                if (coverCropperContext === 'modal') {
                    // Modal viết bài blog
                    const input = document.getElementById('modal_cover_image');
                    if (input && window.DataTransfer) {
                        try {
                            const dt = new DataTransfer();
                            dt.items.add(editedFile);
                            input.files = dt.files;
                        } catch(e) {
                            console.warn('DataTransfer error:', e);
                        }
                    }

                    const preview = document.getElementById('modal-cover-preview');
                    const placeholder = document.getElementById('modal-cover-placeholder');
                    const actionsBar = document.getElementById('modal-cover-actions-bar');
                    const container = document.getElementById('modal-cover-container');

                    if (preview) {
                        preview.src = URL.createObjectURL(blob);
                        preview.classList.remove('hidden');
                    }
                    if (placeholder) placeholder.classList.add('hidden');
                    if (actionsBar) actionsBar.classList.remove('hidden');
                    if (container) {
                        container.classList.remove('border-dashed');
                        container.classList.add('border-solid', 'border-amber-400');
                    }
                } else {
                    // Context 'page' (create hoặc edit bài viết)
                    const input = document.getElementById('cover_image');
                    if (input && window.DataTransfer) {
                        try {
                            const dt = new DataTransfer();
                            dt.items.add(editedFile);
                            input.files = dt.files;
                        } catch(e) {
                            console.warn('DataTransfer error:', e);
                        }
                    }

                    const preview = document.getElementById('cover-preview');
                    const placeholder = document.getElementById('cover-placeholder');
                    const actionsBar = document.getElementById('page-cover-actions-bar');

                    if (preview) {
                        preview.src = URL.createObjectURL(blob);
                        preview.classList.remove('hidden');
                    }
                    if (placeholder) placeholder.classList.add('hidden');
                    if (actionsBar) actionsBar.classList.remove('hidden');
                }

                closeCoverCropperModal();

                if (btnApply) {
                    btnApply.disabled = false;
                    btnApply.innerHTML = originalBtnHtml;
                }

                if (window.showToast) {
                    window.showToast({
                        title: 'Đã cắt & chỉnh sửa ảnh',
                        message: 'Ảnh bìa đã được tối ưu tỷ lệ chuẩn thành công!',
                        type: 'success'
                    });
                }
            }, 'image/jpeg', 0.92);

        } catch (error) {
            console.error('Lỗi khi cắt ảnh:', error);
            if (btnApply) {
                btnApply.disabled = false;
                btnApply.innerHTML = originalBtnHtml;
            }
            alert('Trình duyệt không thể xử lý ảnh này do chính sách bảo mật máy chủ (CORS). Vui lòng chọn ảnh trực tiếp từ máy tính để chỉnh sửa.');
        }
    }
</script>
