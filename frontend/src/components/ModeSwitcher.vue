<script setup lang="ts">
import { useAppModeStore } from '@/stores/appMode'
import { useRouter } from 'vue-router'
import { TruckIcon, BriefcaseIcon } from '@heroicons/vue/24/outline'

const modeStore = useAppModeStore()
const router = useRouter()

function switchMode(mode: 'general' | 'rental') {
  modeStore.setMode(mode)
  router.push(mode === 'rental' ? '/rental' : '/')
}
</script>

<template>
  <div class="px-3 pb-3">
    <p class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider px-1 mb-2">Mode Aplikasi</p>
    <div class="flex gap-1 p-1 bg-slate-100 dark:bg-slate-700/60 rounded-xl">
      <button
        @click="switchMode('rental')"
        :class="[
          'flex-1 flex items-center justify-center gap-1.5 py-2 px-2 rounded-lg text-xs font-semibold transition-all duration-200',
          modeStore.mode === 'rental'
            ? 'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 shadow-sm'
            : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'
        ]"
      >
        <TruckIcon class="w-3.5 h-3.5 shrink-0" />
        <span>Rental Mobil</span>
      </button>
      <button
        @click="switchMode('general')"
        :class="[
          'flex-1 flex items-center justify-center gap-1.5 py-2 px-2 rounded-lg text-xs font-semibold transition-all duration-200',
          modeStore.mode === 'general'
            ? 'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 shadow-sm'
            : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'
        ]"
      >
        <BriefcaseIcon class="w-3.5 h-3.5 shrink-0" />
        <span>Keuangan Umum</span>
      </button>
    </div>
  </div>
</template>
