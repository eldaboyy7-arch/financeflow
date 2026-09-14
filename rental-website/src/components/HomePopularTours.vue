<script setup lang="ts">
import { onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useTourPackages } from '@/composables/useTourPackages'

const { packages: tourPackages, fetchTourPackages } = useTourPackages()

onMounted(() => {
  fetchTourPackages()
})

const tourScenicPhotos: Record<string, string> = {
  '1': '/images/destinations/lagoi.jpg',
  '2': '/images/destinations/trikora.jpg',
  '3': '/images/destinations/treasure-bay.jpg',
  'hiace-commuter': '/images/destinations/lagoi.jpg',
  'hiace-premio': '/images/destinations/trikora.jpg',
  'custom-charter': '/images/destinations/treasure-bay.jpg',
}

const tourBadges: Record<string, string> = {
  '1': 'Paket 1 Hari',
  '2': 'Paket 2 Hari 1 Malam',
  '3': 'Paket Kustom',
  'hiace-commuter': 'Paket 1 Hari',
  'hiace-premio': 'Paket 2 Hari 1 Malam',
  'custom-charter': 'Paket Kustom',
}
</script>

<template>
  <section class="py-14 sm:py-20 bg-white border-t border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Section Header -->
      <div class="flex items-end justify-between gap-3 mb-6 sm:mb-10">
        <div>
          <p class="text-[10px] sm:text-xs font-black uppercase tracking-widest text-amber-500 mb-1.5 sm:mb-2">
            PAKET TOUR
          </p>
          <h2 class="text-xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">
            Paket Tour Populer
          </h2>
          <p class="hidden sm:block text-sm text-slate-500 mt-1 max-w-xl">
            Pilih paket tour terbaik untuk menjelajahi keindahan Bintan bersama kami.
          </p>
        </div>

        <RouterLink
          to="/paket-tour-bintan"
          class="inline-flex items-center gap-1 text-xs sm:text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors shrink-0 group py-1"
        >
          <span class="sm:hidden">Lihat Semua</span>
          <span class="hidden sm:inline">Lihat Semua Paket Tour</span>
          <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
          </svg>
        </RouterLink>
      </div>

      <!-- 3 Scenic Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-5 sm:gap-6">
        <RouterLink
          v-for="(pkg, idx) in tourPackages.slice(0, 3)"
          :key="pkg.id || idx"
          to="/paket-tour-bintan"
          class="group relative rounded-2xl sm:rounded-3xl overflow-hidden aspect-[16/11] sm:aspect-[16/10] bg-slate-900 shadow-md hover:shadow-xl transition-all duration-300 block"
        >
          <!-- Scenic Background Photo -->
          <img
            :src="tourScenicPhotos[pkg.id] || tourScenicPhotos[pkg.slug] || pkg.vehiclePhoto || '/images/destinations/lagoi.jpg'"
            :alt="pkg.title"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-85 group-hover:opacity-95"
            loading="lazy"
          />

          <!-- Gradient Overlay -->
          <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>

          <!-- Top Badge -->
          <div class="absolute top-3.5 left-3.5 z-10">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-white/90 text-slate-900 shadow-sm backdrop-blur-xs">
              {{ tourBadges[pkg.id] || tourBadges[pkg.slug] || pkg.badge || pkg.duration || 'Wisata Bintan' }}
            </span>
          </div>

          <!-- Bottom Content (Title & Price Tag) -->
          <div class="absolute bottom-0 inset-x-0 p-4 sm:p-5 z-10">
            <h3 class="font-display text-base sm:text-lg font-bold text-white leading-snug group-hover:text-amber-300 transition-colors line-clamp-1 mb-1">
              {{ pkg.title }}
            </h3>
            <div class="flex items-center justify-between">
              <span class="text-xs text-slate-300 font-medium">
                {{ pkg.price && pkg.price !== '0' && pkg.price !== 'Rp 0' ? `Mulai dari ${pkg.price}` : 'Konsultasi Gratis' }}
              </span>
              <span class="text-xs font-bold text-amber-400 inline-flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                Detail
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
              </span>
            </div>
          </div>
        </RouterLink>
      </div>

    </div>
  </section>
</template>
