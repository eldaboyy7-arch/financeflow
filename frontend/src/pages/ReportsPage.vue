<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { reportsApi, type MonthlyReport } from '@/api/reports'
import { useFormatCurrency } from '@/composables/useFormatCurrency'
import SelectInput from '@/components/SelectInput.vue'
import MoneySpinner from '@/components/MoneySpinner.vue'
import type { SelectOption } from '@/components/SelectInput.vue'
import dayjs from 'dayjs'
import 'dayjs/locale/id'
import {
  ArrowTrendingUpIcon,
  ArrowTrendingDownIcon,
  ScaleIcon,
  BanknotesIcon,
  ChartBarIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  PrinterIcon,
  InboxIcon,
  BuildingLibraryIcon,
  ArrowUpRightIcon,
  ArrowDownRightIcon,
} from '@heroicons/vue/24/outline'

dayjs.locale('id')

const { t } = useI18n()
const { formatCurrency, formatPercent } = useFormatCurrency()

const now = dayjs()
const selectedYear = ref(now.year())
const selectedMonth = ref(now.month() + 1)
const activeTab = ref<'expense' | 'income' | 'accounts'>('expense')
const report = ref<MonthlyReport | null>(null)
const loading = ref(false)
const error = ref('')

const years = Array.from({ length: 5 }, (_, i) => now.year() - i)
const yearOptions: SelectOption[] = years.map((y) => ({
  value: y,
  label: String(y),
}))

const monthOptions: SelectOption[] = [
  { value: 1, label: 'Januari' },
  { value: 2, label: 'Februari' },
  { value: 3, label: 'Maret' },
  { value: 4, label: 'April' },
  { value: 5, label: 'Mei' },
  { value: 6, label: 'Juni' },
  { value: 7, label: 'Juli' },
  { value: 8, label: 'Agustus' },
  { value: 9, label: 'September' },
  { value: 10, label: 'Oktober' },
  { value: 11, label: 'November' },
  { value: 12, label: 'Desember' },
]

const monthLabel = computed(() => monthOptions.find((m) => m.value === Number(selectedMonth.value))?.label)

const reportCache = new Map<string, MonthlyReport>()

async function loadReport(force = false) {
  const key = `${selectedYear.value}-${selectedMonth.value}`
  if (reportCache.has(key) && !force) {
    report.value = reportCache.get(key)!
    return
  }

  loading.value = true
  error.value = ''
  try {
    const { data } = await reportsApi.monthly(Number(selectedYear.value), Number(selectedMonth.value))
    report.value = data
    reportCache.set(key, data)
  } catch {
    error.value = 'Gagal memuat laporan keuangan.'
  } finally {
    loading.value = false
  }
}

function prevMonth() {
  if (selectedMonth.value === 1) {
    selectedMonth.value = 12
    selectedYear.value--
  } else {
    selectedMonth.value--
  }
  loadReport()
}

function nextMonth() {
  if (selectedMonth.value === 12) {
    selectedMonth.value = 1
    selectedYear.value++
  } else {
    selectedMonth.value++
  }
  loadReport()
}

function printReport() {
  window.print()
}

onMounted(loadReport)

const totalExpenseBreakdown = computed(() =>
  report.value?.expense_breakdown.reduce((s, i) => s + i.total, 0) ?? 0
)

const totalIncomeBreakdown = computed(() =>
  report.value?.income_breakdown.reduce((s, i) => s + i.total, 0) ?? 0
)

// Donut chart options for reports
const expenseDonutOptions = computed(() => ({
  chart: { type: 'donut', background: 'transparent' },
  labels: report.value?.expense_breakdown.map((b) => b.category) ?? [],
  colors: report.value?.expense_breakdown.map((b) => b.color || '#F43F5E') ?? [],
  legend: { show: false },
  dataLabels: { enabled: false },
  plotOptions: { pie: { donut: { size: '75%' } } },
  stroke: { width: 0 },
  tooltip: {
    theme: 'dark',
    y: { formatter: (val: number) => `Rp ${Number(val).toLocaleString('id-ID')}` },
  },
}))

const expenseDonutSeries = computed(() =>
  report.value?.expense_breakdown.map((b) => b.total) ?? []
)
</script>

<template>
  <div class="space-y-5 sm:space-y-6 print:space-y-4">
    <!-- Header & Period Filter Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 print:hidden">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">Laporan Keuangan</h1>
        <p class="text-xs sm:text-sm text-slate-400 dark:text-slate-500 mt-0.5">
          Ringkasan arus kas, tabungan, dan rincian per kategori
        </p>
      </div>

      <!-- Period Navigator & Controls -->
      <div class="flex flex-wrap items-center gap-2">
        <!-- Prev / Next Month Stepper -->
        <div class="flex items-center bg-slate-100 dark:bg-slate-800 rounded-xl p-1 border border-slate-200/60 dark:border-slate-700/60">
          <button
            @click="prevMonth"
            class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-white dark:hover:bg-slate-700 transition-all"
            title="Bulan Sebelumnya"
          >
            <ChevronLeftIcon class="w-4 h-4" />
          </button>
          <span class="px-2 text-xs font-bold text-slate-700 dark:text-slate-300 min-w-[90px] text-center truncate">
            {{ monthLabel }} {{ selectedYear }}
          </span>
          <button
            @click="nextMonth"
            class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-white dark:hover:bg-slate-700 transition-all"
            title="Bulan Berikutnya"
          >
            <ChevronRightIcon class="w-4 h-4" />
          </button>
        </div>

        <!-- Quick Selects -->
        <div class="w-32 hidden sm:block">
          <SelectInput
            v-model="selectedMonth"
            :options="monthOptions"
            @change="loadReport"
            placeholder="Pilih Bulan"
          />
        </div>
        <div class="w-24 hidden sm:block">
          <SelectInput
            v-model="selectedYear"
            :options="yearOptions"
            @change="loadReport"
            placeholder="Pilih Tahun"
          />
        </div>

        <!-- Print / Export Button -->
        <button
          @click="printReport"
          class="p-2 sm:px-3 sm:py-2 text-xs font-semibold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-all flex items-center gap-1.5 shadow-sm"
          title="Cetak Laporan / PDF"
        >
          <PrinterIcon class="w-4 h-4 text-slate-500" />
          <span class="hidden sm:inline">Cetak / PDF</span>
        </button>
      </div>
    </div>

    <!-- Error Alert -->
    <div v-if="error" class="card p-4 text-sm text-rose-600 bg-rose-50 dark:bg-rose-900/20 rounded-xl">
      {{ error }}
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="card p-4 sm:p-8 flex items-center justify-center">
      <MoneySpinner size="md" text="Menganalisis laporan keuangan bulanan..." subtext="Mengalkulasi arus kas & rasio tabungan" />
    </div>

    <!-- REPORT CONTENT -->
    <template v-if="report && !loading">
      <!-- Summary KPI Cards (2x2 on Mobile, 4 Cols on Desktop) -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total Income -->
        <div class="bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-3.5 sm:p-5 shadow-sm hover:shadow-md hover:border-emerald-200 dark:hover:border-emerald-800/40 transition-all flex flex-col justify-between">
          <div class="flex items-center justify-between gap-1.5 mb-2.5">
            <div class="flex items-center gap-1.5 sm:gap-2">
              <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-100 dark:border-emerald-800/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                <ArrowTrendingUpIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-[2.5]" />
              </div>
              <span class="text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 truncate">Pemasukan</span>
            </div>
            <span class="text-[9px] sm:text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-1.5 py-0.5 rounded-full border border-emerald-100 dark:border-emerald-800/30">
              Masuk
            </span>
          </div>

          <div class="my-0.5 sm:my-1">
            <p class="text-base sm:text-xl font-black text-slate-900 dark:text-white tracking-tight tabular-nums truncate">
              {{ formatCurrency(report.total_income) }}
            </p>
          </div>

          <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700/60 text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 mt-1">
            <span
              v-if="report.comparison.income_change !== 0"
              :class="report.comparison.income_change >= 0 ? 'text-emerald-600 dark:text-emerald-400 font-medium' : 'text-rose-500 font-medium'"
              class="flex items-center gap-0.5"
            >
              <component :is="report.comparison.income_change >= 0 ? ArrowUpRightIcon : ArrowDownRightIcon" class="w-3 h-3 stroke-2" />
              {{ Math.abs(report.comparison.income_change) }}%
            </span>
            <span v-else class="text-slate-400">0%</span>
            <span class="text-slate-400">vs bulan lalu</span>
          </div>
        </div>

        <!-- Total Expense -->
        <div class="bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-3.5 sm:p-5 shadow-sm hover:shadow-md hover:border-rose-200 dark:hover:border-rose-800/40 transition-all flex flex-col justify-between">
          <div class="flex items-center justify-between gap-1.5 mb-2.5">
            <div class="flex items-center gap-1.5 sm:gap-2">
              <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-100 dark:border-rose-800/40 flex items-center justify-center text-rose-600 dark:text-rose-400 shrink-0">
                <ArrowTrendingDownIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-[2.5]" />
              </div>
              <span class="text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 truncate">Pengeluaran</span>
            </div>
            <span class="text-[9px] sm:text-[10px] font-semibold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 px-1.5 py-0.5 rounded-full border border-rose-100 dark:border-rose-800/30">
              Keluar
            </span>
          </div>

          <div class="my-0.5 sm:my-1">
            <p class="text-base sm:text-xl font-black text-slate-900 dark:text-white tracking-tight tabular-nums truncate">
              {{ formatCurrency(report.total_expense) }}
            </p>
          </div>

          <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700/60 text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 mt-1">
            <span
              v-if="report.comparison.expense_change !== 0"
              :class="report.comparison.expense_change <= 0 ? 'text-emerald-600 dark:text-emerald-400 font-medium' : 'text-rose-500 font-medium'"
              class="flex items-center gap-0.5"
            >
              <component :is="report.comparison.expense_change <= 0 ? ArrowDownRightIcon : ArrowUpRightIcon" class="w-3 h-3 stroke-2" />
              {{ Math.abs(report.comparison.expense_change) }}%
            </span>
            <span v-else class="text-slate-400">0%</span>
            <span class="text-slate-400">vs bulan lalu</span>
          </div>
        </div>

        <!-- Net Cash Flow -->
        <div class="bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-3.5 sm:p-5 shadow-sm hover:shadow-md hover:border-primary-200 dark:hover:border-primary-800/40 transition-all flex flex-col justify-between">
          <div class="flex items-center justify-between gap-1.5 mb-2.5">
            <div class="flex items-center gap-1.5 sm:gap-2">
              <div
                class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl flex items-center justify-center border shrink-0"
                :class="report.net_cash_flow >= 0
                  ? 'bg-blue-50 dark:bg-blue-950/50 border-blue-100 dark:border-blue-800/40 text-[#0066FF] dark:text-blue-400'
                  : 'bg-rose-50 dark:bg-rose-950/50 border-rose-100 dark:border-rose-800/40 text-rose-600 dark:text-rose-400'"
              >
                <ScaleIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-[2]" />
              </div>
              <span class="text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 truncate">Arus Kas Bersih</span>
            </div>
            <span
              class="text-[9px] sm:text-[10px] font-semibold px-1.5 py-0.5 rounded-full border"
              :class="report.net_cash_flow >= 0
                ? 'text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800/30'
                : 'text-rose-700 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800/30'"
            >
              {{ report.net_cash_flow >= 0 ? 'Surplus' : 'Defisit' }}
            </span>
          </div>

          <div class="my-0.5 sm:my-1">
            <p
              class="text-base sm:text-xl font-black tracking-tight tabular-nums truncate"
              :class="report.net_cash_flow >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'"
            >
              {{ formatCurrency(report.net_cash_flow) }}
            </p>
          </div>

          <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700/60 text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 mt-1">
            <span>Tabungan bersih</span>
            <span :class="report.net_cash_flow >= 0 ? 'text-emerald-600 dark:text-emerald-400 font-medium' : 'text-rose-500 font-medium'">
              {{ report.net_cash_flow >= 0 ? 'Surplus bulanan' : 'Defisit bulanan' }}
            </span>
          </div>
        </div>

        <!-- Saving Rate -->
        <div class="bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-3.5 sm:p-5 shadow-sm hover:shadow-md hover:border-primary-200 dark:hover:border-primary-800/40 transition-all flex flex-col justify-between">
          <div class="flex items-center justify-between gap-1.5 mb-2.5">
            <div class="flex items-center gap-1.5 sm:gap-2">
              <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-800/40 text-[#0066FF] dark:text-blue-400 flex items-center justify-center shrink-0">
                <BanknotesIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-2" />
              </div>
              <span class="text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 truncate">Rasio Tabungan</span>
            </div>
            <span
              class="text-[9px] sm:text-[10px] font-semibold px-1.5 py-0.5 rounded-full border"
              :class="report.saving_rate >= 20
                ? 'text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800/30'
                : 'text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800/30'"
            >
              {{ report.saving_rate >= 20 ? 'Sehat' : 'Perlu Dijaga' }}
            </span>
          </div>

          <div class="my-0.5 sm:my-1">
            <p class="text-base sm:text-xl font-black text-slate-900 dark:text-white tracking-tight tabular-nums truncate">
              {{ report.saving_rate }}%
            </p>
          </div>

          <div class="space-y-1 pt-2 border-t border-slate-100 dark:border-slate-700/60 mt-1">
            <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
              <div
                class="h-full rounded-full transition-all duration-500"
                :class="report.saving_rate >= 30 ? 'bg-emerald-500' : report.saving_rate >= 10 ? 'bg-amber-500' : 'bg-rose-500'"
                :style="{ width: `${Math.min(Math.max(report.saving_rate, 0), 100)}%` }"
              ></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Mobile Tab Switcher (Visible on Mobile Only) -->
      <div class="flex sm:hidden p-1 bg-slate-100 dark:bg-slate-800 rounded-xl print:hidden">
        <button
          type="button"
          @click="activeTab = 'expense'"
          :class="[
            'flex-1 py-1.5 text-xs font-semibold rounded-lg transition-all',
            activeTab === 'expense'
              ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm'
              : 'text-slate-500 dark:text-slate-400',
          ]"
        >
          Pengeluaran ({{ report.expense_breakdown.length }})
        </button>
        <button
          type="button"
          @click="activeTab = 'income'"
          :class="[
            'flex-1 py-1.5 text-xs font-semibold rounded-lg transition-all',
            activeTab === 'income'
              ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm'
              : 'text-slate-500 dark:text-slate-400',
          ]"
        >
          Pemasukan ({{ report.income_breakdown.length }})
        </button>
        <button
          type="button"
          @click="activeTab = 'accounts'"
          :class="[
            'flex-1 py-1.5 text-xs font-semibold rounded-lg transition-all',
            activeTab === 'accounts'
              ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm'
              : 'text-slate-500 dark:text-slate-400',
          ]"
        >
          Rekening ({{ report.account_balances.length }})
        </button>
      </div>

      <!-- Breakdown + Accounts (Tabbed on Mobile, 3-Column Grid on Desktop) -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- 1. Expense Breakdown -->
        <div
          class="card p-4 sm:p-5 space-y-4"
          :class="{ 'hidden sm:block': activeTab !== 'expense' }"
        >
          <div class="flex items-center justify-between pb-1 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
              <h3 class="font-bold text-slate-900 dark:text-white text-sm">Rincian Pengeluaran</h3>
            </div>
            <span class="text-xs font-bold text-slate-900 dark:text-white tabular-nums">
              {{ formatCurrency(totalExpenseBreakdown) }}
            </span>
          </div>

          <!-- Donut chart for visual clarity -->
          <div v-if="report.expense_breakdown.length" class="flex items-center justify-center py-2">
            <apexchart type="donut" height="180" :options="expenseDonutOptions" :series="expenseDonutSeries" />
          </div>

          <!-- Items list -->
          <div v-if="report.expense_breakdown.length" class="space-y-3 max-h-80 overflow-y-auto pr-1">
            <div
              v-for="item in report.expense_breakdown"
              :key="item.category"
              class="space-y-1.5 p-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors"
            >
              <div class="flex items-center justify-between text-xs">
                <div class="flex items-center gap-2 min-w-0">
                  <span class="text-base leading-none shrink-0">{{ item.icon }}</span>
                  <span class="font-semibold text-slate-800 dark:text-slate-200 truncate">{{ item.category }}</span>
                </div>
                <div class="text-right shrink-0 tabular-nums">
                  <span class="font-bold text-slate-900 dark:text-white">{{ formatCurrency(item.total) }}</span>
                  <span class="text-slate-400 text-[11px] ml-1">
                    ({{ totalExpenseBreakdown ? Math.round((item.total / totalExpenseBreakdown) * 100) : 0 }}%)
                  </span>
                </div>
              </div>

              <!-- Progress bar -->
              <div class="h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                <div
                  class="h-full rounded-full transition-all duration-500"
                  :style="{
                    width: totalExpenseBreakdown ? `${(item.total / totalExpenseBreakdown) * 100}%` : '0%',
                    backgroundColor: item.color || '#F43F5E',
                  }"
                ></div>
              </div>
            </div>
          </div>
          <div v-else class="text-center text-slate-400 dark:text-slate-500 py-10 space-y-1">
            <InboxIcon class="w-8 h-8 mx-auto opacity-40" />
            <p class="text-xs">Tidak ada transaksi pengeluaran</p>
          </div>
        </div>

        <!-- 2. Income Breakdown -->
        <div
          class="card p-4 sm:p-5 space-y-4"
          :class="{ 'hidden sm:block': activeTab !== 'income' }"
        >
          <div class="flex items-center justify-between pb-1 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
              <h3 class="font-bold text-slate-900 dark:text-white text-sm">Rincian Pemasukan</h3>
            </div>
            <span class="text-xs font-bold text-slate-900 dark:text-white tabular-nums">
              {{ formatCurrency(totalIncomeBreakdown) }}
            </span>
          </div>

          <!-- Items list -->
          <div v-if="report.income_breakdown.length" class="space-y-3 max-h-96 overflow-y-auto pr-1">
            <div
              v-for="item in report.income_breakdown"
              :key="item.category"
              class="space-y-1.5 p-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors"
            >
              <div class="flex items-center justify-between text-xs">
                <div class="flex items-center gap-2 min-w-0">
                  <span class="text-base leading-none shrink-0">{{ item.icon }}</span>
                  <span class="font-semibold text-slate-800 dark:text-slate-200 truncate">{{ item.category }}</span>
                </div>
                <div class="text-right shrink-0 tabular-nums">
                  <span class="font-bold text-slate-900 dark:text-white">{{ formatCurrency(item.total) }}</span>
                  <span class="text-slate-400 text-[11px] ml-1">
                    ({{ totalIncomeBreakdown ? Math.round((item.total / totalIncomeBreakdown) * 100) : 0 }}%)
                  </span>
                </div>
              </div>

              <!-- Progress bar -->
              <div class="h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                <div
                  class="h-full rounded-full transition-all duration-500"
                  :style="{
                    width: totalIncomeBreakdown ? `${(item.total / totalIncomeBreakdown) * 100}%` : '0%',
                    backgroundColor: item.color || '#10B981',
                  }"
                ></div>
              </div>
            </div>
          </div>
          <div v-else class="text-center text-slate-400 dark:text-slate-500 py-10 space-y-1">
            <InboxIcon class="w-8 h-8 mx-auto opacity-40" />
            <p class="text-xs">Tidak ada transaksi pemasukan</p>
          </div>
        </div>

        <!-- 3. Account Balances -->
        <div
          class="card p-4 sm:p-5 space-y-4"
          :class="{ 'hidden sm:block': activeTab !== 'accounts' }"
        >
          <div class="flex items-center justify-between pb-1 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-primary-500"></span>
              <h3 class="font-bold text-slate-900 dark:text-white text-sm">Posisi Saldo Rekening</h3>
            </div>
            <span class="text-xs text-slate-400">
              {{ report.account_balances.length }} Rekening
            </span>
          </div>

          <div v-if="report.account_balances.length" class="space-y-2.5 max-h-96 overflow-y-auto pr-1">
            <div
              v-for="acc in report.account_balances"
              :key="acc.name"
              class="flex items-center justify-between p-3 rounded-2xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 hover:bg-slate-100/60 dark:hover:bg-slate-700/60 transition-colors"
            >
              <div class="flex items-center gap-2.5 min-w-0">
                <div
                  class="w-9 h-9 rounded-xl flex items-center justify-center text-base shrink-0 shadow-inner"
                  :style="{ backgroundColor: (acc.color || '#6366F1') + '1A' }"
                >
                  {{ acc.icon || '💳' }}
                </div>
                <div class="min-w-0">
                  <p class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">{{ acc.name }}</p>
                  <p class="text-[10px] text-slate-400 uppercase tracking-wider">{{ acc.type || 'Akun' }}</p>
                </div>
              </div>
              <span class="text-xs sm:text-sm font-bold tabular-nums text-slate-900 dark:text-white shrink-0 ml-2">
                {{ formatCurrency(acc.balance) }}
              </span>
            </div>
          </div>
          <div v-else class="text-center text-slate-400 dark:text-slate-500 py-10 space-y-1">
            <BuildingLibraryIcon class="w-8 h-8 mx-auto opacity-40" />
            <p class="text-xs">Belum ada rekening aktif</p>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>
