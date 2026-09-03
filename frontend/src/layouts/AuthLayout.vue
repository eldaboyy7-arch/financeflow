<script setup lang="ts">
import BrandLogo from '@/components/BrandLogo.vue'
import BoyWorkingIllustration from '@/components/illustrations/BoyWorkingIllustration.vue'

withDefaults(
  defineProps<{
    title?: string
    subtitle?: string
    showMobileIllustration?: boolean
  }>(),
  {
    title: 'Kelola keuanganmu dengan lebih mudah',
    subtitle: 'Financial Flow membantu kamu merencanakan, melacak, dan mengembangkan keuangan pribadi & bisnis secara efisien.',
    showMobileIllustration: true,
  }
)
</script>

<template>
  <div class="min-h-screen w-full bg-[#F8FAFC] dark:bg-slate-950 flex flex-col justify-between overflow-x-hidden select-none font-sans text-slate-900 dark:text-white">
    <!-- Main Split-Screen Container -->
    <div class="flex-1 flex flex-col lg:flex-row w-full max-w-[1440px] mx-auto min-h-screen">
      
      <!-- ================= LEFT SHOWCASE PANEL (DESKTOP) ================= -->
      <div class="hidden lg:flex flex-col justify-between w-1/2 xl:w-[55%] p-10 xl:p-14 relative z-10">
        <!-- Top: Brand Logo -->
        <div class="flex items-center">
          <BrandLogo variant="full" size="md" show-tagline />
        </div>

        <!-- Center: Headline & Custom Illustration -->
        <div class="my-auto space-y-8 max-w-xl">
          <!-- Dynamic Headline & Subtitle -->
          <div class="space-y-3">
            <h1 class="text-3xl xl:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-[1.2]">
              {{ title }}
            </h1>
            <p class="text-sm xl:text-base text-slate-500 dark:text-slate-400 leading-relaxed font-normal">
              {{ subtitle }}
            </p>
          </div>

          <!-- Illustration Area (Slot or Default BoyWorkingIllustration) -->
          <div class="pt-2">
            <slot name="illustration">
              <BoyWorkingIllustration size="lg" />
            </slot>
          </div>
        </div>

        <!-- Bottom Footer (Left) -->
        <div class="flex items-center justify-between text-xs text-slate-400 dark:text-slate-500 pt-6">
          <p>© 2026 Financial Flow. Semua hak cipta dilindungi.</p>
          <div class="flex items-center gap-6">
            <a href="#" class="hover:text-slate-600 dark:hover:text-slate-300 transition-colors">Kebijakan Privasi</a>
            <a href="#" class="hover:text-slate-600 dark:hover:text-slate-300 transition-colors">Syarat & Ketentuan</a>
          </div>
        </div>
      </div>

      <!-- ================= RIGHT AUTH FORM PANEL ================= -->
      <div class="flex-1 flex flex-col items-center justify-center p-4 sm:p-6 lg:p-12 xl:p-16 relative z-20">
        <!-- Mobile Top Brand Logo (Visible only on Mobile) -->
        <div class="flex lg:hidden items-center justify-center py-6">
          <BrandLogo variant="full" size="lg" show-tagline />
        </div>

        <!-- Floating Elevated Form Card -->
        <div class="w-full max-w-[420px] bg-white dark:bg-slate-900 rounded-[28px] sm:rounded-[36px] p-6 sm:p-9 shadow-xl shadow-slate-200/60 dark:shadow-none border border-slate-100 dark:border-slate-800">
          <slot />
        </div>

        <!-- Mobile Bottom Illustration (Visible only on Mobile if enabled) -->
        <div v-if="showMobileIllustration" class="block lg:hidden w-full max-w-[280px] pt-6 pb-2">
          <slot name="mobile-illustration">
            <BoyWorkingIllustration size="sm" />
          </slot>
        </div>

        <!-- Mobile Footer Copyright -->
        <div class="block lg:hidden text-center text-[11px] text-slate-400 dark:text-slate-500 pt-4 pb-2">
          <p>© 2026 Financial Flow. Semua hak cipta dilindungi.</p>
        </div>
      </div>

    </div>
  </div>
</template>

<style scoped>
/* Clean, subtle modern scrollbar prevention */
body {
  overflow-x: hidden;
}
</style>
