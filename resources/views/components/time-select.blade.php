@props(['name', 'value' => '08:00', 'class' => ''])

@php
    $parts = explode(':', $value);
    $currentHour = count($parts) > 0 ? str_pad($parts[0], 2, '0', STR_PAD_LEFT) : '08';
    $currentMinute = count($parts) > 1 ? str_pad($parts[1], 2, '0', STR_PAD_LEFT) : '00';
    $id = 'ts_' . \Illuminate\Support\Str::random(6);

    if (!function_exists('getClockPos')) {
        function getClockPos($val, $isMinute = false) {
            $num = (int)$val;
            if ($isMinute) {
                $stop = $num / 5; 
                $radius = 80;
            } else {
                if ($num === 0) $num = 24;
                $isInner = ($num >= 13 && $num <= 24);
                $stop = $num % 12;
                if ($stop === 0) $stop = 12;
                $radius = $isInner ? 48 : 82;
            }
            $angle = $stop * 30;
            $center = 105;
            $rad = deg2rad($angle - 90);
            $x = $center + $radius * cos($rad) - 17;
            $y = $center + $radius * sin($rad) - 17;
            
            // Format to ensure dot decimal separator regardless of locale
            $x_str = number_format($x, 2, '.', '');
            $y_str = number_format($y, 2, '.', '');
            
            return "left: {$x_str}px; top: {$y_str}px;";
        }
    }
@endphp

<div class="relative time-select-wrapper {{ $class }}" id="{{ $id }}">
    <div class="relative w-full">
        <input type="text" name="{{ $name }}" id="{{ $id }}_input" value="{{ $value }}"
               onclick="openClockPopup('{{ $id }}')"
               oninput="handleTimeInput(this, '{{ $id }}')"
               onblur="formatTimeInput(this, '{{ $id }}')"
               class="w-full pl-4 pr-11 py-3.5 rounded-xl border border-gray-200 outline-none focus:border-primary focus:ring-1 focus:ring-primary bg-white hover:bg-gray-50 transition-colors font-bold text-[15px]"
               placeholder="08:00"
               maxlength="5" autocomplete="off">
        <span class="material-symbols-outlined text-gray-500 absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">schedule</span>
    </div>

    <div id="{{ $id }}_popup" class="hidden fixed inset-0 z-[99999] flex items-center justify-center px-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" onclick="closeClockPopup('{{ $id }}')"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-white rounded-[28px] shadow-2xl p-5 select-none w-[280px] max-w-full">
            <!-- Header Tabs -->
            <div class="flex items-center justify-center gap-1 text-4xl font-black mb-6 mt-2">
                <input type="text" id="{{ $id }}_tab_hour" value="{{ $currentHour }}"
                       onfocus="switchClockTab('{{ $id }}', 'hour')"
                       oninput="handleTabInput('{{ $id }}', 'hour', this)"
                       onblur="formatTabInput('{{ $id }}', 'hour', this)"
                       style="width: 60px;"
                       class="text-center px-1 py-1.5 rounded-xl bg-primary/10 text-primary transition-colors outline-none cursor-text"
                       maxlength="2" autocomplete="off">
                <span class="text-gray-400 pb-2">:</span>
                <input type="text" id="{{ $id }}_tab_minute" value="{{ $currentMinute }}"
                       onfocus="switchClockTab('{{ $id }}', 'minute')"
                       oninput="handleTabInput('{{ $id }}', 'minute', this)"
                       onblur="formatTabInput('{{ $id }}', 'minute', this)"
                       style="width: 60px;"
                       class="text-center px-1 py-1.5 rounded-xl text-gray-400 hover:bg-gray-100 transition-colors outline-none cursor-text"
                       maxlength="2" autocomplete="off">
            </div>

            <!-- Hour Clock -->
            <div id="{{ $id }}_view_hour" class="relative mx-auto rounded-full bg-gray-50" style="width: 210px; height: 210px;">
                <div class="absolute w-2 h-2 rounded-full bg-primary" style="left: 101px; top: 101px;"></div>
                @for($i = 0; $i < 24; $i++)
                    @php 
                        $h = str_pad($i, 2, '0', STR_PAD_LEFT); 
                        $pos = getClockPos($h, false);
                    @endphp
                    <button type="button" style="{{ $pos }}" onclick="pickHour('{{ $id }}', '{{ $h }}')" class="absolute w-[34px] h-[34px] p-0 m-0 leading-none rounded-full flex items-center justify-center font-bold text-sm transition-colors {{ $currentHour === $h ? 'bg-primary text-white' : 'text-gray-700 hover:bg-orange-100' }}">
                        {{ $h }}
                    </button>
                @endfor
            </div>

            <!-- Minute Clock -->
            <div id="{{ $id }}_view_minute" class="hidden relative mx-auto rounded-full bg-gray-50" style="width: 210px; height: 210px;">
                <div class="absolute w-2 h-2 rounded-full bg-primary" style="left: 101px; top: 101px;"></div>
                @foreach(['00', '05', '10', '15', '20', '25', '30', '35', '40', '45', '50', '55'] as $m)
                    @php $pos = getClockPos($m, true); @endphp
                    <button type="button" style="{{ $pos }}" onclick="pickMinute('{{ $id }}', '{{ $m }}')" class="absolute w-[34px] h-[34px] p-0 m-0 leading-none rounded-full flex items-center justify-center font-bold text-sm transition-colors {{ $currentMinute === $m ? 'bg-primary text-white' : 'text-gray-700 hover:bg-orange-100' }}">
                        {{ $m }}
                    </button>
                @endforeach
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="button" onclick="closeClockPopup('{{ $id }}')" class="px-6 py-2.5 font-bold text-[15px] text-white bg-primary hover:bg-primary-dark rounded-xl transition-colors">Xong</button>
            </div>
        </div>
    </div>
</div>

@once
<script>
    function openClockPopup(id) {
        // Reset z-index for all time-select wrappers
        document.querySelectorAll('.time-select-wrapper').forEach(el => {
            el.style.zIndex = 'auto';
        });
        document.querySelectorAll('[id$="_popup"]').forEach(el => {
            if (el.id !== id + '_popup') el.classList.add('hidden');
        });
        const popup = document.getElementById(id + '_popup');
        const wrapper = document.getElementById(id);
        if (popup && popup.classList.contains('hidden')) {
            popup.classList.remove('hidden');
            popup.style.left = '';
            popup.style.right = '';
            if (wrapper) wrapper.style.zIndex = 100; // Elevate active wrapper to prevent sibling overlap
            switchClockTab(id, 'hour'); // Always open hour view first
        }
    }

    function closeClockPopup(id) {
        document.getElementById(id + '_popup').classList.add('hidden');
        const wrapper = document.getElementById(id);
        if (wrapper) wrapper.style.zIndex = 'auto';
    }

    function switchClockTab(id, type) {
        const tabHour = document.getElementById(id + '_tab_hour');
        const tabMinute = document.getElementById(id + '_tab_minute');
        const viewHour = document.getElementById(id + '_view_hour');
        const viewMinute = document.getElementById(id + '_view_minute');

        const activeClass = 'text-center px-1 py-1.5 rounded-xl bg-primary/10 text-primary transition-colors outline-none cursor-text';
        const inactiveClass = 'text-center px-1 py-1.5 rounded-xl text-gray-400 hover:bg-gray-100 transition-colors outline-none cursor-text';

        if (type === 'hour') {
            tabHour.className = activeClass;
            tabMinute.className = inactiveClass;
            viewHour.classList.remove('hidden');
            viewMinute.classList.add('hidden');
        } else {
            tabMinute.className = activeClass;
            tabHour.className = inactiveClass;
            viewMinute.classList.remove('hidden');
            viewHour.classList.add('hidden');
        }
    }

    function updateDisplay(id) {
        const hour = document.getElementById(id + '_tab_hour').value.padStart(2, '0');
        const minute = document.getElementById(id + '_tab_minute').value.padStart(2, '0');
        const val = hour + ':' + minute;
        document.getElementById(id + '_input').value = val;
    }

    function updateActiveState(containerId, val) {
        const container = document.getElementById(containerId);
        container.querySelectorAll('button').forEach(btn => {
            if (btn.textContent.trim() === val) {
                btn.className = 'absolute w-[34px] h-[34px] p-0 m-0 leading-none rounded-full flex items-center justify-center font-bold text-sm transition-colors bg-primary text-white';
            } else {
                btn.className = 'absolute w-[34px] h-[34px] p-0 m-0 leading-none rounded-full flex items-center justify-center font-bold text-sm transition-colors text-gray-700 hover:bg-orange-100';
            }
        });
    }

    function pickHour(id, val) {
        document.getElementById(id + '_tab_hour').value = val;
        updateActiveState(id + '_view_hour', val);
        updateDisplay(id);
        setTimeout(() => switchClockTab(id, 'minute'), 250);
    }

    function pickMinute(id, val) {
        document.getElementById(id + '_tab_minute').value = val;
        updateActiveState(id + '_view_minute', val);
        updateDisplay(id);
    }

    function handleTabInput(id, type, input) {
        let val = input.value.replace(/[^0-9]/g, '');
        if (val.length > 2) val = val.substring(0, 2);
        input.value = val;

        if (val.length === 2) {
            let num = parseInt(val);
            if (type === 'hour') {
                if (num > 23) num = 23;
                val = num.toString().padStart(2, '0');
                input.value = val;
                updateActiveState(id + '_view_hour', val);
            } else {
                if (num > 59) num = 59;
                val = num.toString().padStart(2, '0');
                input.value = val;
                let roundedMin = Math.round(num / 5) * 5;
                if (roundedMin === 60) roundedMin = 0;
                updateActiveState(id + '_view_minute', roundedMin.toString().padStart(2, '0'));
            }
            updateDisplay(id);
        }
    }

    function formatTabInput(id, type, input) {
        let val = input.value.replace(/[^0-9]/g, '');
        if (val === '') val = '0';
        let num = parseInt(val);
        if (type === 'hour' && num > 23) num = 23;
        if (type === 'minute' && num > 59) num = 59;
        
        val = num.toString().padStart(2, '0');
        input.value = val;
        
        if (type === 'hour') {
            updateActiveState(id + '_view_hour', val);
        } else {
            let roundedMin = Math.round(num / 5) * 5;
            if (roundedMin === 60) roundedMin = 0;
            updateActiveState(id + '_view_minute', roundedMin.toString().padStart(2, '0'));
        }
        updateDisplay(id);
    }

    function handleTimeInput(input, id) {
        let val = input.value.replace(/[^0-9]/g, ''); // keep only numbers
        if (val.length > 2) {
            val = val.substring(0, 2) + ':' + val.substring(2, 4);
        }
        input.value = val;

        if (val.length === 5) {
            let parts = val.split(':');
            let h = parseInt(parts[0]);
            let m = parseInt(parts[1]);
            if (h > 23) h = 23;
            if (m > 59) m = 59;
            let hh = h.toString().padStart(2, '0');
            let mm = m.toString().padStart(2, '0');
            let finalVal = hh + ':' + mm;
            
            if (input.value !== finalVal) input.value = finalVal;

            // Sync clock UI
            document.getElementById(id + '_tab_hour').value = hh;
            document.getElementById(id + '_tab_minute').value = mm;
            updateActiveState(id + '_view_hour', hh);
            // Nearest 5 minutes for active state
            let roundedMin = Math.round(m / 5) * 5;
            if (roundedMin === 60) roundedMin = 0;
            updateActiveState(id + '_view_minute', roundedMin.toString().padStart(2, '0'));
        }
    }

    function formatTimeInput(input, id) {
        let val = input.value.replace(/[^0-9]/g, '');
        if (val.length > 0) {
            let h = 0, m = 0;
            if (val.length <= 2) {
                h = parseInt(val);
            } else {
                h = parseInt(val.substring(0, 2));
                m = parseInt(val.substring(2, 4));
            }
            if (h > 23) h = 23;
            if (m > 59) m = 59;
            
            let hh = h.toString().padStart(2, '0');
            let mm = m.toString().padStart(2, '0');
            input.value = hh + ':' + mm;
            
            document.getElementById(id + '_tab_hour').value = hh;
            document.getElementById(id + '_tab_minute').value = mm;
            updateActiveState(id + '_view_hour', hh);
            let roundedMin = Math.round(m / 5) * 5;
            if (roundedMin === 60) roundedMin = 0;
            updateActiveState(id + '_view_minute', roundedMin.toString().padStart(2, '0'));
        } else {
            input.value = "00:00"; // fallback
            document.getElementById(id + '_tab_hour').value = "00";
            document.getElementById(id + '_tab_minute').value = "00";
            updateActiveState(id + '_view_hour', "00");
            updateActiveState(id + '_view_minute', "00");
        }
    }
</script>
@endonce
