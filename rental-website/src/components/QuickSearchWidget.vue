<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'

const emit = defineEmits<{
  (e: 'search', params: { vehicleType: string; startDate: string; endDate: string; passengers: number }): void
}>()

const widgetContainerRef = ref<HTMLElement | null>(null)
const activePopover = ref<'vehicle' | 'startDate' | 'endDate' | 'passengers' | null>(null)

// Form State
const vehicleType = ref('all')
const startDate = ref('')
const endDate = ref('')
const passengers = ref(1)

// Options Definitions
const vehicleOptions = [
  { value: 'all', label: 'Semua Jenis', sub: 'Seluruh armada tersedia', icon: 'all' },
  { value: 'city-car', label: 'City Car (Agya)', sub: '4–5 Kursi • Lincah & Hemat', icon: 'car' },
  { value: 'mpv', label: 'MPV Keluarga (Avanza / Veloz)', sub: '7 Kursi • Paling Populer', icon: 'mpv' },
  { value: 'hiace', label: 'HiAce Minibus', sub: '11–15 Kursi • Termasuk Supir', icon: 'van' },
]

const passengerOptions = [
  { value: 1, label: '1 - 2 Orang', sub: 'Solo traveler / Pasangan' },
  { value: 4, label: '3 - 4 Orang', sub: 'Keluarga kecil / City Car' },
  { value: 7, label: '5 - 7 Orang', sub: 'Keluarga besar / MPV' },
  { value: 15, label: '8 - 15 Orang', sub: 'Rombongan wisata / HiAce' },
]

// Display computed labels
const selectedVehicleLabel = computed(() => {
  const opt = vehicleOptions.find(o => o.value === vehicleType.value)
  return opt ? opt.label : 'Semua Jenis'
})

const selectedPassengersLabel = computed(() => {
  const opt = passengerOptions.find(o => o.value === passengers.value)
  return opt ? opt.label : '1 - 2 Orang'
})

// Month names and formatting
const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
const monthShortNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
const dayHeaders = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']

function formatDateDisplay(dateStr: string): string {
  if (!dateStr) return 'Pilih tanggal'
  const [y, m, d] = dateStr.split('-').map(Number)
  if (!y || !m || !d) return 'Pilih tanggal'
  return `${d} ${monthShortNames[m - 1]} ${y}`
}

// Custom Calendar Logic
const today = new Date()
const calYear = ref(today.getFullYear())
const calMonth = ref(today.getMonth()) // 0 to 11

function nextMonth() {
  if (calMonth.value === 11) {
    calMonth.value = 0
    calYear.value++
  } else {
    calMonth.value++
  }
}

function prevMonth() {
  const isCurrentOrPast = calYear.value === today.getFullYear() && calMonth.value <= today.getMonth()
  if (isCurrentOrPast) return
  if (calMonth.value === 0) {
    calMonth.value = 11
    calYear.value--
  } else {
    calMonth.value--
  }
}

const canPrevMonth = computed(() => {
  return !(calYear.value === today.getFullYear() && calMonth.value <= today.getMonth())
})

const daysInCalMonth = computed(() => {
  return new Date(calYear.value, calMonth.value + 1, 0).getDate()
})

const firstDayOffset = computed(() => {
  return new Date(calYear.value, calMonth.value, 1).getDay()
})

function isPastDay(day: number) {
  const check = new Date(calYear.value, calMonth.value, day)
  const t = new Date(today.getFullYear(), today.getMonth(), today.getDate())
  return check < t
}

function isTodayDay(day: number) {
  return calYear.value === today.getFullYear() && calMonth.value === today.getMonth() && day === today.getDate()
}

function isDateSelected(day: number, dateStr: string) {
  if (!dateStr) return false
  const [y, m, d] = dateStr.split('-').map(Number)
  return y === calYear.value && m === calMonth.value + 1 && d === day
}

function isDateInRange(day: number) {
  if (!startDate.value || !endDate.value) return false
  const cur = new Date(calYear.value, calMonth.value, day)
  const [sy, sm, sd] = startDate.value.split('-').map(Number)
  const [ey, em, ed] = endDate.value.split('-').map(Number)
  const s = new Date(sy, sm - 1, sd)
  const e = new Date(ey, em - 1, ed)
  return cur > s && cur < e
}

function handleDayClick(day: number, targetField: 'startDate' | 'endDate') {
  if (isPastDay(day)) return

  const mStr = String(calMonth.value + 1).padStart(2, '0')
  const dStr = String(day).padStart(2, '0')
  const formatted = `${calYear.value}-${mStr}-${dStr}`

  if (targetField === 'startDate') {
    startDate.value = formatted
    // Auto validate if endDate is before startDate
    if (endDate.value && endDate.value < formatted) {
      endDate.value = ''
    }
    // Smoothly prompt to pick end date
    activePopover.value = 'endDate'
  } else {
    // If selecting endDate before startDate, set startDate instead
    if (startDate.value && formatted < startDate.value) {
      startDate.value = formatted
      endDate.value = ''
    } else {
      endDate.value = formatted
      activePopover.value = null
    }
  }
}

// Popover control
function togglePopover(type: 'vehicle' | 'startDate' | 'endDate' | 'passengers') {
  if (activePopover.value === type) {
    activePopover.value = null
  } else {
    activePopover.value = type
    // If opening calendar, synchronize cal view to selected or today
    if (type === 'startDate' && startDate.value) {
      const [y, m] = startDate.value.split('-').map(Number)
      calYear.value = y
      calMonth.value = m - 1
    } else if (type === 'endDate' && (endDate.value || startDate.value)) {
      const refDate = endDate.value || startDate.value
      const [y, m] = refDate.split('-').map(Number)
      calYear.value = y
      calMonth.value = m - 1
    }
  }
}

function selectVehicle(val: string) {
  vehicleType.value = val
  activePopover.value = null
}

function selectPassengers(val: number) {
  passengers.value = val
  activePopover.value = null
}

function handleSearch() {
  activePopover.value = null
  emit('search', {
    vehicleType: vehicleType.value,
    startDate: startDate.value,
    endDate: endDate.value,
    passengers: Number(passengers.value)
  })

  setTimeout(() => {
    const armadaEl = document.getElementById('armada')
    if (armadaEl) {
      armadaEl.scrollIntoView({ behavior: 'smooth' })
    }
  }, 50)
}

function handleClickOutside(e: MouseEvent) {
  if (widgetContainerRef.value && !widgetContainerRef.value.contains(e.target as Node)) {
    activePopover.value = null
  }
}

onMounted(() => {
  if (typeof window !== 'undefined') {
    window.addEventListener('click', handleClickOutside)
  }
})

onUnmounted(() => {
  if (typeof window !== 'undefined') {
    window.removeEventListener('click', handleClickOutside)
  }
})
</script>

<template>
  <div ref="widgetContainerRef" class="relative z-30 max-w-5xl mx-auto px-4 sm:px-6 select-none">
    <!-- Elevated White Card Container -->
    <div class="bg-white rounded-2xl sm:rounded-3xl shadow-2xl shadow-slate-950/20 border border-slate-100 p-2 sm:p-3 relative">
      
      <!-- DESKTOP / TABLET: 1 Horizontal Row with separators (>= sm) -->
      <div class="hidden sm:grid sm:grid-cols-12 items-center divide-x divide-slate-100">
        
        <!-- 1. Pilih Armada (Cols 3) -->
        <div class="col-span-3 px-3.5 py-2 relative">
          <button
            type="button"
            @click.stop="togglePopover('vehicle')"
            class="w-full text-left group cursor-pointer focus:outline-none"
          >
            <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1 flex items-center gap-1.5 group-hover:text-blue-600 transition-colors">
              <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
              </svg>
              Pilih Armada
            </span>
            <div class="flex items-center justify-between gap-1">
              <span class="text-xs md:text-sm font-black text-slate-900 truncate">
                {{ selectedVehicleLabel }}
              </span>
              <svg
                class="w-3.5 h-3.5 text-slate-400 transition-transform shrink-0"
                :class="{ 'rotate-180 text-blue-600': activePopover === 'vehicle' }"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
          </button>

          <!-- Dropdown Popover: Pilih Armada -->
          <div
            v-if="activePopover === 'vehicle'"
            class="absolute left-0 top-full mt-3 w-72 bg-white rounded-2xl shadow-2xl border border-slate-100 p-2 z-50 animate-in fade-in zoom-in-95 duration-150"
            @click.stop
          >
            <div class="space-y-1">
              <button
                v-for="opt in vehicleOptions"
                :key="opt.value"
                type="button"
                @click="selectVehicle(opt.value)"
                class="w-full text-left px-3 py-2.5 rounded-xl flex items-center justify-between transition-colors cursor-pointer"
                :class="vehicleType === opt.value ? 'bg-blue-50/80 text-blue-700 font-bold' : 'hover:bg-slate-50 text-slate-700'"
              >
                <div>
                  <span class="block text-xs sm:text-sm font-bold leading-tight">{{ opt.label }}</span>
                  <span class="block text-[11px] text-slate-400 font-normal mt-0.5">{{ opt.sub }}</span>
                </div>
                <svg v-if="vehicleType === opt.value" class="w-4 h-4 text-blue-600 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- 2. Tanggal Mulai (Cols 2) -->
        <div class="col-span-2 px-3.5 py-2 relative">
          <button
            type="button"
            @click.stop="togglePopover('startDate')"
            class="w-full text-left group cursor-pointer focus:outline-none"
          >
            <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1 flex items-center gap-1.5 group-hover:text-blue-600 transition-colors">
              <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              Tgl Mulai
            </span>
            <div class="flex items-center justify-between gap-1">
              <span
                class="text-xs md:text-sm font-black truncate"
                :class="startDate ? 'text-slate-900' : 'text-slate-400 font-medium'"
              >
                {{ formatDateDisplay(startDate) }}
              </span>
              <svg
                class="w-3.5 h-3.5 text-slate-400 transition-transform shrink-0"
                :class="{ 'rotate-180 text-blue-600': activePopover === 'startDate' }"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
          </button>

          <!-- Calendar Popover: Tgl Mulai -->
          <div
            v-if="activePopover === 'startDate'"
            class="absolute left-0 top-full mt-3 w-80 bg-white rounded-2xl shadow-2xl border border-slate-100 p-4 z-50 animate-in fade-in zoom-in-95 duration-150"
            @click.stop
          >
            <!-- Month Header -->
            <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
              <button
                type="button"
                @click="prevMonth"
                :disabled="!canPrevMonth"
                class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
              </button>
              <span class="text-xs sm:text-sm font-black text-slate-900">
                {{ monthNames[calMonth] }} {{ calYear }}
              </span>
              <button
                type="button"
                @click="nextMonth"
                class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-100 transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
              </button>
            </div>

            <!-- Day Headers -->
            <div class="grid grid-cols-7 gap-1 text-center mb-1.5">
              <span v-for="d in dayHeaders" :key="d" class="text-[10px] font-black uppercase text-slate-400 py-0.5">
                {{ d }}
              </span>
            </div>

            <!-- Calendar Days Grid -->
            <div class="grid grid-cols-7 gap-1">
              <!-- Empty leading days -->
              <div v-for="blank in firstDayOffset" :key="'blank-' + blank" class="w-8 h-8"></div>
              
              <!-- Days -->
              <button
                v-for="day in daysInCalMonth"
                :key="day"
                type="button"
                @click="handleDayClick(day, 'startDate')"
                :disabled="isPastDay(day)"
                class="w-8 h-8 rounded-xl text-xs flex items-center justify-center transition-all cursor-pointer"
                :class="[
                  isPastDay(day)
                    ? 'text-slate-300 cursor-not-allowed'
                    : isDateSelected(day, startDate)
                      ? 'bg-blue-600 text-white font-black shadow-md shadow-blue-600/30'
                      : isDateInRange(day)
                        ? 'bg-blue-50 text-blue-700 font-bold'
                        : isTodayDay(day)
                          ? 'border border-blue-500 text-blue-600 font-bold hover:bg-blue-50'
                          : 'text-slate-800 hover:bg-slate-100 font-semibold'
                ]"
              >
                {{ day }}
              </button>
            </div>

            <!-- Quick footer -->
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
              <span class="text-slate-400">Pilih tanggal sewa dimulai</span>
              <button
                v-if="startDate"
                type="button"
                @click="startDate = ''; activePopover = null"
                class="text-red-500 hover:underline font-bold"
              >
                Hapus
              </button>
            </div>
          </div>
        </div>

        <!-- 3. Tanggal Selesai (Cols 2) -->
        <div class="col-span-2 px-3.5 py-2 relative">
          <button
            type="button"
            @click.stop="togglePopover('endDate')"
            class="w-full text-left group cursor-pointer focus:outline-none"
          >
            <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1 flex items-center gap-1.5 group-hover:text-blue-600 transition-colors">
              <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              Tgl Selesai
            </span>
            <div class="flex items-center justify-between gap-1">
              <span
                class="text-xs md:text-sm font-black truncate"
                :class="endDate ? 'text-slate-900' : 'text-slate-400 font-medium'"
              >
                {{ formatDateDisplay(endDate) }}
              </span>
              <svg
                class="w-3.5 h-3.5 text-slate-400 transition-transform shrink-0"
                :class="{ 'rotate-180 text-blue-600': activePopover === 'endDate' }"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
          </button>

          <!-- Calendar Popover: Tgl Selesai -->
          <div
            v-if="activePopover === 'endDate'"
            class="absolute left-0 top-full mt-3 w-80 bg-white rounded-2xl shadow-2xl border border-slate-100 p-4 z-50 animate-in fade-in zoom-in-95 duration-150"
            @click.stop
          >
            <!-- Month Header -->
            <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
              <button
                type="button"
                @click="prevMonth"
                :disabled="!canPrevMonth"
                class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
              </button>
              <span class="text-xs sm:text-sm font-black text-slate-900">
                {{ monthNames[calMonth] }} {{ calYear }}
              </span>
              <button
                type="button"
                @click="nextMonth"
                class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-100 transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
              </button>
            </div>

            <!-- Day Headers -->
            <div class="grid grid-cols-7 gap-1 text-center mb-1.5">
              <span v-for="d in dayHeaders" :key="d" class="text-[10px] font-black uppercase text-slate-400 py-0.5">
                {{ d }}
              </span>
            </div>

            <!-- Calendar Days Grid -->
            <div class="grid grid-cols-7 gap-1">
              <div v-for="blank in firstDayOffset" :key="'blank-' + blank" class="w-8 h-8"></div>
              
              <button
                v-for="day in daysInCalMonth"
                :key="day"
                type="button"
                @click="handleDayClick(day, 'endDate')"
                :disabled="isPastDay(day)"
                class="w-8 h-8 rounded-xl text-xs flex items-center justify-center transition-all cursor-pointer"
                :class="[
                  isPastDay(day)
                    ? 'text-slate-300 cursor-not-allowed'
                    : isDateSelected(day, endDate)
                      ? 'bg-blue-600 text-white font-black shadow-md shadow-blue-600/30'
                      : isDateInRange(day)
                        ? 'bg-blue-50 text-blue-700 font-bold'
                        : isTodayDay(day)
                          ? 'border border-blue-500 text-blue-600 font-bold hover:bg-blue-50'
                          : 'text-slate-800 hover:bg-slate-100 font-semibold'
                ]"
              >
                {{ day }}
              </button>
            </div>

            <!-- Quick footer -->
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
              <span class="text-slate-400">Pilih tanggal sewa berakhir</span>
              <button
                v-if="endDate"
                type="button"
                @click="endDate = ''; activePopover = null"
                class="text-red-500 hover:underline font-bold"
              >
                Hapus
              </button>
            </div>
          </div>
        </div>

        <!-- 4. Jumlah Penumpang (Cols 2) -->
        <div class="col-span-2 px-3.5 py-2 relative">
          <button
            type="button"
            @click.stop="togglePopover('passengers')"
            class="w-full text-left group cursor-pointer focus:outline-none"
          >
            <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1 flex items-center gap-1.5 group-hover:text-blue-600 transition-colors">
              <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
              </svg>
              Penumpang
            </span>
            <div class="flex items-center justify-between gap-1">
              <span class="text-xs md:text-sm font-black text-slate-900 truncate">
                {{ selectedPassengersLabel }}
              </span>
              <svg
                class="w-3.5 h-3.5 text-slate-400 transition-transform shrink-0"
                :class="{ 'rotate-180 text-blue-600': activePopover === 'passengers' }"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
          </button>

          <!-- Dropdown Popover: Jumlah Penumpang -->
          <div
            v-if="activePopover === 'passengers'"
            class="absolute right-0 top-full mt-3 w-64 bg-white rounded-2xl shadow-2xl border border-slate-100 p-2 z-50 animate-in fade-in zoom-in-95 duration-150"
            @click.stop
          >
            <div class="space-y-1">
              <button
                v-for="opt in passengerOptions"
                :key="opt.value"
                type="button"
                @click="selectPassengers(opt.value)"
                class="w-full text-left px-3 py-2.5 rounded-xl flex items-center justify-between transition-colors cursor-pointer"
                :class="passengers === opt.value ? 'bg-blue-50/80 text-blue-700 font-bold' : 'hover:bg-slate-50 text-slate-700'"
              >
                <div>
                  <span class="block text-xs sm:text-sm font-bold leading-tight">{{ opt.label }}</span>
                  <span class="block text-[11px] text-slate-400 font-normal mt-0.5">{{ opt.sub }}</span>
                </div>
                <svg v-if="passengers === opt.value" class="w-4 h-4 text-blue-600 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- 5. Tombol Cari Armada (Cols 3) -->
        <div class="col-span-3 pl-2 pr-1">
          <button
            @click="handleSearch"
            type="button"
            class="w-full h-12 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-black text-xs sm:text-sm inline-flex items-center justify-center gap-2 shadow-lg shadow-slate-900/20 transition-all hover:shadow-slate-900/30 active:scale-95 cursor-pointer"
          >
            <span>Cari Armada</span>
            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- MOBILE VIEW (< sm): Clean custom cards with expandable popovers -->
      <div class="sm:hidden space-y-2.5 p-1">
        
        <!-- Field 1: Pilih Armada (Mobile) -->
        <div class="rounded-xl border border-slate-200/80 bg-slate-50/50 overflow-hidden">
          <button
            type="button"
            @click="togglePopover('vehicle')"
            class="w-full p-3 flex items-center justify-between text-left cursor-pointer"
          >
            <div class="flex items-center gap-2.5 min-w-0 flex-1">
              <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
              </div>
              <div class="min-w-0 flex-1">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 leading-none mb-1">Pilih Armada</span>
                <span class="block text-xs font-black text-slate-900 truncate">{{ selectedVehicleLabel }}</span>
              </div>
            </div>
            <svg
              class="w-4 h-4 text-slate-400 transition-transform shrink-0"
              :class="{ 'rotate-180 text-blue-600': activePopover === 'vehicle' }"
              fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>

          <!-- Mobile Expanded List: Armada -->
          <div v-if="activePopover === 'vehicle'" class="p-2 pt-0 border-t border-slate-200/60 bg-white space-y-1">
            <button
              v-for="opt in vehicleOptions"
              :key="opt.value"
              type="button"
              @click="selectVehicle(opt.value)"
              class="w-full text-left p-2.5 rounded-lg flex items-center justify-between transition-colors"
              :class="vehicleType === opt.value ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-700 hover:bg-slate-50'"
            >
              <div>
                <span class="block text-xs font-bold">{{ opt.label }}</span>
                <span class="block text-[10px] text-slate-400">{{ opt.sub }}</span>
              </div>
              <svg v-if="vehicleType === opt.value" class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </button>
          </div>
        </div>

        <!-- Field 2: Tanggal Mulai (Mobile) -->
        <div class="rounded-xl border border-slate-200/80 bg-slate-50/50 overflow-hidden">
          <button
            type="button"
            @click="togglePopover('startDate')"
            class="w-full p-3 flex items-center justify-between text-left cursor-pointer"
          >
            <div class="flex items-center gap-2.5 min-w-0 flex-1">
              <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </div>
              <div class="min-w-0 flex-1">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 leading-none mb-1">Tanggal Mulai</span>
                <span class="block text-xs font-black text-slate-900 truncate">{{ formatDateDisplay(startDate) }}</span>
              </div>
            </div>
            <svg
              class="w-4 h-4 text-slate-400 transition-transform shrink-0"
              :class="{ 'rotate-180 text-blue-600': activePopover === 'startDate' }"
              fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>

          <!-- Mobile Calendar: Mulai -->
          <div v-if="activePopover === 'startDate'" class="p-3 border-t border-slate-200/60 bg-white">
            <div class="flex items-center justify-between mb-2">
              <button type="button" @click="prevMonth" :disabled="!canPrevMonth" class="p-1 rounded-lg hover:bg-slate-100 disabled:opacity-30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
              </button>
              <span class="text-xs font-bold text-slate-900">{{ monthNames[calMonth] }} {{ calYear }}</span>
              <button type="button" @click="nextMonth" class="p-1 rounded-lg hover:bg-slate-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
              </button>
            </div>
            <div class="grid grid-cols-7 gap-1 text-center mb-1 text-[10px] font-bold text-slate-400">
              <span v-for="d in dayHeaders" :key="d">{{ d }}</span>
            </div>
            <div class="grid grid-cols-7 gap-1">
              <div v-for="blank in firstDayOffset" :key="'blank-m-' + blank" class="w-7 h-7"></div>
              <button
                v-for="day in daysInCalMonth"
                :key="day"
                type="button"
                @click="handleDayClick(day, 'startDate')"
                :disabled="isPastDay(day)"
                class="w-7 h-7 rounded-lg text-xs flex items-center justify-center font-bold"
                :class="isPastDay(day) ? 'text-slate-300' : isDateSelected(day, startDate) ? 'bg-blue-600 text-white' : 'text-slate-800 hover:bg-slate-100'"
              >
                {{ day }}
              </button>
            </div>
          </div>
        </div>

        <!-- Field 3: Tanggal Selesai (Mobile) -->
        <div class="rounded-xl border border-slate-200/80 bg-slate-50/50 overflow-hidden">
          <button
            type="button"
            @click="togglePopover('endDate')"
            class="w-full p-3 flex items-center justify-between text-left cursor-pointer"
          >
            <div class="flex items-center gap-2.5 min-w-0 flex-1">
              <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </div>
              <div class="min-w-0 flex-1">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 leading-none mb-1">Tanggal Selesai</span>
                <span class="block text-xs font-black text-slate-900 truncate">{{ formatDateDisplay(endDate) }}</span>
              </div>
            </div>
            <svg
              class="w-4 h-4 text-slate-400 transition-transform shrink-0"
              :class="{ 'rotate-180 text-blue-600': activePopover === 'endDate' }"
              fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>

          <!-- Mobile Calendar: Selesai -->
          <div v-if="activePopover === 'endDate'" class="p-3 border-t border-slate-200/60 bg-white">
            <div class="flex items-center justify-between mb-2">
              <button type="button" @click="prevMonth" :disabled="!canPrevMonth" class="p-1 rounded-lg hover:bg-slate-100 disabled:opacity-30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
              </button>
              <span class="text-xs font-bold text-slate-900">{{ monthNames[calMonth] }} {{ calYear }}</span>
              <button type="button" @click="nextMonth" class="p-1 rounded-lg hover:bg-slate-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
              </button>
            </div>
            <div class="grid grid-cols-7 gap-1 text-center mb-1 text-[10px] font-bold text-slate-400">
              <span v-for="d in dayHeaders" :key="d">{{ d }}</span>
            </div>
            <div class="grid grid-cols-7 gap-1">
              <div v-for="blank in firstDayOffset" :key="'blank-e-' + blank" class="w-7 h-7"></div>
              <button
                v-for="day in daysInCalMonth"
                :key="day"
                type="button"
                @click="handleDayClick(day, 'endDate')"
                :disabled="isPastDay(day)"
                class="w-7 h-7 rounded-lg text-xs flex items-center justify-center font-bold"
                :class="isPastDay(day) ? 'text-slate-300' : isDateSelected(day, endDate) ? 'bg-blue-600 text-white' : 'text-slate-800 hover:bg-slate-100'"
              >
                {{ day }}
              </button>
            </div>
          </div>
        </div>

        <!-- Field 4: Jumlah Penumpang (Mobile) -->
        <div class="rounded-xl border border-slate-200/80 bg-slate-50/50 overflow-hidden">
          <button
            type="button"
            @click="togglePopover('passengers')"
            class="w-full p-3 flex items-center justify-between text-left cursor-pointer"
          >
            <div class="flex items-center gap-2.5 min-w-0 flex-1">
              <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
              </div>
              <div class="min-w-0 flex-1">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 leading-none mb-1">Jumlah Penumpang</span>
                <span class="block text-xs font-black text-slate-900 truncate">{{ selectedPassengersLabel }}</span>
              </div>
            </div>
            <svg
              class="w-4 h-4 text-slate-400 transition-transform shrink-0"
              :class="{ 'rotate-180 text-blue-600': activePopover === 'passengers' }"
              fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>

          <!-- Mobile Expanded List: Penumpang -->
          <div v-if="activePopover === 'passengers'" class="p-2 pt-0 border-t border-slate-200/60 bg-white space-y-1">
            <button
              v-for="opt in passengerOptions"
              :key="opt.value"
              type="button"
              @click="selectPassengers(opt.value)"
              class="w-full text-left p-2.5 rounded-lg flex items-center justify-between transition-colors"
              :class="passengers === opt.value ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-700 hover:bg-slate-50'"
            >
              <div>
                <span class="block text-xs font-bold">{{ opt.label }}</span>
                <span class="block text-[10px] text-slate-400">{{ opt.sub }}</span>
              </div>
              <svg v-if="passengers === opt.value" class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </button>
          </div>
        </div>

        <!-- Mobile Submit Button -->
        <button
          @click="handleSearch"
          type="button"
          class="w-full py-3.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-black text-sm inline-flex items-center justify-center gap-2 shadow-lg shadow-slate-900/20 transition-all active:scale-95 cursor-pointer mt-1"
        >
          <span>Cari Armada</span>
          <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
        </button>
      </div>

    </div>
  </div>
</template>
