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
  <!-- Sidebar-friendly vertical filter (desktop) + horizontal (mobile) -->
  <div class="space-y-3">
    <!-- Search Input -->
    <div class="relative">
      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
      </div>
      <input
        type="text"
        :value="searchQuery"
        @input="emit('update:searchQuery', ($event.target as HTMLInputElement).value)"
        placeholder="Cari mobil..."
        class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
      />
      <button
        v-if="searchQuery"
        @click="emit('update:searchQuery', '')"
        class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600"
        type="button"
      >
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <!-- Transmission Filter — vertical on desktop (inside sidebar), horizontal scroll on mobile -->
    <div class="flex lg:flex-col gap-1.5 overflow-x-auto lg:overflow-visible pb-0.5 lg:pb-0">
      <span class="hidden lg:block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Transmisi</span>
      <button
        v-for="opt in transmissionOptions"
        :key="opt.value"
        @click="emit('update:transmissionFilter', opt.value)"
        type="button"
        :class="[
          'px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap transition-colors border shrink-0',
          transmissionFilter === opt.value
            ? 'bg-slate-900 text-white border-slate-900'
            : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100 hover:text-slate-900'
        ]"
      >
        {{ opt.label }}
      </button>
    </div>

    <!-- Reset filter (jika aktif) -->
    <div
      v-if="searchQuery || transmissionFilter !== 'all'"
      class="flex items-center justify-between text-xs text-slate-400 pt-1"
    >
      <span>Filter aktif</span>
      <button
        @click="emit('reset')"
        class="text-blue-600 hover:text-blue-800 font-medium"
        type="button"
      >
        Reset
      </button>
    </div>
  </div>
</template>
