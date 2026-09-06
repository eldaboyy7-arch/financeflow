<script setup lang="ts">
import { ref, computed } from 'vue'
import { RouterLink } from 'vue-router'
import { tourPackages, type TourPackage } from '@/config/tourPackages'
import { siteConfig } from '@/config/site'

const packages = computed(() => tourPackages)

// Active gallery photo per package card
const activePhotos = ref<Record<string, number>>({})

const getActivePhoto = (pkg: TourPackage) => {
  if (!pkg.galleryPhotos || pkg.galleryPhotos.length === 0) return pkg.vehiclePhoto
  const idx = activePhotos.value[pkg.id] ?? 0
  return pkg.galleryPhotos[idx] || pkg.vehiclePhoto
}

const setActivePhoto = (pkgId: string, idx: number) => {
  activePhotos.value[pkgId] = idx
}

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
            <span>Khusus Armada Toyota HiAce 15 Kursi</span>
          </div>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
            Paket Tour &amp; Wisata Bintan
          </h2>
          <p class="text-sm sm:text-base text-slate-600 mt-1 max-w-2xl">
            Jelajahi keindahan Pulau Bintan bersama rombongan keluarga &amp; rekan kerja. Mobil berfasilitas <strong>Karaoke System</strong>, sudah <strong>Include Supir &amp; BBM</strong>.
          </p>
        </div>

        <RouterLink
          to="/paket-tour-bintan"
          class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-white border border-slate-300 hover:border-slate-400 hover:bg-slate-100 text-slate-800 text-xs sm:text-sm font-semibold transition-colors shadow-xs shrink-0 self-start md:self-auto"
        >
          <span>Lihat Rincian Itinerary Lengkap</span>
          <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </RouterLink>
      </div>

      <!-- Packages Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div
          v-for="pkg in packages"
          :key="pkg.id"
          class="bg-white rounded-2xl border border-slate-200 overflow-hidden flex flex-col hover:border-slate-300 hover:shadow-md transition-all group"
        >
          <!-- Vehicle Visual / Header Thumbnail -->
          <div class="relative aspect-[16/10] bg-slate-100 overflow-hidden">
            <img
              :src="getActivePhoto(pkg)"
              :alt="pkg.title"
              loading="lazy"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            />

            <!-- Badge Category -->
            <div v-if="pkg.badge" class="absolute top-3 left-3 z-10">
              <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-900/85 text-white backdrop-blur-xs shadow-xs">
                {{ pkg.badge }}
              </span>
            </div>

            <!-- Capacity & Include Pill -->
            <div class="absolute bottom-3 left-3 z-10">
              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-600 text-white shadow-sm">
                Include Supir &amp; BBM &bull; 15 Person
              </span>
            </div>

            <!-- Gallery switcher thumbnails if multiple photos exist -->
            <div
              v-if="pkg.galleryPhotos && pkg.galleryPhotos.length > 1"
              class="absolute bottom-3 right-3 z-10 flex items-center gap-1 bg-slate-900/80 backdrop-blur-xs p-1 rounded-lg border border-slate-700/80 shadow-md"
            >
              <button
                v-for="(photo, pIdx) in pkg.galleryPhotos"
                :key="pIdx"
                @click.stop="setActivePhoto(pkg.id, pIdx)"
                type="button"
                :class="(activePhotos[pkg.id] ?? 0) === pIdx ? 'ring-2 ring-blue-500 scale-110' : 'opacity-70 hover:opacity-100'"
                class="w-5 h-5 rounded overflow-hidden transition-all shrink-0"
                :title="'Foto ' + (pIdx + 1)"
              >
                <img :src="photo" class="w-full h-full object-cover" />
              </button>
            </div>
          </div>

          <!-- Card Content Body -->
          <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
            <div>
              <!-- Subtitle & Title -->
              <span class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider block">
                {{ pkg.subtitle }}
              </span>
              <h3 class="text-lg font-bold text-slate-900 leading-snug group-hover:text-blue-600 transition-colors mt-0.5">
                {{ pkg.title }}
              </h3>

              <!-- Price Box (1-to-1 matching client reference) -->
              <div class="mt-3.5 p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                <div>
                  <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">
                    {{ pkg.priceLabel }}
                  </span>
                  <div class="text-xl font-extrabold text-blue-600 leading-tight">
                    {{ pkg.price }}
                  </div>
                </div>
                <div class="text-right">
                  <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                    <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                    </svg>
                    <span>Karaoke System</span>
                  </span>
                </div>
              </div>

              <!-- Rute Tur & Facilities Highlights -->
              <div class="mt-3.5 space-y-2 text-xs">
                <div class="bg-blue-50/50 p-2.5 rounded-lg border border-blue-100/60">
                  <span class="text-[11px] font-bold text-blue-900 block mb-0.5">Rute Tur Populer:</span>
                  <p class="text-[11px] text-slate-700 leading-relaxed font-medium">
                    {{ pkg.tourRoute }}
                  </p>
                </div>

                <div class="text-slate-600 text-[11px] flex items-center gap-1.5 pt-1">
                  <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  <span>{{ pkg.facilities[0] }}</span>
                </div>
              </div>
            </div>

            <!-- Card Footer: WhatsApp Action & Detail Link -->
            <div class="pt-3 border-t border-slate-100 flex items-center gap-2">
              <a
                :href="getWhatsAppUrl(pkg.ctaWhatsappText)"
                target="_blank"
                rel="noopener noreferrer"
                class="flex-1 inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors shadow-xs"
              >
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                </svg>
                Booking via WhatsApp
              </a>

              <RouterLink
                to="/paket-tour-bintan"
                class="px-3 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-medium transition-colors"
                title="Lihat detail rute & fasilitas"
              >
                Detail
              </RouterLink>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</template>
