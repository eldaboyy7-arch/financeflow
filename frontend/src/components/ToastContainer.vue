<script setup lang="ts">
import { useUiStore } from '@/stores/ui'
import {
  CheckCircleIcon,
  ExclamationCircleIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon,
  XMarkIcon,
} from '@heroicons/vue/24/outline'

const uiStore = useUiStore()
</script>

<template>
  <Teleport to="body">
    <div
      class="fixed top-5 right-4 sm:right-6 z-[9999] flex flex-col gap-2.5 max-w-sm w-full pointer-events-none select-none"
      aria-live="assertive"
    >
      <TransitionGroup name="toast">
        <div
          v-for="toast in uiStore.toasts"
          :key="toast.id"
          class="pointer-events-auto flex items-start gap-3 p-3.5 rounded-2xl bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border shadow-xl shadow-slate-900/5 transition-all"
          :class="[
            toast.type === 'success'
              ? 'border-emerald-200/80 dark:border-emerald-800/50 shadow-emerald-500/10'
              : toast.type === 'error'
              ? 'border-rose-200/80 dark:border-rose-800/50 shadow-rose-500/10'
              : toast.type === 'warning'
              ? 'border-amber-200/80 dark:border-amber-800/50 shadow-amber-500/10'
              : 'border-blue-200/80 dark:border-blue-800/50 shadow-blue-500/10',
          ]"
        >
          <!-- SVG Icon by Type -->
          <div
            class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 mt-0.5"
            :class="[
              toast.type === 'success'
                ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400'
                : toast.type === 'error'
                ? 'bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400'
                : toast.type === 'warning'
                ? 'bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400'
                : 'bg-blue-50 dark:bg-blue-950/60 text-[#0066FF] dark:text-blue-400',
            ]"
          >
            <CheckCircleIcon v-if="toast.type === 'success'" class="w-5 h-5 stroke-[2]" />
            <ExclamationCircleIcon v-else-if="toast.type === 'error'" class="w-5 h-5 stroke-[2]" />
            <ExclamationTriangleIcon v-else-if="toast.type === 'warning'" class="w-5 h-5 stroke-[2]" />
            <InformationCircleIcon v-else class="w-5 h-5 stroke-[2]" />
          </div>

          <!-- Message -->
          <div class="flex-1 min-w-0 pt-0.5">
            <p class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-100 leading-snug">
              {{ toast.message }}
            </p>
          </div>

          <!-- Close button -->
          <button
            @click="uiStore.removeToast(toast.id)"
            class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shrink-0"
            title="Tutup"
          >
            <XMarkIcon class="w-4 h-4" />
          </button>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(30px) scale(0.95);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(30px) scale(0.9);
}
</style>
