<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useVehiclesStore } from '@/stores/vehicles'
import api from '@/api/axios'
import {
  TruckIcon,
  ClipboardDocumentListIcon,
  ArrowTrendingUpIcon,
  ArrowTrendingDownIcon,
} from '@heroicons/vue/24/outline'

const vehiclesStore = useVehiclesStore()

const now     = new Date()
const month   = ref(now.getMonth() + 1)
const year    = ref(now.getFullYear())
const report  = ref<any>(null)
const loading = ref(false)

const monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']
const years = [now.getFullYear(), now.getFullYear() - 1, now.getFullYear() - 2]

async function fetchReport() {
  loading.value = true
  try {
    report.value = await vehiclesStore.fetchReport(month.value, year.value)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  vehiclesStore.fetchVehicles()
  fetchReport()
})

function formatRp(n: number) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(n)
}
</script>

<template>
  <div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
          <ClipboardDocumentListIcon class="w-7 h-7 text-blue-600" />
          Laporan Armada
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Laba-rugi per unit kendaraan</p>
      </div>

      <!-- Period Picker -->
      <div class="flex gap-2">
        <select v-model.number="month" @change="fetchReport" class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
          <option v-for="(m, i) in monthNames" :key="i" :value="i + 1">{{ m }}</option>
        </select>
        <select v-model.number="year" @change="fetchReport" class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
          <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
        </select>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-16 text-slate-400">Memuat laporan...</div>

    <template v-else-if="report">
      <!-- Total Summary -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm text-center">
          <ArrowTrendingUpIcon class="w-8 h-8 text-emerald-500 mx-auto mb-2" />
          <p class="text-xs text-slate-400 mb-1">Total Sewa Masuk</p>
          <p class="text-xl font-bold text-emerald-600">{{ formatRp(report.total_income) }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm text-center">
          <ArrowTrendingDownIcon class="w-8 h-8 text-red-400 mx-auto mb-2" />
          <p class="text-xs text-slate-400 mb-1">Total Biaya Operasional</p>
          <p class="text-xl font-bold text-red-500">{{ formatRp(report.total_expense) }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm text-center">
          <TruckIcon class="w-8 h-8 text-blue-500 mx-auto mb-2" />
          <p class="text-xs text-slate-400 mb-1">Laba Bersih</p>
          <p :class="['text-xl font-bold', report.total_profit >= 0 ? 'text-blue-600' : 'text-red-500']">
            {{ formatRp(report.total_profit) }}
          </p>
        </div>
      </div>

      <!-- Per Vehicle Table -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
          <h2 class="font-semibold text-slate-700 dark:text-slate-200">
            Detail Per Kendaraan — {{ monthNames[month - 1] }} {{ year }}
          </h2>
        </div>

        <div v-if="report.vehicles.length === 0" class="p-10 text-center text-slate-400">
          Belum ada data armada untuk periode ini.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700/40">
              <tr>
                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Kendaraan</th>
                <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Sewa Masuk</th>
                <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Biaya Keluar</th>
                <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Laba Bersih</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
              <tr
                v-for="v in report.vehicles"
                :key="v.id"
                class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors"
              >
                <td class="px-5 py-3.5">
                  <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" :style="{ backgroundColor: v.color + '22' }">
                      <TruckIcon class="w-4 h-4" :style="{ color: v.color }" />
                    </div>
                    <div>
                      <p class="font-semibold text-slate-800 dark:text-white">{{ v.name }}</p>
                      <p class="text-xs text-slate-400">{{ v.plate_number ?? '-' }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3.5 text-right font-medium text-emerald-600">{{ formatRp(v.income) }}</td>
                <td class="px-4 py-3.5 text-right font-medium text-red-500">{{ formatRp(v.expense) }}</td>
                <td class="px-5 py-3.5 text-right">
                  <span :class="['font-bold px-3 py-1 rounded-full text-xs', v.profit >= 0 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300']">
                    {{ formatRp(v.profit) }}
                  </span>
                </td>
              </tr>
            </tbody>
            <!-- Footer total -->
            <tfoot class="bg-slate-50 dark:bg-slate-700/40 border-t-2 border-slate-200 dark:border-slate-600">
              <tr>
                <td class="px-5 py-3.5 font-bold text-slate-700 dark:text-slate-200">TOTAL</td>
                <td class="px-4 py-3.5 text-right font-bold text-emerald-600">{{ formatRp(report.total_income) }}</td>
                <td class="px-4 py-3.5 text-right font-bold text-red-500">{{ formatRp(report.total_expense) }}</td>
                <td class="px-5 py-3.5 text-right">
                  <span :class="['font-bold px-3 py-1 rounded-full text-xs', report.total_profit >= 0 ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-red-100 text-red-700']">
                    {{ formatRp(report.total_profit) }}
                  </span>
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </template>
  </div>
</template>
