<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { tourPackages, type TourPackage } from '@/config/tourPackages'
import { siteConfig } from '@/config/site'

// Active photo index per package
const activePhotoIndexes = ref<Record<string, number>>({})

const getActivePhoto = (pkg: TourPackage) => {
  if (!pkg.galleryPhotos || pkg.galleryPhotos.length === 0) return pkg.vehiclePhoto
  const idx = activePhotoIndexes.value[pkg.id] ?? 0
  return pkg.galleryPhotos[idx] || pkg.vehiclePhoto
}

const setActivePhoto = (pkgId: string, idx: number) => {
  activePhotoIndexes.value[pkgId] = idx
}

const getWhatsAppUrl = (text: string) => {
  const phone = siteConfig.rentalPhone.replace(/\D/g, '')
  return `https://wa.me/${phone}?text=${encodeURIComponent(text)}`
}

onMounted(() => {
  document.title = 'Paket Tour Bintan HiAce (Commuter & Premio) - Include Supir & BBM | 3 Putri Mulya'
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
      <header class="max-w-3xl mb-10 sm:mb-12">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold mb-3">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <span>Khusus Armada Toyota HiAce 15 Penumpang</span>
        </div>

        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-[1.15] mb-4">
          Paket Tour Bintan — Toyota HiAce (Include Supir &amp; BBM)
        </h1>

        <p class="text-base sm:text-lg text-slate-600 leading-relaxed">
          Pilihan paket wisata terbaik di Pulau Bintan untuk rombongan keluarga, instansi, atau sahabat. Seluruh armada Toyota HiAce kami berkapasitas <strong>15 Person</strong>, dilengkapi fasilitas <strong>Karaoke System</strong>, dan tarif sudah <strong>All-In (Include Supir &amp; BBM)</strong>.
        </p>
      </header>

      <!-- Reassurance Key Pillars -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-12">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-start gap-3.5 shadow-xs">
          <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"/>
            </svg>
          </div>
          <div>
            <h4 class="text-sm font-bold text-slate-900">Karaoke System On-Board</h4>
            <p class="text-xs text-slate-500 mt-1">Perjalanan bebas bosan dengan Smart TV plafon, sound system, &amp; mic wireless.</p>
          </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-start gap-3.5 shadow-xs">
          <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
          <div>
            <h4 class="text-sm font-bold text-slate-900">Include Supir &amp; BBM</h4>
            <p class="text-xs text-slate-500 mt-1">Tarif transparan mulai Rp 1.000.000, Anda tinggal duduk santai menikmati liburan.</p>
          </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-start gap-3.5 shadow-xs">
          <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
          <div>
            <h4 class="text-sm font-bold text-slate-900">Rute Wisata Terfavorit</h4>
            <p class="text-xs text-slate-500 mt-1">Mencakup Sleeping Buddha, Gurun Pasir Danau Biru, Kawasan Lagoi, &amp; Patung Seribu.</p>
          </div>
        </div>
      </div>

      <!-- Detailed Tour Packages List -->
      <div class="space-y-10">
        <article
          v-for="pkg in tourPackages"
          :key="pkg.id"
          :id="pkg.slug"
          class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow"
        >
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">
            
            <!-- Left/Top: Vehicle Visual & Photo Switcher -->
            <div class="lg:col-span-5 relative overflow-hidden min-h-[340px] lg:min-h-full flex flex-col justify-between bg-slate-950 group">
              <!-- Full-bleed Active Photo (Fills 100% height of column) -->
              <img
                :src="getActivePhoto(pkg)"
                :alt="pkg.title"
                class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              />

              <!-- Gradient overlay for contrast -->
              <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/20 to-slate-950/50 pointer-events-none"></div>

              <!-- Top Controls: Badge & Capacity -->
              <div class="relative z-10 p-4 sm:p-5 flex items-center justify-between gap-2">
                <span
                  v-if="pkg.badge"
                  class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-blue-600 text-white shadow-md"
                >
                  {{ pkg.badge }}
                </span>

                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-900/80 text-white backdrop-blur-xs border border-white/10 shadow-sm">
                  👥 {{ pkg.capacity }}
                </span>
              </div>

              <!-- Bottom Controls: Interactive Thumbnail Gallery Switcher -->
              <div class="relative z-10 p-4 sm:p-5 space-y-2">
                <div class="flex items-center justify-between text-white text-xs font-medium">
                  <span class="flex items-center gap-1 text-[11px] text-slate-200">
                    <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Galeri Unit &amp; Interior:</span>
                  </span>
                  <span class="text-[10px] text-slate-300">Klik untuk ganti foto</span>
                </div>

                <div
                  v-if="pkg.galleryPhotos && pkg.galleryPhotos.length > 1"
                  class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none"
                >
                  <button
                    v-for="(img, idx) in pkg.galleryPhotos"
                    :key="idx"
                    @click="setActivePhoto(pkg.id, idx)"
                    type="button"
                    :class="(activePhotoIndexes[pkg.id] ?? 0) === idx ? 'ring-2 ring-blue-500 scale-105 shadow-md' : 'opacity-70 hover:opacity-100'"
                    class="w-16 h-12 rounded-xl overflow-hidden shrink-0 border border-white/20 transition-all bg-slate-900"
                    :title="'Lihat foto ' + (idx + 1)"
                  >
                    <img :src="img" class="w-full h-full object-cover" />
                  </button>
                </div>
              </div>
            </div>

            <!-- Right/Bottom: Package Details -->
            <div class="lg:col-span-7 p-6 sm:p-8 flex flex-col justify-between">
              <div>
                <!-- Subtitle & Title -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                  <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">
                      {{ pkg.subtitle }}
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 leading-snug mt-0.5">
                      {{ pkg.title }}
                    </h2>
                  </div>
                </div>

                <!-- Price Box matching Reference -->
                <div class="my-4 p-4 rounded-2xl bg-blue-50/70 border border-blue-100 flex items-center justify-between">
                  <div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">
                      {{ pkg.priceLabel }}
                    </span>
                    <div class="text-2xl sm:text-3xl font-black text-blue-600 leading-tight mt-0.5">
                      {{ pkg.price }}
                    </div>
                  </div>
                  <div class="text-right space-y-1">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                      Include Supir &amp; BBM
                    </span>
                    <span class="block text-[11px] text-slate-500 font-medium">Kapasitas 15 Penumpang</span>
                  </div>
                </div>

                <p class="text-sm text-slate-600 leading-relaxed mb-6">
                  {{ pkg.description }}
                </p>

                <!-- Tour Itinerary & Facilities (Matching Client Section) -->
                <div class="mb-6 space-y-4">
                  <h3 class="text-sm font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    <span>Tour Itinerary &amp; Facilities</span>
                  </h3>

                  <!-- Facilities List -->
                  <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 space-y-2">
                    <span class="text-xs font-bold text-slate-800 block">Fasilitas Kendaraan (Facility):</span>
                    <ul class="space-y-1.5 text-xs text-slate-600">
                      <li v-for="(fac, fIdx) in pkg.facilities" :key="fIdx" class="flex items-start gap-2">
                        <span class="text-blue-600 font-bold">&bull;</span>
                        <span>{{ fac }}</span>
                      </li>
                    </ul>
                  </div>

                  <!-- Rute Tur -->
                  <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100/80">
                    <span class="text-xs font-bold text-blue-900 block mb-1">Rute Tur (Tour Route):</span>
                    <p class="text-xs sm:text-sm font-semibold text-slate-800 leading-relaxed">
                      {{ pkg.tourRoute }}
                    </p>
                  </div>
                </div>

                <!-- Detailed Inclusions & Exclusions -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-slate-100 mb-6">
                  <div>
                    <h4 class="text-xs font-bold text-emerald-800 uppercase tracking-wider mb-2">Termasuk dalam Paket:</h4>
                    <ul class="space-y-1.5 text-xs text-slate-600">
                      <li v-for="(inc, iIdx) in pkg.included" :key="iIdx" class="flex items-start gap-1.5">
                        <span class="text-emerald-600 font-bold">&check;</span>
                        <span>{{ inc }}</span>
                      </li>
                    </ul>
                  </div>

                  <div>
                    <h4 class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tidak Termasuk:</h4>
                    <ul class="space-y-1.5 text-xs text-slate-500">
                      <li v-for="(exc, eIdx) in pkg.excluded" :key="eIdx" class="flex items-start gap-1.5">
                        <span class="text-slate-400">&bull;</span>
                        <span>{{ exc }}</span>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              <!-- Footer CTA -->
              <div class="pt-5 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50 -mx-6 -mb-6 sm:-mx-8 sm:-mb-8 p-6 sm:p-8 rounded-b-3xl">
                <div>
                  <span class="text-[11px] text-slate-500 block font-medium">Status Pemesanan:</span>
                  <p class="text-xs font-semibold text-slate-800">Penjemputan Pelabuhan / Bandara / Resort</p>
                </div>

                <a
                  :href="getWhatsAppUrl(pkg.ctaWhatsappText)"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm shadow-md transition-all active:scale-95 shrink-0"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                  </svg>
                  Book This Tour via WhatsApp
                </a>
              </div>
            </div>

          </div>
        </article>
      </div>

      <!-- FAQ & Consultation Box -->
      <div class="mt-14 bg-white rounded-3xl border border-slate-200 p-6 sm:p-10 text-center max-w-2xl mx-auto shadow-sm">
        <h3 class="text-lg font-bold text-slate-900 mb-2">
          Butuh Rute Khusus atau Penyesuaian Waktu?
        </h3>
        <p class="text-xs sm:text-sm text-slate-600 mb-6 leading-relaxed">
          Admin 3 Putri Mulya siap membantu mengatur jadwal penjemputan dari Pelabuhan Sri Bintan Pura, Bandara RHF, atau Resort Lagoi sesuai jam kedatangan Anda.
        </p>
        <div class="flex flex-wrap items-center justify-center gap-3">
          <a
            :href="getWhatsAppUrl('Halo 3 Putri Mulya, saya ingin konsultasi jadwal dan rute tour HiAce di Bintan.')"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition-colors"
          >
            Chat WhatsApp Admin
          </a>
          <RouterLink
            to="/"
            class="inline-flex items-center gap-1 px-5 py-3 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-xs transition-colors"
          >
            Kembali ke Beranda
          </RouterLink>
        </div>
      </div>

    </div>
  </div>
</template>
