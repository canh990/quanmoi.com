<article id="public-blog-card-{{ $blog->id }}" data-blog-id="{{ $blog->id }}" class="level-1-card flex flex-col h-full group border border-outline-variant hover:border-amber-400/80 hover:ring-2 hover:ring-amber-400/30 hover:shadow-lg transition-all duration-300 relative">
    <div class="relative w-full">
        <a href="{{ route('blog.show', $blog->slug) }}" class="blog-card-cover-frame">
            @if($blog->category)
                <div class="absolute top-3 left-3 z-10 bg-primary-container text-on-primary px-2 py-0.5 rounded-md font-label-md text-label-md uppercase font-bold max-w-[calc(100%-4rem)] truncate">{{ $blog->category->name }}</div>
            @endif
            <div class="absolute bottom-3 right-3 z-10 px-2.5 py-0.5 rounded-md bg-black/60 backdrop-blur-md text-white font-semibold text-[10px] flex items-center gap-1 shadow-sm" title="Thời gian đăng: {{ ($blog->published_at ?? $blog->created_at)?->format('d/m/Y H:i') }}">
                <span class="material-symbols-outlined text-[12px] text-amber-300">schedule</span>
                <span>{{ ($blog->published_at ?? $blog->created_at)?->diffForHumans() ?? 'Vừa xong' }}</span>
            </div>
            <img src="{{ $blog->cover_image_url ?? 'https://placehold.co/600x400/png' }}" alt="{{ $blog->title }}"/>
        </a>
        {{-- Nút Bookmark ở góc trên bên phải ảnh --}}
        <button type="button" 
                onclick="event.preventDefault(); event.stopPropagation(); toggleBlogBookmark(this, '{{ $blog->id }}', '{{ $blog->slug }}', '{{ addslashes($blog->title) }}')" 
                data-blog-id="{{ $blog->id }}" 
                data-blog-slug="{{ $blog->slug }}" 
                class="blog-bookmark-btn absolute top-3 right-3 z-10 w-8 h-8 bg-white/90 hover:bg-white backdrop-blur-md rounded-full flex items-center justify-center text-slate-700 hover:text-amber-500 shadow-sm hover:scale-110 active:scale-90 transition-transform cursor-pointer" 
                title="Lưu bài viết"
                aria-label="Lưu bài viết">
            <span class="material-symbols-outlined text-[18px]">bookmark</span>
        </button>
    </div>
    <div class="p-4 flex flex-col flex-grow">
        <a href="{{ route('blog.show', $blog->slug) }}" class="flex-grow block">
            <h3 class="font-title-md text-title-md text-on-surface mb-2 line-clamp-2 group-hover:text-primary transition-colors font-bold">{{ $blog->title }}</h3>
            <p class="text-text-muted font-body-sm text-body-sm mb-4 line-clamp-3">{{ $blog->excerpt ?? Str::limit(strip_tags($blog->content), 120) }}</p>
        </a>
        <div class="mt-auto pt-4 border-t border-outline-variant flex justify-between items-center gap-2">
            <span class="font-label-sm text-label-sm text-text-muted flex items-center gap-1 truncate min-w-0">
                @if($blog->user)
                    <a href="{{ route('blog.author', $blog->user->id) }}" class="hover:text-primary transition-colors font-medium hover:underline truncate" title="Xem bài viết của tác giả {{ $blog->user->ho_ten }}">
                        {{ Str::limit($blog->user->ho_ten, 15) }}
                    </a>
                @else
                    <span>Tác giả</span>
                @endif
                <span>•</span>
                <span class="shrink-0">{{ ($blog->published_at ?? $blog->created_at)?->format('d/m/Y') ?? 'Mới' }}</span>
            </span>
            <div class="font-label-sm text-label-sm text-text-muted flex items-center gap-1.5 sm:gap-2 shrink-0">
                <span class="blog-view-count-wrap flex items-center gap-1 font-semibold text-slate-600 hover:text-amber-700 transition-all px-2 py-0.5 rounded-lg border border-transparent hover:border-amber-400 hover:ring-2 hover:ring-amber-400/30 hover:shadow-xs cursor-pointer whitespace-nowrap" data-blog-slug="{{ $blog->slug }}" data-blog-id="{{ $blog->id }}" title="{{ number_format($blog->view_count ?? 0) }} lượt xem">
                    <span class="material-symbols-outlined text-[15px] text-amber-600">visibility</span>
                    <span class="blog-view-count-text">{{ number_format($blog->view_count ?? 0) }}</span>
                </span>
                <span class="flex items-center gap-1 font-semibold text-slate-600 hover:text-primary transition-all px-2 py-0.5 rounded-lg border border-transparent hover:border-amber-400 hover:ring-2 hover:ring-amber-400/30 hover:shadow-xs cursor-pointer whitespace-nowrap" title="{{ number_format($blog->comment_count ?? 0) }} bình luận">
                    <span class="material-symbols-outlined text-[15px] text-primary">forum</span>
                    <span>{{ number_format($blog->comment_count ?? 0) }}</span>
                </span>
            </div>
        </div>
    </div>
</article>

