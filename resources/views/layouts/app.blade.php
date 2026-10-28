<!DOCTYPE html>
<html lang="vi" class="light">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0, viewport-fit=cover" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quán Mới - Khám phá tinh hoa ẩm thực địa phương')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preload" href="{{ asset('fonts/material-symbols-outlined.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&family=Raleway:wght@500;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=block" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .material-symbols-outlined.fill-1,
        .material-symbols-outlined.fill-icon,
        .material-symbols-outlined.material-symbols-filled,
        .material-symbols-filled {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
    @stack('styles')
    @stack('seo')
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col relative antialiased selection:bg-primary-fixed selection:text-on-primary-fixed pt-[72px]">
    <!-- Global Header -->
    @hasSection('header')
        @yield('header')
    @else
        @include('layouts.navigation')
    @endif

    <!-- Main Dynamic Content -->
   
    @yield('content')


    <!-- Global Footer -->
    @hasSection('footer')
        @yield('footer')
    @else
        @include('layouts.footer')
    @endif

    <!-- Location Search Modal Widget -->
    @include('layouts.location-modal')
    
    <!-- Logout Modal Widget -->
    @include('layouts.logout-modal')

    <!-- Global Toast Widget -->
    @include('layouts.toast')

    <!-- Scroll to Top Button -->
    <button id="scrollToTopBtn" 
        class="fixed bottom-24 right-6 z-40 p-3 bg-primary text-white rounded-full shadow-[0_4px_14px_rgba(160,65,0,0.25)] opacity-0 translate-y-10 pointer-events-none transition-all duration-300 hover:bg-primary/90 hover:-translate-y-1 hover:shadow-[0_8px_24px_rgba(160,65,0,0.35)] focus:outline-none flex items-center justify-center group"
        title="Lên đầu trang"
        aria-label="Lên đầu trang">
        <span class="material-symbols-outlined text-[24px] group-hover:-translate-y-0.5 transition-transform">arrow_upward</span>
    </button>

    <!-- Floating AI Chatbot Widget (Bottom Right) -->
    @include('layouts.ai-chatbot')

    <script>
        window.toggleSave = async function(btn, quanId) {
            const card = btn.closest('a, article, .group');
            const quanName = card?.querySelector('h3, h4')?.textContent?.trim() || '';
            const quanImg = card?.querySelector('img')?.getAttribute('src') || null;

            @guest
                if (window.showToast) {
                    window.showToast({
                        eyebrow: 'Lưu địa điểm yêu thích',
                        title: 'Đăng nhập để lưu quán',
                        message: quanName ? `Đăng nhập ngay để lưu "${quanName}" vào bộ sưu tập của bạn.` : 'Vui lòng đăng nhập để lưu quán vào danh sách yêu thích.',
                        type: 'warning',
                        image: quanImg,
                        actionText: 'Đăng nhập ngay',
                        actionUrl: "{{ route('login') }}"
                    });
                } else {
                    window.location.href = "{{ route('login') }}";
                }
                return;
            @endguest

            if (btn.dataset.loading === 'true') return;

            // Đồng bộ trạng thái tất cả các nút lưu cùng ID quán trên trang
            const matchingButtons = document.querySelectorAll(`button[data-quan-id="${quanId}"]`);
            const primaryIcon = btn.querySelector('.material-symbols-outlined');
            const isIconFilled = (el) => el && /['"]?FILL['"]?\s*1/i.test(el.style.fontVariationSettings || el.getAttribute('style') || '');
            const currentlySaved = btn.dataset.saved !== undefined
                ? btn.dataset.saved === '1'
                : isIconFilled(primaryIcon);
            const targetSaved = !currentlySaved;

            const applySaveState = (state) => {
                matchingButtons.forEach(b => {
                    b.dataset.saved = state ? '1' : '0';
                    b.title = state ? 'Bỏ lưu quán' : 'Lưu quán';
                    const icon = b.querySelector('.material-symbols-outlined');
                    if (icon) {
                        icon.style.fontVariationSettings = state ? "'FILL' 1" : "'FILL' 0";
                    }
                });
            };

            // 1. Phản hồi giao diện tức thì (Optimistic UI)
            applySaveState(targetSaved);
            matchingButtons.forEach(b => {
                b.style.transform = 'scale(1.25)';
                setTimeout(() => { b.style.transform = 'scale(1)'; }, 180);
            });

            // 2. Hiển thị Toast thông báo
            if (window.showToast) {
                if (targetSaved) {
                    window.showToast({
                        eyebrow: 'Bộ sưu tập yêu thích',
                        title: quanName ? `Đã lưu "${quanName}"` : 'Đã lưu quán thành công!',
                        message: 'Địa điểm đã được thêm vào danh sách quán yêu thích của bạn.',
                        type: 'saved',
                        image: quanImg,
                        actionText: 'Xem quán đã lưu',
                        actionUrl: "{{ route('quan-da-luu.index') }}"
                    });
                } else {
                    window.showToast({
                        eyebrow: 'Đã cập nhật danh sách',
                        title: quanName ? `Đã bỏ lưu "${quanName}"` : 'Đã bỏ lưu quán',
                        message: 'Địa điểm đã được gỡ khỏi danh sách yêu thích.',
                        type: 'unsaved',
                        image: quanImg
                    });
                }
            }

            matchingButtons.forEach(b => b.dataset.loading = 'true');

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch(`/quan-da-luu/${quanId}/toggle`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                });

                if (res.status === 401) {
                    applySaveState(currentlySaved);
                    window.location.href = "{{ route('login') }}";
                    return;
                }

                const data = await res.json();
                if (data.success) {
                    const finalSaved = data.status === 'saved';
                    applySaveState(finalSaved);
                } else {
                    applySaveState(currentlySaved);
                    if (window.showToast) {
                        window.showToast({
                            title: 'Không thể thực hiện',
                            message: data.message || 'Đã có lỗi xảy ra, vui lòng thử lại.',
                            type: 'error'
                        });
                    }
                }
            } catch (err) {
                console.error(err);
                applySaveState(currentlySaved);
                if (window.showToast) {
                    window.showToast({
                        title: 'Lỗi kết nối',
                        message: 'Không thể gửi yêu cầu đến máy chủ, vui lòng thử lại.',
                        type: 'error'
                    });
                }
            } finally {
                matchingButtons.forEach(b => b.dataset.loading = 'false');
            }
        };

        // Lưu / Bỏ lưu bài viết blog yêu thích (Bookmark / Đọc sau - Độc lập)
        window.toggleBlogBookmark = async function(btn, blogId, blogSlug, blogTitle) {
            const card = btn.closest('a, article, .group, div');
            const title = blogTitle || card?.querySelector('h1, h3, h4, h5')?.textContent?.trim() || 'Bài viết';
            const img = card?.querySelector('img')?.getAttribute('src') || null;

            @guest
                if (window.showToast) {
                    window.showToast({
                        eyebrow: 'Lưu bài viết yêu thích',
                        title: 'Đăng nhập để lưu bài viết',
                        message: title ? `Đăng nhập ngay để lưu bài viết "${title}" vào trang bài viết đã lưu của bạn.` : 'Vui lòng đăng nhập để lưu bài viết vào danh sách đọc sau.',
                        type: 'warning',
                        image: img,
                        actionText: 'Đăng nhập ngay',
                        actionUrl: "{{ route('login') }}"
                    });
                } else {
                    window.location.href = "{{ route('login') }}";
                }
                return;
            @endguest

            const key = 'quanmoi_blog_bookmarks';
            let bookmarks = [];
            try {
                bookmarks = JSON.parse(localStorage.getItem(key) || '[]');
            } catch(e) {
                bookmarks = [];
            }

            const identifier = String(blogSlug || blogId);
            const index = bookmarks.indexOf(identifier);
            const isSaved = index > -1;
            const nextSaved = !isSaved;

            if (nextSaved) {
                bookmarks.push(identifier);
            } else {
                bookmarks.splice(index, 1);
            }

            try {
                localStorage.setItem(key, JSON.stringify(bookmarks));
            } catch(e) {}

            // Cập nhật tất cả các nút cùng bài viết trên trang
            const selector = `[data-blog-id="${blogId}"], [data-blog-slug="${blogSlug}"]`;
            document.querySelectorAll(selector).forEach(b => {
                b.dataset.saved = nextSaved ? '1' : '0';
                b.setAttribute('title', nextSaved ? 'Bỏ lưu bài viết' : 'Lưu bài viết');
                const icon = b.querySelector('.material-symbols-outlined');
                if (icon) {
                    icon.style.fontVariationSettings = nextSaved ? "'FILL' 1" : "'FILL' 0";
                    if (nextSaved) {
                        icon.classList.add('text-amber-500');
                    } else {
                        icon.classList.remove('text-amber-500');
                    }
                }
                const label = b.querySelector('.blog-bookmark-text');
                if (label) {
                    label.textContent = nextSaved ? 'Đã lưu' : 'Lưu bài viết';
                }
                b.style.transform = 'scale(1.2)';
                setTimeout(() => { b.style.transform = 'scale(1)'; }, 180);
            });

            // Toast thông báo kèm nút "Xem trang bài viết đã lưu"
            if (window.showToast) {
                if (nextSaved) {
                    window.showToast({
                        eyebrow: 'Lưu bài viết yêu thích',
                        title: title ? `Đã lưu "${title}"` : 'Đã lưu bài viết!',
                        message: 'Bài viết đã được chuyển vào trang "Bài viết đã lưu" của bạn.',
                        type: 'bookmark',
                        image: img,
                        actionText: 'Xem trang bài viết đã lưu',
                        actionUrl: "{{ route('nguoi-dung.blog.saved') }}",
                        duration: 5000
                    });
                } else {
                    window.showToast({
                        eyebrow: 'Đã bỏ lưu bài viết',
                        title: title ? `Đã bỏ lưu "${title}"` : 'Đã bỏ lưu bài viết',
                        message: 'Bài viết đã được gỡ khỏi trang "Bài viết đã lưu".',
                        type: 'unbookmark',
                        image: img,
                        duration: 3500
                    });
                }
            }

            @auth
            if (blogId) {
                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                    const res = await fetch(`/thanh-vien/blog/${blogId}/bookmark`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (data) {
                        const totalCount = typeof data.total_count !== 'undefined' ? data.total_count : null;
                        if (totalCount !== null) {
                            document.querySelectorAll('.blog-saved-total-count-badge, #sidebar-saved-blog-count').forEach(badge => {
                                badge.textContent = totalCount > 99 ? '99+' : totalCount;
                                if (totalCount > 0) {
                                    badge.classList.remove('hidden');
                                } else {
                                    badge.classList.add('hidden');
                                }
                            });
                        }

                        // Phát broadcast sync để các tab khác đồng bộ số lượng và icon
                        if ('BroadcastChannel' in window) {
                            try {
                                const bc = new BroadcastChannel('qm_blog_bookmarks_sync');
                                bc.postMessage({
                                    blogId: blogId,
                                    blogSlug: blogSlug,
                                    saved: nextSaved,
                                    totalCount: totalCount
                                });
                                setTimeout(() => bc.close(), 1000);
                            } catch(e) {}
                        }
                    }
                } catch(err) {
                    console.warn('Lỗi đồng bộ bookmark lên máy chủ:', err);
                }
            }
            @endauth
        };

        @auth
        @php
            $authSavedBlogs = Auth::user()->savedBlogs()->where('blogs.status', 'published')->select('blogs.id', 'blogs.slug')->get();
        @endphp
        window.__SERVER_SAVED_BLOGS = @json($authSavedBlogs);
        @endauth

        // Tự động khôi phục trạng thái bookmark chuẩn xác khi tải trang
        function initBlogBookmarks() {
            try {
                const key = 'quanmoi_blog_bookmarks';
                let bookmarks = [];

                // Khi đã đăng nhập, cơ sở dữ liệu là nguồn chuẩn xác nhất
                if (window.__SERVER_SAVED_BLOGS && Array.isArray(window.__SERVER_SAVED_BLOGS)) {
                    bookmarks = [];
                    window.__SERVER_SAVED_BLOGS.forEach(b => {
                        if (b.slug) bookmarks.push(String(b.slug));
                        if (b.id) bookmarks.push(String(b.id));
                    });
                    try {
                        localStorage.setItem(key, JSON.stringify(bookmarks));
                    } catch(e) {}
                } else {
                    try {
                        bookmarks = JSON.parse(localStorage.getItem(key) || '[]');
                    } catch(e) {
                        bookmarks = [];
                    }
                }

                const set = new Set(bookmarks.map(String));
                document.querySelectorAll('.blog-bookmark-btn').forEach(b => {
                    const id = b.dataset.blogId ? String(b.dataset.blogId) : '';
                    const slug = b.dataset.blogSlug ? String(b.dataset.blogSlug) : '';
                    const isSaved = (id && set.has(id)) || (slug && set.has(slug));

                    b.dataset.saved = isSaved ? '1' : '0';
                    b.setAttribute('title', isSaved ? 'Bỏ lưu bài viết' : 'Lưu bài viết');
                    const icon = b.querySelector('.material-symbols-outlined');
                    if (icon) {
                        icon.style.fontVariationSettings = isSaved ? "'FILL' 1" : "'FILL' 0";
                        if (isSaved) {
                            icon.classList.add('text-amber-500');
                        } else {
                            icon.classList.remove('text-amber-500');
                        }
                    }
                    const label = b.querySelector('.blog-bookmark-text');
                    if (label) {
                        label.textContent = isSaved ? 'Đã lưu' : 'Lưu bài viết';
                    }
                });
            } catch(e) {}
        }

        // Lắng nghe broadcast đồng bộ thời gian thực giữa các tab
        if ('BroadcastChannel' in window) {
            try {
                const syncBc = new BroadcastChannel('qm_blog_bookmarks_sync');
                syncBc.onmessage = function(ev) {
                    const msg = ev.data;
                    if (!msg) return;
                    if (typeof msg.totalCount !== 'undefined' && msg.totalCount !== null) {
                        document.querySelectorAll('.blog-saved-total-count-badge, #sidebar-saved-blog-count').forEach(badge => {
                            badge.textContent = msg.totalCount > 99 ? '99+' : msg.totalCount;
                            if (msg.totalCount > 0) badge.classList.remove('hidden');
                            else badge.classList.add('hidden');
                        });
                    }
                    if (msg.blogId || msg.blogSlug) {
                        const selector = `[data-blog-id="${msg.blogId}"], [data-blog-slug="${msg.blogSlug}"]`;
                        document.querySelectorAll(selector).forEach(b => {
                            if (!b.classList.contains('blog-bookmark-btn')) return;
                            b.dataset.saved = msg.saved ? '1' : '0';
                            b.setAttribute('title', msg.saved ? 'Bỏ lưu bài viết' : 'Lưu bài viết');
                            const icon = b.querySelector('.material-symbols-outlined');
                            if (icon) {
                                icon.style.fontVariationSettings = msg.saved ? "'FILL' 1" : "'FILL' 0";
                                if (msg.saved) icon.classList.add('text-amber-500');
                                else icon.classList.remove('text-amber-500');
                            }
                        });
                    }
                };
            } catch(e) {}
        }

        document.addEventListener('DOMContentLoaded', initBlogBookmarks);
        window.addEventListener('pageshow', initBlogBookmarks);
    </script>
    @stack('scripts')
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scrollToTopBtn = document.getElementById('scrollToTopBtn');

            if (scrollToTopBtn) {
                window.addEventListener('scroll', () => {
                    if (window.scrollY > 300) {
                        scrollToTopBtn.classList.remove('opacity-0', 'translate-y-10', 'pointer-events-none');
                        scrollToTopBtn.classList.add('opacity-100', 'translate-y-0');
                    } else {
                        scrollToTopBtn.classList.add('opacity-0', 'translate-y-10', 'pointer-events-none');
                        scrollToTopBtn.classList.remove('opacity-100', 'translate-y-0');
                    }
                });

                scrollToTopBtn.addEventListener('click', () => {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }
        });
    </script>
</body>
</html>
