<script setup lang="ts">
import { onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { tourPackages } from '@/config/tourPackages'
import { siteConfig } from '@/config/site'

const getWhatsAppUrl = (text: string) => {
  const phone = siteConfig.rentalPhone.replace(/\D/g, '')
  return `https://wa.me/${phone}?text=${encodeURIComponent(text)}`
}

onMounted(() => {
  // SEO title & meta description dinamis
  document.title = 'Paket Tour & Travel Bintan - Sewa Mobil & HiAce | 3 Putri Mulya'
  window.scrollTo({ top: 0, behavior: 'smooth' })
})
</script>

<template>
  <div class="bg-slate-50 min-h-screen py-8 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Breadcrumb Navigation -->
      <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6" aria-label="Breadcrumb">
        <RouterLink to="/" class="hover:text-blue-600 transition-colors">Beranda</RouterLink>
        <span>/</span>
        <span class="text-slate-900 font-semibold">Paket Tour Bintan</span>
      </nav>

      <!-- Page Header & Semantic H1 for SEO -->
      <header class="max-w-3xl mb-10 sm:mb-14">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold mb-3">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <span>Layanan Wisata &amp; Perjalanan Resmi</span>
        </div>

        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-[1.15] mb-4">
          Paket Tour &amp; Travel Bintan — Solusi Wisata &amp; Rombongan
        </h1>

        <p class="text-base sm:text-lg text-slate-600 leading-relaxed">
          Nikmati perjalanan menyenangkan di Pulau Bintan dengan armada resmi terawat dan pengemudi berpengalaman. Pilihan lengkap mulai dari sewa Toyota HiAce 15 kursi untuk rombongan instansi/keluarga besar hingga mobil keluarga privat. Rute dan jadwal penjemputan dapat disesuaikan sepenuhnya.
        </p>
      </header>

      <!-- Key Value Banner -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-12">
        <div class="bg-white rounded-xl border border-slate-200 p-4 flex items-start gap-3 shadow-xs">
          <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
          <div>
            <h4 class="text-sm font-bold text-slate-900">Armada Sendiri &amp; Terawat</h4>
            <p class="text-xs text-slate-500 mt-0.5">Kondisi mesin prima, kabin bersih ber-AC dingin, siap pakai perjalanan jauh.</p>
          </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 flex items-start gap-3 shadow-xs">
          <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
          </div>
          <div>
            <h4 class="text-sm font-bold text-slate-900">Driver Paham Rute Lokal</h4>
            <p class="text-xs text-slate-500 mt-0.5">Pengemudi ramah menguasai rute wisata, kuliner, dan jalanan Pulau Bintan.</p>
          </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 flex items-start gap-3 shadow-xs">
          <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
          </div>
          <div>
            <h4 class="text-sm font-bold text-slate-900">Jadwal &amp; Rute Fleksibel</h4>
            <p class="text-xs text-slate-500 mt-0.5">Penjemputan tepat waktu di Pelabuhan Sri Bintan Pura, Bandara RHF, atau Hotel.</p>
          </div>
        </div>
      </div>

      <!-- Detailed Tour Packages List -->
      <div class="space-y-8">
        <article
          v-for="pkg in tourPackages"
          :key="pkg.id"
          :id="pkg.slug"
          class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow"
        >
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">
            
            <!-- Left/Top: Vehicle Visual -->
            <div class="lg:col-span-5 relative bg-slate-100 min-h-[260px] lg:min-h-full">
              <img
                v-if="pkg.vehiclePhoto"
                :src="pkg.vehiclePhoto"
                :alt="pkg.title"
                class="w-full h-full object-cover"
              />
              <div
                v-else
                class="w-full h-full flex flex-col items-center justify-center text-slate-400 p-6 text-center min-h-[260px]"
              >
                <svg class="w-16 h-16 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
                <span class="text-sm font-medium text-slate-500">Pilihan Rute Bebas Sesuai Kebutuhan</span>
              </div>

              <!-- Badge -->
              <div v-if="pkg.badge" class="absolute top-4 left-4 z-10">
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-slate-900/90 text-white backdrop-blur-xs shadow-sm">
                  {{ pkg.badge }}
                </span>
              </div>
            </div>

            <!-- Right/Bottom: Package Details -->
            <div class="lg:col-span-7 p-6 sm:p-8 flex flex-col justify-between">
              <div>
                <!-- Title & Meta -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                  <h2 class="text-xl sm:text-2xl font-black text-slate-900 leading-snug">
                    {{ pkg.title }}
                  </h2>
                  <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 shrink-0 self-start">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ pkg.duration }}
                  </span>
                </div>

                <p class="text-sm text-slate-600 leading-relaxed mb-6">
                  {{ pkg.description }}
                </p>

                <!-- Specs Pills -->
                <div class="grid grid-cols-2 gap-3 mb-6">
                  <div class="bg-slate-50 border border-slate-100 rounded-xl p-3">
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Armada Utama</span>
                    <span class="text-xs sm:text-sm font-bold text-slate-800">{{ pkg.vehicle }}</span>
                  </div>
                  <div class="bg-slate-50 border border-slate-100 rounded-xl p-3">
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Kapasitas Peserta</span>
                    <span class="text-xs sm:text-sm font-bold text-slate-800">{{ pkg.capacity }}</span>
                  </div>
                </div>

                <!-- Itinerary Steps -->
                <div class="mb-6">
                  <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Ringkasan Alur Perjalanan (Itinerary)
                  </h3>
                  <ol class="space-y-2 text-xs sm:text-sm text-slate-600 pl-4 border-l-2 border-blue-100 ml-2">
                    <li v-for="(step, sIdx) in pkg.itinerary" :key="sIdx" class="relative pl-3">
                      <span class="absolute -left-[19px] top-1.5 w-2 h-2 rounded-full bg-blue-600 ring-4 ring-blue-50"></span>
                      <span>{{ step }}</span>
                    </li>
                  </ol>
                </div>

                <!-- Inclusions & Exclusions Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-slate-100 mb-6">
                  <!-- Included -->
                  <div>
                    <h4 class="text-xs font-bold text-emerald-800 uppercase tracking-wider mb-2 flex items-center gap-1">
                      <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                      </svg>
                      Fasilitas Termasuk:
                    </h4>
                    <ul class="space-y-1.5 text-xs text-slate-600">
                      <li v-for="(inc, iIdx) in pkg.included" :key="iIdx" class="flex items-start gap-1.5">
                        <span class="text-emerald-500 font-bold">&check;</span>
                        <span>{{ inc }}</span>
                      </li>
                    </ul>
                  </div>

                  <!-- Excluded -->
                  <div>
                    <h4 class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1">
                      <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                      </svg>
                      Belum Termasuk:
                    </h4>
                    <ul class="space-y-1.5 text-xs text-slate-500">
                      <li v-for="(exc, eIdx) in pkg.excluded" :key="eIdx" class="flex items-start gap-1.5">
                        <span class="text-slate-400">&bull;</span>
                        <span>{{ exc }}</span>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              <!-- Price Status & CTA Action -->
              <div class="pt-5 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50 -mx-6 -mb-6 sm:-mx-8 sm:-mb-8 p-6 sm:p-8 rounded-b-3xl">
                <div>
                  <span class="text-[11px] text-slate-500 block font-medium">Informasi Tarif Sewa:</span>
                  <p class="text-xs sm:text-sm font-semibold text-slate-800">{{ pkg.priceNote }}</p>
                </div>

                <a
                  :href="getWhatsAppUrl(pkg.ctaWhatsappText)"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-md transition-all active:scale-95 shrink-0"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                  </svg>
                  Konsultasi via WhatsApp
                </a>
              </div>
            </div>

          </div>
        </article>
      </div>

      <!-- Additional FAQ / Reassurance footer on tour page -->
      <div class="mt-14 bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 text-center max-w-2xl mx-auto">
        <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-2">
          Punya Rencana Perjalanan Khusus atau Rombongan Besar?
        </h3>
        <p class="text-xs sm:text-sm text-slate-600 mb-6 leading-relaxed">
          Admin 3 Putri Mulya siap membantu mengatur jadwal penjemputan dari pelabuhan, bandara, atau resort dan merekomendasikan armada yang paling efisien untuk rombongan Anda.
        </p>
        <div class="flex flex-wrap items-center justify-center gap-3">
          <a
            :href="getWhatsAppUrl('Halo 3 Putri Mulya, saya ingin konsultasi rute dan paket tour rombongan di Bintan.')"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition-colors"
          >
            Chat WhatsApp Admin
          </a>
          <RouterLink
            to="/"
            class="inline-flex items-center gap-1 px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-xs transition-colors"
          >
            Kembali ke Katalog Armada
          </RouterLink>
        </div>
      </div>

    </div>
  </div>
</template>
