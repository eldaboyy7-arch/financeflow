<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useTourPackages } from '@/composables/useTourPackages'
import type { TourPackage } from '@/config/tourPackages'
import { siteConfig } from '@/config/site'

const { packages: tourPackages, fetchTourPackages } = useTourPackages()

// Active photo index per package
const activePhotoIndexes = ref<Record<string, number>>({})

// Collapsible detail accordion state (per package id)
const expandedDetails = ref<Record<string, boolean>>({})

const getActivePhoto = (pkg: TourPackage) => {
  if (!pkg.galleryPhotos || pkg.galleryPhotos.length === 0) return pkg.vehiclePhoto
  const idx = activePhotoIndexes.value[pkg.id] ?? 0
  return pkg.galleryPhotos[idx] || pkg.vehiclePhoto
}

const setActivePhoto = (pkgId: string, idx: number) => {
  activePhotoIndexes.value[pkgId] = idx
}

const toggleDetails = (pkgId: string) => {
  expandedDetails.value[pkgId] = !expandedDetails.value[pkgId]
}

const isExpanded = (pkgId: string) => {
  return !!expandedDetails.value[pkgId]
}

const getRouteStops = (routeStr: string): string[] => {
  return routeStr
    .split('-')
    .map(s => s.trim())
    .filter(Boolean)
}

const getWhatsAppUrl = (text: string) => {
  const phone = siteConfig.rentalPhone.replace(/\D/g, '')
  return `https://wa.me/${phone}?text=${encodeURIComponent(text)}`
}

onMounted(() => {
  document.title = 'Paket Tour Bintan HiAce (Commuter & Premio) - Include Supir & BBM | 3 Putri Mulya'
  window.scrollTo({ top: 0, behavior: 'smooth' })
  fetchTourPackages()
})
</script>

<template>
  <div class="bg-slate-50 min-h-screen py-6 sm:py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Breadcrumb Navigation -->
      <nav class="flex items-center gap-2 text-xs text-slate-500 mb-5 sm:mb-8" aria-label="Breadcrumb">
        <RouterLink to="/" class="hover:text-blue-600 transition-colors inline-flex items-center gap-1">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
          </svg>
          <span>Beranda</span>
        </RouterLink>
        <span class="text-slate-300">/</span>
        <span class="text-slate-900 font-semibold">Paket Tour Bintan</span>
      </nav>

      <!-- Page Header -->
      <header class="max-w-3xl mb-6 sm:mb-10">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 border border-blue-200/80 text-blue-700 text-xs font-semibold mb-3">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m-8 4h8m-8 4h4m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2z"/>
          </svg>
          <span>Armada Khusus HiAce (Commuter 15 Kursi &amp; Premio Luxury VIP)</span>
        </div>

        <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight leading-tight mb-3">
          Paket Tour Wisata Pulau Bintan
        </h1>

        <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
          Pilihan terbaik perjalanan wisata keliling Pulau Bintan untuk rombongan keluarga, instansi, atau sahabat. Seluruh armada Toyota HiAce berkapasitas <strong>11 hingga 15 penumpang</strong>, dilengkapi fasilitas <strong>Karaoke System</strong>, dan tarif sudah <strong>All-In (Sudah Termasuk Mobil + Supir + BBM)</strong>.
        </p>
      </header>

      <!-- Compact Value Proposition Strip -->
      <div class="bg-white rounded-xl sm:rounded-2xl border border-slate-200/90 p-3 sm:p-4 mb-8 sm:mb-12 shadow-xs">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5 sm:gap-4">
          <!-- Pillar 1 -->
          <div class="flex items-center gap-2.5 p-2 sm:p-2.5 rounded-lg bg-slate-50 border border-slate-100">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
              </svg>
            </div>
            <div class="min-w-0">
              <span class="text-xs font-bold text-slate-900 block truncate">Include Supir &amp; BBM</span>
              <span class="text-[10px] sm:text-[11px] text-slate-500 block truncate">Tarif All-In transparan</span>
            </div>
          </div>

          <!-- Pillar 2 -->
          <div class="flex items-center gap-2.5 p-2 sm:p-2.5 rounded-lg bg-slate-50 border border-slate-100">
            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"/>
              </svg>
            </div>
            <div class="min-w-0">
              <span class="text-xs font-bold text-slate-900 block truncate">Karaoke On-Board</span>
              <span class="text-[10px] sm:text-[11px] text-slate-500 block truncate">Smart TV &amp; Mic Wireless</span>
            </div>
          </div>

          <!-- Pillar 3 -->
          <div class="flex items-center gap-2.5 p-2 sm:p-2.5 rounded-lg bg-slate-50 border border-slate-100">
            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
            <div class="min-w-0">
              <span class="text-xs font-bold text-slate-900 block truncate">Kapasitas 15 Kursi</span>
              <span class="text-[10px] sm:text-[11px] text-slate-500 block truncate">Kabin luas &amp; lega</span>
            </div>
          </div>

          <!-- Pillar 4 -->
          <div class="flex items-center gap-2.5 p-2 sm:p-2.5 rounded-lg bg-slate-50 border border-slate-100">
            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
            </div>
            <div class="min-w-0">
              <span class="text-xs font-bold text-slate-900 block truncate">Jemput Fleksibel</span>
              <span class="text-[10px] sm:text-[11px] text-slate-500 block truncate">Pelabuhan / Bandara / Hotel</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Detailed Tour Packages List -->
      <div class="space-y-6 sm:space-y-8 mb-12 sm:mb-16">
        <article
          v-for="pkg in tourPackages"
          :key="pkg.id"
          :id="pkg.slug"
          class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 overflow-hidden shadow-xs hover:border-slate-300 hover:shadow-md transition-all duration-300"
        >
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">
            
            <!-- Left Side: Vehicle Visual & Dedicated Gallery Row -->
            <div class="lg:col-span-5 bg-slate-950 flex flex-col justify-between">
              <!-- Main Active Photo with controlled aspect ratio -->
              <div class="relative aspect-[16/10] sm:aspect-[4/3] lg:aspect-[16/11] bg-slate-900 overflow-hidden group">
                <img
                  :src="getActivePhoto(pkg)"
                  :alt="pkg.title"
                  loading="lazy"
                  decoding="async"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                />

                <!-- Dark gradient on top/bottom of photo for badge clarity -->
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-slate-950/40 pointer-events-none"></div>

                <!-- Top Badges: Badge Type & Capacity -->
                <div class="absolute top-2.5 left-2.5 right-2.5 z-10 flex items-center justify-between gap-2">
                  <span
                    v-if="pkg.badge"
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold shadow-xs text-white"
                    :class="pkg.id === 'tour-hiace-premio' ? 'bg-indigo-600' : (pkg.id === 'tour-hiace-custom' ? 'bg-amber-600' : 'bg-blue-600')"
                  >
                    <svg v-if="pkg.id === 'tour-hiace-premio'" class="w-3 h-3 text-amber-300" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span>{{ pkg.badge }}</span>
                  </span>

                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-900/85 text-white backdrop-blur-xs border border-white/10 shadow-xs">
                    <svg class="w-3 h-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>{{ pkg.capacity }}</span>
                  </span>
                </div>

                <!-- Bottom Photo Caption -->
                <div class="absolute bottom-2.5 left-2.5 right-2.5 z-10 flex items-center justify-between text-[11px] text-slate-300">
                  <span class="font-semibold text-white truncate drop-shadow-sm">{{ pkg.vehicle }}</span>
                  <span class="text-[10px] text-slate-400 shrink-0 ml-1">Klik thumbnail untuk ganti</span>
                </div>
              </div>

              <!-- Dedicated Thumbnail Switcher Strip -->
              <div
                v-if="pkg.galleryPhotos && pkg.galleryPhotos.length > 1"
                class="p-2 sm:p-2.5 bg-slate-950 border-t border-slate-800 flex items-center gap-2 overflow-x-auto scrollbar-none"
              >
                <button
                  v-for="(img, idx) in pkg.galleryPhotos"
                  :key="idx"
                  @click="setActivePhoto(pkg.id, idx)"
                  type="button"
                  :class="(activePhotoIndexes[pkg.id] ?? 0) === idx ? 'ring-2 ring-blue-500 scale-100 opacity-100' : 'opacity-60 hover:opacity-100'"
                  class="w-14 h-10 sm:w-16 sm:h-11 rounded-lg overflow-hidden shrink-0 border border-slate-700 bg-slate-900 transition-all"
                  :title="'Lihat foto galeri ' + (idx + 1)"
                >
                  <img :src="img" class="w-full h-full object-cover" />
                </button>
              </div>
            </div>

            <!-- Right Side: Clean Package Details & Structured Information -->
            <div class="lg:col-span-7 p-4 sm:p-6 lg:p-7 flex flex-col justify-between">
              <div>
                <!-- Subtitle & Package Title -->
                <div class="mb-3">
                  <div class="flex items-center gap-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                    <span class="text-blue-600 font-extrabold">{{ pkg.vehicle }}</span>
                    <span>&bull;</span>
                    <span>{{ pkg.duration }}</span>
                  </div>
                  <h2 class="text-xl sm:text-2xl font-black text-slate-900 leading-snug">
                    {{ pkg.title }}
                  </h2>
                </div>

                <!-- Price & Inclusion Highlight Box -->
                <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-blue-50/70 border border-blue-100/90 mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                  <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">
                      {{ pkg.priceLabel }}
                    </span>
                    <div class="flex items-baseline gap-1.5 mt-0.5">
                      <span class="text-2xl sm:text-3xl font-black text-blue-700 tracking-tight leading-none">
                        {{ pkg.price }}
                      </span>
                      <span class="text-xs text-slate-600 font-medium leading-none">/hari</span>
                    </div>
                  </div>

                  <div class="flex sm:flex-col items-center sm:items-end justify-between gap-1 text-right">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                      <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                      </svg>
                      <span>Include Supir &amp; BBM</span>
                    </span>
                    <span class="text-[11px] text-slate-500 font-medium">All-In Tanpa Biaya Tersembunyi</span>
                  </div>
                </div>

                <!-- Short Package Description -->
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-4">
                  {{ pkg.description }}
                </p>

                <!-- Key Highlights (Duration, Capacity, Karaoke) -->
                <div class="grid grid-cols-3 gap-2 py-3 border-y border-slate-100 mb-4 text-center">
                  <div class="p-1.5 rounded-lg bg-slate-50">
                    <span class="text-[10px] text-slate-400 block font-medium">Durasi</span>
                    <span class="text-xs font-bold text-slate-800 block truncate">8 – 10 Jam</span>
                  </div>
                  <div class="p-1.5 rounded-lg bg-slate-50">
                    <span class="text-[10px] text-slate-400 block font-medium">Kapasitas</span>
                    <span class="text-xs font-bold text-slate-800 block truncate">15 Kursi</span>
                  </div>
                  <div class="p-1.5 rounded-lg bg-slate-50">
                    <span class="text-[10px] text-slate-400 block font-medium">Fasilitas</span>
                    <span class="text-xs font-bold text-blue-700 block truncate">Karaoke TV</span>
                  </div>
                </div>

                <!-- Structured Visual Tour Route -->
                <div class="mb-4">
                  <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                      <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                      </svg>
                      <span>Rute Wisata yang Dikunjungi:</span>
                    </span>
                  </div>

                  <!-- Route Step Chips -->
                  <div class="flex flex-wrap items-center gap-1.5">
                    <template v-for="(stop, sIdx) in getRouteStops(pkg.tourRoute)" :key="sIdx">
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-slate-100 border border-slate-200 text-[11px] font-semibold text-slate-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                        <span>{{ stop }}</span>
                      </span>
                      <svg
                        v-if="sIdx < getRouteStops(pkg.tourRoute).length - 1"
                        class="w-3 h-3 text-slate-400 shrink-0"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                      </svg>
                    </template>
                  </div>
                </div>

                <!-- Collapsible Detailed Itinerary & Inclusions Accordion -->
                <div class="mt-4 pt-4 border-t border-slate-100">
                  <button
                    @click="toggleDetails(pkg.id)"
                    type="button"
                    class="w-full flex items-center justify-between py-2 px-3 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-800 text-xs font-bold transition-colors"
                  >
                    <span class="inline-flex items-center gap-1.5">
                      <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                      </svg>
                      <span>{{ isExpanded(pkg.id) ? 'Sembunyikan Rincian Lengkap' : 'Lihat Itinerary & Fasilitas Lengkap' }}</span>
                    </span>
                    <svg
                      class="w-4 h-4 text-slate-500 transition-transform duration-200"
                      :class="isExpanded(pkg.id) ? 'rotate-180' : ''"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                  </button>

                  <!-- Expanded Content Section -->
                  <div v-show="isExpanded(pkg.id)" class="mt-3 space-y-4 pt-2">
                    <!-- Facilities List -->
                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200/70">
                      <span class="text-xs font-bold text-slate-900 block mb-2">Fasilitas Kendaraan &amp; Hiburan:</span>
                      <ul class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 text-xs text-slate-600">
                        <li v-for="(fac, fIdx) in pkg.facilities" :key="fIdx" class="flex items-start gap-1.5">
                          <svg class="w-3.5 h-3.5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                          </svg>
                          <span>{{ fac }}</span>
                        </li>
                      </ul>
                    </div>

                    <!-- 8-Point Detailed Itinerary -->
                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200/70">
                      <span class="text-xs font-bold text-slate-900 block mb-2">Rencana Perjalanan (Itinerary):</span>
                      <ol class="space-y-2 text-xs text-slate-600">
                        <li v-for="(item, iIdx) in pkg.itinerary" :key="iIdx" class="flex items-start gap-2">
                          <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">
                            {{ String(iIdx + 1).padStart(2, '0') }}
                          </span>
                          <span class="leading-relaxed">{{ item }}</span>
                        </li>
                      </ol>
                    </div>

                    <!-- Inclusions vs Exclusions -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                      <!-- Included -->
                      <div class="p-3 rounded-xl bg-emerald-50/50 border border-emerald-100">
                        <span class="text-[11px] font-bold text-emerald-900 uppercase tracking-wider block mb-1.5">
                          Termasuk dalam Paket:
                        </span>
                        <ul class="space-y-1 text-xs text-slate-600">
                          <li v-for="(inc, incIdx) in pkg.included" :key="incIdx" class="flex items-start gap-1.5">
                            <svg class="w-3 h-3 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>{{ inc }}</span>
                          </li>
                        </ul>
                      </div>

                      <!-- Excluded -->
                      <div class="p-3 rounded-xl bg-slate-100/70 border border-slate-200/80">
                        <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider block mb-1.5">
                          Tidak Termasuk:
                        </span>
                        <ul class="space-y-1 text-xs text-slate-500">
                          <li v-for="(exc, excIdx) in pkg.excluded" :key="excIdx" class="flex items-start gap-1.5">
                            <span class="text-slate-400 font-bold">&bull;</span>
                            <span>{{ exc }}</span>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Footer CTA & WhatsApp Booking Action -->
              <div class="pt-4 mt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                  <span class="text-[10px] text-slate-400 block font-medium">Titik Penjemputan:</span>
                  <span class="text-xs font-semibold text-slate-800">Pelabuhan / Bandara / Hotel Bintan</span>
                </div>

                <a
                  :href="getWhatsAppUrl(pkg.ctaWhatsappText)"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-xs transition-all active:scale-95 shrink-0"
                >
                  <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                  </svg>
                  <span>Pesan via WhatsApp</span>
                </a>
              </div>
            </div>

          </div>
        </article>
      </div>

      <!-- 5. Quick Comparison Table: HiAce Commuter vs HiAce Premio -->
      <section class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 p-5 sm:p-8 mb-12 shadow-xs">
        <div class="max-w-2xl mb-6">
          <span class="text-xs font-bold text-blue-600 uppercase tracking-wider block mb-1">Panduan Memilih</span>
          <h3 class="text-lg sm:text-2xl font-black text-slate-900 tracking-tight">
            Perbandingan HiAce Commuter vs HiAce Premio Luxury
          </h3>
          <p class="text-xs sm:text-sm text-slate-600 mt-1">
            Lihat perbedaan spesifikasi dan kenyamanan untuk menyesuaikan dengan kebutuhan rombongan Anda.
          </p>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs sm:text-sm border-collapse">
            <thead>
              <tr class="border-b border-slate-200 text-slate-500">
                <th class="py-3 px-3 sm:px-4 font-semibold">Spesifikasi &amp; Fasilitas</th>
                <th class="py-3 px-3 sm:px-4 font-bold text-slate-900 bg-slate-50/70 rounded-t-lg">
                  HiAce Commuter
                </th>
                <th class="py-3 px-3 sm:px-4 font-bold text-indigo-900 bg-indigo-50/70 rounded-t-lg">
                  HiAce Premio Luxury
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr>
                <td class="py-3 px-3 sm:px-4 font-medium text-slate-600">Tarif Sewa (All-In)</td>
                <td class="py-3 px-3 sm:px-4 font-black text-slate-900 bg-slate-50/40">Rp 1.000.000 /hari</td>
                <td class="py-3 px-3 sm:px-4 font-black text-indigo-700 bg-indigo-50/30">Rp 1.200.000 /hari</td>
              </tr>
              <tr>
                <td class="py-3 px-3 sm:px-4 font-medium text-slate-600">Kapasitas Tempat Duduk</td>
                <td class="py-3 px-3 sm:px-4 text-slate-800 bg-slate-50/40">15 Kursi Penumpang</td>
                <td class="py-3 px-3 sm:px-4 text-slate-800 bg-indigo-50/30">15 Kursi Penumpang</td>
              </tr>
              <tr>
                <td class="py-3 px-3 sm:px-4 font-medium text-slate-600">Supir &amp; BBM</td>
                <td class="py-3 px-3 sm:px-4 text-emerald-700 font-bold bg-slate-50/40">Sudah Termasuk (All-In)</td>
                <td class="py-3 px-3 sm:px-4 text-emerald-700 font-bold bg-indigo-50/30">Sudah Termasuk (All-In)</td>
              </tr>
              <tr>
                <td class="py-3 px-3 sm:px-4 font-medium text-slate-600">Fasilitas Karaoke</td>
                <td class="py-3 px-3 sm:px-4 text-slate-700 bg-slate-50/40">Sound System + Mic</td>
                <td class="py-3 px-3 sm:px-4 font-bold text-slate-900 bg-indigo-50/30">Smart TV Plafon + Double Wireless Mic</td>
              </tr>
              <tr>
                <td class="py-3 px-3 sm:px-4 font-medium text-slate-600">Interior &amp; Kenyamanan</td>
                <td class="py-3 px-3 sm:px-4 text-slate-700 bg-slate-50/40">Standar HiAce Nyaman &amp; Bersih</td>
                <td class="py-3 px-3 sm:px-4 font-semibold text-slate-900 bg-indigo-50/30">Kursi Kulit VIP, Ambient Light, Lantai Kayu</td>
              </tr>
              <tr>
                <td class="py-3 px-3 sm:px-4 font-medium text-slate-600">Cocok Untuk</td>
                <td class="py-3 px-3 sm:px-4 text-slate-600 bg-slate-50/40">Wisata Keluarga Hemat &amp; Komunitas</td>
                <td class="py-3 px-3 sm:px-4 text-slate-700 bg-indigo-50/30">Tamu VIP, Instansi Kedinasan, Liburan Mewah</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- 6. Three-Step Booking Flow -->
      <section class="mb-12">
        <div class="text-center max-w-xl mx-auto mb-8">
          <span class="text-xs font-bold text-blue-600 uppercase tracking-wider block mb-1">Kemudahan Layanan</span>
          <h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
            Cara Pesan Paket Tour dalam 3 Langkah
          </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
          <!-- Step 1 -->
          <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs relative">
            <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-black text-xs flex items-center justify-center mb-3">
              1
            </div>
            <h4 class="text-sm font-bold text-slate-900 mb-1">Pilih Paket &amp; Tanggal</h4>
            <p class="text-xs text-slate-500 leading-relaxed">
              Tentukan jenis armada (HiAce Commuter atau HiAce Premio) dan tanggal liburan Anda ke Pulau Bintan.
            </p>
          </div>

          <!-- Step 2 -->
          <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs relative">
            <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-black text-xs flex items-center justify-center mb-3">
              2
            </div>
            <h4 class="text-sm font-bold text-slate-900 mb-1">Konfirmasi via WhatsApp</h4>
            <p class="text-xs text-slate-500 leading-relaxed">
              Hubungi admin dengan satu klik untuk konfirmasi ketersediaan tanggal dan titik jemput rombongan.
            </p>
          </div>

          <!-- Step 3 -->
          <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs relative">
            <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-black text-xs flex items-center justify-center mb-3">
              3
            </div>
            <h4 class="text-sm font-bold text-slate-900 mb-1">Penjemputan Tepat Waktu</h4>
            <p class="text-xs text-slate-500 leading-relaxed">
              Supir profesional kami siap menyambut rombongan di Pelabuhan Sri Bintan Pura, Bandara RHF, atau Resort Lagoi.
            </p>
          </div>
        </div>
      </section>

      <!-- 7. Consultation & Custom Route Banner -->
      <div class="bg-slate-900 rounded-2xl sm:rounded-3xl p-6 sm:p-10 text-white text-center max-w-3xl mx-auto shadow-md">
        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-slate-800 border border-slate-700 text-slate-300 text-xs font-medium mb-3">
          <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
          </svg>
          <span>Konsultasi Bebas Biaya</span>
        </span>
        <h3 class="text-xl sm:text-2xl font-black text-white mb-2">
          Butuh Rute Khusus atau Penyesuaian Jadwal?
        </h3>
        <p class="text-xs sm:text-sm text-slate-300 max-w-xl mx-auto mb-6 leading-relaxed">
          Admin 3 Putri Mulya siap membantu mengatur jadwal penjemputan dari Pelabuhan Sri Bintan Pura, Bandara RHF, atau Resort Lagoi sesuai jam kedatangan Anda.
        </p>
        <div class="flex flex-wrap items-center justify-center gap-3">
          <a
            :href="getWhatsAppUrl('Halo 3 Putri Mulya, saya ingin konsultasi rute khusus dan jadwal tour HiAce di Bintan.')"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs sm:text-sm transition-all shadow-sm active:scale-95"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
            </svg>
            <span>Chat WhatsApp Admin</span>
          </a>
          <RouterLink
            to="/"
            class="inline-flex items-center gap-1.5 px-5 py-3 rounded-xl border border-slate-700 hover:bg-slate-800 text-slate-300 font-semibold text-xs sm:text-sm transition-colors"
          >
            <span>Kembali ke Beranda</span>
          </RouterLink>
        </div>
      </div>

    </div>
  </div>
</template>
