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
  <div class="space-y-4 sm:space-y-5">

    <!-- ══ HEADER BANNER ══════════════════════════════════════════════════════ -->
    <div class="card overflow-hidden relative bg-gradient-to-br from-blue-50 via-slate-50 to-white dark:from-slate-800 dark:via-slate-800 dark:to-slate-800/90">
      <!-- Decorative bg circle -->
      <div class="absolute -right-8 -top-8 w-52 h-52 rounded-full bg-blue-100/60 dark:bg-blue-900/20 pointer-events-none" />
      <div class="absolute right-28 -bottom-10 w-36 h-36 rounded-full bg-indigo-100/40 dark:bg-indigo-900/10 pointer-events-none" />

      <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 sm:p-6">
        <!-- Left: text -->
        <div class="z-10">
          <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 text-xs font-semibold mb-3">
            <TruckIcon class="w-3.5 h-3.5" />
            <span>Rental &amp; Paket Tour</span>
          </div>
          <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">
            Ringkasan Rental
            <span class="block sm:inline"> · {{ monthNames[month - 1] }} {{ year }}</span>
          </h1>
          <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1.5 max-w-sm">
            Pantau pendapatan sewa, biaya operasional {{ vehiclesStore.vehicles.length }} armada, dan performa paket wisata.
          </p>
          <!-- Action buttons -->
          <div class="flex items-center gap-2 mt-4 flex-wrap">
            <button
              @click="router.push('/rental/transaksi')"
              class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-sm font-bold shadow transition-all"
            >
              <PlusIcon class="w-4 h-4" />
              Catat Transaksi
            </button>
            <button
              @click="router.push('/rental/paket-tour')"
              class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white dark:bg-slate-700 hover:bg-blue-50 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-sm font-semibold border border-slate-200 dark:border-slate-600 shadow-xs transition-all"
            >
              <MapPinIcon class="w-4 h-4 text-blue-500" />
              Paket Tour
            </button>
          </div>
        </div>

        <!-- Right: car SVG illustration -->
        <div class="hidden sm:flex items-end justify-end z-10 shrink-0 pr-2">
          <svg viewBox="0 0 200 110" class="w-44 xl:w-52 drop-shadow-sm" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- body shadow -->
            <ellipse cx="100" cy="104" rx="82" ry="6" fill="#CBD5E1" fill-opacity="0.5"/>
            <!-- main body -->
            <rect x="10" y="55" width="180" height="45" rx="10" fill="#E2E8F0"/>
            <rect x="10" y="55" width="180" height="45" rx="10" fill="url(#carBodyGrad)"/>
            <!-- roof -->
            <path d="M45 55 C50 30 80 22 100 22 C120 22 150 30 155 55 Z" fill="#CBD5E1"/>
            <path d="M45 55 C50 30 80 22 100 22 C120 22 150 30 155 55 Z" fill="url(#roofGrad)"/>
            <!-- windshield -->
            <path d="M58 54 C62 35 80 26 100 26 C120 26 138 35 142 54 Z" fill="#BAE6FD" fill-opacity="0.7"/>
            <!-- window lines -->
            <line x1="100" y1="26" x2="100" y2="54" stroke="#94A3B8" stroke-width="1.2" stroke-opacity="0.5"/>
            <!-- door line -->
            <line x1="100" y1="55" x2="100" y2="98" stroke="#94A3B8" stroke-width="1" stroke-opacity="0.4"/>
            <!-- left door handle -->
            <rect x="68" y="73" width="18" height="4" rx="2" fill="#94A3B8" fill-opacity="0.5"/>
            <!-- right door handle -->
            <rect x="116" y="73" width="18" height="4" rx="2" fill="#94A3B8" fill-opacity="0.5"/>
            <!-- front headlight -->
            <rect x="166" y="65" width="18" height="8" rx="4" fill="#FEF08A"/>
            <rect x="170" y="66" width="10" height="6" rx="3" fill="#FDE047"/>
            <!-- rear light -->
            <rect x="16" y="65" width="14" height="8" rx="4" fill="#FCA5A5"/>
            <!-- front bumper -->
            <rect x="160" y="85" width="28" height="8" rx="4" fill="#CBD5E1"/>
            <!-- rear bumper -->
            <rect x="12" y="85" width="28" height="8" rx="4" fill="#CBD5E1"/>
            <!-- wheel back -->
            <circle cx="45" cy="98" r="13" fill="#334155"/>
            <circle cx="45" cy="98" r="8" fill="#64748B"/>
            <circle cx="45" cy="98" r="4" fill="#CBD5E1"/>
            <!-- wheel front -->
            <circle cx="155" cy="98" r="13" fill="#334155"/>
            <circle cx="155" cy="98" r="8" fill="#64748B"/>
            <circle cx="155" cy="98" r="4" fill="#CBD5E1"/>
            <!-- decoration stripe -->
            <rect x="10" y="68" width="180" height="3" rx="1.5" fill="#3B82F6" fill-opacity="0.3"/>
            <defs>
              <linearGradient id="carBodyGrad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.6"/>
                <stop offset="100%" stop-color="#E2E8F0" stop-opacity="0"/>
              </linearGradient>
              <linearGradient id="roofGrad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.4"/>
                <stop offset="100%" stop-color="#CBD5E1" stop-opacity="0"/>
              </linearGradient>
            </defs>
          </svg>
        </div>
      </div>
    </div>

    <!-- ══ LOADING ═══════════════════════════════════════════════════════════ -->
    <div v-if="vehiclesStore.loading" class="card p-12 flex items-center justify-center">
      <MoneySpinner size="md" text="Memuat ringkasan rental..." subtext="Menghitung total pendapatan dan biaya operasional" />
    </div>

    <template v-else>

      <!-- ① ALERT: armada di bengkel -->
      <div
        v-if="maintenanceVehicles.length > 0"
        class="rounded-2xl border border-amber-200 dark:border-amber-800/60 bg-amber-50 dark:bg-amber-950/20 px-4 py-3.5 flex items-start gap-3"
      >
        <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/50 flex items-center justify-center shrink-0 mt-0.5">
          <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-bold text-amber-800 dark:text-amber-300">
            {{ maintenanceVehicles.length }} armada sedang di bengkel
          </p>
          <p class="text-xs text-amber-700 dark:text-amber-400 mt-0.5">
            <span class="font-medium">{{ maintenanceVehicles.map(v => v.name).join(', ') }}</span>
            · Pastikan segera ditangani agar tidak mengurangi potensi pendapatan sewa.
          </p>
        </div>
        <button @click="router.push('/rental/armada')" class="text-xs font-bold text-amber-700 dark:text-amber-300 hover:underline shrink-0 mt-0.5">
          Kelola →
        </button>
      </div>

      <!-- ② ALERT: armada aktif disewa -->
      <div
        v-if="rentedVehicles.length > 0"
        class="rounded-2xl border border-blue-200 dark:border-blue-800/60 bg-blue-50 dark:bg-blue-950/20 px-4 py-3 flex items-center gap-3"
      >
        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse shrink-0"></span>
        <p class="text-xs text-blue-700 dark:text-blue-300 flex-1">
          <span class="font-bold">{{ rentedVehicles.length }} unit sedang berjalan</span>
          · {{ rentedVehicles.map(v => v.name).join(', ') }}
        </p>
        <button @click="router.push('/rental/transaksi')" class="text-xs font-bold text-blue-700 dark:text-blue-300 hover:underline shrink-0">
          Lihat Transaksi →
        </button>
      </div>

      <!-- ③ ONBOARDING: belum ada transaksi bulan ini -->
      <div
        v-if="isBlankMonth && vehiclesStore.vehicles.length > 0"
        class="card border border-dashed border-primary-200 dark:border-primary-800/60 bg-gradient-to-br from-primary-50/60 to-slate-50 dark:from-primary-950/20 dark:to-slate-800/80 p-5 sm:p-6"
      >
        <div class="flex items-start gap-4">
          <div class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/50 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" viewBox="0 0 20 20" fill="currentColor">
              <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-slate-900 dark:text-white">
              Mulai catat di {{ monthNames[month - 1] }} {{ year }}
            </p>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
              Kamu punya {{ vehiclesStore.vehicles.length }} armada siap. Catat sewa pertama bulan ini untuk mulai memantau performa keuangan rental.
            </p>
          </div>
        </div>
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
          <button
            @click="openQuickIncome(sortedVehicles[0])"
            class="flex items-center gap-2.5 px-3.5 py-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-emerald-300 dark:hover:border-emerald-700 hover:bg-emerald-50/50 dark:hover:bg-emerald-950/20 text-left transition-all group shadow-2xs"
          >
            <span class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-950/50 flex items-center justify-center shrink-0">
              <BanknotesIcon class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
            </span>
            <span>
              <p class="text-xs font-bold text-slate-800 dark:text-slate-200">Catat Sewa Cepat</p>
              <p class="text-[10px] text-slate-400">{{ sortedVehicles[0]?.name || 'Pilih armada' }}</p>
            </span>
          </button>
          <button
            @click="router.push('/rental/transaksi')"
            class="flex items-center gap-2.5 px-3.5 py-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 text-left transition-all group shadow-2xs"
          >
            <span class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0">
              <PlusIcon class="w-4 h-4 text-slate-600 dark:text-slate-300" />
            </span>
            <span>
              <p class="text-xs font-bold text-slate-800 dark:text-slate-200">Catat Transaksi</p>
              <p class="text-[10px] text-slate-400">Sewa atau biaya operasional</p>
            </span>
          </button>
          <button
            @click="router.push('/rental/laporan')"
            class="flex items-center gap-2.5 px-3.5 py-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-700 text-left transition-all group shadow-2xs"
          >
            <span class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-950/50 flex items-center justify-center shrink-0">
              <ArrowTrendingUpIcon class="w-4 h-4 text-blue-600 dark:text-blue-400" />
            </span>
            <span>
              <p class="text-xs font-bold text-slate-800 dark:text-slate-200">Lihat Laporan</p>
              <p class="text-[10px] text-slate-400">Riwayat bulan sebelumnya</p>
            </span>
          </button>
        </div>
      </div>

      <!-- ══ 4 METRIC CARDS dengan Ring Chart ═══════════════════════════════ -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">

        <!-- 1. Total Sewa Masuk -->
        <div class="card p-4 sm:p-5 flex flex-col gap-1">
          <!-- Header row -->
          <div class="flex items-center justify-between gap-2">
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center shrink-0">
              <BanknotesIcon class="w-4.5 h-4.5 sm:w-5 sm:h-5 text-emerald-600 dark:text-emerald-400" />
            </div>
            <div class="w-6 h-6 rounded-lg bg-emerald-50 dark:bg-emerald-900/40 flex items-center justify-center">
              <ArrowTrendingUpIcon class="w-3.5 h-3.5 text-emerald-500" />
            </div>
          </div>
          <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-1">Total Sewa Masuk</p>

          <!-- Ring chart -->
          <div class="flex items-center justify-center py-2">
            <div class="relative w-28 h-28 sm:w-32 sm:h-32">
              <svg viewBox="0 0 120 120" class="w-full h-full -rotate-90">
                <!-- Track -->
                <circle cx="60" cy="60" r="48" fill="none" stroke="#E2E8F0" stroke-width="10" class="dark:[stroke:#1E293B]"/>
                <!-- Fill -->
                <circle
                  cx="60" cy="60" r="48" fill="none"
                  stroke="#10B981" stroke-width="10"
                  stroke-linecap="round"
                  :stroke-dasharray="ringDash(totalIncome > 0 ? Math.min(totalIncome / (totalIncome + totalExpense + 1), 1) : 0)"
                  class="transition-all duration-700"
                />
              </svg>
              <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-1">
                <p class="text-[11px] sm:text-xs font-bold text-slate-900 dark:text-white tabular-nums leading-tight">
                  {{ formatCurrency(totalIncome) }}
                </p>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700/60">
            <span class="inline-flex items-center gap-1 text-[10px] sm:text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">
              <ArrowTrendingUpIcon class="w-3 h-3" /> Pemasukan sewa
            </span>
            <span class="text-[10px] text-slate-400">Bulan ini</span>
          </div>
        </div>

        <!-- 2. Biaya Operasional -->
        <div class="card p-4 sm:p-5 flex flex-col gap-1">
          <div class="flex items-center justify-between gap-2">
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-rose-100 dark:bg-rose-900/50 flex items-center justify-center shrink-0">
              <ReceiptRefundIcon class="w-4.5 h-4.5 sm:w-5 sm:h-5 text-rose-600 dark:text-rose-400" />
            </div>
            <div class="w-6 h-6 rounded-lg bg-rose-50 dark:bg-rose-900/40 flex items-center justify-center">
              <ArrowTrendingUpIcon class="w-3.5 h-3.5 text-rose-500 rotate-180" />
            </div>
          </div>
          <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-1">Biaya Operasional</p>

          <div class="flex items-center justify-center py-2">
            <div class="relative w-28 h-28 sm:w-32 sm:h-32">
              <svg viewBox="0 0 120 120" class="w-full h-full -rotate-90">
                <circle cx="60" cy="60" r="48" fill="none" stroke="#E2E8F0" stroke-width="10" class="dark:[stroke:#1E293B]"/>
                <circle
                  cx="60" cy="60" r="48" fill="none"
                  stroke="#F43F5E" stroke-width="10"
                  stroke-linecap="round"
                  :stroke-dasharray="ringDash(totalExpense > 0 ? Math.min(totalExpense / (totalIncome + totalExpense + 1), 1) : 0)"
                  class="transition-all duration-700"
                />
              </svg>
              <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-1">
                <p class="text-[11px] sm:text-xs font-bold text-slate-900 dark:text-white tabular-nums leading-tight">
                  {{ formatCurrency(totalExpense) }}
                </p>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700/60">
            <span class="inline-flex items-center gap-1 text-[10px] sm:text-[11px] font-semibold text-rose-500 dark:text-rose-400">
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.22 14.78a.75.75 0 001.06 0l7.22-7.22v5.69a.75.75 0 001.5 0v-7.5a.75.75 0 00-.75-.75h-7.5a.75.75 0 000 1.5h5.69l-7.22 7.22a.75.75 0 000 1.06z" clip-rule="evenodd"/></svg>
              Beban armada
            </span>
            <span class="text-[10px] text-slate-400">Bensin &amp; servis</span>
          </div>
        </div>

        <!-- 3. Laba Bersih -->
        <div class="card p-4 sm:p-5 flex flex-col gap-1">
          <div class="flex items-center justify-between gap-2">
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center shrink-0">
              <ChartBarIcon class="w-4.5 h-4.5 sm:w-5 sm:h-5 text-blue-600 dark:text-blue-400" />
            </div>
            <div class="w-6 h-6 rounded-lg bg-blue-50 dark:bg-blue-900/40 flex items-center justify-center">
              <svg class="w-3.5 h-3.5" :class="totalProfit >= 0 ? 'text-blue-500' : 'text-rose-500'" fill="currentColor" viewBox="0 0 20 20">
                <path v-if="totalProfit >= 0" fill-rule="evenodd" d="M12.577 4.878a.75.75 0 01.919-.53l4.78 1.281a.75.75 0 01.531.919l-1.281 4.78a.75.75 0 01-1.449-.387l.81-3.022a19.407 19.407 0 00-5.594 5.203.75.75 0 01-1.139.093L7 10.06l-4.72 4.72a.75.75 0 01-1.06-1.061l5.25-5.25a.75.75 0 011.06 0l3.074 3.073a20.923 20.923 0 015.545-4.931l-3.042-.815a.75.75 0 01-.53-.918z" clip-rule="evenodd"/>
                <path v-else fill-rule="evenodd" d="M1.22 5.222a.75.75 0 011.06 0L7 9.942l3.768-3.769a.75.75 0 011.113.058 20.908 20.908 0 013.813 7.254l1.574-2.727a.75.75 0 011.3.75l-2.475 4.286a.75.75 0 01-1.025.275l-4.287-2.475a.75.75 0 01.75-1.3l2.71 1.565a19.422 19.422 0 00-3.013-6.024L7.53 11.533a.75.75 0 01-1.06 0l-5.25-5.25a.75.75 0 010-1.061z" clip-rule="evenodd"/>
              </svg>
            </div>
          </div>
          <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-1">Laba Bersih</p>

          <div class="flex items-center justify-center py-2">
            <div class="relative w-28 h-28 sm:w-32 sm:h-32">
              <svg viewBox="0 0 120 120" class="w-full h-full -rotate-90">
                <circle cx="60" cy="60" r="48" fill="none" stroke="#E2E8F0" stroke-width="10" class="dark:[stroke:#1E293B]"/>
                <circle
                  cx="60" cy="60" r="48" fill="none"
                  :stroke="totalProfit >= 0 ? '#3B82F6' : '#F43F5E'" stroke-width="10"
                  stroke-linecap="round"
                  :stroke-dasharray="ringDash(totalIncome > 0 ? Math.min(Math.abs(totalProfit) / (totalIncome + 1), 1) : 0)"
                  class="transition-all duration-700"
                />
              </svg>
              <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-1">
                <p :class="['text-[11px] sm:text-xs font-bold tabular-nums leading-tight', totalProfit >= 0 ? 'text-slate-900 dark:text-white' : 'text-rose-600']">
                  {{ formatCurrency(totalProfit) }}
                </p>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700/60">
            <span class="inline-flex items-center gap-1 text-[10px] sm:text-[11px] font-semibold text-blue-600 dark:text-blue-400">
              <ArrowTrendingUpIcon class="w-3 h-3" /> Hasil bersih
            </span>
            <span class="text-[10px] text-slate-400">{{ totalProfit >= 0 ? 'Surplus laba' : 'Defisit' }}</span>
          </div>
        </div>

        <!-- 4. Unit Armada — multi-segment ring -->
        <div class="card p-4 sm:p-5 flex flex-col gap-1">
          <div class="flex items-center justify-between gap-2">
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-violet-100 dark:bg-violet-900/50 flex items-center justify-center shrink-0">
              <TruckIcon class="w-4.5 h-4.5 sm:w-5 sm:h-5 text-violet-600 dark:text-violet-400" />
            </div>
            <div class="w-6 h-6 rounded-lg bg-violet-50 dark:bg-violet-900/40 flex items-center justify-center">
              <svg class="w-3.5 h-3.5 text-violet-500" fill="currentColor" viewBox="0 0 20 20"><path d="M3.196 12.87l-.825.483a.75.75 0 000 1.294l7.25 4.25a.75.75 0 00.758 0l7.25-4.25a.75.75 0 000-1.294l-.825-.484-5.666 3.322a1.5 1.5 0 01-1.516 0l-5.426-3.32z"/><path d="M16.94 9.123l.75-.44a.75.75 0 000-1.29l-7.25-4.26a.75.75 0 00-.76 0L2.43 7.393a.75.75 0 000 1.29l.75.44 5.33-3.131a1.5 1.5 0 011.52 0l6.91 4.131z"/><path d="M8.31 13.723l-5.01-2.944-1.48.868a.75.75 0 000 1.296l7.25 4.25a.75.75 0 00.758 0l7.25-4.25a.75.75 0 000-1.296l-1.48-.868-5.01 2.944a1.5 1.5 0 01-1.278 0z"/></svg>
            </div>
          </div>
          <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-1">Unit Armada</p>

          <!-- Ring + legend side by side di desktop, stacked di mobile -->
          <div class="flex items-center justify-center gap-3 py-2">
            <!-- Ring -->
            <div class="relative w-24 h-24 sm:w-28 sm:h-28 shrink-0">
              <svg viewBox="0 0 120 120" class="w-full h-full -rotate-90">
                <!-- Track -->
                <circle cx="60" cy="60" r="48" fill="none" stroke="#E2E8F0" stroke-width="10" class="dark:[stroke:#1E293B]"/>
                <!-- Emerald: available -->
                <circle
                  v-if="availableCount > 0"
                  cx="60" cy="60" r="48" fill="none"
                  stroke="#10B981" stroke-width="10"
                  stroke-linecap="butt"
                  :stroke-dasharray="`${availableArc.toFixed(1)} ${C}`"
                  stroke-dashoffset="0"
                />
                <!-- Blue: rented -->
                <circle
                  v-if="rentedCount > 0"
                  cx="60" cy="60" r="48" fill="none"
                  stroke="#3B82F6" stroke-width="10"
                  stroke-linecap="butt"
                  :stroke-dasharray="`${rentedArc.toFixed(1)} ${C}`"
                  :stroke-dashoffset="`-${availableArc.toFixed(1)}`"
                />
                <!-- Amber: maintenance -->
                <circle
                  v-if="maintenanceCount > 0"
                  cx="60" cy="60" r="48" fill="none"
                  stroke="#F59E0B" stroke-width="10"
                  stroke-linecap="butt"
                  :stroke-dasharray="`${maintenArc.toFixed(1)} ${C}`"
                  :stroke-dashoffset="`-${(availableArc + rentedArc).toFixed(1)}`"
                />
                <!-- Fallback: all-green when no vehicles yet -->
                <circle
                  v-if="vehiclesStore.vehicles.length === 0"
                  cx="60" cy="60" r="48" fill="none"
                  stroke="#E2E8F0" stroke-width="10"
                  :stroke-dasharray="`${C * 0.03} ${C}`"
                />
              </svg>
              <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                <p class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white leading-none">{{ vehiclesStore.vehicles.length }}</p>
                <p class="text-[10px] text-slate-400 mt-0.5">Mobil</p>
              </div>
            </div>

            <!-- Legend -->
            <div class="flex flex-col gap-1.5 text-[11px]">
              <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                <span class="text-slate-600 dark:text-slate-300">{{ availableCount }} siap</span>
              </div>
              <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-500 shrink-0"></span>
                <span class="text-slate-600 dark:text-slate-300">{{ rentedCount }} sewa</span>
              </div>
              <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shrink-0"></span>
                <span class="text-slate-600 dark:text-slate-300">{{ maintenanceCount }} servis</span>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700/60">
            <span class="text-[10px] sm:text-[11px] font-semibold text-violet-600 dark:text-violet-400">Total armada</span>
            <span :class="['text-[10px] font-medium', maintenanceCount > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400']">
              {{ fleetStatusText }}
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
