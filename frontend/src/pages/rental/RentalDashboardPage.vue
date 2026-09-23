<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useVehiclesStore } from '@/stores/vehicles'
import { useFormatCurrency } from '@/composables/useFormatCurrency'
import { useAuthStore } from '@/stores/auth'
import { useTourPackagesStore } from '@/stores/tourPackages'
import MoneySpinner from '@/components/MoneySpinner.vue'
import {
  PlusIcon,
  ChevronRightIcon,
  ArrowTrendingUpIcon,
  BanknotesIcon,
  TruckIcon,
  MapPinIcon,
  GlobeAltIcon,
  ChartBarIcon,
  ReceiptRefundIcon,
} from '@heroicons/vue/24/outline'
import QuickVehicleIncomeModal from '@/components/rental/QuickVehicleIncomeModal.vue'

const vehiclesStore = useVehiclesStore()
const tourStore     = useTourPackagesStore()
const { formatCurrency } = useFormatCurrency()
const authStore = useAuthStore()
const router    = useRouter()

const now       = new Date()
const month     = ref(now.getMonth() + 1)
const year      = ref(now.getFullYear())
const monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']
const rentalWebsiteUrl = (import.meta.env.VITE_RENTAL_WEBSITE_URL as string) || (import.meta.env.DEV ? 'http://localhost:5175' : 'https://3putrimulya-rent.vercel.app')

onMounted(() => {
  vehiclesStore.fetchVehicles(month.value, year.value)
  tourStore.fetchPackages()
})

const totalIncome   = computed(() => vehiclesStore.vehicles.reduce((s, v) => s + (v.summary?.income  || 0), 0))
const totalExpense  = computed(() => vehiclesStore.vehicles.reduce((s, v) => s + (v.summary?.expense || 0), 0))
const totalProfit   = computed(() => totalIncome.value - totalExpense.value)
const availableCount  = computed(() => vehiclesStore.vehicles.filter(v => v.status === 'available').length)
const rentedCount     = computed(() => vehiclesStore.vehicles.filter(v => v.status === 'rented').length)
const maintenanceCount= computed(() => vehiclesStore.vehicles.filter(v => v.status === 'maintenance').length)
const activeTourCount = computed(() => tourStore.packages.filter(p => p.is_active).length)

const sortedVehicles = computed(() =>
  [...vehiclesStore.vehicles].sort((a, b) => {
    const incomeA = a.summary?.income || 0
    const incomeB = b.summary?.income || 0
    if (incomeA !== incomeB) return incomeB - incomeA
    return (a.name || '').localeCompare(b.name || '', 'id')
  })
)

const fleetStatusText = computed(() => {
  if (maintenanceCount.value > 0 && rentedCount.value > 0)
    return `${rentedCount.value} jalan · ${maintenanceCount.value} servis`
  if (maintenanceCount.value > 0) return `${maintenanceCount.value} unit perlu perhatian`
  if (rentedCount.value > 0)      return `${rentedCount.value} unit sedang disewa`
  return 'Semua unit siap beroperasi'
})

const isBlankMonth        = computed(() => totalIncome.value === 0 && totalExpense.value === 0)
const maintenanceVehicles = computed(() => vehiclesStore.vehicles.filter(v => v.status === 'maintenance'))
const rentedVehicles      = computed(() => vehiclesStore.vehicles.filter(v => v.status === 'rented'))

function statusBadge(s: string) {
  if (s === 'available') return { label: 'Tersedia', class: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400' }
  if (s === 'rented')    return { label: 'Disewa',   class: 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400' }
  return { label: 'Servis', class: 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400' }
}

const showQuickIncomeModal       = ref(false)
const selectedVehicleForIncome   = ref<any>(null)

function openQuickIncome(vehicle: any) {
  selectedVehicleForIncome.value = vehicle
  showQuickIncomeModal.value     = true
}

function onRentalSaved() {
  vehiclesStore.fetchVehicles(month.value, year.value, true)
}

// ── Ring chart helpers ────────────────────────────────────────────────────────
// cx=60,cy=60,r=48 → circumference = 2π×48 ≈ 301.6
const C = 301.6

/**
 * Hasilkan stroke-dasharray untuk ring chart.
 * ratio: 0–1, 0 = empty, 1 = full ring
 * Kalau 0 atau tidak ada data → tampilkan sedikit saja (3%) agar ring tidak hilang
 */
function ringDash(ratio: number): string {
  const pct = Math.max(ratio, 0.03)
  return `${(pct * C).toFixed(1)} ${C}`
}

// Ring untuk armada — 3 segmen stacked
const totalVehicles = computed(() => vehiclesStore.vehicles.length || 1)
const availableArc  = computed(() => (availableCount.value  / totalVehicles.value) * C)
const rentedArc     = computed(() => (rentedCount.value     / totalVehicles.value) * C)
const maintenArc    = computed(() => (maintenanceCount.value / totalVehicles.value) * C)
</script>

<template>
  <div class="space-y-2.5 sm:space-y-4 lg:space-y-5">

    <!-- ══ HEADER BANNER ══════════════════════════════════════════════════════ -->
    <div
      class="card overflow-hidden relative min-h-[140px] sm:min-h-[185px] lg:min-h-[205px] flex items-center bg-gradient-to-r from-[#d8edf9] via-[#e5f2fa] to-[#f2f8fc] dark:from-slate-900 dark:via-slate-850 dark:to-slate-800 border border-sky-100/90 dark:border-slate-700/80"
    >
      <!-- HiAce panorama image: feathered mask on the left so there is ZERO hard cutoff edge -->
      <div
        class="absolute right-0 top-0 bottom-0 w-full sm:w-[60%] lg:w-[50%] pointer-events-none select-none overflow-hidden"
        style="-webkit-mask-image: linear-gradient(to right, transparent 0%, rgba(0,0,0,0.2) 15%, black 45%); mask-image: linear-gradient(to right, transparent 0%, rgba(0,0,0,0.2) 15%, black 45%);"
      >
        <img
          src="/header-rental.png"
          alt="Armada Rental Mobil Bintan"
          class="w-full h-full object-cover sm:object-contain object-right-bottom"
          draggable="false"
        />
      </div>

      <!-- Soft protective gradient overlay for text readability -->
      <div
        class="absolute inset-0 bg-gradient-to-r from-[#d8edf9] via-[#d8edf9]/85 to-transparent sm:via-[#d8edf9]/50 dark:from-slate-900 dark:via-slate-900/90 dark:to-transparent pointer-events-none"
      />

      <!-- Content text on the left -->
      <div class="relative z-10 p-3.5 sm:p-5 lg:p-6 flex flex-col justify-center gap-1 sm:gap-2 max-w-[65%] sm:max-w-md lg:max-w-lg">
        <!-- Badge -->
        <div class="inline-flex items-center gap-1 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full bg-white/95 dark:bg-slate-800/95 backdrop-blur-sm text-blue-700 dark:text-blue-300 text-[9px] sm:text-xs font-semibold w-fit border border-blue-100 dark:border-blue-800/60 shadow-2xs">
          <TruckIcon class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-blue-600 dark:text-blue-400" />
          <span>Rental &amp; Paket Tour</span>
        </div>

        <!-- Title -->
        <h1 class="text-sm sm:text-xl lg:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-snug">
          Ringkasan Rental
          <span class="block text-xs sm:text-xl lg:text-2xl font-bold sm:font-extrabold text-slate-700 dark:text-slate-300 sm:text-slate-900 sm:dark:text-white">· {{ monthNames[month - 1] }} {{ year }}</span>
        </h1>

        <!-- Subtitle: clean, no ellipsis, no 'dan...' cut off -->
        <p class="text-[10px] sm:text-xs lg:text-sm text-slate-600 dark:text-slate-300 leading-snug">
          Pantau sewa, operasional {{ vehiclesStore.vehicles.length }} armada &amp; paket wisata.
        </p>

        <!-- Action buttons -->
        <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap pt-0.5 sm:pt-1">
          <button
            @click="router.push('/rental/transaksi')"
            class="inline-flex items-center gap-1 sm:gap-1.5 px-2.5 py-1.5 sm:px-4 sm:py-2 rounded-lg sm:rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-[10px] sm:text-xs lg:text-sm font-bold shadow-sm transition-all active:scale-95"
          >
            <PlusIcon class="w-3 h-3 sm:w-4 sm:h-4" />
            <span class="sm:inline hidden">Catat Transaksi</span>
            <span class="sm:hidden inline">Catat Sewa</span>
          </button>
          <button
            @click="router.push('/rental/paket-tour')"
            class="inline-flex items-center gap-1 sm:gap-1.5 px-2.5 py-1.5 sm:px-4 sm:py-2 rounded-lg sm:rounded-xl bg-white/95 dark:bg-slate-800/95 backdrop-blur-sm hover:bg-white dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-[10px] sm:text-xs lg:text-sm font-semibold border border-slate-200/90 dark:border-slate-600 shadow-2xs transition-all active:scale-95"
          >
            <MapPinIcon class="w-3 h-3 sm:w-4 sm:h-4 text-blue-500" />
            <span>Paket Tour</span>
          </button>
        </div>
      </div>
    </div>

    <!-- ══ LOADING ═══════════════════════════════════════════════════════════ -->
    <div v-if="vehiclesStore.loading" class="card p-8 sm:p-12 flex items-center justify-center">
      <MoneySpinner size="md" text="Memuat ringkasan rental..." subtext="Menghitung total pendapatan dan biaya operasional" />
    </div>

    <template v-else>

      <!-- ① ALERT: armada di bengkel -->
      <div
        v-if="maintenanceVehicles.length > 0"
        class="rounded-xl sm:rounded-2xl border border-amber-200 dark:border-amber-800/60 bg-amber-50 dark:bg-amber-950/20 px-3 py-2 sm:px-4 sm:py-3.5 flex items-start gap-2.5 sm:gap-3"
      >
        <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg bg-amber-100 dark:bg-amber-900/50 flex items-center justify-center shrink-0 mt-0.5">
          <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-amber-600 dark:text-amber-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-xs sm:text-sm font-bold text-amber-800 dark:text-amber-300">
            {{ maintenanceVehicles.length }} armada sedang di bengkel
          </p>
          <p class="text-[10px] sm:text-xs text-amber-700 dark:text-amber-400 mt-0.5 line-clamp-1 sm:line-clamp-none">
            <span class="font-medium">{{ maintenanceVehicles.map(v => v.name).join(', ') }}</span>
            · Segera tangani agar tidak mengurangi potensi pendapatan.
          </p>
        </div>
        <button @click="router.push('/rental/armada')" class="text-[11px] sm:text-xs font-bold text-amber-700 dark:text-amber-300 hover:underline shrink-0 mt-0.5">
          Kelola →
        </button>
      </div>

      <!-- ② ALERT: armada aktif disewa -->
      <div
        v-if="rentedVehicles.length > 0"
        class="rounded-xl sm:rounded-2xl border border-blue-200 dark:border-blue-800/60 bg-blue-50 dark:bg-blue-950/20 px-3 py-2 sm:px-4 sm:py-3 flex items-center gap-2.5 sm:gap-3"
      >
        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse shrink-0"></span>
        <p class="text-[11px] sm:text-xs text-blue-700 dark:text-blue-300 flex-1 truncate">
          <span class="font-bold">{{ rentedVehicles.length }} unit sedang berjalan</span>
          · {{ rentedVehicles.map(v => v.name).join(', ') }}
        </p>
        <button @click="router.push('/rental/transaksi')" class="text-[11px] sm:text-xs font-bold text-blue-700 dark:text-blue-300 hover:underline shrink-0">
          Lihat →
        </button>
      </div>

      <!-- ③ ONBOARDING: belum ada transaksi bulan ini (super compact di mobile) -->
      <div
        v-if="isBlankMonth && vehiclesStore.vehicles.length > 0"
        class="card border border-dashed border-primary-200 dark:border-primary-800/60 bg-gradient-to-br from-primary-50/60 to-slate-50 dark:from-primary-950/20 dark:to-slate-800/80 p-2.5 sm:p-5"
      >
        <div class="flex items-center sm:items-start gap-2.5 sm:gap-3.5">
          <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-primary-100 dark:bg-primary-900/50 flex items-center justify-center shrink-0">
            <svg class="w-3.5 h-3.5 sm:w-5 sm:h-5 text-primary-600 dark:text-primary-400" viewBox="0 0 20 20" fill="currentColor">
              <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white leading-tight">
              Mulai catat di {{ monthNames[month - 1] }} {{ year }}
              <span class="text-[10px] sm:text-xs font-normal text-slate-500 dark:text-slate-400">· {{ vehiclesStore.vehicles.length }} armada siap</span>
            </p>
            <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5 hidden sm:block">
              Catat sewa pertama bulan ini untuk mulai memantau performa keuangan rental secara real-time.
            </p>
          </div>
        </div>

        <div class="mt-2 sm:mt-3 grid grid-cols-3 gap-1.5 sm:gap-2">
          <button
            @click="openQuickIncome(sortedVehicles[0])"
            class="flex flex-col sm:flex-row items-center sm:items-start gap-1 sm:gap-2 px-1.5 py-1.5 sm:px-3 sm:py-2.5 rounded-lg sm:rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-emerald-300 dark:hover:border-emerald-700 hover:bg-emerald-50/50 dark:hover:bg-emerald-950/20 text-center sm:text-left transition-all shadow-2xs"
          >
            <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-md sm:rounded-lg bg-emerald-100 dark:bg-emerald-950/50 flex items-center justify-center shrink-0">
              <BanknotesIcon class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-emerald-600 dark:text-emerald-400" />
            </span>
            <span class="min-w-0">
              <p class="text-[9px] sm:text-xs font-bold text-slate-800 dark:text-slate-200 leading-tight truncate">Sewa Cepat</p>
              <p class="text-[9px] text-slate-400 hidden sm:block truncate">{{ sortedVehicles[0]?.name || 'Pilih mobil' }}</p>
            </span>
          </button>

          <button
            @click="router.push('/rental/transaksi')"
            class="flex flex-col sm:flex-row items-center sm:items-start gap-1 sm:gap-2 px-1.5 py-1.5 sm:px-3 sm:py-2.5 rounded-lg sm:rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 text-center sm:text-left transition-all shadow-2xs"
          >
            <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-md sm:rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0">
              <PlusIcon class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-slate-600 dark:text-slate-300" />
            </span>
            <span class="min-w-0">
              <p class="text-[9px] sm:text-xs font-bold text-slate-800 dark:text-slate-200 leading-tight truncate">Transaksi</p>
              <p class="text-[9px] text-slate-400 hidden sm:block truncate">Pemasukan &amp; beban</p>
            </span>
          </button>

          <button
            @click="router.push('/rental/laporan')"
            class="flex flex-col sm:flex-row items-center sm:items-start gap-1 sm:gap-2 px-1.5 py-1.5 sm:px-3 sm:py-2.5 rounded-lg sm:rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-700 text-center sm:text-left transition-all shadow-2xs"
          >
            <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-md sm:rounded-lg bg-blue-100 dark:bg-blue-950/50 flex items-center justify-center shrink-0">
              <ArrowTrendingUpIcon class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-blue-600 dark:text-blue-400" />
            </span>
            <span class="min-w-0">
              <p class="text-[9px] sm:text-xs font-bold text-slate-800 dark:text-slate-200 leading-tight truncate">Laporan</p>
              <p class="text-[9px] text-slate-400 hidden sm:block truncate">Bulan sebelumnya</p>
            </span>
          </button>
        </div>
      </div>

      <!-- ══ 4 METRIC CARDS dengan Ring Chart (Judul di Samping Icon & Diagram Lebih Besar) ══ -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3.5 lg:gap-4">

        <!-- 1. Total Sewa Masuk -->
        <div class="card p-2.5 sm:p-4 lg:p-5 flex flex-col justify-between">
          <!-- Title beside Icon -->
          <div class="flex items-center justify-between gap-1">
            <div class="flex items-center gap-1.5 min-w-0">
              <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center shrink-0">
                <BanknotesIcon class="w-3.5 h-3.5 sm:w-4.5 sm:h-4.5 text-emerald-600 dark:text-emerald-400" />
              </div>
              <span class="text-[10px] sm:text-xs font-bold text-slate-800 dark:text-slate-200 leading-tight">Total Sewa Masuk</span>
            </div>
            <div class="w-4 h-4 sm:w-5 sm:h-5 rounded-md bg-emerald-50 dark:bg-emerald-900/40 flex items-center justify-center shrink-0">
              <ArrowTrendingUpIcon class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-emerald-500" />
            </div>
          </div>

          <!-- Enlarged Ring chart -->
          <div class="flex items-center justify-center py-1.5 sm:py-2">
            <div class="relative w-[76px] h-[76px] sm:w-[92px] sm:h-[92px] lg:w-[100px] lg:h-[100px] shrink-0">
              <svg viewBox="0 0 120 120" class="w-full h-full -rotate-90">
                <circle cx="60" cy="60" r="48" fill="none" stroke="#E2E8F0" stroke-width="11" class="dark:[stroke:#1E293B]"/>
                <circle
                  cx="60" cy="60" r="48" fill="none"
                  stroke="#10B981" stroke-width="11"
                  stroke-linecap="round"
                  :stroke-dasharray="ringDash(totalIncome > 0 ? Math.min(totalIncome / (totalIncome + totalExpense + 1), 1) : 0)"
                  class="transition-all duration-700"
                />
              </svg>
              <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-0.5">
                <p class="text-[10px] sm:text-xs lg:text-sm font-extrabold text-slate-900 dark:text-white tabular-nums leading-tight">
                  {{ formatCurrency(totalIncome) }}
                </p>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-between pt-1.5 sm:pt-2 border-t border-slate-100 dark:border-slate-700/60 text-[9px] sm:text-[10px]">
            <span class="inline-flex items-center gap-0.5 font-semibold text-emerald-600 dark:text-emerald-400 truncate">
              <ArrowTrendingUpIcon class="w-2.5 h-2.5 hidden sm:inline" /> Pemasukan
            </span>
            <span class="text-slate-400 shrink-0">Bulan ini</span>
          </div>
        </div>

        <!-- 2. Biaya Operasional -->
        <div class="card p-2.5 sm:p-4 lg:p-5 flex flex-col justify-between">
          <!-- Title beside Icon -->
          <div class="flex items-center justify-between gap-1">
            <div class="flex items-center gap-1.5 min-w-0">
              <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg bg-rose-100 dark:bg-rose-900/50 flex items-center justify-center shrink-0">
                <ReceiptRefundIcon class="w-3.5 h-3.5 sm:w-4.5 sm:h-4.5 text-rose-600 dark:text-rose-400" />
              </div>
              <span class="text-[10px] sm:text-xs font-bold text-slate-800 dark:text-slate-200 leading-tight">Biaya Operasional</span>
            </div>
            <div class="w-4 h-4 sm:w-5 sm:h-5 rounded-md bg-rose-50 dark:bg-rose-900/40 flex items-center justify-center shrink-0">
              <ArrowTrendingUpIcon class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-rose-500 rotate-180" />
            </div>
          </div>

          <!-- Enlarged Ring chart -->
          <div class="flex items-center justify-center py-1.5 sm:py-2">
            <div class="relative w-[76px] h-[76px] sm:w-[92px] sm:h-[92px] lg:w-[100px] lg:h-[100px] shrink-0">
              <svg viewBox="0 0 120 120" class="w-full h-full -rotate-90">
                <circle cx="60" cy="60" r="48" fill="none" stroke="#E2E8F0" stroke-width="11" class="dark:[stroke:#1E293B]"/>
                <circle
                  cx="60" cy="60" r="48" fill="none"
                  stroke="#F43F5E" stroke-width="11"
                  stroke-linecap="round"
                  :stroke-dasharray="ringDash(totalExpense > 0 ? Math.min(totalExpense / (totalIncome + totalExpense + 1), 1) : 0)"
                  class="transition-all duration-700"
                />
              </svg>
              <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-0.5">
                <p class="text-[10px] sm:text-xs lg:text-sm font-extrabold text-slate-900 dark:text-white tabular-nums leading-tight">
                  {{ formatCurrency(totalExpense) }}
                </p>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-between pt-1.5 sm:pt-2 border-t border-slate-100 dark:border-slate-700/60 text-[9px] sm:text-[10px]">
            <span class="inline-flex items-center gap-0.5 font-semibold text-rose-500 dark:text-rose-400 truncate">
              Beban
            </span>
            <span class="text-slate-400 shrink-0">Bensin &amp; servis</span>
          </div>
        </div>

        <!-- 3. Laba Bersih -->
        <div class="card p-2.5 sm:p-4 lg:p-5 flex flex-col justify-between">
          <!-- Title beside Icon -->
          <div class="flex items-center justify-between gap-1">
            <div class="flex items-center gap-1.5 min-w-0">
              <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center shrink-0">
                <ChartBarIcon class="w-3.5 h-3.5 sm:w-4.5 lg:w-5 text-blue-600 dark:text-blue-400" />
              </div>
              <span class="text-[10px] sm:text-xs font-bold text-slate-800 dark:text-slate-200 leading-tight">Laba Bersih</span>
            </div>
            <div class="w-4 h-4 sm:w-5 sm:h-5 rounded-md bg-blue-50 dark:bg-blue-900/40 flex items-center justify-center shrink-0">
              <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3" :class="totalProfit >= 0 ? 'text-blue-500' : 'text-rose-500'" fill="currentColor" viewBox="0 0 20 20">
                <path v-if="totalProfit >= 0" fill-rule="evenodd" d="M12.577 4.878a.75.75 0 01.919-.53l4.78 1.281a.75.75 0 01.531.919l-1.281 4.78a.75.75 0 01-1.449-.387l.81-3.022a19.407 19.407 0 00-5.594 5.203.75.75 0 01-1.139.093L7 10.06l-4.72 4.72a.75.75 0 01-1.06-1.061l5.25-5.25a.75.75 0 011.06 0l3.074 3.073a20.923 20.923 0 015.545-4.931l-3.042-.815a.75.75 0 01-.53-.918z" clip-rule="evenodd"/>
                <path v-else fill-rule="evenodd" d="M1.22 5.222a.75.75 0 011.06 0L7 9.942l3.768-3.769a.75.75 0 011.113.058 20.908 20.908 0 013.813 7.254l1.574-2.727a.75.75 0 011.3.75l-2.475 4.286a.75.75 0 01-1.025.275l-4.287-2.475a.75.75 0 01.75-1.3l2.71 1.565a19.422 19.422 0 00-3.013-6.024L7.53 11.533a.75.75 0 01-1.06 0l-5.25-5.25a.75.75 0 010-1.061z" clip-rule="evenodd"/>
              </svg>
            </div>
          </div>

          <!-- Enlarged Ring chart -->
          <div class="flex items-center justify-center py-1.5 sm:py-2">
            <div class="relative w-[76px] h-[76px] sm:w-[92px] sm:h-[92px] lg:w-[100px] lg:h-[100px] shrink-0">
              <svg viewBox="0 0 120 120" class="w-full h-full -rotate-90">
                <circle cx="60" cy="60" r="48" fill="none" stroke="#E2E8F0" stroke-width="11" class="dark:[stroke:#1E293B]"/>
                <circle
                  cx="60" cy="60" r="48" fill="none"
                  :stroke="totalProfit >= 0 ? '#3B82F6' : '#F43F5E'" stroke-width="11"
                  stroke-linecap="round"
                  :stroke-dasharray="ringDash(totalIncome > 0 ? Math.min(Math.abs(totalProfit) / (totalIncome + 1), 1) : 0)"
                  class="transition-all duration-700"
                />
              </svg>
              <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-0.5">
                <p :class="['text-[10px] sm:text-xs lg:text-sm font-extrabold tabular-nums leading-tight', totalProfit >= 0 ? 'text-slate-900 dark:text-white' : 'text-rose-600']">
                  {{ formatCurrency(totalProfit) }}
                </p>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-between pt-1.5 sm:pt-2 border-t border-slate-100 dark:border-slate-700/60 text-[9px] sm:text-[10px]">
            <span class="inline-flex items-center gap-0.5 font-semibold text-blue-600 dark:text-blue-400 truncate">
              <ArrowTrendingUpIcon class="w-2.5 h-2.5 hidden sm:inline" /> Laba
            </span>
            <span class="text-slate-400 shrink-0">{{ totalProfit >= 0 ? 'Surplus' : 'Defisit' }}</span>
          </div>
        </div>

        <!-- 4. Unit Armada — multi-segment ring -->
        <div class="card p-2.5 sm:p-4 lg:p-5 flex flex-col justify-between">
          <!-- Title beside Icon -->
          <div class="flex items-center justify-between gap-1">
            <div class="flex items-center gap-1.5 min-w-0">
              <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg bg-violet-100 dark:bg-violet-900/50 flex items-center justify-center shrink-0">
                <TruckIcon class="w-3.5 h-3.5 sm:w-4.5 lg:w-5 text-violet-600 dark:text-violet-400" />
              </div>
              <span class="text-[10px] sm:text-xs font-bold text-slate-800 dark:text-slate-200 leading-tight">Unit Armada</span>
            </div>
            <div class="w-4 h-4 sm:w-5 sm:h-5 rounded-md bg-violet-50 dark:bg-violet-900/40 flex items-center justify-center shrink-0">
              <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-violet-500" fill="currentColor" viewBox="0 0 20 20"><path d="M3.196 12.87l-.825.483a.75.75 0 000 1.294l7.25 4.25a.75.75 0 00.758 0l7.25-4.25a.75.75 0 000-1.294l-.825-.484-5.666 3.322a1.5 1.5 0 01-1.516 0l-5.426-3.32z"/><path d="M16.94 9.123l.75-.44a.75.75 0 000-1.29l-7.25-4.26a.75.75 0 00-.76 0L2.43 7.393a.75.75 0 000 1.29l.75.44 5.33-3.131a1.5 1.5 0 011.52 0l6.91 4.131z"/><path d="M8.31 13.723l-5.01-2.944-1.48.868a.75.75 0 000 1.296l7.25 4.25a.75.75 0 00.758 0l7.25-4.25a.75.75 0 00-1.296l-1.48-.868-5.01 2.944a1.5 1.5 0 01-1.278 0z"/></svg>
            </div>
          </div>

          <!-- Fixed Ring chart & Legend -->
          <div class="flex items-center justify-center gap-1.5 sm:gap-2.5 lg:gap-3 py-1.5 sm:py-2">
            <div class="relative w-[72px] h-[72px] sm:w-[84px] sm:h-[84px] lg:w-[92px] lg:h-[92px] shrink-0">
              <svg viewBox="0 0 120 120" class="w-full h-full -rotate-90">
                <circle cx="60" cy="60" r="48" fill="none" stroke="#E2E8F0" stroke-width="11" class="dark:[stroke:#1E293B]"/>
                <circle v-if="availableCount > 0" cx="60" cy="60" r="48" fill="none" stroke="#10B981" stroke-width="11" stroke-linecap="butt" :stroke-dasharray="`${availableArc.toFixed(1)} ${C}`" stroke-dashoffset="0"/>
                <circle v-if="rentedCount > 0" cx="60" cy="60" r="48" fill="none" stroke="#3B82F6" stroke-width="11" stroke-linecap="butt" :stroke-dasharray="`${rentedArc.toFixed(1)} ${C}`" :stroke-dashoffset="`-${availableArc.toFixed(1)}`"/>
                <circle v-if="maintenanceCount > 0" cx="60" cy="60" r="48" fill="none" stroke="#F59E0B" stroke-width="11" stroke-linecap="butt" :stroke-dasharray="`${maintenArc.toFixed(1)} ${C}`" :stroke-dashoffset="`-${(availableArc + rentedArc).toFixed(1)}`"/>
                <circle v-if="vehiclesStore.vehicles.length === 0" cx="60" cy="60" r="48" fill="none" stroke="#E2E8F0" stroke-width="11" :stroke-dasharray="`${C * 0.03} ${C}`"/>
              </svg>
              <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                <p class="text-sm sm:text-lg lg:text-xl font-extrabold text-slate-900 dark:text-white leading-none">{{ vehiclesStore.vehicles.length }}</p>
                <p class="text-[8px] sm:text-[9px] text-slate-400 mt-0.5">Mobil</p>
              </div>
            </div>
            <div class="flex flex-col gap-0.5 sm:gap-1 text-[9px] sm:text-[10px] shrink-0">
              <div class="flex items-center gap-1">
                <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-emerald-500 shrink-0"></span>
                <span class="text-slate-600 dark:text-slate-300 font-medium">{{ availableCount }} siap</span>
              </div>
              <div class="flex items-center gap-1">
                <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-blue-500 shrink-0"></span>
                <span class="text-slate-600 dark:text-slate-300 font-medium">{{ rentedCount }} sewa</span>
              </div>
              <div class="flex items-center gap-1">
                <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-amber-400 shrink-0"></span>
                <span class="text-slate-600 dark:text-slate-300 font-medium">{{ maintenanceCount }} servis</span>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-between pt-1.5 sm:pt-2 border-t border-slate-100 dark:border-slate-700/60 text-[9px] sm:text-[10px]">
            <span class="font-semibold text-violet-600 dark:text-violet-400">Total</span>
            <span :class="['font-medium', maintenanceCount > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400']">
              {{ maintenanceCount > 0 ? `${maintenanceCount} bengkel` : 'Siap semua' }}
            </span>
          </div>
        </div>
      </div>

      <!-- ══ TOUR BANNER ════════════════════════════════════════════════════ -->
      <div class="card p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-gradient-to-r from-blue-50/60 via-indigo-50/30 to-slate-50 dark:from-slate-800 dark:via-slate-800/90 dark:to-slate-800 border border-blue-100/80 dark:border-slate-700">
        <div class="flex items-center gap-3.5">
          <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-primary-600 text-white flex items-center justify-center shrink-0 shadow-xs">
            <MapPinIcon class="w-5 h-5 sm:w-6 sm:h-6" />
          </div>
          <div>
            <div class="flex items-center gap-2 flex-wrap">
              <h3 class="text-sm font-bold text-slate-900 dark:text-white">Layanan Paket Tour Bintan</h3>
              <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                {{ activeTourCount }} Paket Aktif di Web
              </span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
              Tarif all-in (armada HiAce + driver + BBM). Pemasukan pemesanan tour langsung masuk ke Laporan Keuangan.
            </p>
          </div>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
          <button
            @click="router.push('/rental/paket-tour')"
            class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-600 text-xs font-semibold shadow-xs transition"
          >
            Kelola Paket Tour
            <ChevronRightIcon class="w-3.5 h-3.5" />
          </button>
          <a
            :href="`${rentalWebsiteUrl}/paket-tour-bintan`"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center justify-center gap-1 p-2 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold shadow-xs transition shrink-0"
            title="Buka di Website Publik"
          >
            <GlobeAltIcon class="w-4 h-4" />
          </a>
        </div>
      </div>

      <!-- ══ VEHICLE LIST ════════════════════════════════════════════════════ -->
      <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
          <div>
            <h2 class="text-sm font-bold text-slate-900 dark:text-white">Performa Armada Bulan Ini</h2>
            <p class="text-xs text-slate-400">Ringkasan pendapatan &amp; laba per mobil</p>
          </div>
          <button
            @click="router.push('/rental/laporan')"
            class="text-xs font-semibold text-primary-600 dark:text-primary-400 hover:underline flex items-center gap-1"
          >
            Laporan Lengkap <ChevronRightIcon class="w-3.5 h-3.5" />
          </button>
        </div>

        <div v-if="vehiclesStore.vehicles.length === 0" class="p-10 sm:p-12 flex flex-col items-center text-center">
          <div class="w-14 h-14 bg-slate-100 dark:bg-slate-700/60 rounded-2xl flex items-center justify-center mb-4">
            <TruckIcon class="w-7 h-7 text-slate-400" />
          </div>
          <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum ada armada mobil</p>
          <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 mb-4 max-w-xs">
            Tambahkan mobil pertama untuk mulai mencatat sewa dan biaya operasional.
          </p>
          <button @click="router.push('/rental/armada')" class="btn-primary text-sm py-2 px-4">
            Tambah Mobil Pertama
          </button>
        </div>

        <template v-else>
          <div class="divide-y divide-slate-100 dark:divide-slate-700/60">
            <div
              v-for="v in sortedVehicles"
              :key="v.id"
              class="px-4 sm:px-5 py-3.5 flex items-center justify-between gap-3 hover:bg-slate-50/80 dark:hover:bg-slate-700/30 transition-colors"
            >
              <div class="flex items-center gap-3.5 min-w-0">
                <div
                  class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center shrink-0"
                  :style="{ backgroundColor: (v.color || '#3B82F6') + '20' }"
                >
                  <TruckIcon class="w-4.5 h-4.5 sm:w-5 sm:h-5" :style="{ color: v.color || '#3B82F6' }" />
                </div>
                <div class="min-w-0">
                  <div class="flex items-center gap-1.5 flex-wrap">
                    <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ v.name }}</p>
                    <span class="badge text-[10px] shrink-0" :class="statusBadge(v.status).class">
                      {{ statusBadge(v.status).label }}
                    </span>
                  </div>
                  <p class="text-xs text-slate-400 mt-0.5">
                    {{ v.plate_number || 'Tanpa Plat' }}<span v-if="v.daily_rate"> · {{ formatCurrency(v.daily_rate) }}/hari</span>
                  </p>
                </div>
              </div>

              <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <div class="text-right hidden xs:block sm:block">
                  <p :class="['text-xs sm:text-sm font-bold tabular-nums', (v.summary?.profit || 0) >= 0 ? 'text-slate-800 dark:text-slate-100' : 'text-rose-500']">
                    {{ formatCurrency(v.summary?.profit || 0) }}
                  </p>
                  <p class="text-[10px] text-slate-400">Laba bersih</p>
                </div>
                <button
                  type="button"
                  @click="openQuickIncome(v)"
                  class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/50 text-[11px] sm:text-xs font-bold transition-all active:scale-95 shadow-2xs shrink-0"
                >
                  <BanknotesIcon class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                  <span>+ Sewa</span>
                </button>
              </div>
            </div>
          </div>
        </template>
      </div>

    </template>

    <!-- Quick Vehicle Income Modal -->
    <QuickVehicleIncomeModal
      v-model:show="showQuickIncomeModal"
      :vehicle="selectedVehicleForIncome"
      @saved="onRentalSaved"
    />
  </div>
</template>
