<script setup lang="ts">
import { computed } from 'vue'
import { RouterLink } from 'vue-router'
import { tourPackages } from '@/config/tourPackages'
import { siteConfig } from '@/config/site'

const packages = computed(() => tourPackages)

const getWhatsAppUrl = (text: string) => {
  const phone = siteConfig.rentalPhone.replace(/\D/g, '')
  return `https://wa.me/${phone}?text=${encodeURIComponent(text)}`
}
</script>

<template>
  <section id="tour-bintan" class="py-12 sm:py-16 bg-slate-50 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Section Header -->
      <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 sm:mb-10 gap-4">
        <div>
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold mb-3">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Wisata &amp; Rombongan</span>
          </div>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
            Paket Tour &amp; Travel Bintan
          </h2>
          <p class="text-sm sm:text-base text-slate-600 mt-1 max-w-2xl">
            Solusi transportasi wisata keluarga, instansi dinas, dan rombongan. Siap jemput dari Pelabuhan Sri Bintan Pura, Bandara RHF, atau Resort.
          </p>
        </div>

        <RouterLink
          to="/paket-tour-bintan"
          class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-white border border-slate-300 hover:border-slate-400 hover:bg-slate-100 text-slate-800 text-xs sm:text-sm font-semibold transition-colors shadow-xs shrink-0 self-start md:self-auto"
        >
          <span>Lihat Rincian Semua Paket</span>
          <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </RouterLink>
      </div>

      <!-- Packages Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div
          v-for="pkg in packages"
          :key="pkg.id"
          class="bg-white rounded-2xl border border-slate-200 overflow-hidden flex flex-col hover:border-slate-300 hover:shadow-md transition-all group"
        >
          <!-- Vehicle Visual / Header Thumbnail -->
          <div class="relative aspect-[16/10] bg-slate-100 overflow-hidden">
            <img
              v-if="pkg.vehiclePhoto"
              :src="pkg.vehiclePhoto"
              :alt="pkg.title"
              loading="lazy"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            />
            <div
              v-else
              class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-slate-100 p-4 text-center"
            >
              <svg class="w-12 h-12 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
              </svg>
              <span class="text-xs font-medium text-slate-500">Rute Fleksibel Sesuai Request</span>
            </div>

            <!-- Badge Category -->
            <div v-if="pkg.badge" class="absolute top-3 left-3 z-10">
              <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-900/85 text-white backdrop-blur-xs shadow-xs">
                {{ pkg.badge }}
              </span>
            </div>

            <!-- Duration Pill -->
            <div class="absolute bottom-3 right-3 z-10">
              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-white/95 text-slate-800 shadow-sm border border-slate-200/80">
                <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ pkg.duration }}
              </span>
            </div>
          </div>

          <!-- Card Content Body -->
          <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
            <div>
              <h3 class="text-base sm:text-lg font-bold text-slate-900 leading-snug group-hover:text-blue-600 transition-colors">
                {{ pkg.title }}
              </h3>

              <!-- Vehicle & Capacity Badges -->
              <div class="flex flex-wrap items-center gap-2 mt-2 text-xs text-slate-600">
                <span class="inline-flex items-center gap-1 bg-slate-100 px-2 py-0.5 rounded font-medium">
                  <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 19H5a2 2 0 01-2-2V7a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2z"/>
                  </svg>
                  {{ pkg.vehicle }}
                </span>
                <span class="inline-flex items-center gap-1 bg-slate-100 px-2 py-0.5 rounded font-medium">
                  <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                  </svg>
                  {{ pkg.capacity }}
                </span>
              </div>

              <!-- Brief Description -->
              <p class="text-xs sm:text-sm text-slate-600 mt-3 leading-relaxed line-clamp-2">
                {{ pkg.description }}
              </p>
            </div>

            <!-- Card Footer: Price Note & Action Buttons -->
            <div class="pt-4 border-t border-slate-100 space-y-3">
              <div class="text-xs text-slate-500 font-medium">
                <span class="block text-[11px] text-slate-400">Status Tarif:</span>
                <span class="font-semibold text-slate-800">{{ pkg.priceNote }}</span>
              </div>

              <div class="flex items-center gap-2">
                <a
                  :href="getWhatsAppUrl(pkg.ctaWhatsappText)"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="flex-1 inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors shadow-xs"
                >
                  <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                  </svg>
                  Tanya Paket
                </a>

                <RouterLink
                  to="/paket-tour-bintan"
                  class="px-3 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-medium transition-colors"
                  title="Lihat detail itinerary dan fasilitas"
                >
                  Detail
                </RouterLink>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</template>
