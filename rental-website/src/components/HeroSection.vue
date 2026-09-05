<script setup lang="ts">
import type { PublicVehicle } from '@/types/fleet'

defineProps<{
  featuredVehicle?: PublicVehicle | null
}>()
</script>

<template>
  <section class="relative bg-slate-950 text-white overflow-hidden border-b border-slate-800">
    <!-- 1. Full-Bleed Edge-to-Edge Hero (Starts at y=0, Zero Top Padding, Clean HD Visual) -->
    <div class="relative w-full overflow-hidden">
      <picture class="w-full">
        <!-- Mobile portrait visual (< 640px) -->
        <source media="(max-width: 639px)" srcset="/images/hero-mobile.png" />
        <!-- Desktop landscape visual (>= 640px) -->
        <source media="(min-width: 640px)" srcset="/images/hero-desktop.png" />
        <img
          src="/images/hero-desktop.png"
          alt="Rental Mobil &amp; Tour Bintan - 3 Putri Mulya"
          class="w-full h-auto block"
          loading="eager"
        />
      </picture>

      <!-- 2. Clean Typography & CTA in the Open Sky (Zero Overlap on Cars) -->
      <div class="absolute inset-x-0 top-20 sm:top-24 md:top-28 lg:top-32 z-20 text-center px-4 max-w-4xl mx-auto pointer-events-auto">
        <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black text-slate-950 tracking-tight leading-tight drop-shadow-xs">
          Jelajahi Bintan dengan Nyaman
        </h1>

        <p class="text-sm sm:text-base md:text-lg text-slate-800 font-semibold mt-2.5 sm:mt-3 max-w-2xl mx-auto leading-relaxed drop-shadow-xs">
          Rental mobil dan paket perjalanan di Bintan untuk kebutuhan pribadi, keluarga, maupun rombongan.
        </p>

        <div class="mt-3.5 sm:mt-5 flex flex-wrap items-center justify-center gap-3">
          <a
            href="#cara-perjalanan"
            class="inline-flex items-center gap-2 px-6 py-2.5 sm:px-7 sm:py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm shadow-xl hover:shadow-blue-600/40 transition-all active:scale-95 text-center"
          >
            <span>Lihat Pilihan Armada</span>
            <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
            </svg>
          </a>
        </div>
      </div>
    </div>
  </section>
</template>
