<script setup lang="ts">
withDefaults(
  defineProps<{
    variant?: 'full' | 'compact' | 'icon'
    size?: 'sm' | 'md' | 'lg' | 'xl'
    showTagline?: boolean
    title?: string
  }>(),
  {
    variant: 'compact',
    size: 'md',
    showTagline: false,
    title: 'FinancialFlow',
  }
)
</script>

<template>
  <div class="inline-flex items-center gap-3 select-none">
    <!-- Icon Mark -->
    <div
      :class="[
        'flex items-center justify-center shrink-0 transition-transform duration-200',
        size === 'sm' ? 'w-7 h-7' : '',
        size === 'md' ? 'w-8 h-8' : '',
        size === 'lg' ? 'w-11 h-11' : '',
        size === 'xl' ? 'w-14 h-14' : '',
      ]"
    >
      <svg
        viewBox="0 0 100 100"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        class="w-full h-full drop-shadow-sm"
      >
        <!-- Top Wave (Blue Flow Wing) -->
        <path
          d="M 16 38 C 24 16 52 8 92 8 C 96 8 97 12 94 16 C 80 30 58 38 36 38 C 26 38 18 38 16 38 Z"
          fill="#0066FF"
        />
        <!-- Bottom Wave / Stem (Adaptive Dark / White Flow) -->
        <path
          d="M 6 92 L 22 46 C 36 46 64 46 80 46 C 86 46 87 51 82 56 C 68 68 46 72 26 72 L 15 92 H 6 Z"
          class="fill-slate-900 dark:fill-white"
        />
      </svg>
    </div>

    <!-- Typography -->
    <div v-if="variant !== 'icon'" class="flex flex-col">
      <div class="flex items-center gap-0.5 leading-none">
        <span
          :class="[
            'font-extrabold tracking-tight text-slate-900 dark:text-white',
            size === 'sm' ? 'text-sm' : '',
            size === 'md' ? 'text-base' : '',
            size === 'lg' ? 'text-xl' : '',
            size === 'xl' ? 'text-2xl' : '',
          ]"
        >
          Financial<span class="text-[#0066FF]">Flow</span>
        </span>
      </div>

      <!-- Tagline -->
      <span
        v-if="showTagline || variant === 'full'"
        class="text-[9px] font-bold uppercase tracking-[0.22em] text-slate-400 dark:text-slate-500 mt-1 flex items-center gap-1.5"
      >
        PLAN <span class="text-[#0066FF] text-[6px]">•</span> TRACK <span class="text-[#0066FF] text-[6px]">•</span> GROW
      </span>
    </div>
  </div>
</template>
