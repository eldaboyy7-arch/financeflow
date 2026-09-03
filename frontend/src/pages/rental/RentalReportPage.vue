<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { useVehiclesStore } from '@/stores/vehicles'
import { useFormatCurrency } from '@/composables/useFormatCurrency'
import MoneySpinner from '@/components/MoneySpinner.vue'
import {
  ArrowTrendingUpIcon,
  ArrowTrendingDownIcon,
  BanknotesIcon,
  TruckIcon,
  ChartBarIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  PrinterIcon,
  ChevronDownIcon,
} from '@heroicons/vue/24/outline'

const vehiclesStore = useVehiclesStore()
const { formatCurrency } = useFormatCurrency()

const now = new Date()
const month = ref(now.getMonth() + 1)
const year = ref(now.getFullYear())

const report = ref<any>(null)
const loading = ref(false)
const showPeriodPopover = ref(false)
const popoverRef = ref<HTMLElement | null>(null)
const expandedVehicleId = ref<number | null>(null)

const monthNames = [
  'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
]
const shortMonthNames = [
  'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
  'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
]
const years = [now.getFullYear(), now.getFullYear() - 1, now.getFullYear() - 2]

const printedAt = computed(() => {
  return new Date().toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }) + ' WIB'
})

async function fetchReport() {
  loading.value = true
  try {
    report.value = await vehiclesStore.fetchReport(month.value, year.value)
  } finally {
    loading.value = false
  }
}

function prevMonth() {
  if (month.value === 1) {
    month.value = 12
    year.value -= 1
  } else {
    month.value -= 1
  }
  fetchReport()
}

function nextMonth() {
  const isCurrentMonth = month.value === (now.getMonth() + 1) && year.value === now.getFullYear()
  if (isCurrentMonth) return
  if (month.value === 12) {
    month.value = 1
    year.value += 1
  } else {
    month.value += 1
  }
  fetchReport()
}

const isNextDisabled = computed(() =>
  month.value === (now.getMonth() + 1) && year.value === now.getFullYear()
)

function selectMonth(m: number) {
  month.value = m
  showPeriodPopover.value = false
  fetchReport()
}

function selectYear(y: number) {
  year.value = y
  fetchReport()
}

function toggleExpand(id: number) {
  expandedVehicleId.value = expandedVehicleId.value === id ? null : id
}

function printReport() {
  window.print()
}

function formatDate(dateStr: string) {
  if (!dateStr) return ''
  if (/^\d{1,2}\s[A-Za-z]{3}\s\d{4}$/.test(dateStr)) return dateStr
  const d = new Date(dateStr)
  if (isNaN(d.getTime())) return dateStr
  return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
}

function handleClickOutside(e: MouseEvent) {
  if (popoverRef.value && !popoverRef.value.contains(e.target as Node)) {
    showPeriodPopover.value = false
  }
}

// Persentase Margin Laba Keseluruhan
const profitMargin = computed(() => {
  if (!report.value || report.value.total_income <= 0) return 0
  const margin = (report.value.total_profit / report.value.total_income) * 100
  return Math.round(margin * 10) / 10
})

// Kendaraan yang memiliki transaksi
const activeVehicles = computed(() =>
  (report.value?.vehicles || []).filter((v: any) => v.income > 0 || v.expense > 0)
)

// ── Vertical Column Chart (Tinggi Tetap 240px, Kelihatan Semua Mobil) ────────
const columnChartOptions = computed(() => {
  const count = (report.value?.vehicles || []).length
  return {
    chart: {
      type: 'bar',
      fontFamily: 'Inter, system-ui, sans-serif',
      toolbar: { show: false },
      background: 'transparent',
      zoom: { enabled: false },
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: count <= 2 ? '30%' : count <= 4 ? '45%' : '60%',
        borderRadius: 4,
        borderRadiusApplication: 'end',
      },
    },
    dataLabels: { enabled: false },
    colors: ['#10B981', '#F43F5E'],
    xaxis: {
      categories: (report.value?.vehicles || []).map((v: any) => {
        const name = v.name || 'Armada'
        return v.plate_number ? [name, `(${v.plate_number})`] : [name]
      }),
      axisBorder: { show: false },
      axisTicks: { show: false },
      labels: {
        style: { colors: '#94A3B8', fontSize: '11px', fontWeight: 600 },
        rotate: 0,
        trim: false,
        maxHeight: 60,
      },
    },
    yaxis: {
      labels: {
        style: { colors: '#94A3B8', fontSize: '10px', fontWeight: 500 },
        formatter: (val: number) => {
          if (val >= 1000000) return `${(val / 1000000).toFixed(1)}jt`
          if (val >= 1000) return `${(val / 1000).toFixed(0)}rb`
          return `${val}`
        },
      },
    },
    grid: {
      borderColor: '#33415518',
      strokeDashArray: 4,
      xaxis: { lines: { show: false } },
      yaxis: { lines: { show: true } },
      padding: { top: 0, right: 10, bottom: 0, left: 5 },
    },
    legend: {
      show: false,
    },
    tooltip: {
      theme: 'dark',
      shared: true,
      intersect: false,
      y: {
        formatter: (val: number) => `Rp ${Number(val).toLocaleString('id-ID')}`,
      },
    },
  }
})

const columnChartSeries = computed(() => {
  const list = report.value?.vehicles || []
  return [
    {
      name: 'Sewa Masuk',
      data: list.map((v: any) => Number(v.income) || 0),
    },
    {
      name: 'Biaya Operasional',
      data: list.map((v: any) => Number(v.expense) || 0),
    },
  ]
})

onMounted(() => {
  document.addEventListener('mousedown', handleClickOutside)
  vehiclesStore.fetchVehicles()
  fetchReport()
})

onBeforeUnmount(() => {
  document.removeEventListener('mousedown', handleClickOutside)
})
</script>

<template>
  <div class="space-y-4 sm:space-y-5 print:space-y-3">
    <!-- Header: Custom Month/Year Picker & Cetak -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 print:hidden">
      <div>
        <h1 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white">Laporan Laba Rugi Armada</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
          Perbandingan pendapatan sewa dan rincian biaya operasional armada
        </p>
      </div>

      <!-- Custom Stepper & Popover -->
      <div class="flex items-center justify-between sm:justify-end gap-2 w-full sm:w-auto">
        <div ref="popoverRef" class="relative flex-1 sm:flex-none">
          <div class="flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-1 shadow-sm justify-between sm:justify-start">
            <button
              @click="prevMonth"
              class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
              title="Bulan Sebelumnya"
            >
              <ChevronLeftIcon class="w-4 h-4" />
            </button>

            <!-- Trigger Button for Popover -->
            <button
              @click="showPeriodPopover = !showPeriodPopover"
              class="flex items-center gap-1.5 px-3 py-1 text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-700/70 rounded-lg transition-colors"
            >
              <span>{{ monthNames[month - 1] }} {{ year }}</span>
              <ChevronDownIcon
                class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200"
                :class="showPeriodPopover ? 'rotate-180' : ''"
              />
            </button>

            <button
              @click="nextMonth"
              :disabled="isNextDisabled"
              :class="[
                'p-1.5 rounded-lg transition-colors',
                isNextDisabled
                  ? 'text-slate-300 dark:text-slate-600 cursor-not-allowed'
                  : 'text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700'
              ]"
              title="Bulan Berikutnya"
            >
              <ChevronRightIcon class="w-4 h-4" />
            </button>
          </div>

          <!-- Custom Popover Menu -->
          <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="transform -translate-y-2 opacity-0"
            enter-to-class="transform translate-y-0 opacity-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="transform translate-y-0 opacity-100"
            leave-to-class="transform -translate-y-2 opacity-0"
          >
            <div
              v-if="showPeriodPopover"
              class="absolute left-0 sm:right-0 sm:left-auto top-full mt-2 z-50 p-3.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl w-72"
            >
              <!-- Year Switcher Pills -->
              <div class="flex items-center gap-1 mb-3 p-1 bg-slate-100 dark:bg-slate-700/60 rounded-xl">
                <button
                  v-for="y in years"
                  :key="y"
                  @click="selectYear(y)"
                  :class="[
                    'flex-1 py-1 text-xs font-bold rounded-lg transition-all',
                    year === y
                      ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm'
                      : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
                  ]"
                >
                  {{ y }}
                </button>
              </div>

              <!-- Months 3x4 Grid -->
              <div class="grid grid-cols-3 gap-1.5">
                <button
                  v-for="(mName, idx) in shortMonthNames"
                  :key="idx"
                  @click="selectMonth(idx + 1)"
                  :class="[
                    'py-2 text-xs font-semibold rounded-xl transition-all',
                    month === idx + 1
                      ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 font-bold shadow-sm'
                      : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/70'
                  ]"
                >
                  {{ mName }}
                </button>
              </div>
            </div>
          </Transition>
        </div>

        <!-- Print Button -->
        <button
          @click="printReport"
          class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold border border-slate-200 dark:border-slate-700 shadow-sm transition-all shrink-0"
        >
          <PrinterIcon class="w-3.5 h-3.5 text-slate-500" />
          <span>Cetak</span>
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="card p-12 flex items-center justify-center print:hidden">
      <MoneySpinner size="md" :text="`Menghitung laporan ${monthNames[month - 1]} ${year}...`" />
    </div>

    <template v-else-if="report">
      <!-- TAMPILAN INTERAKTIF DI LAYAR WEB (Disembunyikan saat dicetak) -->
      <div class="space-y-4 sm:space-y-5 print:hidden">
      <!-- 3 Kartu Ringkasan Finansial Utama -->
      <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 sm:gap-4">
        <!-- 1. Sewa Masuk -->
        <div class="card p-3.5 sm:p-5 flex flex-col justify-between">
          <div class="flex items-center justify-between gap-1.5 mb-1.5">
            <span class="text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 truncate">
              Sewa Masuk
            </span>
            <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
              <ArrowTrendingUpIcon class="w-4 h-4 sm:w-5 sm:h-5 stroke-[2.5]" />
            </div>
          </div>
          <div>
            <p class="text-base sm:text-2xl font-bold text-emerald-600 dark:text-emerald-400 tabular-nums tracking-tight">
              {{ formatCurrency(report.total_income) }}
            </p>
            <p class="text-[10px] text-slate-400 mt-0.5 truncate">Total omzet sewa</p>
          </div>
        </div>

        <!-- 2. Biaya Operasional -->
        <div class="card p-3.5 sm:p-5 flex flex-col justify-between">
          <div class="flex items-center justify-between gap-1.5 mb-1.5">
            <span class="text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 truncate">
              Biaya Operasional
            </span>
            <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
              <ArrowTrendingDownIcon class="w-4 h-4 sm:w-5 sm:h-5 stroke-[2.5]" />
            </div>
          </div>
          <div>
            <p class="text-base sm:text-2xl font-bold text-rose-500 dark:text-rose-400 tabular-nums tracking-tight">
              {{ formatCurrency(report.total_expense) }}
            </p>
            <p class="text-[10px] text-slate-400 mt-0.5 truncate">BBM, servis, perawatan</p>
          </div>
        </div>

        <!-- 3. Laba Bersih & Margin -->
        <div class="card p-3.5 sm:p-5 col-span-2 sm:col-span-1 flex items-center justify-between">
          <div>
            <div class="flex items-center gap-1.5 mb-1">
              <span class="text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400">Laba Bersih</span>
              <span
                v-if="report.total_income > 0"
                class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300"
              >
                Margin {{ profitMargin }}%
              </span>
            </div>
            <p :class="['text-lg sm:text-2xl font-bold tabular-nums tracking-tight', report.total_profit >= 0 ? 'text-slate-900 dark:text-white' : 'text-rose-500']">
              {{ formatCurrency(report.total_profit) }}
            </p>
            <p class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5">
              {{ report.total_profit >= 0 ? 'Surplus Keuntungan Bersih' : 'Defisit Biaya Operasional' }}
            </p>
          </div>
          <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center shrink-0">
            <BanknotesIcon class="w-5 h-5 sm:w-6 sm:h-6 stroke-[2.2]" />
          </div>
        </div>
      </div>

      <!-- GRAFIK PERBANDINGAN ANTAR-MOBIL -->
      <div v-if="report.vehicles && report.vehicles.length > 0" class="card p-3.5 sm:p-5 print:hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 mb-2">
          <div>
            <h2 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
              <ChartBarIcon class="w-4 h-4 text-slate-400" />
              Perbandingan Kinerja Antar Mobil
            </h2>
            <p class="text-[11px] text-slate-400 mt-0.5">
              Komparasi pemasukan dan pengeluaran per unit mobil
            </p>
          </div>

          <!-- Legend -->
          <div class="flex items-center gap-3 text-xs font-semibold">
            <span class="inline-flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400">
              <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
              Sewa Masuk
            </span>
            <span class="inline-flex items-center gap-1.5 text-rose-500 dark:text-rose-400">
              <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0"></span>
              Biaya Operasional
            </span>
          </div>
        </div>

        <div v-if="activeVehicles.length > 0" class="w-full overflow-x-auto pt-1 pb-1">
          <div :style="{ minWidth: (report.vehicles?.length || 0) > 4 ? `${(report.vehicles?.length || 0) * 85}px` : '100%' }">
            <apexchart
              type="bar"
              height="240"
              :options="columnChartOptions"
              :series="columnChartSeries"
            />
          </div>
        </div>
        <div v-else class="h-28 sm:h-36 flex flex-col items-center justify-center text-slate-400 gap-1 text-center">
          <TruckIcon class="w-6 h-6 opacity-40" />
          <p class="text-xs">Belum ada transaksi di bulan {{ monthNames[month - 1] }} {{ year }}</p>
        </div>
      </div>

      <!-- ================= SATU KARTU RINCIAN TERPADU (SIMPLE & LANGSUNG PAHAM) ================= -->
      <div class="card overflow-hidden">
        <!-- Header Rincian -->
        <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
          <div>
            <h2 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">
              Rincian Performa Tiap Mobil
            </h2>
            <p class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5">
              Klik pada mobil untuk melihat rincian transaksinya
            </p>
          </div>
          <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
            {{ report.vehicles?.length || 0 }} Unit
          </span>
        </div>

        <!-- Ringkasan Pos Kategori Sederhana (Chip Halus, Tanpa Header Berteriak) -->
        <div
          v-if="(report.expense_breakdown?.length || 0) > 0 || (report.income_breakdown?.length || 0) > 0"
          class="px-4 py-2.5 bg-slate-50/60 dark:bg-slate-800/40 border-b border-slate-100 dark:border-slate-700/60 flex items-center gap-1.5 flex-wrap text-xs"
        >
          <span class="text-[11px] font-semibold text-slate-400 mr-1">Rincian pos:</span>
          <!-- Pemasukan -->
          <span
            v-for="item in report.income_breakdown"
            :key="'inc-' + item.category_id"
            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-[11px] font-medium"
          >
            <span>{{ item.icon }}</span>
            <span>{{ item.category }}:</span>
            <strong class="tabular-nums">{{ formatCurrency(item.total) }}</strong>
          </span>
          <!-- Pengeluaran -->
          <span
            v-for="item in report.expense_breakdown"
            :key="'exp-' + item.category_id"
            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 text-[11px] font-medium"
          >
            <span>{{ item.icon }}</span>
            <span>{{ item.category }}:</span>
            <strong class="tabular-nums">{{ formatCurrency(item.total) }}</strong>
          </span>
        </div>

        <!-- Empty State -->
        <div v-if="!report.vehicles || report.vehicles.length === 0" class="p-8 sm:p-12 flex flex-col items-center text-center">
          <div class="w-12 h-12 bg-slate-100 dark:bg-slate-700/60 rounded-2xl flex items-center justify-center mb-3">
            <ChartBarIcon class="w-6 h-6 text-slate-400" />
          </div>
          <p class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300">Belum ada unit armada</p>
          <p class="text-xs text-slate-400 mt-1 max-w-xs">
            Daftarkan mobil di tab Armada untuk melihat analisis laba rugi.
          </p>
        </div>

        <!-- List Kendaraan Bersih & Nyaman (Universal untuk Desktop & Mobile) -->
        <div v-else class="divide-y divide-slate-100 dark:divide-slate-700/60">
          <div
            v-for="v in report.vehicles"
            :key="v.id"
            class="transition-colors"
          >
            <!-- Baris Utama Mobil (Clean Row) -->
            <div
              @click="toggleExpand(v.id)"
              class="p-3.5 sm:p-4 flex items-center justify-between cursor-pointer hover:bg-slate-50/70 dark:hover:bg-slate-800/50"
            >
              <!-- Info Mobil & Angka Sewa/Biaya -->
              <div class="flex items-center gap-3 min-w-0 pr-2">
                <div
                  class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center shrink-0"
                  :style="{ backgroundColor: (v.color || '#2563EB') + '20' }"
                >
                  <TruckIcon class="w-4 h-4 sm:w-5 sm:h-5" :style="{ color: v.color || '#2563EB' }" />
                </div>
                <div class="min-w-0">
                  <div class="flex items-center gap-2">
                    <h3 class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm truncate leading-tight">
                      {{ v.name }}
                    </h3>
                    <span class="text-[11px] text-slate-400 font-medium shrink-0">
                      {{ v.plate_number || 'Tanpa Plat' }}
                    </span>
                  </div>
                  <!-- Sewa & Biaya bersanding rapi -->
                  <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                    Sewa: <strong class="text-emerald-600 dark:text-emerald-400 font-semibold tabular-nums">{{ formatCurrency(v.income) }}</strong>
                    <span class="mx-1.5 text-slate-300 dark:text-slate-600">·</span>
                    Biaya: <strong class="text-rose-500 dark:text-rose-400 font-semibold tabular-nums">{{ formatCurrency(v.expense) }}</strong>
                  </p>
                </div>
              </div>

              <!-- Laba Bersih & Panah Expand -->
              <div class="flex items-center gap-2 shrink-0">
                <div class="text-right">
                  <span class="text-[9px] sm:text-[10px] uppercase font-bold text-slate-400 block tracking-wider">
                    Laba Bersih
                  </span>
                  <span
                    :class="[
                      'text-xs sm:text-sm font-black tabular-nums',
                      v.profit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500'
                    ]"
                  >
                    {{ v.profit >= 0 ? '+' : '' }}{{ formatCurrency(v.profit) }}
                  </span>
                </div>
                <ChevronDownIcon
                  class="w-4 h-4 text-slate-400 transition-transform duration-200"
                  :class="expandedVehicleId === v.id ? 'rotate-180' : ''"
                />
              </div>
            </div>

            <!-- Drawer Transaksi Detail Saat Mobil Diklik -->
            <div v-if="expandedVehicleId === v.id" class="px-4 pb-3.5 pt-1 bg-slate-50/70 dark:bg-slate-800/40 border-t border-slate-100 dark:border-slate-700/50">
              <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/80 dark:border-slate-700/80 space-y-2">
                <div class="flex items-center justify-between text-xs font-bold text-slate-700 dark:text-slate-200 border-b border-slate-100 dark:border-slate-700/60 pb-1.5">
                  <span>Catatan Transaksi {{ v.name }}:</span>
                  <span class="text-[10px] text-slate-400 font-normal">{{ v.transactions?.length || 0 }} transaksi tercatat</span>
                </div>

                <div v-if="!v.transactions || v.transactions.length === 0" class="text-xs text-slate-400 italic py-1.5 text-center">
                  Belum ada catatan transaksi di bulan ini.
                </div>

                <div v-else class="divide-y divide-slate-100 dark:divide-slate-700/50 max-h-48 overflow-y-auto">
                  <div
                    v-for="tx in v.transactions"
                    :key="tx.id"
                    class="py-1.5 flex items-center justify-between text-xs"
                  >
                    <div class="flex items-center gap-2 min-w-0 pr-2">
                      <span class="text-base leading-none shrink-0">
                        {{ tx.category?.icon || (tx.type === 'income' ? '💰' : '⛽') }}
                      </span>
                      <div class="min-w-0">
                        <span class="font-semibold text-slate-800 dark:text-slate-200 truncate block">
                          {{ tx.category?.name || 'Transaksi' }}
                          <span v-if="tx.description" class="font-normal text-slate-500 dark:text-slate-400 text-[11px] ml-1">
                            — {{ tx.description }}
                          </span>
                        </span>
                        <span class="text-[10px] text-slate-400">{{ formatDate(tx.date) }}</span>
                      </div>
                    </div>
                    <span
                      :class="[
                        'font-bold tabular-nums shrink-0 text-xs',
                        tx.type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500 dark:text-rose-400'
                      ]"
                    >
                      {{ tx.type === 'income' ? '+' : '−' }}{{ formatCurrency(tx.amount) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Keseluruhan Footer (Sangat Ringkas & Tegas) -->
        <div
          v-if="report.vehicles && report.vehicles.length > 0"
          class="p-3.5 sm:p-4 bg-slate-100/70 dark:bg-slate-800/90 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between"
        >
          <div>
            <span class="text-xs font-bold text-slate-900 dark:text-white block">
              Total Seluruh Armada
            </span>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
              Sewa: <strong class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ formatCurrency(report.total_income) }}</strong>
              <span class="mx-1 text-slate-300 dark:text-slate-600">·</span>
              Biaya: <strong class="text-rose-500 dark:text-rose-400 font-semibold">{{ formatCurrency(report.total_expense) }}</strong>
            </p>
          </div>

          <div class="text-right">
            <span class="text-[9px] uppercase font-bold text-slate-400 block tracking-wider">
              Total Laba
            </span>
            <span
              :class="[
                'text-sm sm:text-base font-black tabular-nums',
                report.total_profit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500'
              ]"
            >
              {{ report.total_profit >= 0 ? '+' : '' }}{{ formatCurrency(report.total_profit) }}
            </span>
          </div>
        </div>
      </div>
    </div>

      <!-- ============================================================= -->
      <!-- DOKUMEN CETAK FORMAL & SEDERHANA (Hanya Muncul Saat Dicetak)  -->
      <!-- Format Hitam Putih / Abu-abu A4 yang Rapi & Rincian Detail    -->
      <!-- ============================================================= -->
      <div class="hidden print:block text-slate-900 bg-white font-sans text-xs leading-normal">
        <!-- 1. KOP LAPORAN -->
        <div class="border-b-2 border-slate-900 pb-2.5 mb-4">
          <div class="flex items-start justify-between">
            <div>
              <h1 class="text-base font-black tracking-tight text-slate-900 uppercase">
                LAPORAN KEUANGAN & OPERASIONAL ARMADA RENTAL
              </h1>
              <p class="text-[11px] font-semibold text-slate-600 mt-0.5">
                Sistem Manajemen Rental Mobil — FinanceFlow
              </p>
            </div>
            <div class="text-right text-[11px] text-slate-700 font-medium">
              <p><strong>Periode:</strong> {{ monthNames[month - 1] }} {{ year }}</p>
              <p><strong>Dicetak:</strong> {{ printedAt }}</p>
            </div>
          </div>
        </div>

        <!-- 2. REKAPITULASI FINANSIAL UTAMA -->
        <div class="border border-slate-300 rounded p-2.5 mb-4 bg-slate-50/60">
          <div class="grid grid-cols-4 gap-2 text-center">
            <div class="border-r border-slate-200 pr-2">
              <span class="text-[10px] uppercase font-bold text-slate-500 block">Total Sewa Masuk</span>
              <span class="text-sm font-bold text-slate-900 font-mono">{{ formatCurrency(report.total_income) }}</span>
            </div>
            <div class="border-r border-slate-200 pr-2">
              <span class="text-[10px] uppercase font-bold text-slate-500 block">Total Biaya Operasional</span>
              <span class="text-sm font-bold text-slate-900 font-mono">{{ formatCurrency(report.total_expense) }}</span>
            </div>
            <div class="border-r border-slate-200 pr-2">
              <span class="text-[10px] uppercase font-bold text-slate-500 block">Laba Bersih Armada</span>
              <span class="text-sm font-bold text-slate-900 font-mono">
                {{ report.total_profit >= 0 ? '+' : '' }}{{ formatCurrency(report.total_profit) }}
              </span>
            </div>
            <div>
              <span class="text-[10px] uppercase font-bold text-slate-500 block">Total Armada Aktif</span>
              <span class="text-sm font-bold text-slate-900">{{ report.vehicles?.length || 0 }} Unit</span>
            </div>
          </div>
        </div>

        <!-- 3. TABEL I: REKAPITULASI PER MOBIL -->
        <div class="mb-5">
          <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-1 pb-1 border-b border-slate-300">
            I. Rekapitulasi Performa Keuangan Tiap Armada
          </h2>
          <table class="w-full border-collapse text-left text-xs">
            <thead>
              <tr class="border-y border-slate-800 bg-slate-100 font-bold text-slate-800">
                <th class="py-1.5 px-2 w-8 text-center">No</th>
                <th class="py-1.5 px-2">Nama Mobil</th>
                <th class="py-1.5 px-2">Plat Nomor</th>
                <th class="py-1.5 px-2 text-right">Sewa Masuk (Rp)</th>
                <th class="py-1.5 px-2 text-right">Biaya (Rp)</th>
                <th class="py-1.5 px-2 text-right">Laba Bersih (Rp)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
              <tr v-for="(v, i) in report.vehicles" :key="v.id">
                <td class="py-1.5 px-2 text-center text-slate-600">{{ i + 1 }}</td>
                <td class="py-1.5 px-2 font-semibold text-slate-900">{{ v.name }}</td>
                <td class="py-1.5 px-2 text-slate-700 font-mono">{{ v.plate_number || '-' }}</td>
                <td class="py-1.5 px-2 text-right font-mono">{{ Number(v.income).toLocaleString('id-ID') }}</td>
                <td class="py-1.5 px-2 text-right font-mono">{{ Number(v.expense).toLocaleString('id-ID') }}</td>
                <td class="py-1.5 px-2 text-right font-bold font-mono">
                  {{ v.profit >= 0 ? '+' : '' }}{{ Number(v.profit).toLocaleString('id-ID') }}
                </td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="border-t-2 border-slate-800 font-bold bg-slate-100">
                <td colspan="3" class="py-1.5 px-2 text-center uppercase tracking-wide">Total Seluruh Armada</td>
                <td class="py-1.5 px-2 text-right font-mono">{{ Number(report.total_income).toLocaleString('id-ID') }}</td>
                <td class="py-1.5 px-2 text-right font-mono">{{ Number(report.total_expense).toLocaleString('id-ID') }}</td>
                <td class="py-1.5 px-2 text-right font-mono">
                  {{ report.total_profit >= 0 ? '+' : '' }}{{ Number(report.total_profit).toLocaleString('id-ID') }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- 4. TABEL II: RINCIAN DETAIL APA-APA SAJA PER MOBIL (INTI DETAIL LAPORAN) -->
        <div class="mb-5 page-break-avoid">
          <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-2 pb-1 border-b border-slate-300">
            II. Rincian Detail Transaksi Per Mobil (Pemasukan & Pengeluaran Apa Saja)
          </h2>

          <div v-for="v in report.vehicles" :key="'print-tx-' + v.id" class="mb-3.5 border border-slate-300 rounded p-2 page-break-avoid">
            <div class="flex items-center justify-between font-bold text-xs bg-slate-100 px-2 py-1 border-b border-slate-200 mb-1">
              <span>🚗 {{ v.name }} — Plat: {{ v.plate_number || 'Tanpa Plat' }}</span>
              <span class="text-[11px] font-normal text-slate-600">
                {{ v.transactions?.length || 0 }} Catatan Transaksi
              </span>
            </div>

            <div v-if="!v.transactions || v.transactions.length === 0" class="text-slate-400 italic text-[11px] px-2 py-1">
              Tidak ada catatan transaksi pada periode ini.
            </div>

            <table v-else class="w-full text-left text-[11px] border-collapse">
              <thead>
                <tr class="border-b border-slate-300 text-slate-700 font-semibold">
                  <th class="py-1 px-1.5 w-6 text-center">No</th>
                  <th class="py-1 px-1.5 w-24">Tanggal</th>
                  <th class="py-1 px-1.5 w-28">Kategori</th>
                  <th class="py-1 px-1.5 w-24">Jenis</th>
                  <th class="py-1 px-1.5">Keterangan</th>
                  <th class="py-1 px-1.5 text-right w-28">Jumlah (Rp)</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="(tx, idx) in v.transactions" :key="tx.id">
                  <td class="py-1 px-1.5 text-center text-slate-500">{{ idx + 1 }}</td>
                  <td class="py-1 px-1.5 text-slate-700 whitespace-nowrap">{{ formatDate(tx.date) }}</td>
                  <td class="py-1 px-1.5 font-medium text-slate-900">{{ tx.category?.name || '-' }}</td>
                  <td class="py-1 px-1.5">
                    <span :class="tx.type === 'income' ? 'text-slate-900 font-semibold' : 'text-slate-600 font-semibold'">
                      {{ tx.type === 'income' ? 'Sewa Masuk' : 'Pengeluaran' }}
                    </span>
                  </td>
                  <td class="py-1 px-1.5 text-slate-600">{{ tx.description || '-' }}</td>
                  <td class="py-1 px-1.5 text-right font-mono font-medium">
                    {{ tx.type === 'income' ? '+' : '-' }}{{ Number(tx.amount).toLocaleString('id-ID') }}
                  </td>
                </tr>
              </tbody>
              <tfoot>
                <tr class="border-t border-slate-300 font-bold bg-slate-50 text-[11px]">
                  <td colspan="5" class="py-1 px-1.5 text-right">Subtotal {{ v.name }}:</td>
                  <td class="py-1 px-1.5 text-right font-mono">
                    {{ v.profit >= 0 ? '+' : '' }}{{ Number(v.profit).toLocaleString('id-ID') }}
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        <!-- 5. TABEL III: REKAPITULASI POS BIAYA OPERASIONAL (UNTUK APA SAJA) -->
        <div v-if="report.expense_breakdown && report.expense_breakdown.length > 0" class="mb-5 page-break-avoid">
          <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-1 pb-1 border-b border-slate-300">
            III. Rekapitulasi Pos Biaya Operasional (Pengeluaran Untuk Apa Saja)
          </h2>
          <table class="w-full text-left text-xs border-collapse">
            <thead>
              <tr class="border-y border-slate-300 bg-slate-100 font-semibold text-slate-700">
                <th class="py-1 px-2 w-8 text-center">No</th>
                <th class="py-1 px-2">Pos Pengeluaran</th>
                <th class="py-1 px-2 text-right">Total Biaya (Rp)</th>
                <th class="py-1 px-2 text-right">Porsi (%)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
              <tr v-for="(exp, i) in report.expense_breakdown" :key="exp.category">
                <td class="py-1 px-2 text-center text-slate-500">{{ i + 1 }}</td>
                <td class="py-1 px-2 font-medium text-slate-900">{{ exp.category }}</td>
                <td class="py-1 px-2 text-right font-mono">{{ Number(exp.total).toLocaleString('id-ID') }}</td>
                <td class="py-1 px-2 text-right text-slate-600">
                  {{ report.total_expense > 0 ? ((exp.total / report.total_expense) * 100).toFixed(1) + '%' : '0%' }}
                </td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="border-t border-slate-800 font-bold bg-slate-100">
                <td colspan="2" class="py-1 px-2 text-right">Total Biaya:</td>
                <td class="py-1 px-2 text-right font-mono">{{ Number(report.total_expense).toLocaleString('id-ID') }}</td>
                <td class="py-1 px-2 text-right">100%</td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- 6. LEMBAR PENGESAHAN / TANDA TANGAN -->
        <div class="mt-8 pt-4 border-t border-slate-300 page-break-avoid">
          <div class="grid grid-cols-2 text-center text-xs">
            <div>
              <p class="text-slate-600 mb-14">Dibuat & Dilaporkan Oleh,</p>
              <p class="font-bold underline text-slate-900">( Admin / Bagian Operasional )</p>
            </div>
            <div>
              <p class="text-slate-600 mb-14">Diketahui & Disetujui Oleh,</p>
              <p class="font-bold underline text-slate-900">( Pemilik / Pimpinan Rental )</p>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<style scoped>
@media print {
  @page {
    margin: 1.2cm 1cm;
    size: A4 portrait;
  }
  .page-break-avoid {
    page-break-inside: avoid;
    break-inside: avoid;
  }
}
</style>
