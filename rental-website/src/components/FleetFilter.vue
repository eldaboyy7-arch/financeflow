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
  <div class="space-y-2.5">
    <!-- Transmission Dropdown Filter (Matching Mockup) -->
    <div class="relative">
      <select
        :value="transmissionFilter"
        @change="emit('update:transmissionFilter', ($event.target as HTMLSelectElement).value as TransmissionFilter)"
        class="w-full appearance-none px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-pointer shadow-2xs pr-9"
      >
        <option
          v-for="opt in transmissionOptions"
          :key="opt.value"
          :value="opt.value"
        >
          {{ opt.label }}
        </option>
      </select>
      <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
      </div>
    </div>

    <!-- Search Input (Matching Mockup) -->
    <div class="relative">
      <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
      </div>
      <input
        type="text"
        :value="searchQuery"
        @input="emit('update:searchQuery', ($event.target as HTMLInputElement).value)"
        placeholder="Cari mobil..."
        class="w-full pl-9 pr-8 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-2xs"
      />
      <button
        v-if="searchQuery"
        @click="emit('update:searchQuery', '')"
        class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600"
        type="button"
        title="Hapus pencarian"
      >
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <!-- Reset Link if filters applied -->
    <div
      v-if="searchQuery || transmissionFilter !== 'all'"
      class="flex items-center justify-between text-xs text-slate-500 pt-0.5 px-1"
    >
      <span class="text-[11px]">Filter aktif</span>
      <button
        @click="emit('reset')"
        class="text-[11px] text-blue-600 hover:text-blue-800 font-semibold"
        type="button"
      >
        Reset filter
      </button>
    </div>
  </div>
</template>
