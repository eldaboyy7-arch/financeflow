<script setup lang="ts">
import { RouterLink } from 'vue-router'
import type { PublicVehicle } from '@/types/fleet'

defineProps<{
  featuredVehicle?: PublicVehicle | null
}>()
</script>

<template>
  <section class="relative bg-white border-b border-slate-200 pt-8 pb-12 sm:pt-12 sm:pb-16 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
        <!-- Left Column: Customer-Oriented Copy & CTAs -->
        <div class="lg:col-span-5">
          <!-- Status Pill -->
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-medium mb-5 border border-slate-200">
            <img src="/images/logo-3pm.png" alt="3PM" class="w-4 h-4 object-contain" />
            <span class="font-semibold text-slate-900">3 Putri Mulya</span>
            <span class="text-slate-300">&bull;</span>
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            <span>Armada Terawat &amp; Bersih</span>
          </div>

          <!-- Natural, Customer-Centric Headline -->
          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-[1.15] mb-4">
            Jelajahi Bintan dengan Nyaman
          </h1>

          <!-- Clear Customer Subcopy -->
          <p class="text-base sm:text-lg text-slate-600 mb-8 leading-relaxed max-w-2xl">
            Rental mobil dan paket perjalanan di Bintan untuk kebutuhan pribadi, keluarga, maupun rombongan.
          </p>

          <!-- Action Buttons -->
          <div class="flex flex-wrap items-center gap-3.5 mb-8">
            <a
              href="#armada"
              class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-sm transition-colors"
            >
              Lihat Pilihan Armada
              <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </a>

            <RouterLink
              to="/paket-tour-bintan"
              class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-800 font-semibold text-sm shadow-sm transition-colors"
            >
              Paket Tour HiAce
              <svg class="w-4 h-4 ml-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </RouterLink>
          </div>

          <!-- Reassuring Feature Points -->
          <div class="flex flex-wrap items-center gap-y-2 gap-x-6 text-xs font-medium text-slate-500 pt-4 border-t border-slate-100">
            <div class="flex items-center gap-1.5">
              <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              <span>Tarif Harian Transparan</span>
            </div>
            <div class="flex items-center gap-1.5">
              <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              <span>Unit Siap Jalan</span>
            </div>
            <div class="flex items-center gap-1.5">
              <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              <span>Include Supir &amp; BBM (HiAce)</span>
            </div>
          </div>
        </div>

        <!-- Right Column: Official 3 Putri Mulya Fleet Hero Visual (Desktop & Mobile Responsive) -->
        <div class="lg:col-span-7">
          <div class="relative rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl border border-slate-200/90 bg-slate-900 group">
            <picture>
              <!-- Mobile portrait visual for screens < 640px -->
              <source media="(max-width: 639px)" srcset="/images/hero-mobile.jpg" />
              <!-- Desktop landscape visual for screens >= 640px -->
              <source media="(min-width: 640px)" srcset="/images/hero-desktop.jpg" />
              <img
                src="/images/hero-desktop.jpg"
                alt="Armada Resmi 3 Putri Mulya di Bintan: Agya, Avanza, Veloz, HiAce Premio"
                class="w-full h-auto object-cover group-hover:scale-[1.02] transition-transform duration-500"
                loading="eager"
              />
            </picture>

            <!-- Bottom Floating Caption -->
            <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-slate-950/85 via-slate-950/40 to-transparent p-3 sm:p-4 flex items-center justify-between text-white text-xs">
              <span class="inline-flex items-center gap-1.5 font-semibold text-white">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                Armada Resmi 3 Putri Mulya
              </span>
              <span class="text-slate-300 text-[11px] font-medium hidden sm:inline">
                Agya &bull; Avanza &bull; Veloz &bull; HiAce Premio
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
