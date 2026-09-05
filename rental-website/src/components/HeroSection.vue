<script setup lang="ts">
import { RouterLink } from 'vue-router'
import type { PublicVehicle } from '@/types/fleet'

defineProps<{
  featuredVehicle?: PublicVehicle | null
}>()
</script>

<template>
  <section class="relative bg-slate-950 text-white overflow-hidden border-b border-slate-800">
    <!-- 1. Full-Bleed Edge-to-Edge Hero Image Container -->
    <div class="relative w-full overflow-hidden">
      <picture class="w-full">
        <!-- Mobile portrait visual (< 640px) -->
        <source media="(max-width: 639px)" srcset="/images/hero-mobile.png" />
        <!-- Desktop landscape visual (>= 640px) -->
        <source media="(min-width: 640px)" srcset="/images/hero-desktop.png" />
        <img
          src="/images/hero-desktop.png"
          alt="Rental Mobil &amp; Tour Bintan - 3 Putri Mulya"
          class="w-full h-[75svh] sm:h-auto object-cover object-bottom sm:object-contain sm:object-top block"
          loading="eager"
        />
      </picture>

      <!-- 2. Clean Typography & 2 CTAs Centered in the Open Sky (Zero Overlap on Cars) -->
      <div class="absolute inset-x-0 top-[18%] sm:top-20 md:top-24 lg:top-28 z-20 text-center px-4 max-w-4xl mx-auto pointer-events-auto">
        <!-- Eyebrow Tag -->
        <div class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-white/80 backdrop-blur-md border border-slate-200/80 text-slate-800 text-[10px] sm:text-xs font-semibold tracking-wider uppercase mb-2.5 sm:mb-3 shadow-xs">
          <span>RENTAL MOBIL &amp; TOUR BINTAN</span>
        </div>

        <h1 class="font-display text-2xl sm:text-4xl md:text-5xl lg:text-6xl font-black text-slate-950 tracking-tight leading-tight">
          Jelajahi Bintan dengan Nyaman
        </h1>

        <p class="text-xs sm:text-base md:text-lg text-slate-700 font-medium mt-1.5 sm:mt-2.5 max-w-xs sm:max-w-2xl mx-auto leading-relaxed">
          Rental mobil dan paket perjalanan di Bintan untuk kebutuhan pribadi, keluarga, maupun rombongan.
        </p>

        <!-- 2 CTAs (Lihat Pilihan Armada + Paket Tour HiAce) -->
        <div class="mt-4 sm:mt-6 flex flex-wrap items-center justify-center gap-3">
          <a
            href="#armada"
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 sm:px-6 sm:py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs sm:text-sm shadow-md transition-all active:scale-95 text-center"
          >
            <span>Lihat Pilihan Armada</span>
            <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </a>

          <RouterLink
            to="/paket-tour-bintan"
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 sm:px-6 sm:py-3 rounded-xl bg-white/95 hover:bg-white text-slate-900 border border-slate-300 hover:border-slate-400 font-semibold text-xs sm:text-sm shadow-sm backdrop-blur-xs transition-all active:scale-95 text-center"
          >
            <span>Paket Tour HiAce</span>
            <span class="text-blue-600 font-bold">&rarr;</span>
          </RouterLink>
        </div>
      </div>
    </div>
  </section>
</template>
