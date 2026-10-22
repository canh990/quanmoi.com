{{--
    ==================================================================================
    _safelist.blade.php
    File này KHÔNG được render ra browser. Mục đích duy nhất là làm "safelist" cho
    Tailwind CSS v4 (@tailwindcss/vite) — liệt kê toàn bộ class động được ghép từ
    biến PHP (như $roleTheme, $btnCls, $gradFrom, v.v.) để Tailwind KHÔNG bị purge.

    Nguyên nhân mất màu / mất chữ:
      - Tailwind v4 dùng content-based scanning. Khi class được build bằng cú pháp
        PHP như: "from-{{ $color }}-600" hoặc ghép qua mảng PHP, Tailwind không
        nhận ra và loại khỏi CSS output → giao diện trắng, mất nền, mất chữ.
    Giải pháp:
      - Liệt kê toàn bộ class đó ra đây (dạng class="...") để Tailwind scan thấy.
      - Khai báo @source '../views/admin/_safelist.blade.php' trong app.css.
    ==================================================================================
--}}

{{-- GRADIENT BANNERS: Role Cards Vai Trò --}}
<div class="
  bg-gradient-to-r bg-gradient-to-tr bg-gradient-to-br bg-gradient-to-tl
  from-rose-600 from-rose-700 from-rose-800 from-rose-900
  to-rose-700 to-rose-800 to-rose-900
  from-amber-500 from-amber-600 from-amber-700 from-amber-800
  to-amber-600 to-amber-700 to-amber-800
  from-blue-600 from-blue-700 from-blue-800
  to-blue-700 to-blue-800 to-blue-900
  from-violet-600 from-violet-700 from-violet-800
  to-violet-600 to-violet-700 to-violet-800
  from-indigo-600 from-indigo-700 from-indigo-800
  to-indigo-600 to-indigo-700 to-indigo-800 to-violet-700
  from-slate-600 from-slate-700 from-slate-800 from-slate-900
  to-slate-700 to-slate-800 to-slate-900 to-slate-950
  from-emerald-600 from-emerald-700 to-emerald-700 to-emerald-800
  from-teal-600 to-teal-700
"></div>

{{-- BACKGROUND COLORS --}}
<div class="
  bg-rose-50 bg-rose-100 bg-rose-200 bg-rose-500 bg-rose-600 bg-rose-700 bg-rose-800 bg-rose-900
  bg-amber-50 bg-amber-100 bg-amber-200 bg-amber-500 bg-amber-600 bg-amber-700 bg-amber-800
  bg-blue-50 bg-blue-100 bg-blue-200 bg-blue-500 bg-blue-600 bg-blue-700 bg-blue-800
  bg-violet-50 bg-violet-100 bg-violet-500 bg-violet-600 bg-violet-700
  bg-indigo-50 bg-indigo-100 bg-indigo-500 bg-indigo-600 bg-indigo-700 bg-indigo-800
  bg-slate-50 bg-slate-100 bg-slate-200 bg-slate-500 bg-slate-700 bg-slate-800 bg-slate-900 bg-slate-950
  bg-emerald-50 bg-emerald-100 bg-emerald-600 bg-emerald-700
  bg-white bg-black bg-transparent
  bg-white/10 bg-white/20 bg-white/25 bg-white/30 bg-white/40
  bg-black/10 bg-black/20 bg-black/25 bg-black/30 bg-black/40
"></div>

{{-- HOVER BACKGROUND COLORS --}}
<div class="
  hover:bg-rose-50 hover:bg-rose-100 hover:bg-rose-600 hover:bg-rose-700 hover:bg-rose-800
  hover:bg-amber-50 hover:bg-amber-100 hover:bg-amber-600 hover:bg-amber-700 hover:bg-amber-800
  hover:bg-blue-50 hover:bg-blue-100 hover:bg-blue-600 hover:bg-blue-700 hover:bg-blue-800
  hover:bg-indigo-50 hover:bg-indigo-100 hover:bg-indigo-600 hover:bg-indigo-700 hover:bg-indigo-800
  hover:bg-violet-50 hover:bg-violet-600 hover:bg-violet-700
  hover:bg-slate-50 hover:bg-slate-100 hover:bg-slate-700 hover:bg-slate-800 hover:bg-slate-900 hover:bg-slate-950
  hover:bg-emerald-50 hover:bg-emerald-100 hover:bg-emerald-600 hover:bg-emerald-700
  hover:bg-white hover:bg-white/10 hover:bg-white/20
"></div>

{{-- TEXT COLORS --}}
<div class="
  text-rose-500 text-rose-600 text-rose-700 text-rose-800 text-rose-900
  text-amber-500 text-amber-600 text-amber-700 text-amber-800 text-amber-900
  text-blue-500 text-blue-600 text-blue-700 text-blue-800 text-blue-900
  text-violet-500 text-violet-600 text-violet-700 text-violet-800
  text-indigo-500 text-indigo-600 text-indigo-700 text-indigo-800 text-indigo-900 text-indigo-950
  text-slate-400 text-slate-500 text-slate-600 text-slate-700 text-slate-800 text-slate-900
  text-emerald-600 text-emerald-700 text-emerald-800 text-emerald-900
  text-white text-black text-gray-900
  text-white/70 text-white/80 text-white/90
"></div>

{{-- BORDER COLORS --}}
<div class="
  border border-2
  border-rose-100 border-rose-200 border-rose-300 border-rose-400 border-rose-500
  border-amber-100 border-amber-200 border-amber-300 border-amber-400 border-amber-500
  border-blue-100 border-blue-200 border-blue-300 border-blue-400 border-blue-500
  border-violet-100 border-violet-200 border-violet-300 border-violet-400
  border-indigo-100 border-indigo-200 border-indigo-300 border-indigo-400
  border-slate-100 border-slate-200 border-slate-300 border-slate-400 border-slate-500
  border-emerald-100 border-emerald-200 border-emerald-300 border-emerald-400
  border-white border-white/20 border-white/30 border-white/40 border-white/50
  border-black/10 border-black/20 border-transparent
"></div>

{{-- FONT WEIGHTS & SIZES --}}
<div class="
  font-normal font-medium font-semibold font-bold font-extrabold font-black font-mono
  text-[9px] text-[10px] text-[11px] text-[12px] text-xs text-sm text-base text-lg text-xl
  leading-none leading-tight leading-snug leading-normal leading-relaxed
  tracking-tight tracking-normal tracking-wide tracking-wider tracking-widest
  uppercase lowercase capitalize normal-case
"></div>

{{-- LAYOUT UTILITIES --}}
<div class="
  flex flex-col flex-row flex-wrap flex-nowrap flex-1 flex-none flex-shrink-0
  grid grid-cols-1 grid-cols-2 grid-cols-3 grid-cols-4
  md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4
  sm:flex-row sm:grid-cols-2 md:flex-row
  items-start items-center items-end items-stretch
  justify-start justify-center justify-end justify-between justify-around
  self-start self-center self-end
  gap-0.5 gap-1 gap-1.5 gap-2 gap-2.5 gap-3 gap-4 gap-5 gap-6
  w-full w-auto w-5 w-6 w-7 w-8 w-9 w-10 w-12 w-14 w-16 w-20 w-24 w-28 w-32 w-36 w-40 w-48 w-56 w-64 w-72 w-80 w-96
  h-auto h-full h-5 h-6 h-7 h-8 h-9 h-10 h-12 h-14 h-16 h-20 h-24 h-28 h-32
  min-w-0 min-h-0 max-w-full max-h-full
  shrink-0 grow grow-0
"></div>

{{-- SPACING --}}
<div class="
  p-0 p-1 p-2 p-3 p-4 p-5 p-6
  px-1 px-2 px-3 px-4 px-5 px-6
  py-0.5 py-1 py-1.5 py-2 py-2.5 py-3 py-3.5 py-4 py-5 py-6
  pt-1 pt-2 pt-3 pt-4 pb-1 pb-2 pb-3 pb-4
  pl-1 pl-2 pl-3 pl-4 pl-6 pl-8 pl-10
  pr-1 pr-2 pr-3 pr-4
  m-0 mt-0 mt-0.5 mt-1 mt-1.5 mt-2 mt-3 mt-4 mt-auto
  mb-0 mb-1 mb-2 mb-3 mb-4 ml-auto mr-auto
  space-y-0.5 space-y-1 space-y-1.5 space-y-2 space-y-2.5 space-y-3 space-y-4
"></div>

{{-- BORDER RADIUS --}}
<div class="
  rounded rounded-sm rounded-md rounded-lg rounded-xl rounded-2xl rounded-3xl rounded-full
  rounded-t-xl rounded-b-xl rounded-l-xl rounded-r-xl
"></div>

{{-- SHADOWS --}}
<div class="
  shadow-none shadow-2xs shadow-xs shadow-sm shadow-md shadow-lg shadow-xl shadow-2xl
  hover:shadow-sm hover:shadow-md hover:shadow-lg
"></div>

{{-- OVERFLOW & DISPLAY --}}
<div class="
  overflow-hidden overflow-visible overflow-auto overflow-scroll
  overflow-x-hidden overflow-x-auto overflow-x-scroll
  overflow-y-hidden overflow-y-auto overflow-y-scroll
  block inline inline-block inline-flex hidden
  sm:hidden md:hidden lg:hidden xl:hidden
  sm:block md:block lg:block xl:block
  sm:flex md:flex lg:flex xl:flex
  sm:inline md:inline lg:inline xl:inline
  sm:grid md:grid lg:grid xl:grid
"></div>

{{-- TRANSITIONS & ANIMATIONS --}}
<div class="
  transition transition-all transition-colors transition-opacity transition-transform
  duration-150 duration-200 duration-300 duration-500
  ease-in ease-out ease-in-out
  hover:shadow-md active:scale-95 active:scale-[0.98] active:scale-[0.97]
  animate-spin animate-pulse animate-bounce
"></div>

{{-- POSITION & Z-INDEX --}}
<div class="
  relative absolute fixed sticky inset-0 inset-y-0
  top-0 right-0 bottom-0 left-0
  z-10 z-20 z-30 z-40 z-50
  translate-x-full -translate-x-full translate-x-0
"></div>

{{-- MISCELLANEOUS UTILITIES --}}
<div class="
  truncate break-all break-words break-normal whitespace-nowrap whitespace-normal
  select-none cursor-pointer cursor-default cursor-not-allowed
  opacity-0 opacity-50 opacity-100
  drop-shadow-xs drop-shadow-sm
  object-cover object-contain object-center
  border-b border-t border-l border-r border-b-2 border-t-2
  divide-y divide-x divide-slate-100 divide-slate-200 divide-gray-100 divide-gray-200
  backdrop-blur-xs backdrop-blur-sm backdrop-blur-md
  fill-1
  italic not-italic
"></div>

{{-- RESPONSIVE PREFIXES (inline para Tailwind detectar) --}}
<div class="
  sm:w-44 sm:w-56 sm:flex sm:hidden sm:inline sm:block sm:grid sm:px-0 sm:py-0
  md:flex md:hidden md:block md:w-72 md:flex-row md:items-center md:gap-4
  lg:flex lg:hidden lg:block lg:flex-row lg:grid-cols-3 lg:w-56 lg:w-60 lg:w-72 lg:w-80
  xl:grid-cols-4 xl:w-60 xl:w-80
"></div>
