<script setup lang="ts">
import { useAppModeStore } from '@/stores/appMode'
import { TruckIcon, BriefcaseIcon } from '@heroicons/vue/24/outline'

const modeStore = useAppModeStore()
</script>

<template>
  <div class="mx-3 mb-4 rounded-xl border border-gray-200 dark:border-gray-700/60 bg-gray-50 dark:bg-gray-800/50 p-1 flex gap-1">
    <!-- Rental Mode Button -->
    <button
      @click="modeStore.setMode('rental')"
      :class="[
        'flex-1 flex items-center justify-center gap-1.5 px-2 py-2 rounded-lg text-xs font-semibold transition-all duration-200',
        modeStore.mode === 'rental'
          ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30'
          : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'
      ]"
    >
      <TruckIcon class="h-3.5 w-3.5 shrink-0" />
      <span class="truncate">Rental Mobil</span>
    </button>

    <!-- General Mode Button -->
    <button
      @click="modeStore.setMode('general')"
      :class="[
        'flex-1 flex items-center justify-center gap-1.5 px-2 py-2 rounded-lg text-xs font-semibold transition-all duration-200',
        modeStore.mode === 'general'
          ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30'
          : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'
      ]"
    >
      <BriefcaseIcon class="h-3.5 w-3.5 shrink-0" />
      <span class="truncate">Keuangan Umum</span>
    </button>
  </div>
</template>
