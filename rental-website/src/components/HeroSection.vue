<script setup lang="ts">
import { RouterLink } from 'vue-router'
import type { PublicVehicle } from '@/types/fleet'

defineProps<{
  featuredVehicle?: PublicVehicle | null
}>()
</script>

<template>
  <!-- Hero: Split Layout — Teks Kiri + Foto Kanan Full Height -->
  <section class="bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- lg: baris dengan tinggi tetap agar kolom kanan bisa self-stretch -->
      <div class="flex flex-col lg:flex-row lg:min-h-[500px] xl:min-h-[540px]">

        <!-- Kolom Kiri: Teks & CTAs -->
        <div class="w-full lg:w-[48%] flex flex-col justify-center text-left
                    pt-10 pb-4 sm:pt-12 sm:pb-6 lg:py-14 lg:pr-10 xl:pr-14 min-w-0">
          <p class="text-[10px] sm:text-xs font-bold uppercase tracking-widest text-blue-600 mb-3">
            RENTAL MOBIL &amp; TOUR BINTAN
          </p>
          <h1 class="font-display text-[1.55rem] leading-tight sm:text-4xl lg:text-5xl xl:text-[3.25rem] font-black text-slate-900 tracking-tight lg:leading-[1.08] mb-4">
            Jelajahi Bintan<br />dengan Nyaman
          </h1>
          <p class="text-sm sm:text-base text-slate-600 leading-relaxed mb-6 max-w-sm lg:max-w-none">
            Rental mobil dan paket perjalanan di Bintan untuk kebutuhan pribadi, keluarga, maupun rombongan.
          </p>
          <div class="flex flex-col sm:flex-row gap-3">
            <a
              href="#armada"
              class="inline-flex items-center justify-center gap-2 px-5 py-3.5 sm:py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm shadow-md transition-all active:scale-95"
            >
              Lihat Pilihan Armada
              <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
              </svg>
            </a>
            <RouterLink
              to="/paket-tour-bintan"
              class="inline-flex items-center justify-center gap-2 px-5 py-3.5 sm:py-3 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-900 font-bold text-sm shadow-sm transition-all active:scale-95"
            >
              Paket Tour HiAce
              <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
              </svg>
            </RouterLink>
          </div>
        </div>

        <!-- Kolom Kanan Desktop: Foto Armada Full-Height (self-stretch + absolute) -->
        <!-- Desktop: div ini stretch ke penuh tinggi flex parent, foto mengisi 100% -->
        <div class="hidden lg:block lg:w-[52%] relative self-stretch min-h-[360px]">
          <img
            src="/images/hero-desktop.png"
            alt="Armada Rental Mobil & Tour Bintan - 3 Putri Mulya"
            class="absolute inset-0 w-full h-full object-cover object-center"
            loading="eager"
          />
        </div>

        <!-- Mobile: Foto kecil di bawah teks (block hanya di < lg) -->
        <div class="lg:hidden w-full pb-4">
          <img
            src="/images/hero-mobile.png"
            alt="Armada Rental Mobil & Tour Bintan - 3 Putri Mulya"
            class="w-full max-h-[180px] object-contain object-center"
            loading="eager"
          />
        </div>

      </div>
    </div>
  </section>
</template>
