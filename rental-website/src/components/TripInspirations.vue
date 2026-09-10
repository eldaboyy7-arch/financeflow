<script setup lang="ts">
import { computed } from 'vue'
import { RouterLink } from 'vue-router'
import { siteConfig } from '@/config/site'
import { destinationsList } from '@/config/destinations'

// 3 Destinasi Pilihan di Homepage agar tidak terlalu penuh
const destinations = computed(() => destinationsList.filter(d => d.featured).slice(0, 3))

const getWhatsAppUrl = (text: string) => {
  const phone = siteConfig.rentalPhone.replace(/\D/g, '')
  return `https://wa.me/${phone}?text=${encodeURIComponent(text)}`
}
</script>

<template>
  <section id="inspirasi" class="py-12 sm:py-20 bg-slate-50 border-t border-slate-200 scroll-mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <!-- Desktop: judul kiri + 3 kartu kanan -->
      <!-- Mobile: judul di atas, 3 kartu horizontal swipe -->
      <div class="lg:flex lg:gap-10 xl:gap-14 items-start">

        <!-- Kolom Judul (kiri di desktop, atas di mobile) -->
        <div class="lg:w-72 xl:w-80 shrink-0 mb-6 lg:mb-0 lg:pt-1">
          <p class="text-xs font-bold uppercase tracking-widest text-blue-600 mb-2">
            Inspirasi Perjalanan
          </p>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mb-2.5">
            Destinasi Populer di Bintan
          </h2>
          <p class="text-sm text-slate-600 leading-relaxed mb-5">
            Pilihan kawasan wisata favorit yang siap kami antar dengan unit rental atau paket tour.
          </p>

          <!-- Note (desktop only) -->
          <div class="hidden lg:block text-xs text-slate-500 space-y-2">
            <p class="flex items-start gap-2">
              <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span>Punya rencana rute custom keliling Bintan? Konsultasikan langsung bersama tim kami.</span>
            </p>
            <div class="flex gap-3 pt-1">
              <RouterLink to="/armada" class="font-semibold text-blue-600 hover:underline text-xs">Lihat Semua Armada →</RouterLink>
              <RouterLink to="/paket-tour-bintan" class="font-semibold text-blue-600 hover:underline text-xs">Paket HiAce →</RouterLink>
            </div>
          </div>
        </div>

        <!-- Kolom Kartu (3 Destinasi) -->
        <div class="flex-1 min-w-0">
          <!-- Mobile: horizontal swipeable snap | Desktop: 3-col grid -->
          <div class="flex gap-3.5 overflow-x-auto pb-2 snap-x snap-mandatory no-scrollbar
                      lg:grid lg:grid-cols-3 lg:gap-4 lg:overflow-visible lg:pb-0">
            <div
              v-for="dest in destinations"
              :key="dest.id"
              class="snap-start shrink-0 w-[78vw] sm:w-[280px] lg:w-auto
                     bg-white rounded-2xl border border-slate-200 overflow-hidden flex flex-col
                     hover:border-blue-300 hover:shadow-md transition-all group"
            >
              <!-- Image -->
              <div class="relative aspect-[4/3] overflow-hidden bg-slate-900">
                <img
                  v-if="dest.image"
                  :src="dest.image"
                  :alt="dest.name"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                  loading="lazy"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-slate-950/10 pointer-events-none"></div>
                
                <!-- Badge -->
                <div class="absolute top-2 left-2 z-10">
                  <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-900/80 text-slate-100 border border-white/20 backdrop-blur-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                    <span class="truncate max-w-[130px]">{{ dest.badge }}</span>
                  </span>
                </div>

                <!-- Credit -->
                <div class="absolute top-2 right-2 z-10">
                  <span
                    :title="'Sumber foto: ' + dest.photoCreditFull"
                    class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[8px] font-medium bg-slate-950/65 text-slate-200/90 backdrop-blur-sm border border-white/15 cursor-help"
                  >
                    <svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="truncate max-w-[80px]">{{ dest.photoCredit }}</span>
                  </span>
                </div>

                <!-- Title over image -->
                <div class="absolute bottom-2.5 left-3 right-3 z-10">
                  <h3 class="text-sm font-bold text-white leading-tight drop-shadow-md group-hover:text-blue-200 transition-colors line-clamp-1">
                    {{ dest.name }}
                  </h3>
                </div>
              </div>

              <!-- Body -->
              <div class="p-3.5 flex-1 flex flex-col justify-between">
                <p class="text-xs text-slate-600 leading-relaxed mb-3 line-clamp-3">
                  {{ dest.description }}
                </p>
                <div class="pt-2 border-t border-slate-100">
                  <a
                    :href="getWhatsAppUrl(dest.waText)"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors"
                  >
                    <span>Tanya Rute Wisata</span>
                    <span aria-hidden="true">&rarr;</span>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Mobile Swipe Hint (Hidden on Desktop) -->
          <div class="flex lg:hidden items-center justify-between text-[11px] text-slate-500 mt-2.5 px-1">
            <span class="inline-flex items-center gap-1">
              <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
              </svg>
              Geser untuk destinasi lainnya
            </span>
            <RouterLink to="/destinasi" class="font-bold text-blue-600">
              Lihat Semua (15) &rarr;
            </RouterLink>
          </div>

          <!-- Banner link to dedicated /destinasi page (Clean & Compact) -->
          <div class="mt-4 sm:mt-6 flex flex-col sm:flex-row items-center justify-between gap-3 p-3.5 sm:p-4 rounded-xl bg-white border border-slate-200">
            <div class="text-left w-full sm:w-auto">
              <div class="text-xs font-bold text-slate-800">Cari Referensi Wisata Lainnya?</div>
              <div class="text-[11px] text-slate-500 mt-0.5">Tersedia panduan Safari Lagoi, Gunung Bintan, Sleeping Buddha, Trikora, dll (15 Destinasi).</div>
            </div>
            <RouterLink
              to="/destinasi"
              class="w-full sm:w-auto px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold inline-flex items-center justify-center gap-1.5 shadow-xs transition-colors shrink-0"
            >
              <span>Buka Katalog Wisata (15 Tempat)</span>
              <span aria-hidden="true">&rarr;</span>
            </RouterLink>
          </div>

          <!-- Attribution -->
          <p class="text-[10px] text-slate-400 mt-3">
            * Data dan foto destinasi bersumber resmi dari Dinas Kebudayaan &amp; Pariwisata Kab. Bintan (bintantourism.com), Kemenparekraf RI, Dinas Pariwisata Kepri, dan Wikimedia Commons CC BY-SA.
          </p>
        </div>

      </div>
    </div>
  </section>
</template>
