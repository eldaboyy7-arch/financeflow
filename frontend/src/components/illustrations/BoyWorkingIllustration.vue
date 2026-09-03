<script setup lang="ts">
// BoyWorkingIllustration.vue - Using authentic asset from user sprite sheet + live floating fintech cards
withDefaults(
  defineProps<{
    size?: 'sm' | 'md' | 'lg'
  }>(),
  {
    size: 'lg',
  }
)
</script>

<template>
  <div
    :class="[
      'relative flex items-center justify-center select-none mx-auto transition-all',
      size === 'sm' ? 'max-w-[260px]' : 'max-w-[460px] w-full',
    ]"
  >
    <!-- Ambient Blue Cloud Aura -->
    <div class="absolute inset-0 m-auto w-3/4 h-3/4 bg-blue-100/60 dark:bg-blue-950/30 rounded-full blur-2xl pointer-events-none"></div>

    <!-- ================= 1. FLOATING CARD LEFT: TOTAL SALDO ================= -->
    <div
      v-if="size !== 'sm'"
      class="absolute -top-3 -left-4 sm:-left-6 z-20 bg-white dark:bg-slate-800 rounded-2xl p-3 sm:p-3.5 shadow-xl shadow-blue-500/10 border border-slate-100 dark:border-slate-700 animate-float-slow"
    >
      <p class="text-[10px] sm:text-[11px] font-semibold text-slate-400 dark:text-slate-400">Total Saldo</p>
      <p class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white mt-0.5">Rp 24.540.000</p>
      <!-- Sparkline -->
      <div class="w-24 sm:w-28 h-5 mt-1">
        <svg viewBox="0 0 100 20" fill="none" class="w-full h-full">
          <path d="M0 16 Q 20 18, 35 10 T 65 8 T 85 2 T 100 6" fill="none" stroke="#10B981" stroke-width="2.5" stroke-linecap="round" />
          <path d="M0 16 Q 20 18, 35 10 T 65 8 T 85 2 T 100 6 L 100 20 L 0 20 Z" fill="#10B981" fill-opacity="0.15" />
        </svg>
      </div>
    </div>

    <!-- ================= 2. FLOATING CARD RIGHT: PENGELUARAN ================= -->
    <div
      v-if="size !== 'sm'"
      class="absolute top-12 -right-4 sm:-right-6 z-20 bg-white dark:bg-slate-800 rounded-2xl p-3 sm:p-3.5 shadow-xl shadow-blue-500/10 border border-slate-100 dark:border-slate-700 animate-float-delay flex items-center gap-3"
    >
      <div>
        <p class="text-[10px] sm:text-[11px] font-semibold text-slate-400 dark:text-slate-400">Pengeluaran</p>
        <p class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white mt-0.5">- Rp 3.420.000</p>
        <p class="text-[9px] sm:text-[10px] font-medium text-slate-500 dark:text-slate-400">12% dari budget</p>
      </div>
      <!-- Mini Donut -->
      <div class="relative w-8 h-8 shrink-0">
        <svg viewBox="0 0 36 36" class="w-full h-full transform -rotate-90">
          <circle cx="18" cy="18" r="14" fill="none" stroke="#E2E8F0" stroke-width="4" />
          <circle cx="18" cy="18" r="14" fill="none" stroke="#0066FF" stroke-width="4" stroke-dasharray="88" stroke-dashoffset="25" stroke-linecap="round" />
          <circle cx="18" cy="18" r="14" fill="none" stroke="#38BDF8" stroke-width="4" stroke-dasharray="88" stroke-dashoffset="65" stroke-linecap="round" />
        </svg>
      </div>
    </div>

    <!-- ================= 3. AUTHENTIC BOY ON LAPTOP ASSET ================= -->
    <img
      src="/assets/auth/boy_laptop_workspace.png"
      alt="Financial Flow Workspace"
      class="relative z-10 w-full h-auto object-contain max-h-[280px] drop-shadow-md"
    />
  </div>
</template>

<style scoped>
@keyframes floatSlow {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-6px);
  }
}

@keyframes floatDelay {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-7px);
  }
}

.animate-float-slow {
  animation: floatSlow 3.5s ease-in-out infinite;
}

.animate-float-delay {
  animation: floatDelay 3.8s ease-in-out infinite 0.5s;
}
</style>
