<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useVehiclesStore } from '@/stores/vehicles'
import { useRouter } from 'vue-router'
import {
  TruckIcon,
  BanknotesIcon,
  ArrowTrendingUpIcon,
  ArrowTrendingDownIcon,
  WrenchScrewdriverIcon,
  PlusIcon,
  ChevronRightIcon,
} from '@heroicons/vue/24/outline'

const vehiclesStore = useVehiclesStore()
const router = useRouter()

const now = new Date()
const month = ref(now.getMonth() + 1)
const year  = ref(now.getFullYear())

onMounted(() => vehiclesStore.fetchVehicles(month.value, year.value))

const totalIncome  = computed(() => vehiclesStore.vehicles.reduce((s, v) => s + v.summary.income, 0))
const totalExpense = computed(() => vehiclesStore.vehicles.reduce((s, v) => s + v.summary.expense, 0))
const totalProfit  = computed(() => totalIncome.value - totalExpense.value)

const available   = computed(() => vehiclesStore.vehicles.filter(v => v.status === 'available').length)
const rented      = computed(() => vehiclesStore.vehicles.filter(v => v.status === 'rented').length)
const maintenance = computed(() => vehiclesStore.vehicles.filter(v => v.status === 'maintenance').length)

const monthNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des']

function formatRp(n: number) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(n)
}

function statusLabel(s: string) {
  return s === 'available' ? 'Tersedia' : s === 'rented' ? 'Disewa' : 'Servis'
}
function statusColor(s: string) {
  return s === 'available'
    ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'
    : s === 'rented'
    ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
    : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300'
}
</script>

<template>
  <div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
          <TruckIcon class="w-7 h-7 text-blue-600" />
          Ringkasan Rental
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
          {{ monthNames[month - 1] }} {{ year }}
        </p>
      </div>
      <button
        @click="router.push('/rental/armada')"
        class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors shadow-sm"
      >
        <PlusIcon class="w-4 h-4" />
        Tambah Mobil
      </button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center">
            <ArrowTrendingUpIcon class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
          </div>
          <span class="text-sm text-slate-500 dark:text-slate-400 font-medium">Total Sewa Masuk</span>
        </div>
        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ formatRp(totalIncome) }}</p>
      </div>

      <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
            <ArrowTrendingDownIcon class="w-5 h-5 text-red-600 dark:text-red-400" />
          </div>
          <span class="text-sm text-slate-500 dark:text-slate-400 font-medium">Total Biaya Operasional</span>
        </div>
        <p class="text-2xl font-bold text-red-500 dark:text-red-400">{{ formatRp(totalExpense) }}</p>
      </div>

      <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
            <BanknotesIcon class="w-5 h-5 text-blue-600 dark:text-blue-400" />
          </div>
          <span class="text-sm text-slate-500 dark:text-slate-400 font-medium">Laba Bersih Bulan Ini</span>
        </div>
        <p :class="['text-2xl font-bold', totalProfit >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-500']">
          {{ formatRp(totalProfit) }}
        </p>
      </div>
    </div>

    <!-- Fleet Status -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
      <h2 class="font-semibold text-slate-700 dark:text-slate-200 mb-4">Status Armada</h2>
      <div class="grid grid-cols-3 gap-4 text-center">
        <div>
          <p class="text-3xl font-bold text-emerald-600">{{ available }}</p>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">🟢 Tersedia</p>
        </div>
        <div>
          <p class="text-3xl font-bold text-blue-600">{{ rented }}</p>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">🔵 Sedang Disewa</p>
        </div>
        <div>
          <p class="text-3xl font-bold text-amber-500">{{ maintenance }}</p>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">🔧 Di Bengkel</p>
        </div>
      </div>
    </div>

    <!-- Vehicle list quick view -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700">
        <h2 class="font-semibold text-slate-700 dark:text-slate-200">Performa Per Mobil</h2>
        <button @click="router.push('/rental/laporan')" class="text-xs text-blue-600 dark:text-blue-400 font-medium hover:underline flex items-center gap-1">
          Lihat Laporan Lengkap <ChevronRightIcon class="w-3.5 h-3.5" />
        </button>
      </div>

      <div v-if="vehiclesStore.loading" class="p-8 text-center text-slate-400">Memuat data...</div>

      <div v-else-if="vehiclesStore.vehicles.length === 0" class="p-10 text-center">
        <TruckIcon class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3" />
        <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada armada mobil</p>
        <button @click="router.push('/rental/armada')" class="mt-3 text-sm text-blue-600 dark:text-blue-400 hover:underline">+ Tambah Mobil Sekarang</button>
      </div>

      <div v-else>
        <div
          v-for="v in vehiclesStore.vehicles"
          :key="v.id"
          class="flex items-center gap-4 px-5 py-3.5 border-b border-slate-50 dark:border-slate-700/50 last:border-0 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors"
        >
          <!-- Color dot -->
          <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" :style="{ backgroundColor: v.color + '22' }">
            <TruckIcon class="w-5 h-5" :style="{ color: v.color }" />
          </div>
          <div class="flex-1 min-w-0">
            <p class="font-semibold text-slate-800 dark:text-white text-sm truncate">{{ v.name }}</p>
            <p class="text-xs text-slate-400">{{ v.plate_number ?? 'Plat belum diisi' }}</p>
          </div>
          <div class="text-right shrink-0">
            <p :class="['text-sm font-bold', v.summary.profit >= 0 ? 'text-emerald-600' : 'text-red-500']">
              {{ formatRp(v.summary.profit) }}
            </p>
            <span :class="['text-[10px] font-medium px-2 py-0.5 rounded-full', statusColor(v.status)]">
              {{ statusLabel(v.status) }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
