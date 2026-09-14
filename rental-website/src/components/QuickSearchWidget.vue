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

function isDayDisabled(day: number, targetField: 'startDate' | 'endDate'): boolean {
  if (isPastDay(day)) return true
  if (targetField === 'endDate' && startDate.value) {
    const cur = new Date(calYear.value, calMonth.value, day)
    const [sy, sm, sd] = startDate.value.split('-').map(Number)
    const s = new Date(sy, sm - 1, sd)
    return cur < s
  }
  return false
}

function handleDayClick(day: number, targetField: 'startDate' | 'endDate') {
  if (isDayDisabled(day, targetField)) return

  const mStr = String(calMonth.value + 1).padStart(2, '0')
  const dStr = String(day).padStart(2, '0')
  const formatted = `${calYear.value}-${mStr}-${dStr}`

  if (targetField === 'startDate') {
    startDate.value = formatted
    if (endDate.value && endDate.value < formatted) {
      endDate.value = ''
    }
    activePopover.value = null
  } else {
    endDate.value = formatted
    activePopover.value = null
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

// Mobile Bottom Sheet state
const mobileSheet = ref<'vehicle' | 'startDate' | 'endDate' | 'passengers' | null>(null)

const mobileSheetTitle = computed(() => {
  switch (mobileSheet.value) {
    case 'vehicle': return 'Pilih Jenis Armada'
    case 'startDate': return 'Pilih Tanggal Mulai'
    case 'endDate': return 'Pilih Tanggal Selesai'
    case 'passengers': return 'Jumlah Penumpang'
    default: return ''
  }
})

function openMobileSheet(sheet: 'vehicle' | 'startDate' | 'endDate' | 'passengers') {
  mobileSheet.value = sheet
  if (sheet === 'startDate' && startDate.value) {
    const [y, m] = startDate.value.split('-').map(Number)
    calYear.value = y
    calMonth.value = m - 1
  } else if (sheet === 'endDate' && (endDate.value || startDate.value)) {
    const refDate = endDate.value || startDate.value
    const [y, m] = refDate.split('-').map(Number)
    calYear.value = y
    calMonth.value = m - 1
  }
  if (typeof document !== 'undefined') {
    document.body.classList.add('overflow-hidden')
  }
}

function closeMobileSheet() {
  mobileSheet.value = null
  if (typeof document !== 'undefined') {
    document.body.classList.remove('overflow-hidden')
  }
}

function handleMobileDayClick(day: number) {
  const targetField = mobileSheet.value as 'startDate' | 'endDate'
  handleDayClick(day, targetField)
  closeMobileSheet()
}

function clearMobileDate() {
  if (mobileSheet.value === 'startDate') {
    startDate.value = ''
  } else if (mobileSheet.value === 'endDate') {
    endDate.value = ''
  }
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
  if (typeof document !== 'undefined') {
    document.body.classList.remove('overflow-hidden')
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
        <div class="col-span-3 px-3 py-1 relative">
          <button
            type="button"
            @click.stop="togglePopover('vehicle')"
            class="w-full text-left group cursor-pointer p-1.5 rounded-xl hover:bg-slate-50 transition-colors focus:outline-none"
          >
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 group-hover:bg-blue-50 group-hover:text-blue-600 flex items-center justify-center shrink-0 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
              </div>
              <div class="min-w-0 flex-1">
                <span class="block text-xs font-bold text-slate-800 leading-tight">Pilih Armada</span>
                <span class="block text-xs text-slate-500 font-medium truncate mt-0.5">{{ selectedVehicleLabel }}</span>
              </div>
              <svg
                class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 transition-transform shrink-0"
                :class="{ 'rotate-180 text-blue-600': activePopover === 'vehicle' }"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
          </button>

          <!-- Dropdown Popover: Pilih Armada (Opens UPWARDS so never cut off by viewport) -->
          <div
            v-if="activePopover === 'vehicle'"
            class="absolute left-0 bottom-full mb-3 w-72 bg-white rounded-2xl shadow-2xl border border-slate-100 p-2 z-50 animate-in fade-in zoom-in-95 duration-150"
            @click.stop
          >
            <div class="space-y-1">
              <button
                v-for="opt in vehicleOptions"
                :key="opt.value"
                type="button"
                @click="selectVehicle(opt.value)"
                class="w-full text-left px-3 py-2.5 rounded-xl flex items-center justify-between transition-colors cursor-pointer"
                :class="vehicleType === opt.value ? 'bg-blue-50 text-blue-700 font-bold' : 'hover:bg-slate-50 text-slate-700'"
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
            <!-- Caret indicator -->
            <div class="absolute -bottom-1.5 left-8 w-3 h-3 bg-white border-b border-r border-slate-100 rotate-45"></div>
          </div>
        </div>

        <!-- 2. Tanggal Mulai (Cols 2) -->
        <div class="col-span-2 px-3 py-1 relative">
          <button
            type="button"
            @click.stop="togglePopover('startDate')"
            class="w-full text-left group cursor-pointer p-1.5 rounded-xl hover:bg-slate-50 transition-colors focus:outline-none"
          >
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 group-hover:bg-blue-50 group-hover:text-blue-600 flex items-center justify-center shrink-0 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </div>
              <div class="min-w-0 flex-1">
                <span class="block text-xs font-bold text-slate-800 leading-tight">Tanggal Mulai</span>
                <span
                  class="block text-xs font-medium truncate mt-0.5"
                  :class="startDate ? 'text-slate-800 font-semibold' : 'text-slate-400'"
                >
                  {{ formatDateDisplay(startDate) }}
                </span>
              </div>
            </div>
          </button>

          <!-- Calendar Popover: Tanggal Mulai (Opens UPWARDS so never cut off) -->
          <div
            v-if="activePopover === 'startDate'"
            class="absolute left-0 bottom-full mb-3 w-80 bg-white rounded-2xl shadow-2xl border border-slate-100 p-4 z-50 animate-in fade-in zoom-in-95 duration-150"
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
                @click="handleDayClick(day, 'startDate')"
                :disabled="isDayDisabled(day, 'startDate')"
                class="w-8 h-8 rounded-xl text-xs flex items-center justify-center transition-all cursor-pointer"
                :class="[
                  isDayDisabled(day, 'startDate')
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
            <!-- Caret indicator -->
            <div class="absolute -bottom-1.5 left-8 w-3 h-3 bg-white border-b border-r border-slate-100 rotate-45"></div>
          </div>
        </div>

        <!-- 3. Tanggal Selesai (Cols 2) -->
        <div class="col-span-2 px-3 py-1 relative">
          <button
            type="button"
            @click.stop="togglePopover('endDate')"
            class="w-full text-left group cursor-pointer p-1.5 rounded-xl hover:bg-slate-50 transition-colors focus:outline-none"
          >
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 group-hover:bg-blue-50 group-hover:text-blue-600 flex items-center justify-center shrink-0 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </div>
              <div class="min-w-0 flex-1">
                <span class="block text-xs font-bold text-slate-800 leading-tight">Tanggal Selesai</span>
                <span
                  class="block text-xs font-medium truncate mt-0.5"
                  :class="endDate ? 'text-slate-800 font-semibold' : 'text-slate-400'"
                >
                  {{ formatDateDisplay(endDate) }}
                </span>
              </div>
            </div>
          </button>

          <!-- Calendar Popover: Tanggal Selesai (Opens UPWARDS so never cut off) -->
          <div
            v-if="activePopover === 'endDate'"
            class="absolute left-1/2 -translate-x-1/2 bottom-full mb-3 w-80 bg-white rounded-2xl shadow-2xl border border-slate-100 p-4 z-50 animate-in fade-in zoom-in-95 duration-150"
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
                :disabled="isDayDisabled(day, 'endDate')"
                class="w-8 h-8 rounded-xl text-xs flex items-center justify-center transition-all cursor-pointer"
                :class="[
                  isDayDisabled(day, 'endDate')
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
            <!-- Caret indicator -->
            <div class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-b border-r border-slate-100 rotate-45"></div>
          </div>
        </div>

        <!-- 4. Jumlah Penumpang (Cols 2) -->
        <div class="col-span-2 px-3 py-1 relative">
          <button
            type="button"
            @click.stop="togglePopover('passengers')"
            class="w-full text-left group cursor-pointer p-1.5 rounded-xl hover:bg-slate-50 transition-colors focus:outline-none"
          >
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 group-hover:bg-blue-50 group-hover:text-blue-600 flex items-center justify-center shrink-0 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
              </div>
              <div class="min-w-0 flex-1">
                <span class="block text-xs font-bold text-slate-800 leading-tight">Jumlah Penumpang</span>
                <span class="block text-xs text-slate-500 font-medium truncate mt-0.5">{{ selectedPassengersLabel }}</span>
              </div>
              <svg
                class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 transition-transform shrink-0"
                :class="{ 'rotate-180 text-blue-600': activePopover === 'passengers' }"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
          </button>

          <!-- Dropdown Popover: Jumlah Penumpang (Opens UPWARDS, centered directly above Penumpang column) -->
          <div
            v-if="activePopover === 'passengers'"
            class="absolute left-1/2 -translate-x-1/2 bottom-full mb-3 w-60 bg-white rounded-2xl shadow-2xl border border-slate-100 p-2 z-50 animate-in fade-in zoom-in-95 duration-150"
            @click.stop
          >
            <div class="space-y-1">
              <button
                v-for="opt in passengerOptions"
                :key="opt.value"
                type="button"
                @click="selectPassengers(opt.value)"
                class="w-full text-left px-3 py-2.5 rounded-xl flex items-center justify-between transition-colors cursor-pointer"
                :class="passengers === opt.value ? 'bg-blue-50 text-blue-700 font-bold' : 'hover:bg-slate-50 text-slate-700'"
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
            <!-- Caret indicator -->
            <div class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-b border-r border-slate-100 rotate-45"></div>
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

      <!-- MOBILE VIEW (< sm): Unified Travel Card with Native Bottom Sheet Popups (Matching ChatGPT Mockup) -->
      <div class="sm:hidden">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 divide-y divide-slate-100 overflow-hidden">
          
          <!-- 1. Row: Pilih Armada -->
          <button
            type="button"
            @click="openMobileSheet('vehicle')"
            class="w-full px-3.5 py-3 flex items-center justify-between text-left hover:bg-slate-50 active:bg-slate-100 transition-colors cursor-pointer"
          >
            <div class="flex items-center gap-3 min-w-0 flex-1">
              <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
              </div>
              <div class="min-w-0 flex-1">
                <span class="block text-xs font-bold text-slate-800 leading-tight">Pilih Armada</span>
                <span class="block text-xs text-slate-500 font-medium truncate mt-0.5">{{ selectedVehicleLabel }}</span>
              </div>
            </div>
            <svg class="w-4 h-4 text-slate-400 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
          </button>

          <!-- 2. Row: Tanggal Mulai -->
          <button
            type="button"
            @click="openMobileSheet('startDate')"
            class="w-full px-3.5 py-3 flex items-center justify-between text-left hover:bg-slate-50 active:bg-slate-100 transition-colors cursor-pointer"
          >
            <div class="flex items-center gap-3 min-w-0 flex-1">
              <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </div>
              <div class="min-w-0 flex-1">
                <span class="block text-xs font-bold text-slate-800 leading-tight">Tanggal Mulai</span>
                <span class="block text-xs font-medium truncate mt-0.5" :class="startDate ? 'text-slate-800 font-semibold' : 'text-slate-400'">
                  {{ formatDateDisplay(startDate) }}
                </span>
              </div>
            </div>
            <svg class="w-4 h-4 text-slate-400 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
          </button>

          <!-- 3. Row: Tanggal Selesai -->
          <button
            type="button"
            @click="openMobileSheet('endDate')"
            class="w-full px-3.5 py-3 flex items-center justify-between text-left hover:bg-slate-50 active:bg-slate-100 transition-colors cursor-pointer"
          >
            <div class="flex items-center gap-3 min-w-0 flex-1">
              <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </div>
              <div class="min-w-0 flex-1">
                <span class="block text-xs font-bold text-slate-800 leading-tight">Tanggal Selesai</span>
                <span class="block text-xs font-medium truncate mt-0.5" :class="endDate ? 'text-slate-800 font-semibold' : 'text-slate-400'">
                  {{ formatDateDisplay(endDate) }}
                </span>
              </div>
            </div>
            <svg class="w-4 h-4 text-slate-400 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
          </button>

          <!-- 4. Row: Jumlah Penumpang -->
          <button
            type="button"
            @click="openMobileSheet('passengers')"
            class="w-full px-3.5 py-3 flex items-center justify-between text-left hover:bg-slate-50 active:bg-slate-100 transition-colors cursor-pointer"
          >
            <div class="flex items-center gap-3 min-w-0 flex-1">
              <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
              </div>
              <div class="min-w-0 flex-1">
                <span class="block text-xs font-bold text-slate-800 leading-tight">Jumlah Penumpang</span>
                <span class="block text-xs text-slate-500 font-medium truncate mt-0.5">{{ selectedPassengersLabel }}</span>
              </div>
            </div>
            <svg class="w-4 h-4 text-slate-400 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
          </button>

        </div>

        <!-- Mobile Submit Button -->
        <button
          @click="handleSearch"
          type="button"
          class="w-full py-3.5 mt-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm inline-flex items-center justify-center gap-2 shadow-lg shadow-slate-900/20 active:scale-98 transition-all cursor-pointer"
        >
          <span>Cari Armada</span>
          <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
        </button>
      </div>

      <!-- MOBILE BOTTOM SHEET MODAL (Fixed Overlay) -->
      <Teleport to="body">
        <!-- Backdrop -->
        <Transition
          enter-active-class="transition-opacity duration-200 ease-out"
          enter-from-class="opacity-0"
          enter-to-class="opacity-100"
          leave-active-class="transition-opacity duration-150 ease-in"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <div
            v-if="mobileSheet"
            class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-[60] sm:hidden"
            @click="closeMobileSheet"
          ></div>
        </Transition>

        <!-- Sheet Container Slide Up -->
        <Transition
          enter-active-class="transition-transform duration-250 ease-out"
          enter-from-class="translate-y-full"
          enter-to-class="translate-y-0"
          leave-active-class="transition-transform duration-200 ease-in"
          leave-from-class="translate-y-0"
          leave-to-class="translate-y-full"
        >
          <div
            v-if="mobileSheet"
            class="fixed inset-x-0 bottom-0 z-[70] bg-white rounded-t-3xl shadow-2xl p-5 border-t border-slate-100 max-h-[85vh] overflow-y-auto sm:hidden"
          >
            <!-- Drag Handle -->
            <div class="w-12 h-1 bg-slate-300 rounded-full mx-auto mb-3"></div>

            <!-- Header -->
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
              <h3 class="text-base font-black text-slate-900">
                {{ mobileSheetTitle }}
              </h3>
              <button
                type="button"
                @click="closeMobileSheet"
                class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:text-slate-800 transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </div>

            <!-- Content: Vehicle Options -->
            <div v-if="mobileSheet === 'vehicle'" class="space-y-2">
              <button
                v-for="opt in vehicleOptions"
                :key="opt.value"
                type="button"
                @click="selectVehicle(opt.value); closeMobileSheet()"
                class="w-full text-left p-3.5 rounded-2xl flex items-center justify-between transition-colors border"
                :class="vehicleType === opt.value ? 'bg-blue-50/80 border-blue-200 text-blue-900 font-bold' : 'border-slate-100 hover:bg-slate-50 text-slate-800'"
              >
                <div>
                  <span class="block text-sm font-bold">{{ opt.label }}</span>
                  <span class="block text-xs text-slate-400 font-normal mt-0.5">{{ opt.sub }}</span>
                </div>
                <svg v-if="vehicleType === opt.value" class="w-5 h-5 text-blue-600 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
              </button>
            </div>

            <!-- Content: Calendar (StartDate or EndDate) -->
            <div v-else-if="mobileSheet === 'startDate' || mobileSheet === 'endDate'" class="space-y-3">
              <div class="flex items-center justify-between px-1">
                <button
                  type="button"
                  @click="prevMonth"
                  :disabled="!canPrevMonth"
                  class="w-9 h-9 rounded-xl border border-slate-200 hover:bg-slate-50 disabled:opacity-30 flex items-center justify-center text-slate-700"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <span class="text-sm font-black text-slate-900">{{ monthNames[calMonth] }} {{ calYear }}</span>
                <button
                  type="button"
                  @click="nextMonth"
                  class="w-9 h-9 rounded-xl border border-slate-200 hover:bg-slate-50 flex items-center justify-center text-slate-700"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
              </div>

              <!-- Day Headers -->
              <div class="grid grid-cols-7 gap-1 text-center font-bold text-xs text-slate-400 mb-1">
                <span v-for="d in dayHeaders" :key="d">{{ d }}</span>
              </div>

              <!-- Days Grid -->
              <div class="grid grid-cols-7 gap-1.5">
                <div v-for="blank in firstDayOffset" :key="'blank-ms-' + blank" class="h-10"></div>
                <button
                  v-for="day in daysInCalMonth"
                  :key="day"
                  type="button"
                  @click="handleMobileDayClick(day)"
                  :disabled="isDayDisabled(day, mobileSheet as 'startDate' | 'endDate')"
                  class="h-10 rounded-xl text-sm font-bold flex items-center justify-center transition-all cursor-pointer"
                  :class="[
                    isDayDisabled(day, mobileSheet as 'startDate' | 'endDate')
                      ? 'text-slate-300 cursor-not-allowed'
                      : isDateSelected(day, mobileSheet === 'startDate' ? startDate : endDate)
                        ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-black'
                        : isDateInRange(day)
                          ? 'bg-blue-50 text-blue-700'
                          : isTodayDay(day)
                            ? 'border-2 border-blue-500 text-blue-600'
                            : 'text-slate-800 hover:bg-slate-100 active:bg-blue-100'
                  ]"
                >
                  {{ day }}
                </button>
              </div>

              <!-- Mobile Calendar Actions -->
              <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                <button
                  type="button"
                  @click="clearMobileDate(); closeMobileSheet()"
                  class="text-xs font-bold text-red-500 hover:underline p-2"
                >
                  Reset Tanggal
                </button>
                <button
                  type="button"
                  @click="closeMobileSheet()"
                  class="px-5 py-2.5 rounded-xl bg-slate-900 text-white font-bold text-xs shadow-md"
                >
                  Selesai
                </button>
              </div>
            </div>

            <!-- Content: Passenger Options -->
            <div v-else-if="mobileSheet === 'passengers'" class="space-y-2">
              <button
                v-for="opt in passengerOptions"
                :key="opt.value"
                type="button"
                @click="selectPassengers(opt.value); closeMobileSheet()"
                class="w-full text-left p-3.5 rounded-2xl flex items-center justify-between transition-colors border"
                :class="passengers === opt.value ? 'bg-blue-50/80 border-blue-200 text-blue-900 font-bold' : 'border-slate-100 hover:bg-slate-50 text-slate-800'"
              >
                <div>
                  <span class="block text-sm font-bold">{{ opt.label }}</span>
                  <span class="block text-xs text-slate-400 font-normal mt-0.5">{{ opt.sub }}</span>
                </div>
                <svg v-if="passengers === opt.value" class="w-5 h-5 text-blue-600 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
              </button>
            </div>

          </div>
        </Transition>
      </Teleport>

    </div>
  </div>
</template>
