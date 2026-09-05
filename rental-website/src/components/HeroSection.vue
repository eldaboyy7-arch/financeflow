<script setup lang="ts">
import type { PublicVehicle } from '@/types/fleet'

defineProps<{
  featuredVehicle?: PublicVehicle | null
}>()
</script>

<template>
  <section class="relative bg-slate-950 text-white overflow-hidden border-b border-slate-800">
    <!-- Full-Width Edge-to-Edge Hero Artwork (Desktop & Mobile Responsive) -->
    <div class="relative w-full overflow-hidden bg-slate-950">
      <picture>
        <!-- Mobile portrait visual (< 640px) -->
        <source media="(max-width: 639px)" srcset="/images/hero-mobile.jpg" />
        <!-- Desktop landscape visual (>= 640px) -->
        <source media="(min-width: 640px)" srcset="/images/hero-desktop.jpg" />
        <img
          src="/images/hero-desktop.jpg"
          alt="Rental Mobil &amp; Tour Bintan - 3 Putri Mulya"
          class="w-full h-auto object-cover object-top min-h-[480px] sm:min-h-[560px] lg:min-h-[640px] max-h-[88vh] mx-auto block"
          loading="eager"
        />
      </picture>

      <!-- Top Subtle Dark Gradient for seamless transparent Navbar overlay -->
      <div class="absolute inset-x-0 top-0 h-28 sm:h-36 bg-gradient-to-b from-slate-950/85 via-slate-950/40 to-transparent pointer-events-none z-10"></div>

      <!-- Bottom Gradient Mask for smooth blending into the dock -->
      <div class="absolute inset-x-0 bottom-0 h-32 sm:h-48 bg-gradient-to-t from-slate-950 via-slate-950/80 to-transparent pointer-events-none"></div>
    </div>

    <!-- Integrated Modern Action Dock (Full 1 Section) -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12 sm:-mt-20 pb-10 sm:pb-14">
      <div class="bg-slate-900/90 backdrop-blur-md rounded-2xl sm:rounded-3xl border border-white/15 p-6 sm:p-8 shadow-2xl">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
          
          <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-semibold mb-3 border border-blue-400/30 backdrop-blur-xs">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
              <span>Armada Resmi Terawat &amp; Siap Jalan</span>
            </div>

            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white tracking-tight leading-tight">
              Jelajahi Bintan dengan Nyaman
            </h1>

            <p class="text-sm sm:text-base text-slate-300 mt-2 leading-relaxed">
              Rental mobil dan paket perjalanan di Bintan untuk kebutuhan pribadi, keluarga, maupun rombongan.
            </p>

            <div class="flex flex-wrap items-center gap-y-1.5 gap-x-5 text-xs text-slate-400 mt-4 pt-3 border-t border-white/10">
              <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Tarif Harian Transparan
              </span>
              <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Unit Siap Jalan
              </span>
              <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Include Supir &amp; BBM (HiAce)
              </span>
            </div>
          </div>

          <div class="flex items-center shrink-0">
            <a
              href="#armada"
              class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-7 py-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm sm:text-base shadow-xl hover:shadow-blue-600/40 transition-all text-center"
            >
              <span>Lihat Pilihan Armada</span>
              <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
              </svg>
            </a>
          </div>

        </div>
      </div>
    </div>
  </section>
</template>
