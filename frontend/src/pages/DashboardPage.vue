<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { dashboardApi, type DashboardSummary, type ChartData, type ExpenseBreakdownItem } from '@/api/dashboard'
import { useFormatCurrency } from '@/composables/useFormatCurrency'
import { useAuthStore } from '@/stores/auth'
import type { Transaction } from '@/types/transaction'
import ReceiptScannerModal from '@/components/ReceiptScannerModal.vue'
import SmartInsightsCard from '@/components/SmartInsightsCard.vue'
import YourBudgetsCard from '@/components/YourBudgetsCard.vue'
import EmptyState from '@/components/EmptyState.vue'
import { useDashboardStore } from '@/stores/dashboard'
import { useGoalsStore } from '@/stores/goals'
import { useRecurringStore } from '@/stores/recurring'
import {
  BanknotesIcon,
  WalletIcon,
  EyeIcon,
  EyeSlashIcon,
  ArrowUpRightIcon,
  ArrowDownRightIcon,
  ArrowTrendingUpIcon,
  ArrowTrendingDownIcon,
  ScaleIcon,
  InboxIcon,
  CameraIcon,
  SparklesIcon,
  ArrowPathIcon,
  ClockIcon,
  CheckCircleIcon,
  ChartBarIcon,
} from '@heroicons/vue/24/outline'

const { t } = useI18n()
const { formatCurrency, formatAmount, formatDate } = useFormatCurrency()
const authStore = useAuthStore()
const dashboardStore = useDashboardStore()
const goalsStore = useGoalsStore()
const recurringStore = useRecurringStore()

const showScanner = ref(false)
const summary = computed(() => dashboardStore.summary)
const recentTransactions = computed(() => dashboardStore.recentTransactions)
const chartData = computed(() => dashboardStore.chartData)
const breakdown = computed(() => dashboardStore.breakdown)
const chartPeriod = computed(() => dashboardStore.chartPeriod)
const loading = computed(() => dashboardStore.loading)

const hideBalance = ref(localStorage.getItem('financeflow_hide_balance') === 'true')

function toggleHideBalance() {
  hideBalance.value = !hideBalance.value
  localStorage.setItem('financeflow_hide_balance', String(hideBalance.value))
}

const savingRate = computed(() => {
  const inc = summary.value?.income_this_month ?? 0
  const net = summary.value?.net_cash_flow ?? 0
  if (inc <= 0) return null
  return Math.round((net / inc) * 100)
})

const firstName = computed(() => authStore.user?.name?.split(' ')[0] || 'Teman')

const greetingText = computed(() => {
  const hour = new Date().getHours()
  const name = firstName.value
  if (hour < 5) return `Masih terjaga, ${name}?`
  if (hour < 10) return `Selamat Pagi, ${name}`
  if (hour < 15) return `Selamat Siang, ${name}`
  if (hour < 18) return `Selamat Sore, ${name}`
  return `Selamat Malam, ${name}`
})

const greetingSub = computed(() => {
  const hour = new Date().getHours()
  if (hour < 5) return 'Jangan lupa istirahat ya. Ini ringkasan keuanganmu hari ini.'
  if (hour < 10) return 'Semangat hari ini! Yuk cek kondisi keuanganmu.'
  if (hour < 15) return 'Ini update keuanganmu sejauh ini.'
  if (hour < 18) return 'Sudah catat pengeluaran hari ini belum?'
  return 'Yuk review keuangan hari ini sebelum istirahat.'
})

async function loadDashboard(force = false) {
  await Promise.all([
    dashboardStore.fetchDashboard(force),
    goalsStore.fetchGoals(force),
    recurringStore.fetchAll(force),
  ])
}

const chartType = ref<'area' | 'bar'>('area')

function switchPeriod(period: 'monthly' | 'daily') {
  dashboardStore.switchPeriod(period)
}

onMounted(() => {
  loadDashboard()
})

const mainChartOptions = computed(() => ({
  chart: {
    type: chartType.value,
    toolbar: { show: false },
    background: 'transparent',
    fontFamily: 'Inter, system-ui, sans-serif',
    animations: {
      enabled: true,
      easing: 'easeinout',
      speed: 600,
      dynamicAnimation: {
        enabled: true,
        speed: 350,
      },
    },
    dropShadow: chartType.value === 'area' ? {
      enabled: true,
      top: 4,
      left: 0,
      blur: 8,
      opacity: 0.20,
    } : { enabled: false },
  },
  colors: ['#10B981', '#F43F5E'],
  stroke: {
    curve: 'smooth',
    width: chartType.value === 'area' ? 3.5 : 0,
    lineCap: 'round',
  },
  fill: chartType.value === 'area' ? {
    type: 'gradient',
    gradient: {
      shade: 'dark',
      type: 'vertical',
      shadeIntensity: 0.3,
      opacityFrom: 0.45,
      opacityTo: 0.03,
      stops: [0, 90, 100],
    },
  } : {
    type: 'gradient',
    gradient: {
      type: 'vertical',
      shadeIntensity: 0.2,
      opacityFrom: 0.95,
      opacityTo: 0.85,
    },
  },
  plotOptions: {
    bar: {
      columnWidth: '40%',
      borderRadius: 6,
      borderRadiusApplication: 'end',
    },
  },
  markers: {
    size: chartType.value === 'area' ? 4 : 0,
    strokeColors: '#ffffff',
    strokeWidth: 2,
    hover: {
      size: 7,
      sizeOffset: 3,
    },
  },
  dataLabels: { enabled: false },
  legend: {
    position: 'top',
    horizontalAlign: 'right',
    labels: { colors: '#94A3B8' },
    markers: { radius: 6, width: 10, height: 10 },
    itemMargin: { horizontal: 8 },
  },
  xaxis: {
    categories: chartData.value?.labels ?? [],
    axisBorder: { show: false },
    axisTicks: { show: false },
    labels: {
      rotate: 0,
      rotateAlways: false,
      hideOverlappingLabels: true,
      trim: false,
      style: { colors: '#94A3B8', fontSize: '11px', fontWeight: 600 },
    },
  },
  yaxis: {
    labels: {
      style: { colors: '#94A3B8', fontSize: '10px', fontWeight: 500 },
      formatter: (val: number) => {
        if (val >= 1000000) return `Rp${(val / 1000000).toFixed(1)}jt`
        if (val >= 1000) return `Rp${(val / 1000).toFixed(0)}rb`
        return `Rp${val}`
      },
    },
  },
  grid: {
    borderColor: '#33415525',
    strokeDashArray: 4,
    xaxis: { lines: { show: false } },
    yaxis: { lines: { show: true } },
    padding: { top: 0, right: 10, bottom: 0, left: 10 },
  },
  tooltip: {
    theme: 'dark',
    shared: true,
    intersect: false,
    y: {
      formatter: (val: number) => `Rp ${Number(val).toLocaleString('id-ID')}`,
    },
    style: {
      fontSize: '12px',
      fontFamily: 'Inter, system-ui, sans-serif',
    },
  },
}))

const chartSeries = computed(() => [
  { name: 'Pemasukan', data: chartData.value?.income ?? [] },
  { name: 'Pengeluaran', data: chartData.value?.expense ?? [] },
])

const donutOptions = computed(() => ({
  chart: { type: 'donut', background: 'transparent' },
  labels: breakdown.value.map((b) => b.category),
  colors: breakdown.value.map((b) => b.color),
  legend: { position: 'bottom', labels: { colors: '#94A3B8' } },
  dataLabels: { enabled: false },
  plotOptions: { pie: { donut: { size: '72%' } } },
  stroke: { width: 0 },
  tooltip: {
    theme: 'dark',
    y: { formatter: (val: number) => `Rp ${Number(val).toLocaleString('id-ID')}` },
  },
}))

const donutSeries = computed(() => breakdown.value.map((b) => b.total))
const totalBreakdown = computed(() => breakdown.value.reduce((s, b) => s + b.total, 0))
</script>

<template>
  <div class="space-y-5 sm:space-y-6">
    <!-- ================= GREETING & OVERVIEW HERO BANNER ================= -->
    <div class="relative overflow-hidden bg-gradient-to-r from-blue-50/90 via-indigo-50/50 to-white dark:from-slate-800/95 dark:via-slate-800/70 dark:to-slate-900 border border-blue-100 dark:border-slate-700/70 rounded-2xl p-4 sm:p-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
      <!-- Ambient Glow -->
      <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-[#0066FF]/10 dark:bg-[#0066FF]/15 rounded-full blur-2xl pointer-events-none"></div>

      <!-- Left Text & Actions -->
      <div class="space-y-2 z-10 text-center sm:text-left">
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
          {{ greetingText }}
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-lg leading-relaxed font-normal">
          {{ greetingSub }}
        </p>
        
        <!-- Action Buttons Row -->
        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2.5 pt-1.5">
          <RouterLink
            to="/transaksi"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold bg-[#0066FF] hover:bg-[#0052CC] text-white rounded-xl shadow-md shadow-[#0066FF]/25 transition-all active:scale-95"
          >
            <span>+ Catat Transaksi</span>
          </RouterLink>
          <button
            @click="showScanner = true"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold bg-white hover:bg-slate-50 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 rounded-xl shadow-sm transition-all active:scale-95"
          >
            <CameraIcon class="w-4 h-4 text-[#0066FF] dark:text-blue-400 shrink-0" />
            <span>Scan Struk / Bukti</span>
          </button>
        </div>
      </div>

      <!-- Right 3D Character Illustration (Compact, does not block charts) -->
      <div class="shrink-0 relative z-10 hidden sm:flex items-center justify-center max-w-[220px] lg:max-w-[260px]">
        <img
          src="/assets/3d/dashboard_hero_boy.png"
          alt="Financial Flow Overview"
          class="w-full h-auto object-contain drop-shadow-lg animate-float-slow max-h-[140px] rounded-2xl"
        />
      </div>
    </div>

    <!-- Summary Cards (Hero Card + 3 Metric Pillars) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
      <!-- Loading skeleton -->
      <template v-if="loading">
        <div v-for="i in 4" :key="i" class="bg-white dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-3.5 sm:p-5 animate-pulse">
          <div class="flex items-center justify-between mb-3">
            <div class="w-8 h-8 bg-slate-200 dark:bg-slate-700 rounded-xl"></div>
            <div class="h-3 bg-slate-200 dark:bg-slate-700 rounded w-16"></div>
          </div>
          <div class="h-6 bg-slate-200 dark:bg-slate-700 rounded w-28 mb-2"></div>
          <div class="h-3 bg-slate-200 dark:bg-slate-700 rounded w-20"></div>
        </div>
      </template>

      <template v-else-if="summary">
        <!-- 1. HERO CARD: Total Saldo / Net Worth -->
        <div class="relative overflow-hidden bg-gradient-to-br from-[#0066FF] via-[#0052CC] to-[#003882] dark:from-[#002f73] dark:via-slate-800 dark:to-[#001a44] border border-blue-400/30 dark:border-blue-500/20 rounded-2xl p-3.5 sm:p-5 shadow-lg shadow-blue-500/15 text-white flex flex-col justify-between group transition-transform hover:-translate-y-0.5 duration-200">
          <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 dark:bg-blue-400/10 rounded-full blur-xl pointer-events-none"></div>

          <div>
            <div class="flex items-center justify-between gap-1.5 mb-2.5">
              <div class="flex items-center gap-1.5 sm:gap-2">
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-white/15 dark:bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/20 dark:border-white/10 shrink-0">
                  <WalletIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-white" />
                </div>
                <span class="text-[11px] sm:text-xs font-semibold text-primary-100 truncate">Total Saldo</span>
              </div>
              <button
                @click="toggleHideBalance"
                class="p-1 rounded-lg bg-white/10 hover:bg-white/20 text-white/80 hover:text-white transition-colors"
                :title="hideBalance ? 'Tampilkan Saldo' : 'Sembunyikan Saldo'"
              >
                <EyeSlashIcon v-if="hideBalance" class="w-3.5 h-3.5" />
                <EyeIcon v-else class="w-3.5 h-3.5" />
              </button>
            </div>

            <div class="my-0.5 sm:my-1">
              <p class="text-base sm:text-2xl font-black tracking-tight tabular-nums truncate">
                {{ hideBalance ? '••••••••' : formatCurrency(summary.total_balance) }}
              </p>
            </div>
          </div>

          <div class="flex items-center justify-between pt-2 border-t border-white/15 dark:border-white/10 text-[10px] sm:text-[11px] text-primary-100/90 mt-1">
            <span class="truncate">Semua rekening</span>
            <span class="inline-flex items-center gap-1 font-medium bg-white/15 px-1.5 py-0.5 rounded-full text-[9px] sm:text-[10px]">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
              Aktif
            </span>
          </div>
        </div>

        <!-- 2. CARD: Pemasukan Bulan Ini -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-3.5 sm:p-5 shadow-sm hover:shadow-md hover:border-emerald-200 dark:hover:border-emerald-800/40 transition-all flex flex-col justify-between group">
          <div>
            <div class="flex items-center justify-between gap-1.5 mb-2.5">
              <div class="flex items-center gap-1.5 sm:gap-2">
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-100 dark:border-emerald-800/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                  <ArrowUpRightIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-[2.5]" />
                </div>
                <span class="text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 truncate">Pemasukan</span>
              </div>
              <span class="text-[9px] sm:text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-1.5 py-0.5 rounded-full border border-emerald-100 dark:border-emerald-800/30">
                Masuk
              </span>
            </div>

            <div class="my-0.5 sm:my-1">
              <p class="text-base sm:text-xl font-black text-slate-900 dark:text-white tracking-tight tabular-nums truncate">
                {{ hideBalance ? '••••••••' : formatCurrency(summary.income_this_month) }}
              </p>
            </div>
          </div>

          <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700/60 text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 mt-1">
            <span>Bulan ini</span>
            <span class="text-emerald-600 dark:text-emerald-400 font-medium">Bulan berjalan</span>
          </div>
        </div>

        <!-- 3. CARD: Pengeluaran Bulan Ini -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-3.5 sm:p-5 shadow-sm hover:shadow-md hover:border-rose-200 dark:hover:border-rose-800/40 transition-all flex flex-col justify-between group">
          <div>
            <div class="flex items-center justify-between gap-1.5 mb-2.5">
              <div class="flex items-center gap-1.5 sm:gap-2">
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-100 dark:border-rose-800/40 flex items-center justify-center text-rose-600 dark:text-rose-400 shrink-0">
                  <ArrowDownRightIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-[2.5]" />
                </div>
                <span class="text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 truncate">Pengeluaran</span>
              </div>
              <span class="text-[9px] sm:text-[10px] font-semibold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 px-1.5 py-0.5 rounded-full border border-rose-100 dark:border-rose-800/30">
                Keluar
              </span>
            </div>

            <div class="my-0.5 sm:my-1">
              <p class="text-base sm:text-xl font-black text-slate-900 dark:text-white tracking-tight tabular-nums truncate">
                {{ hideBalance ? '••••••••' : formatCurrency(summary.expense_this_month) }}
              </p>
            </div>
          </div>

          <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700/60 text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 mt-1">
            <span>Bulan ini</span>
            <span class="text-rose-500 dark:text-rose-400 font-medium">Pengeluaran</span>
          </div>
        </div>

        <!-- 4. CARD: Arus Kas Bersih -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-3.5 sm:p-5 shadow-sm hover:shadow-md hover:border-primary-200 dark:hover:border-primary-800/40 transition-all flex flex-col justify-between group">
          <div>
            <div class="flex items-center justify-between gap-1.5 mb-2.5">
              <div class="flex items-center gap-1.5 sm:gap-2">
                <div
                  class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl flex items-center justify-center border shrink-0"
                  :class="summary.net_cash_flow >= 0
                    ? 'bg-indigo-50 dark:bg-indigo-950/50 border-indigo-100 dark:border-indigo-800/40 text-indigo-600 dark:text-indigo-400'
                    : 'bg-amber-50 dark:bg-amber-950/50 border-amber-100 dark:border-amber-800/40 text-amber-600 dark:text-amber-400'"
                >
                  <ScaleIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-[2]" />
                </div>
                <span class="text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 truncate">Arus Kas Bersih</span>
              </div>
              <span
                v-if="savingRate !== null"
                class="text-[9px] sm:text-[10px] font-semibold px-1.5 py-0.5 rounded-full border"
                :class="savingRate >= 20
                  ? 'text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800/30'
                  : 'text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800/30'"
              >
                {{ savingRate }}% Saving
              </span>
            </div>

            <div class="my-0.5 sm:my-1">
              <p
                class="text-base sm:text-xl font-black tracking-tight tabular-nums truncate"
                :class="summary.net_cash_flow >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'"
              >
                {{ hideBalance ? '••••••••' : formatAmount(summary.net_cash_flow) }}
              </p>
            </div>
          </div>

          <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700/60 text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 mt-1">
            <span>Tabungan bersih</span>
            <span :class="summary.net_cash_flow >= 0 ? 'text-emerald-600 dark:text-emerald-400 font-medium' : 'text-rose-500 font-medium'">
              {{ summary.net_cash_flow >= 0 ? 'Surplus' : 'Defisit' }}
            </span>
          </div>
        </div>
      </template>
    </div>

    <!-- Smart Spending Insights -->
    <SmartInsightsCard />

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
      <!-- Income vs Expense Chart -->
      <div class="card p-4 sm:p-5 lg:col-span-2">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
          <div>
            <h3 class="font-bold text-slate-900 dark:text-white text-sm">{{ t('dasbor.grafikPemasukan') }}</h3>
            <p v-if="chartData?.range_label" class="text-xs text-slate-400 dark:text-slate-500 mt-0.5 font-medium">
              Periode: {{ chartData.range_label }}
            </p>
          </div>

          <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto">
            <!-- Chart Style (Area vs Bar) -->
            <div class="flex gap-0.5 bg-slate-100 dark:bg-slate-800 rounded-lg p-0.5 border border-slate-200/60 dark:border-slate-700/60">
              <button
                type="button"
                @click="chartType = 'area'"
                :class="[
                  'px-2.5 py-1 text-[11px] rounded-md font-semibold transition-all flex items-center gap-1.5',
                  chartType === 'area'
                    ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm'
                    : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200',
                ]"
              >
                <ArrowTrendingUpIcon class="w-3.5 h-3.5" />
                <span>Area</span>
              </button>
              <button
                type="button"
                @click="chartType = 'bar'"
                :class="[
                  'px-2.5 py-1 text-[11px] rounded-md font-semibold transition-all flex items-center gap-1.5',
                  chartType === 'bar'
                    ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm'
                    : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200',
                ]"
              >
                <ChartBarIcon class="w-3.5 h-3.5" />
                <span>Batang</span>
              </button>
            </div>

            <!-- Period Switch (Monthly vs Daily) -->
            <div class="flex gap-0.5 bg-slate-100 dark:bg-slate-800 rounded-lg p-0.5 border border-slate-200/60 dark:border-slate-700/60">
              <button
                v-for="p in [{ key: 'monthly', label: t('dasbor.bulan') }, { key: 'daily', label: t('dasbor.harian') }]"
                :key="p.key"
                @click="switchPeriod(p.key as any)"
                :class="[
                  'px-2.5 py-1 text-[11px] rounded-md font-semibold transition-all',
                  chartPeriod === p.key
                    ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm'
                    : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200',
                ]"
              >{{ p.label }}</button>
            </div>
          </div>
        </div>

        <div v-if="chartData && chartSeries[0].data.length" class="overflow-x-auto">
          <apexchart :type="chartType" height="260" :options="mainChartOptions" :series="chartSeries" />
        </div>
        <div v-else class="flex flex-col items-center justify-center h-56 text-slate-400 dark:text-slate-500 gap-2">
          <InboxIcon class="w-10 h-10 opacity-40" />
          <p class="text-sm">Belum ada data transaksi</p>
        </div>
      </div>

      <!-- Expense Breakdown -->
      <div class="card p-4 sm:p-5">
        <h3 class="font-bold text-slate-900 dark:text-white mb-4 text-sm">{{ t('dasbor.rincianPengeluaran') }}</h3>
        <div v-if="breakdown.length">
          <apexchart type="donut" height="200" :options="donutOptions" :series="donutSeries" />
          <div class="mt-4 space-y-2.5">
            <div v-for="item in breakdown.slice(0, 5)" :key="item.category" class="flex items-center justify-between">
              <div class="flex items-center gap-2 min-w-0">
                <span class="text-base leading-none">{{ item.icon }}</span>
                <span class="text-xs font-medium text-slate-600 dark:text-slate-400 truncate max-w-[110px]">{{ item.category }}</span>
              </div>
              <div class="text-right shrink-0">
                <span class="text-xs font-bold text-slate-900 dark:text-white tabular-nums">{{ formatCurrency(item.total) }}</span>
                <span class="text-[10px] text-slate-400 ml-1">
                  ({{ totalBreakdown ? Math.round((item.total / totalBreakdown) * 100) : 0 }}%)
                </span>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="flex flex-col items-center justify-center h-48 text-slate-400 dark:text-slate-500 gap-2">
          <InboxIcon class="w-10 h-10 opacity-40" />
          <p class="text-sm">Belum ada pengeluaran</p>
        </div>
      </div>
    </div>

    <!-- 2-Column Row: Your Budgets & Recent Transactions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
      <!-- Your Budgets -->
      <div class="lg:col-span-1">
        <YourBudgetsCard />
      </div>

      <!-- Recent Transactions -->
      <div class="card p-4 sm:p-5 lg:col-span-2">
        <div class="flex items-center justify-between mb-3">
          <h3 class="font-bold text-slate-900 dark:text-white text-sm">{{ t('dasbor.transaksiTerbaru') }}</h3>
          <RouterLink to="/transaksi" class="text-xs text-primary-600 hover:text-primary-700 dark:hover:text-primary-400 font-semibold hover:underline">
            Lihat semua →
          </RouterLink>
        </div>
        <div v-if="recentTransactions.length" class="divide-y divide-slate-100 dark:divide-slate-700/50 max-h-[380px] overflow-y-auto pr-1">
          <div v-for="tx in recentTransactions" :key="tx.id" class="flex items-center gap-3 py-3">
            <div
              class="w-9 h-9 rounded-xl flex items-center justify-center text-base shrink-0"
              :style="{ backgroundColor: (tx.category?.color ?? '#6366F1') + '18' }"
            >
              {{ tx.category?.icon }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">
                {{ tx.category?.name }}
              </p>
              <p class="text-xs text-slate-400 dark:text-slate-500 truncate">
                {{ tx.account?.name }}<span v-if="tx.description"> · {{ tx.description }}</span>
              </p>
            </div>
            <div class="text-right shrink-0">
              <p
                class="text-sm font-bold tabular-nums"
                :class="tx.type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : tx.type === 'transfer' ? 'text-[#0066FF] dark:text-blue-400' : 'text-rose-500 dark:text-rose-400'"
              >
                {{ formatAmount(tx.amount, tx.type) }}
              </p>
              <p class="text-[10px] text-slate-400 mt-0.5">{{ formatDate(tx.date) }}</p>
            </div>
          </div>
        </div>
        <div v-else class="py-4">
          <EmptyState
            type="transactions"
            title="Belum ada transaksi"
            description="Yuk catat transaksi pertamamu untuk mulai melacak keuangan."
            action-text="Catat Transaksi Pertama"
            @action="router.push('/transaksi')"
          />
        </div>
      </div>
    </div>

    <!-- 2-Column Row: Goals & Upcoming Bills -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
      <!-- 1. Financial Goals Widget -->
      <div class="card p-4 sm:p-5 space-y-3.5">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-primary-50 dark:bg-primary-950/50 text-primary-600 dark:text-primary-400 flex items-center justify-center">
              <SparklesIcon class="w-4 h-4" />
            </div>
            <h3 class="font-bold text-slate-900 dark:text-white text-sm">Target Impian & Tabungan</h3>
          </div>
          <RouterLink to="/impian" class="text-xs text-primary-600 dark:text-primary-400 font-semibold hover:underline">
            Kelola →
          </RouterLink>
        </div>

        <div v-if="goalsStore.goals.length" class="space-y-3">
          <div
            v-for="g in goalsStore.goals.slice(0, 2)"
            :key="g.id"
            class="p-3 rounded-2xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 space-y-2"
          >
            <div class="flex items-center justify-between text-xs">
              <div class="flex items-center gap-2 truncate">
                <span>{{ g.icon }}</span>
                <span class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ g.name }}</span>
              </div>
              <span class="font-extrabold text-primary-600 dark:text-primary-400">{{ g.percentage }}%</span>
            </div>
            <div class="h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
              <div
                class="h-full rounded-full transition-all duration-500"
                :style="{
                  width: `${Math.min(g.percentage, 100)}%`,
                  backgroundColor: g.is_completed ? '#10B981' : (g.color || '#0066FF'),
                }"
              ></div>
            </div>
            <div class="flex items-center justify-between text-[10px] text-slate-400">
              <span>{{ formatCurrency(g.current_amount) }}</span>
              <span>Target: {{ formatCurrency(g.target_amount) }}</span>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-6 text-slate-400 text-xs space-y-1">
          <p>Belum ada target impian.</p>
          <RouterLink to="/impian" class="text-primary-600 font-semibold hover:underline">+ Buat impian baru</RouterLink>
        </div>
      </div>

      <!-- 2. Upcoming Bills Widget -->
      <div class="card p-4 sm:p-5 space-y-3.5">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center">
              <ArrowPathIcon class="w-4 h-4" />
            </div>
            <h3 class="font-bold text-slate-900 dark:text-white text-sm">Tagihan Mendatang</h3>
          </div>
          <RouterLink to="/langganan" class="text-xs text-primary-600 dark:text-primary-400 font-semibold hover:underline">
            Lihat semua →
          </RouterLink>
        </div>

        <div v-if="recurringStore.upcomingBills.length" class="space-y-2.5">
          <div
            v-for="b in recurringStore.upcomingBills.slice(0, 3)"
            :key="b.id"
            class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800"
          >
            <div class="flex items-center gap-2.5 min-w-0">
              <span class="text-base">{{ b.category?.icon || '📦' }}</span>
              <div class="min-w-0">
                <p class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">{{ b.name }}</p>
                <p class="text-[10px] text-slate-400">{{ b.is_overdue ? 'Lewat jatuh tempo' : b.is_due_today ? 'Hari ini' : `${b.days_until_due} hari lagi` }}</p>
              </div>
            </div>
            <span class="text-xs font-extrabold text-slate-900 dark:text-white tabular-nums shrink-0 ml-2">
              {{ formatCurrency(b.amount) }}
            </span>
          </div>
        </div>
        <div v-else class="text-center py-6 text-slate-400 text-xs space-y-1">
          <p>Tidak ada tagihan jatuh tempo dalam waktu dekat.</p>
          <RouterLink to="/langganan" class="text-primary-600 font-semibold hover:underline">+ Tambah tagihan rutin</RouterLink>
        </div>
      </div>
    </div>

    <!-- Smart Receipt Scanner Modal -->
    <ReceiptScannerModal v-model="showScanner" @saved="loadDashboard" />
  </div>
</template>
