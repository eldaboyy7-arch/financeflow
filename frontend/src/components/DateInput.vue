<script setup lang="ts">
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import {
  CalendarDaysIcon,
  XMarkIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
} from '@heroicons/vue/24/outline'

const props = withDefaults(
  defineProps<{
    modelValue?: string
    placeholder?: string
    minDate?: string
    maxDate?: string
  }>(),
  {
    placeholder: 'Pilih tanggal',
  }
)

const emit = defineEmits<{
  (e: 'update:modelValue', val: string): void
}>()

const isOpen = ref(false)
const containerRef = ref<HTMLElement | null>(null)
const currentView = ref<'days' | 'months' | 'years'>('days')

// Months in Indonesian
const MONTH_NAMES = [
  'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
]

const MONTH_SHORT = [
  'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
  'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
]

const WEEKDAYS = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']

// Calendar view date (tracks currently browsed year and month)
const viewDate = ref(new Date())
const yearRangeStart = ref(Math.floor(new Date().getFullYear() / 12) * 12)

function parseIso(str?: string): Date | null {
  if (!str) return null
  const [y, m, d] = str.split('-').map(Number)
  if (!y || !m || !d) return null
  return new Date(y, m - 1, d)
}

function formatIso(d: Date): string {
  const y = d.getFullYear()
  const m = String(d.getMonth() + 1).padStart(2, '0')
  const dd = String(d.getDate()).padStart(2, '0')
  return `${y}-${m}-${dd}`
}

function formatDisplay(iso?: string): string {
  if (!iso) return ''
  const d = parseIso(iso)
  if (!d) return iso
  return `${d.getDate()} ${MONTH_NAMES[d.getMonth()]} ${d.getFullYear()}`
}

// Sync viewDate with modelValue on change
watch(
  () => props.modelValue,
  (val) => {
    const parsed = parseIso(val)
    if (parsed) {
      viewDate.value = new Date(parsed)
      yearRangeStart.value = Math.floor(parsed.getFullYear() / 12) * 12
    }
  },
  { immediate: true }
)

const currentYear = computed(() => viewDate.value.getFullYear())
const currentMonth = computed(() => viewDate.value.getMonth())

// Days calculation
interface DayCell {
  date: Date
  iso: string
  dayNumber: number
  isCurrentMonth: boolean
  isToday: boolean
  isSelected: boolean
  isDisabled: boolean
}

const dayCells = computed<DayCell[]>(() => {
  const year = currentYear.value
  const month = currentMonth.value

  const firstDayOfWeek = new Date(year, month, 1).getDay() // 0 is Sunday
  const daysInMonth = new Date(year, month + 1, 0).getDate()
  const daysInPrevMonth = new Date(year, month, 0).getDate()

  const todayIso = formatIso(new Date())
  const selectedIso = props.modelValue || ''

  const cells: DayCell[] = []

  // Prev month padding
  for (let i = firstDayOfWeek - 1; i >= 0; i--) {
    const d = new Date(year, month - 1, daysInPrevMonth - i)
    const iso = formatIso(d)
    cells.push({
      date: d,
      iso,
      dayNumber: d.getDate(),
      isCurrentMonth: false,
      isToday: iso === todayIso,
      isSelected: iso === selectedIso,
      isDisabled: checkDisabled(iso),
    })
  }

  // Current month days
  for (let dNum = 1; dNum <= daysInMonth; dNum++) {
    const d = new Date(year, month, dNum)
    const iso = formatIso(d)
    cells.push({
      date: d,
      iso,
      dayNumber: dNum,
      isCurrentMonth: true,
      isToday: iso === todayIso,
      isSelected: iso === selectedIso,
      isDisabled: checkDisabled(iso),
    })
  }

  // Next month padding to fill 42 cells (6 rows) or 35 cells
  const remaining = (7 - (cells.length % 7)) % 7
  for (let dNum = 1; dNum <= remaining; dNum++) {
    const d = new Date(year, month + 1, dNum)
    const iso = formatIso(d)
    cells.push({
      date: d,
      iso,
      dayNumber: dNum,
      isCurrentMonth: false,
      isToday: iso === todayIso,
      isSelected: iso === selectedIso,
      isDisabled: checkDisabled(iso),
    })
  }

  return cells
})

function checkDisabled(iso: string): boolean {
  if (props.minDate && iso < props.minDate) return true
  if (props.maxDate && iso > props.maxDate) return true
  return false
}

// Navigation
function prevMonth() {
  viewDate.value = new Date(currentYear.value, currentMonth.value - 1, 1)
}

function nextMonth() {
  viewDate.value = new Date(currentYear.value, currentMonth.value + 1, 1)
}

function selectDay(cell: DayCell) {
  if (cell.isDisabled) return
  emit('update:modelValue', cell.iso)
  isOpen.value = false
  currentView.value = 'days'
}

function selectMonth(mIndex: number) {
  viewDate.value = new Date(currentYear.value, mIndex, 1)
  currentView.value = 'days'
}

function selectYear(y: number) {
  viewDate.value = new Date(y, currentMonth.value, 1)
  currentView.value = 'days'
}

function selectToday() {
  const today = formatIso(new Date())
  emit('update:modelValue', today)
  viewDate.value = new Date()
  isOpen.value = false
  currentView.value = 'days'
}

function clear(e: Event) {
  e.stopPropagation()
  emit('update:modelValue', '')
}

// Click outside handler
function handleClickOutside(e: MouseEvent) {
  if (containerRef.value && !containerRef.value.contains(e.target as Node)) {
    isOpen.value = false
    currentView.value = 'days'
  }
}

onMounted(() => {
  document.addEventListener('mousedown', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('mousedown', handleClickOutside)
})
</script>

<template>
  <div ref="containerRef" class="relative w-full">
    <!-- Input Trigger Field -->
    <div
      @click="isOpen = !isOpen"
      class="input pl-9 pr-8 cursor-pointer select-none text-xs sm:text-sm font-medium flex items-center justify-between transition-all"
      :class="{ 'ring-2 ring-[#0066FF]/30 border-[#0066FF]': isOpen }"
    >
      <span v-if="modelValue" class="text-slate-900 dark:text-white truncate">
        {{ formatDisplay(modelValue) }}
      </span>
      <span v-else class="text-slate-400 truncate">
        {{ placeholder }}
      </span>
    </div>

    <!-- Calendar Icon -->
    <CalendarDaysIcon
      class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"
    />

    <!-- Clear Icon -->
    <button
      v-if="modelValue"
      type="button"
      @click="clear"
      class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors"
      title="Hapus tanggal"
    >
      <XMarkIcon class="w-3.5 h-3.5" />
    </button>

    <!-- Custom Modern Datepicker Popover -->
    <Transition
      enter-active-class="transition duration-150 ease-out"
      enter-from-class="transform scale-95 opacity-0 -translate-y-1"
      enter-to-class="transform scale-100 opacity-100 translate-y-0"
      leave-active-class="transition duration-100 ease-in"
      leave-from-class="transform scale-100 opacity-100 translate-y-0"
      leave-to-class="transform scale-95 opacity-0 -translate-y-1"
    >
      <div
        v-if="isOpen"
        class="absolute left-0 mt-2 z-50 w-[290px] sm:w-[310px] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl shadow-slate-900/15 p-3.5 select-none backdrop-blur-xl"
      >
        <!-- Header Controls -->
        <div class="flex items-center justify-between mb-3">
          <!-- Month & Year Button Switcher -->
          <div class="flex items-center gap-1">
            <button
              type="button"
              @click="currentView = currentView === 'months' ? 'days' : 'months'"
              class="px-2 py-1 rounded-lg text-xs font-bold text-slate-800 dark:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors flex items-center gap-1"
            >
              <span>{{ MONTH_NAMES[currentMonth] }}</span>
              <span class="text-[10px] text-slate-400">▾</span>
            </button>

            <button
              type="button"
              @click="currentView = currentView === 'years' ? 'days' : 'years'"
              class="px-2 py-1 rounded-lg text-xs font-bold text-slate-800 dark:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors flex items-center gap-1"
            >
              <span>{{ currentYear }}</span>
              <span class="text-[10px] text-slate-400">▾</span>
            </button>
          </div>

          <!-- Arrows -->
          <div class="flex items-center gap-0.5">
            <button
              type="button"
              @click="currentView === 'years' ? yearRangeStart -= 12 : prevMonth()"
              class="p-1.5 rounded-lg text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
              title="Sebelumnya"
            >
              <ChevronLeftIcon class="w-4 h-4 stroke-[2.5]" />
            </button>
            <button
              type="button"
              @click="currentView === 'years' ? yearRangeStart += 12 : nextMonth()"
              class="p-1.5 rounded-lg text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
              title="Berikutnya"
            >
              <ChevronRightIcon class="w-4 h-4 stroke-[2.5]" />
            </button>
          </div>
        </div>

        <!-- ================= VIEW 1: DAYS ================= -->
        <div v-if="currentView === 'days'">
          <!-- Weekday Labels -->
          <div class="grid grid-cols-7 mb-1.5 text-center">
            <span
              v-for="day in WEEKDAYS"
              :key="day"
              class="text-[11px] font-bold text-slate-400 dark:text-slate-500 py-1"
            >
              {{ day }}
            </span>
          </div>

          <!-- Day Cells -->
          <div class="grid grid-cols-7 gap-1">
            <button
              v-for="cell in dayCells"
              :key="cell.iso"
              type="button"
              @click="selectDay(cell)"
              :disabled="cell.isDisabled"
              class="h-8 rounded-xl text-xs font-semibold flex items-center justify-center transition-all relative"
              :class="[
                cell.isSelected
                  ? 'bg-[#0066FF] text-white shadow-md shadow-[#0066FF]/30 font-bold'
                  : cell.isCurrentMonth
                  ? 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800'
                  : 'text-slate-300 dark:text-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800/50',
                cell.isToday && !cell.isSelected ? 'ring-1.5 ring-[#0066FF] text-[#0066FF] font-bold' : '',
                cell.isDisabled ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer',
              ]"
            >
              {{ cell.dayNumber }}
            </button>
          </div>
        </div>

        <!-- ================= VIEW 2: MONTHS (NO HTML SELECT) ================= -->
        <div v-else-if="currentView === 'months'" class="grid grid-cols-3 gap-2 py-1">
          <button
            v-for="(mName, idx) in MONTH_NAMES"
            :key="mName"
            type="button"
            @click="selectMonth(idx)"
            class="py-2.5 px-2 rounded-xl text-xs font-bold text-center transition-all"
            :class="[
              idx === currentMonth
                ? 'bg-[#0066FF] text-white shadow-sm font-bold'
                : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800',
            ]"
          >
            {{ mName }}
          </button>
        </div>

        <!-- ================= VIEW 3: YEARS ================= -->
        <div v-else class="grid grid-cols-3 gap-2 py-1">
          <button
            v-for="y in 12"
            :key="yearRangeStart + y - 1"
            type="button"
            @click="selectYear(yearRangeStart + y - 1)"
            class="py-2.5 px-2 rounded-xl text-xs font-bold text-center transition-all"
            :class="[
              yearRangeStart + y - 1 === currentYear
                ? 'bg-[#0066FF] text-white shadow-sm font-bold'
                : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800',
            ]"
          >
            {{ yearRangeStart + y - 1 }}
          </button>
        </div>

        <!-- Footer: Today quick button -->
        <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
          <button
            type="button"
            @click="selectToday"
            class="font-bold text-[#0066FF] dark:text-blue-400 hover:underline px-1 py-0.5 rounded"
          >
            Hari Ini
          </button>

          <button
            type="button"
            @click="isOpen = false; currentView = 'days'"
            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 px-1 py-0.5"
          >
            Tutup
          </button>
        </div>
      </div>
    </Transition>
  </div>
</template>
