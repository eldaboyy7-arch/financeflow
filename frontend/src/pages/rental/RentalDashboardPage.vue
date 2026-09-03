<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useVehiclesStore } from '@/stores/vehicles'
import { useFormatCurrency } from '@/composables/useFormatCurrency'
import { useAuthStore } from '@/stores/auth'
import MoneySpinner from '@/components/MoneySpinner.vue'
import {
  PlusIcon,
  ChevronRightIcon,
  ArrowTrendingUpIcon,
  ArrowTrendingDownIcon,
  BanknotesIcon,
  TruckIcon,
  WrenchScrewdriverIcon,
  CheckCircleIcon,
  ClockIcon
} from '@heroicons/vue/24/outline'

const vehiclesStore = useVehiclesStore()
const { formatCurrency } = useFormatCurrency()
const authStore = useAuthStore()
const router = useRouter()

const now = new Date()
const month = ref(now.getMonth() + 1)
const year = ref(now.getFullYear())
const monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']
const firstName = computed(() => authStore.user?.name?.split(' ')[0] || 'Owner')

onMounted(() => {
  vehiclesStore.fetchVehicles(month.value, year.value)
})

const totalIncome = computed(() => vehiclesStore.vehicles.reduce((s, v) => s + (v.summary?.income || 0), 0))
const totalExpense = computed(() => vehiclesStore.vehicles.reduce((s, v) => s + (v.summary?.expense || 0), 0))
const totalProfit = computed(() => totalIncome.value - totalExpense.value)
const availableCount = computed(() => vehiclesStore.vehicles.filter(v => v.status === 'available').length)
const rentedCount = computed(() => vehiclesStore.vehicles.filter(v => v.status === 'rented').length)
const maintenanceCount = computed(() => vehiclesStore.vehicles.filter(v => v.status === 'maintenance').length)

function statusBadge(s: string) {
  if (s === 'available') return { label: 'Tersedia', class: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400' }
  if (s === 'rented') return { label: 'Disewa', class: 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400' }
  return { label: 'Servis', class: 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400' }
}
</script>

<template>
  <div class="space-y-5">
    <!-- Header: Clean App Standard Banner -->
    <div class="card p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-semibold mb-2">
          <TruckIcon class="w-3.5 h-3.5 text-primary-600 dark:text-primary-400" />
          Mode Rental Mobil
        </div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
          Ringkasan Rental · {{ monthNames[month - 1] }} {{ year }}
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
          Pantau pendapatan sewa, biaya operasional, dan ketersediaan {{ vehiclesStore.vehicles.length }} armada mobil.
        </p>
      </div>

      <div class="flex items-center gap-2 w-full sm:w-auto">
        <button
          @click="router.push('/rental/transaksi')"
          class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs sm:text-sm font-semibold shadow-sm transition-all"
        >
          <PlusIcon class="w-4 h-4" />
          Catat Transaksi
        </button>
        <button
          @click="router.push('/rental/armada')"
          class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs sm:text-sm font-semibold transition-all"
        >
          <TruckIcon class="w-4 h-4" />
          Armada
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="vehiclesStore.loading" class="card p-12 flex items-center justify-center">
      <MoneySpinner size="md" text="Memuat ringkasan rental..." subtext="Menghitung total pendapatan dan biaya operasional" />
    </div>

    <template v-else>
      <!-- Metric Cards: 4 Clean Columns -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- 1. Sewa Masuk -->
        <div class="card p-4 sm:p-5 flex flex-col justify-between">
          <div class="flex items-center justify-between gap-2 mb-3">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Sewa Masuk</span>
            <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
              <ArrowTrendingUpIcon class="w-4 h-4" />
            </div>
          </div>
          <div>
            <p class="text-base sm:text-xl font-bold text-slate-900 dark:text-white tabular-nums">
              {{ formatCurrency(totalIncome) }}
            </p>
            <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium mt-1">
              Bulan ini
            </p>
          </div>
        </div>

        <!-- 2. Biaya Operasional -->
        <div class="card p-4 sm:p-5 flex flex-col justify-between">
          <div class="flex items-center justify-between gap-2 mb-3">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Biaya Operasional</span>
            <div class="w-8 h-8 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
              <ArrowTrendingDownIcon class="w-4 h-4" />
            </div>
          </div>
          <div>
            <p class="text-base sm:text-xl font-bold text-slate-900 dark:text-white tabular-nums">
              {{ formatCurrency(totalExpense) }}
            </p>
            <p class="text-[11px] text-rose-500 dark:text-rose-400 font-medium mt-1">
              Servis & bensin
            </p>
          </div>
        </div>

        <!-- 3. Laba Bersih -->
        <div class="card p-4 sm:p-5 flex flex-col justify-between">
          <div class="flex items-center justify-between gap-2 mb-3">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Laba Bersih</span>
            <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
              <BanknotesIcon class="w-4 h-4" />
            </div>
          </div>
          <div>
            <p :class="['text-base sm:text-xl font-bold tabular-nums', totalProfit >= 0 ? 'text-slate-900 dark:text-white' : 'text-rose-600']">
              {{ formatCurrency(totalProfit) }}
            </p>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-1">
              {{ totalProfit >= 0 ? 'Surplus laba' : 'Defisit' }}
            </p>
          </div>
        </div>

        <!-- 4. Unit Armada -->
        <div class="card p-4 sm:p-5 flex flex-col justify-between">
          <div class="flex items-center justify-between gap-2 mb-3">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Unit Armada</span>
            <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0">
              <TruckIcon class="w-4 h-4" />
            </div>
          </div>
          <div>
            <p class="text-base sm:text-xl font-bold text-slate-900 dark:text-white tabular-nums">
              {{ vehiclesStore.vehicles.length }} <span class="text-xs font-normal text-slate-500">Mobil</span>
            </p>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-1">
              {{ availableCount }} siap · {{ rentedCount }} sewa · {{ maintenanceCount }} servis
            </p>
          </div>
        </div>
      </div>

      <!-- Fleet Status Pill Overview -->
      <div class="card p-4 sm:p-5">
        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Status Kesiapan Armada</h2>
        <div class="grid grid-cols-3 gap-3 text-center">
          <div class="p-3 rounded-xl bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/30">
            <div class="flex items-center justify-center gap-1.5 mb-1">
              <CheckCircleIcon class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
              <span class="text-xs font-semibold text-emerald-800 dark:text-emerald-300">Tersedia</span>
            </div>
            <p class="text-xl sm:text-2xl font-bold text-emerald-700 dark:text-emerald-400 tabular-nums">{{ availableCount }}</p>
          </div>

          <div class="p-3 rounded-xl bg-blue-50/60 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/30">
            <div class="flex items-center justify-center gap-1.5 mb-1">
              <ClockIcon class="w-4 h-4 text-blue-600 dark:text-blue-400" />
              <span class="text-xs font-semibold text-blue-800 dark:text-blue-300">Sedang Disewa</span>
            </div>
            <p class="text-xl sm:text-2xl font-bold text-blue-700 dark:text-blue-400 tabular-nums">{{ rentedCount }}</p>
          </div>

          <div class="p-3 rounded-xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/30">
            <div class="flex items-center justify-center gap-1.5 mb-1">
              <WrenchScrewdriverIcon class="w-4 h-4 text-amber-600 dark:text-amber-400" />
              <span class="text-xs font-semibold text-amber-800 dark:text-amber-300">Di Bengkel</span>
            </div>
            <p class="text-xl sm:text-2xl font-bold text-amber-700 dark:text-amber-400 tabular-nums">{{ maintenanceCount }}</p>
          </div>
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

        <!-- Clean Standard Empty State -->
        <div v-if="vehiclesStore.vehicles.length === 0" class="p-10 sm:p-12 flex flex-col items-center text-center">
          <div class="w-14 h-14 bg-slate-100 dark:bg-slate-700/60 rounded-2xl flex items-center justify-center mb-4">
            <TruckIcon class="w-7 h-7 text-slate-400" />
          </div>
          <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum ada armada mobil</p>
          <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 mb-4 max-w-xs">
            Tambahkan mobil pertama Anda untuk mulai mencatat sewa dan biaya operasional.
          </p>
          <button @click="router.push('/rental/armada')" class="btn-primary text-sm py-2 px-4">
            Tambah Mobil Pertama
          </button>
        </div>

        <!-- Vehicle Row Items -->
        <div v-else class="divide-y divide-slate-100 dark:divide-slate-700/60">
          <div
            v-for="v in vehiclesStore.vehicles"
            :key="v.id"
            class="p-4 sm:px-5 flex items-center justify-between gap-3 hover:bg-slate-50/70 dark:hover:bg-slate-700/30 transition-colors"
          >
            <div class="flex items-center gap-3.5 min-w-0">
              <div
                class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 text-slate-700 dark:text-slate-200"
                :style="{ backgroundColor: (v.color || '#3B82F6') + '20' }"
              >
                <TruckIcon class="w-5 h-5" :style="{ color: v.color || '#3B82F6' }" />
              </div>
              <div class="min-w-0">
                <div class="flex items-center gap-2">
                  <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ v.name }}</p>
                  <span class="badge text-[10px]" :class="statusBadge(v.status).class">
                    {{ statusBadge(v.status).label }}
                  </span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">
                  {{ v.plate_number || 'Tanpa Plat' }} <span v-if="v.daily_rate">· {{ formatCurrency(v.daily_rate) }}/hari</span>
                </p>
              </div>
            </div>

            <div class="text-right shrink-0">
              <p :class="['text-sm font-bold tabular-nums', (v.summary?.profit || 0) >= 0 ? 'text-slate-900 dark:text-white' : 'text-rose-500']">
                {{ formatCurrency(v.summary?.profit || 0) }}
              </p>
              <p class="text-[10px] text-slate-400">Laba Bersih</p>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>
