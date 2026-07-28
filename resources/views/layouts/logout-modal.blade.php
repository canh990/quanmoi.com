<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="fixed inset-0 z-[9999] hidden items-center justify-center">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity duration-300 opacity-0" id="logoutModalBackdrop" onclick="closeLogoutModal()"></div>
    
    <!-- Modal Content -->
    <div class="bg-white rounded-2xl shadow-2xl w-[90%] max-w-sm relative z-10 transform scale-95 opacity-0 transition-all duration-300 overflow-hidden" id="logoutModalContent">
        <div class="p-6 text-center">
            <!-- Icon -->
            <div class="w-16 h-16 bg-error-container rounded-full mx-auto flex items-center justify-center mb-4 text-error">
                <span class="material-symbols-outlined text-[32px]">logout</span>
            </div>
            
            <h3 class="font-headline-lg-mobile text-headline-lg-mobile text-on-surface mb-2 font-bold">Đăng xuất?</h3>
            <p class="font-body-sm text-body-sm text-on-surface-variant mb-6">Bạn có chắc chắn muốn đăng xuất khỏi Quán Mới không?</p>
            
            <form method="POST" action="{{ route('logout') }}" class="flex flex-col gap-3">
                @csrf
                <button type="submit" class="w-full bg-error text-white font-label-md text-label-md py-3 rounded-xl hover:bg-error/90 active:scale-95 transition-all shadow-md hover:shadow-error/20">
                    CÓ, ĐĂNG XUẤT
                </button>
                <button type="button" onclick="closeLogoutModal()" class="w-full bg-surface-container-low text-on-surface-variant font-label-md text-label-md py-3 rounded-xl hover:bg-surface-container active:scale-95 transition-all">
                    HỦY BỎ
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function openLogoutModal() {
        const modal = document.getElementById('logoutModal');
        const backdrop = document.getElementById('logoutModalBackdrop');
        const content = document.getElementById('logoutModalContent');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Trigger animations
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }
    
    function closeLogoutModal() {
        const modal = document.getElementById('logoutModal');
        const backdrop = document.getElementById('logoutModalBackdrop');
        const content = document.getElementById('logoutModalContent');
        
        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        
        // Hide after animation finishes
        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }, 300);
    }
</script>
