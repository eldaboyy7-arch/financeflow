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
  ArrowTrendingDownIcon,
  BanknotesIcon,
  TruckIcon,
  MapPinIcon,
  GlobeAltIcon,
} from '@heroicons/vue/24/outline'
import QuickVehicleIncomeModal from '@/components/rental/QuickVehicleIncomeModal.vue'

const vehiclesStore = useVehiclesStore()
const tourStore = useTourPackagesStore()
const { formatCurrency } = useFormatCurrency()
const authStore = useAuthStore()
const router = useRouter()

const now = new Date()
const month = ref(now.getMonth() + 1)
const year = ref(now.getFullYear())
const monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']
const firstName = computed(() => authStore.user?.name?.split(' ')[0] || 'Owner')
const rentalWebsiteUrl = (import.meta.env.VITE_RENTAL_WEBSITE_URL as string) || (import.meta.env.DEV ? 'http://localhost:5175' : 'https://3putrimulya-rent.vercel.app')

onMounted(() => {
  vehiclesStore.fetchVehicles(month.value, year.value)
  tourStore.fetchPackages()
})

const totalIncome = computed(() => vehiclesStore.vehicles.reduce((s, v) => s + (v.summary?.income || 0), 0))
const totalExpense = computed(() => vehiclesStore.vehicles.reduce((s, v) => s + (v.summary?.expense || 0), 0))
const totalProfit = computed(() => totalIncome.value - totalExpense.value)
const availableCount = computed(() => vehiclesStore.vehicles.filter(v => v.status === 'available').length)
const rentedCount = computed(() => vehiclesStore.vehicles.filter(v => v.status === 'rented').length)
const maintenanceCount = computed(() => vehiclesStore.vehicles.filter(v => v.status === 'maintenance').length)
const activeTourCount = computed(() => tourStore.packages.filter(p => p.is_active).length)

// Armada diurutkan: yang ada transaksi bulan ini tampil di atas
const sortedVehicles = computed(() =>
  [...vehiclesStore.vehicles].sort((a, b) => {
    const incomeA = a.summary?.income || 0
    const incomeB = b.summary?.income || 0
    if (incomeA !== incomeB) return incomeB - incomeA
    return (a.name || '').localeCompare(b.name || '', 'id')
  })
)

// Teks status dinamis untuk kartu Unit Armada
const fleetStatusText = computed(() => {
  if (maintenanceCount.value > 0 && rentedCount.value > 0)
    return `${rentedCount.value} sedang jalan · ${maintenanceCount.value} servis`
  if (maintenanceCount.value > 0)
    return `${maintenanceCount.value} unit perlu perhatian`
  if (rentedCount.value > 0)
    return `${rentedCount.value} unit sedang disewa`
  return 'Semua unit siap beroperasi'
})

// ── Alert & Onboarding ────────────────────────────────────────────────────────
// Apakah ini "bulan yang belum punya transaksi sama sekali"
const isBlankMonth = computed(() => totalIncome.value === 0 && totalExpense.value === 0)

// Armada yang sedang di bengkel (untuk alert)
const maintenanceVehicles = computed(() =>
  vehiclesStore.vehicles.filter(v => v.status === 'maintenance')
)

// Armada yang saat ini aktif disewa
const rentedVehicles = computed(() =>
  vehiclesStore.vehicles.filter(v => v.status === 'rented')
)

function statusBadge(s: string) {
  if (s === 'available') return { label: 'Tersedia', class: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400' }
  if (s === 'rented') return { label: 'Disewa', class: 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400' }
  return { label: 'Servis', class: 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400' }
}

const showQuickIncomeModal = ref(false)
const selectedVehicleForIncome = ref<any>(null)

function openQuickIncome(vehicle: any) {
  selectedVehicleForIncome.value = vehicle
  showQuickIncomeModal.value = true
}

function onRentalSaved() {
  vehiclesStore.fetchVehicles(month.value, year.value)
}
</script>

<template>
  <div class="space-y-4 sm:space-y-5">
    <!-- Header: Clean App Standard Banner -->
    <div class="card p-4 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold mb-2">
          <TruckIcon class="w-3.5 h-3.5 text-primary-600 dark:text-primary-400" />
          <span>Rental &amp; Paket Tour</span>
        </div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
          Ringkasan Rental · {{ monthNames[month - 1] }} {{ year }}
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
          Pantau pendapatan sewa, biaya operasional {{ vehiclesStore.vehicles.length }} armada, dan performa paket wisata.
        </p>
      </div>

      <div class="flex items-center gap-2 w-full sm:w-auto">
        <button
          @click="router.push('/rental/transaksi')"
          class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs sm:text-sm font-semibold shadow-sm transition-all"
        >
          <PlusIcon class="w-4 h-4" />
          <span>Catat Transaksi</span>
        </button>
        <button
          @click="router.push('/rental/paket-tour')"
          class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl bg-primary-50 hover:bg-primary-100 dark:bg-primary-950/40 dark:hover:bg-primary-900/50 text-primary-700 dark:text-primary-300 text-xs sm:text-sm font-semibold transition-all border border-primary-200/60 dark:border-primary-800/40"
        >
          <MapPinIcon class="w-4 h-4 text-primary-600 dark:text-primary-400" />
          <span>Paket Tour</span>
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="vehiclesStore.loading" class="card p-12 flex items-center justify-center">
      <MoneySpinner size="md" text="Memuat ringkasan rental..." subtext="Menghitung total pendapatan dan biaya operasional" />
    </div>

    <template v-else>

      <!-- ① ALERT: Ada armada di bengkel -->
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
        <button
          @click="router.push('/rental/armada')"
          class="text-xs font-bold text-amber-700 dark:text-amber-300 hover:underline shrink-0 mt-0.5"
        >
          Kelola →
        </button>
      </div>

      <!-- ② ALERT: Ada armada aktif sedang disewa -->
      <div
        v-if="rentedVehicles.length > 0"
        class="rounded-2xl border border-blue-200 dark:border-blue-800/60 bg-blue-50 dark:bg-blue-950/20 px-4 py-3 flex items-center gap-3"
      >
        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse shrink-0"></span>
        <p class="text-xs text-blue-700 dark:text-blue-300 flex-1">
          <span class="font-bold">{{ rentedVehicles.length }} unit sedang berjalan</span>
          · {{ rentedVehicles.map(v => v.name).join(', ') }}
        </p>
        <button
          @click="router.push('/rental/transaksi')"
          class="text-xs font-bold text-blue-700 dark:text-blue-300 hover:underline shrink-0"
        >
          Lihat Transaksi →
        </button>
      </div>

      <!-- ③ ONBOARDING: Belum ada transaksi bulan ini -->
      <div
        v-if="isBlankMonth && vehiclesStore.vehicles.length > 0"
        class="card border-2 border-dashed border-primary-200 dark:border-primary-800/60 bg-gradient-to-br from-primary-50/60 to-slate-50 dark:from-primary-950/20 dark:to-slate-800/80 p-5 sm:p-6"
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

        <!-- 3 langkah aksi -->
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
          <button
            @click="openQuickIncome(sortedVehicles[0])"
            class="flex items-center gap-2.5 px-3.5 py-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-emerald-300 dark:hover:border-emerald-700 hover:bg-emerald-50/50 dark:hover:bg-emerald-950/20 text-left transition-all group shadow-2xs"
          >
            <span class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-950/50 flex items-center justify-center shrink-0 group-hover:bg-emerald-200 dark:group-hover:bg-emerald-900/60 transition-colors">
              <BanknotesIcon class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
            </span>
            <span>
              <p class="text-xs font-bold text-slate-800 dark:text-slate-200">Catat Sewa Cepat</p>
              <p class="text-[10px] text-slate-400">{{ sortedVehicles[0]?.name || 'Pilih armada' }}</p>
            </span>
          </button>

          <button
            @click="router.push('/rental/transaksi')"
            class="flex items-center gap-2.5 px-3.5 py-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700/50 text-left transition-all group shadow-2xs"
          >
            <span class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0 group-hover:bg-slate-200 dark:group-hover:bg-slate-600 transition-colors">
              <PlusIcon class="w-4 h-4 text-slate-600 dark:text-slate-300" />
            </span>
            <span>
              <p class="text-xs font-bold text-slate-800 dark:text-slate-200">Catat Transaksi</p>
              <p class="text-[10px] text-slate-400">Sewa atau biaya operasional</p>
            </span>
          </button>

          <button
            @click="router.push('/rental/laporan')"
            class="flex items-center gap-2.5 px-3.5 py-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-700 hover:bg-blue-50/50 dark:hover:bg-blue-950/20 text-left transition-all group shadow-2xs"
          >
            <span class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-950/50 flex items-center justify-center shrink-0 group-hover:bg-blue-200 dark:group-hover:bg-blue-900/60 transition-colors">
              <ArrowTrendingUpIcon class="w-4 h-4 text-blue-600 dark:text-blue-400" />
            </span>
            <span>
              <p class="text-xs font-bold text-slate-800 dark:text-slate-200">Lihat Laporan</p>
              <p class="text-[10px] text-slate-400">Riwayat bulan sebelumnya</p>
            </span>
          </button>
        </div>
      </div>

      <!-- Metric Cards: 4 Clean Columns dengan Sparklines Responsif -->
      <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- 1. Total Sewa Masuk -->
        <div class="card p-3.5 sm:p-5 flex flex-col justify-between relative overflow-hidden group">
          <div class="flex items-center justify-between gap-2 mb-2">
            <span class="text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400">Total Sewa Masuk</span>
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
              <ArrowTrendingUpIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4" />
            </div>
          </div>
          <div>
            <p class="text-base sm:text-xl font-bold text-slate-900 dark:text-white tabular-nums tracking-tight">
              {{ formatCurrency(totalIncome) }}
            </p>
          </div>
          <!-- Sparkline Chart (Fluid & Responsive untuk Mobile & Tablet) -->
          <div class="my-2 h-7 sm:h-8 w-full relative">
            <svg class="w-full h-full overflow-visible" viewBox="0 0 100 24" preserveAspectRatio="none">
              <defs>
                <linearGradient id="sparkIncomeGrad" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="#10B981" stop-opacity="0.30" />
                  <stop offset="100%" stop-color="#10B981" stop-opacity="0.0" />
                </linearGradient>
              </defs>
              <path
                d="M 0,18 Q 15,22 30,14 T 60,11 T 85,6 L 100,4 L 100,24 L 0,24 Z"
                fill="url(#sparkIncomeGrad)"
              />
              <path
                d="M 0,18 Q 15,22 30,14 T 60,11 T 85,6 L 100,4"
                fill="none"
                stroke="#10B981"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
              <circle cx="100" cy="4" r="2.5" fill="#10B981" />
            </svg>
          </div>
          <div class="flex items-center justify-between text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 pt-1.5 border-t border-slate-100 dark:border-slate-800">
            <span>Bulan ini</span>
            <span class="text-emerald-600 dark:text-emerald-400 font-semibold">Pemasukan sewa</span>
          </div>
        </div>

        <!-- 2. Biaya Operasional -->
        <div class="card p-3.5 sm:p-5 flex flex-col justify-between relative overflow-hidden group">
          <div class="flex items-center justify-between gap-2 mb-2">
            <span class="text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400">Biaya Operasional</span>
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
              <ArrowTrendingDownIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4" />
            </div>
          </div>
          <div>
            <p class="text-base sm:text-xl font-bold text-slate-900 dark:text-white tabular-nums tracking-tight">
              {{ formatCurrency(totalExpense) }}
            </p>
          </div>
          <!-- Sparkline Chart (Fluid & Responsive untuk Mobile & Tablet) -->
          <div class="my-2 h-7 sm:h-8 w-full relative">
            <svg class="w-full h-full overflow-visible" viewBox="0 0 100 24" preserveAspectRatio="none">
              <defs>
                <linearGradient id="sparkExpenseGrad" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="#F43F5E" stop-opacity="0.25" />
                  <stop offset="100%" stop-color="#F43F5E" stop-opacity="0.0" />
                </linearGradient>
              </defs>
              <path
                d="M 0,6 Q 20,4 40,12 T 70,10 T 90,16 L 100,18 L 100,24 L 0,24 Z"
                fill="url(#sparkExpenseGrad)"
              />
              <path
                d="M 0,6 Q 20,4 40,12 T 70,10 T 90,16 L 100,18"
                fill="none"
                stroke="#F43F5E"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
              <circle cx="100" cy="18" r="2.5" fill="#F43F5E" />
            </svg>
          </div>
          <div class="flex items-center justify-between text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 pt-1.5 border-t border-slate-100 dark:border-slate-800">
            <span>Bensin &amp; servis</span>
            <span class="text-rose-500 dark:text-rose-400 font-semibold">Beban armada</span>
          </div>
        </div>

        <!-- 3. Laba Bersih -->
        <div class="card p-3.5 sm:p-5 flex flex-col justify-between relative overflow-hidden group">
          <div class="flex items-center justify-between gap-2 mb-2">
            <span class="text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400">Laba Bersih</span>
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
              <BanknotesIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4" />
            </div>
          </div>
          <div>
            <p :class="['text-base sm:text-xl font-bold tabular-nums tracking-tight', totalProfit >= 0 ? 'text-slate-900 dark:text-white' : 'text-rose-600']">
              {{ formatCurrency(totalProfit) }}
            </p>
          </div>
          <!-- Sparkline Chart (Fluid & Responsive untuk Mobile & Tablet) -->
          <div class="my-2 h-7 sm:h-8 w-full relative">
            <svg class="w-full h-full overflow-visible" viewBox="0 0 100 24" preserveAspectRatio="none">
              <defs>
                <linearGradient id="sparkProfitGrad" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="#3B82F6" stop-opacity="0.28" />
                  <stop offset="100%" stop-color="#3B82F6" stop-opacity="0.0" />
                </linearGradient>
              </defs>
              <path
                d="M 0,16 Q 25,18 45,10 T 75,8 L 100,3 L 100,24 L 0,24 Z"
                fill="url(#sparkProfitGrad)"
              />
              <path
                d="M 0,16 Q 25,18 45,10 T 75,8 L 100,3"
                fill="none"
                stroke="#3B82F6"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
              <circle cx="100" cy="3" r="2.5" fill="#3B82F6" />
            </svg>
          </div>
          <div class="flex items-center justify-between text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 pt-1.5 border-t border-slate-100 dark:border-slate-800">
            <span>{{ totalProfit >= 0 ? 'Surplus laba' : 'Defisit' }}</span>
            <span class="text-blue-600 dark:text-blue-400 font-semibold">Hasil bersih</span>
          </div>
        </div>

        <!-- 4. Unit Armada -->
        <div class="card p-3.5 sm:p-5 flex flex-col justify-between relative overflow-hidden group">
          <div class="flex items-center justify-between gap-2 mb-2">
            <span class="text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400">Unit Armada</span>
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0">
              <TruckIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4" />
            </div>
          </div>
          <div>
            <p class="text-base sm:text-xl font-bold text-slate-900 dark:text-white tabular-nums tracking-tight">
              {{ vehiclesStore.vehicles.length }} <span class="text-xs font-normal text-slate-500">Mobil</span>
            </p>
          </div>
          <!-- Segmented Fleet Track Bar (Fluid & Responsive untuk Mobile & Tablet) -->
          <div class="my-2 h-7 sm:h-8 flex flex-col justify-center">
            <div class="w-full h-2.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden flex gap-0.5 p-0.5 border border-slate-200/60 dark:border-slate-700/60">
              <div
                class="bg-emerald-500 h-full rounded-full transition-all duration-500"
                :style="{ width: vehiclesStore.vehicles.length > 0 ? `${(availableCount / vehiclesStore.vehicles.length) * 100}%` : '0%' }"
                title="Tersedia"
              ></div>
              <div
                class="bg-blue-500 h-full rounded-full transition-all duration-500"
                :style="{ width: vehiclesStore.vehicles.length > 0 ? `${(rentedCount / vehiclesStore.vehicles.length) * 100}%` : '0%' }"
                title="Disewa"
              ></div>
              <div
                class="bg-amber-500 h-full rounded-full transition-all duration-500"
                :style="{ width: vehiclesStore.vehicles.length > 0 ? `${(maintenanceCount / vehiclesStore.vehicles.length) * 100}%` : '0%' }"
                title="Bengkel"
              ></div>
            </div>
            <div class="flex items-center justify-between text-[9px] text-slate-400 mt-1 px-0.5">
              <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ availableCount }} siap</span>
              <span class="text-blue-600 dark:text-blue-400 font-semibold">{{ rentedCount }} sewa</span>
              <span class="text-amber-600 dark:text-amber-400 font-semibold">{{ maintenanceCount }} servis</span>
            </div>
          </div>
          <div class="flex items-center justify-between text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 pt-1.5 border-t border-slate-100 dark:border-slate-800">
            <span>Total armada</span>
            <span :class="['font-semibold', maintenanceCount > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-600 dark:text-slate-300']">
              {{ fleetStatusText }}
            </span>
          </div>
        </div>
      </div>

      <!-- Tour Packages Live Status Banner -->
      <div class="card p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-gradient-to-r from-blue-50/60 via-indigo-50/30 to-slate-50 dark:from-slate-800 dark:via-slate-800/90 dark:to-slate-800 border border-blue-100/80 dark:border-slate-700">
        <div class="flex items-center gap-3.5">
          <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-primary-600 text-white flex items-center justify-center shrink-0 shadow-xs">
            <MapPinIcon class="w-5 h-5 sm:w-6 sm:h-6" />
          </div>
          <div>
            <div class="flex items-center gap-2">
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
            <span>Kelola Paket Tour</span>
            <ChevronRightIcon class="w-3.5 h-3.5" />
          </button>
          <a
            :href="`${rentalWebsiteUrl}/paket-tour-bintan`"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center justify-center gap-1 p-2 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold shadow-xs transition shrink-0"
            title="Buka Halaman Paket Tour di Website Publik"
          >
            <GlobeAltIcon class="w-4 h-4" />
          </a>
        </div>
      </div>

      <!-- Vehicle Performance List -->
      <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
          <div>
            <h2 class="text-sm font-bold text-slate-900 dark:text-white">Performa Armada Bulan Ini</h2>
            <p class="text-xs text-slate-400">Ringkasan pendapatan & laba per mobil</p>
          </div>
          <button
            @click="router.push('/rental/laporan')"
            class="text-xs font-semibold text-primary-600 dark:text-primary-400 hover:underline flex items-center gap-1"
          >
            Laporan Lengkap <ChevronRightIcon class="w-3.5 h-3.5" />
          </button>
        </div>

        <!-- Empty state: belum ada armada sama sekali -->
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

        <!-- Vehicle Row Items (sorted: terbesar income di atas) -->
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
                  title="Catat Pemasukan Sewa Mobil Ini"
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
