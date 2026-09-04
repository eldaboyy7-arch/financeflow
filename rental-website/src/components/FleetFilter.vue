<script setup lang="ts">
import type { TransmissionFilter } from '@/types/fleet'

defineProps<{
  searchQuery: string
  transmissionFilter: TransmissionFilter
  totalUnits: number
  availableUnits: number
}>()

const emit = defineEmits<{
  (e: 'update:searchQuery', val: string): void
  (e: 'update:transmissionFilter', val: TransmissionFilter): void
  (e: 'reset'): void
}>()

const transmissionOptions: { label: string; value: TransmissionFilter }[] = [
  { label: 'Semua Transmisi', value: 'all' },
  { label: 'Matic (Otomatis)', value: 'matic' },
  { label: 'Manual', value: 'manual' },
]
</script>

<template>
  <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5 mb-6">
    <div class="flex flex-col md:flex-row gap-4 items-stretch md:items-center justify-between">
      <!-- Search Input -->
      <div class="relative flex-1">
        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
        </div>
        <input
          type="text"
          :value="searchQuery"
          @input="emit('update:searchQuery', ($event.target as HTMLInputElement).value)"
          placeholder="Cari nama atau merk mobil (misal: Avanza, Innova, Brio)..."
          class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
        />
        <button
          v-if="searchQuery"
          @click="emit('update:searchQuery', '')"
          class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600"
          type="button"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Transmission Filter Pills -->
      <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 scrollbar-none">
        <button
          v-for="opt in transmissionOptions"
          :key="opt.value"
          @click="emit('update:transmissionFilter', opt.value)"
          type="button"
          :class="[
            'px-3.5 py-2 rounded-lg text-xs sm:text-sm font-medium whitespace-nowrap transition-colors border',
            transmissionFilter === opt.value
              ? 'bg-slate-900 text-white border-slate-900 shadow-sm'
              : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100 hover:text-slate-900'
          ]"
        >
          {{ opt.label }}
        </button>
      </div>
    </div>

    <!-- Active Filter Indicator / Reset -->
    <div
      v-if="searchQuery || transmissionFilter !== 'all'"
      class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500"
    >
      <span>Menampilkan hasil filter</span>
      <button
        @click="emit('reset')"
        class="text-blue-600 hover:text-blue-800 font-medium hover:underline"
        type="button"
      >
        Reset Filter
      </button>
    </div>
  </div>
</template>
