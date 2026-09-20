<script setup lang="ts">
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import { RouterLink } from 'vue-router'
import { useTourPackages } from '@/composables/useTourPackages'
import type { TourPackage } from '@/config/tourPackages'
import { siteConfig } from '@/config/site'
import { useCurrency } from '@/composables/useCurrency'
import { useLanguage } from '@/composables/useLanguage'

const { packages: tourPackages, fetchTourPackages } = useTourPackages()
const { currentCurrency, convertPrice } = useCurrency()
const { isEnglish, t } = useLanguage()

// Dynamic package data for comparison table (automatically synced with API / Admin)
const commuterPkg = computed(() =>
  tourPackages.value.find(p => p.slug?.includes('commuter') || p.title?.toLowerCase().includes('commuter'))
)
const premioPkg = computed(() =>
  tourPackages.value.find(p => p.slug?.includes('premio') || p.title?.toLowerCase().includes('premio'))
)

const commuterPrice = computed(() => {
  const raw = commuterPkg.value?.rawPrice || 1400000
  return convertPrice(raw).formatted
})
const commuterOriginalPrice = computed(() => {
  const raw = commuterPkg.value?.rawOriginalPrice || 1800000
  return convertPrice(raw).formatted
})
const premioPrice = computed(() => {
  const raw = premioPkg.value?.rawPrice || 1500000
  return convertPrice(raw).formatted
})
const premioOriginalPrice = computed(() => {
  const raw = premioPkg.value?.rawOriginalPrice || 2000000
  return convertPrice(raw).formatted
})
const commuterCapacity = computed(() => isEnglish.value ? '15 Passenger Seats' : (commuterPkg.value?.capacity || '15 Kursi Penumpang'))
const premioCapacity = computed(() => isEnglish.value ? '11 - 14 Passenger Seats' : (premioPkg.value?.capacity || '11 - 14 Kursi Penumpang'))

const getPackagePrice = (pkg: TourPackage) => {
  const raw = pkg.rawPrice || (pkg.slug?.includes('commuter') ? 1400000 : pkg.slug?.includes('premio') ? 1500000 : 0)
  return convertPrice(raw)
}

const getOriginalPrice = (pkg: TourPackage) => {
  const raw = pkg.rawOriginalPrice || (pkg.slug?.includes('commuter') ? 1800000 : pkg.slug?.includes('premio') ? 2000000 : 0)
  return convertPrice(raw)
}

const getSavings = (pkg: TourPackage) => {
  const original = pkg.rawOriginalPrice || (pkg.slug?.includes('commuter') ? 1800000 : pkg.slug?.includes('premio') ? 2000000 : 0)
  const current = pkg.rawPrice || (pkg.slug?.includes('commuter') ? 1400000 : pkg.slug?.includes('premio') ? 1500000 : 0)
  const diff = Math.max(0, original - current)
  return convertPrice(diff)
}

// Active photo index per package in card
const activePhotoIndexes = ref<Record<string, number>>({})

// Collapsible detail accordion state (per package id)
const expandedDetails = ref<Record<string, boolean>>({})

// Lightbox / Zoom Modal State
const isLightboxOpen = ref(false)
const lightboxPkg = ref<TourPackage | null>(null)
const lightboxPhotoIdx = ref(0)
const zoomLevel = ref(1)

const currentLightboxPhotos = computed<string[]>(() => {
  if (!lightboxPkg.value) return []
  if (lightboxPkg.value.galleryPhotos && lightboxPkg.value.galleryPhotos.length > 0) {
    return lightboxPkg.value.galleryPhotos
  }
  return [lightboxPkg.value.vehiclePhoto]
})

const openLightbox = (pkg: TourPackage, idx: number = 0) => {
  lightboxPkg.value = pkg
  lightboxPhotoIdx.value = idx
  zoomLevel.value = 1
  isLightboxOpen.value = true
}

const closeLightbox = () => {
  isLightboxOpen.value = false
  zoomLevel.value = 1
}

const nextLightboxPhoto = () => {
  if (currentLightboxPhotos.value.length <= 1) return
  lightboxPhotoIdx.value = (lightboxPhotoIdx.value + 1) % currentLightboxPhotos.value.length
  zoomLevel.value = 1
}

const prevLightboxPhoto = () => {
  if (currentLightboxPhotos.value.length <= 1) return
  lightboxPhotoIdx.value = (lightboxPhotoIdx.value - 1 + currentLightboxPhotos.value.length) % currentLightboxPhotos.value.length
  zoomLevel.value = 1
}

const zoomIn = () => {
  if (zoomLevel.value < 3) {
    zoomLevel.value = Number((zoomLevel.value + 0.5).toFixed(1))
  }
}

const zoomOut = () => {
  if (zoomLevel.value > 1) {
    zoomLevel.value = Number((zoomLevel.value - 0.5).toFixed(1))
  }
}

const resetZoom = () => {
  zoomLevel.value = 1
}

const toggleZoom = () => {
  zoomLevel.value = zoomLevel.value === 1 ? 2 : 1
}

const handleKeydown = (e: KeyboardEvent) => {
  if (!isLightboxOpen.value) return
  if (e.key === 'Escape') closeLightbox()
  if (e.key === 'ArrowRight') nextLightboxPhoto()
  if (e.key === 'ArrowLeft') prevLightboxPhoto()
  if (e.key === '+' || e.key === '=') zoomIn()
  if (e.key === '-') zoomOut()
}

watch(isLightboxOpen, (open) => {
  if (typeof document === 'undefined') return
  if (open) {
    document.body.classList.add('overflow-hidden')
  } else {
    document.body.classList.remove('overflow-hidden')
  }
})

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

const getTourWhatsAppUrl = (pkg: TourPackage) => {
  const phone = siteConfig.rentalPhone.replace(/\D/g, '')
  const priceInfo = getPackagePrice(pkg)
  const savings = getSavings(pkg)
  let text = ''

  if (pkg.rawOriginalPrice && pkg.rawOriginalPrice > 0) {
    text = isEnglish.value
      ? `Hello 3 Putri Mulya, I would like to claim the Special Online Promo for ${pkg.title} (${priceInfo.formatted}, Save ${savings.formatted}) in Bintan. Please inform availability for [insert date].`
      : `Halo 3 Putri Mulya, saya ingin klaim Promo Spesial Website untuk ${pkg.title} (${priceInfo.formatted}, Hemat ${savings.formatted}). Mohon info ketersediaan untuk tanggal [isi tanggal].`
  } else {
    text = isEnglish.value
      ? `Hello 3 Putri Mulya, I would like to book the ${pkg.title} (${pkg.vehicle}) in Bintan.`
      : pkg.ctaWhatsappText
    if (priceInfo.isConverted && priceInfo.amount > 0) {
      text += ` (Est. ${priceInfo.formatted})`
    }
  }

  return `https://wa.me/${phone}?text=${encodeURIComponent(text)}`
}

const updatePageTitle = () => {
  document.title = isEnglish.value
    ? 'Bintan HiAce Tour Packages (Commuter & Premio) - Driver & Fuel Included | 3 Putri Mulya'
    : 'Paket Tour Bintan HiAce (Commuter & Premio) - Include Supir & BBM | 3 Putri Mulya'
}

onMounted(() => {
  updatePageTitle()
  window.scrollTo({ top: 0, behavior: 'smooth' })
  fetchTourPackages()
  if (typeof window !== 'undefined') {
    window.addEventListener('keydown', handleKeydown)
  }
})

watch(isEnglish, () => {
  updatePageTitle()
})

onBeforeUnmount(() => {
  if (typeof window !== 'undefined') {
    window.removeEventListener('keydown', handleKeydown)
    document.body.classList.remove('overflow-hidden')
  }
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
          <span>{{ isEnglish ? 'Home' : 'Beranda' }}</span>
        </RouterLink>
        <span class="text-slate-300">/</span>
        <span class="text-slate-900 font-semibold">{{ isEnglish ? 'Bintan Tour Packages' : 'Paket Tour Bintan' }}</span>
      </nav>

      <!-- Page Header -->
      <header v-reveal:fade-up class="max-w-3xl mb-6 sm:mb-10">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 border border-blue-200/80 text-blue-700 text-xs font-semibold mb-3">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m-8 4h8m-8 4h4m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2z"/>
          </svg>
          <span>{{ isEnglish ? 'Exclusive HiAce Fleet (Commuter 15-Seater & Premio Luxury VIP)' : 'Armada Khusus HiAce (Commuter 15 Kursi & Premio Luxury VIP)' }}</span>
        </div>

        <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight leading-tight mb-3">
          {{ isEnglish ? 'Bintan Island Private Tour Packages' : 'Paket Tour Wisata Pulau Bintan' }}
        </h1>

        <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
          {{ isEnglish
            ? 'The premier way to explore Bintan Island for families, corporate retreats, and group vacations. All Toyota HiAce vehicles accommodate 11 to 15 passengers, complete with On-Board Karaoke System, and rates are 100% All-In (Private Vehicle + Professional Chauffeur + Fuel Included).'
            : 'Pilihan terbaik perjalanan wisata keliling Pulau Bintan untuk rombongan keluarga, instansi, atau sahabat. Seluruh armada Toyota HiAce berkapasitas 11 hingga 15 penumpang, dilengkapi fasilitas Karaoke System, dan tarif sudah All-In (Sudah Termasuk Mobil + Supir + BBM).'
          }}
        </p>
      </header>

      <!-- Compact Value Proposition Strip -->
      <div v-reveal:fade-up="60" class="bg-white rounded-xl sm:rounded-2xl border border-slate-200/90 p-3 sm:p-4 mb-8 sm:mb-12 shadow-xs">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5 sm:gap-4">
          <!-- Pillar 1 -->
          <div class="flex items-center gap-2.5 p-2 sm:p-2.5 rounded-lg bg-slate-50 border border-slate-100">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
              </svg>
            </div>
            <div class="min-w-0">
              <span class="text-xs font-bold text-slate-900 block truncate">{{ isEnglish ? 'Driver & Fuel Included' : 'Include Supir & BBM' }}</span>
              <span class="text-[10px] sm:text-[11px] text-slate-500 block truncate">{{ isEnglish ? 'Transparent All-In rate' : 'Tarif All-In transparan' }}</span>
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
              <span class="text-xs font-bold text-slate-900 block truncate">{{ isEnglish ? 'Karaoke On-Board' : 'Karaoke On-Board' }}</span>
              <span class="text-[10px] sm:text-[11px] text-slate-500 block truncate">{{ isEnglish ? 'Smart TV & Wireless Mics' : 'Smart TV & Mic Wireless' }}</span>
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
              <span class="text-xs font-bold text-slate-900 block truncate">{{ isEnglish ? '15-Seat Capacity' : 'Kapasitas 15 Kursi' }}</span>
              <span class="text-[10px] sm:text-[11px] text-slate-500 block truncate">{{ isEnglish ? 'Spacious & comfortable' : 'Kabin luas & lega' }}</span>
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
              <span class="text-xs font-bold text-slate-900 block truncate">{{ isEnglish ? 'Flexible Pickup' : 'Jemput Fleksibel' }}</span>
              <span class="text-[10px] sm:text-[11px] text-slate-500 block truncate">{{ isEnglish ? 'Ferry Port / Airport / Hotel' : 'Pelabuhan / Bandara / Hotel' }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Detailed Tour Packages List -->
      <div class="space-y-4 mb-12 sm:mb-16">
        <article
          v-for="(pkg, idx) in tourPackages"
          :key="pkg.id"
          :id="pkg.slug"
          v-reveal:fade-up="idx * 90"
          class="bg-white rounded-2xl border border-slate-200/90 overflow-hidden shadow-xs hover:shadow-md transition-all duration-300"
        >
          <!-- ── COMPACT DEFAULT VIEW ─────────────────────────────── -->
          <div class="grid grid-cols-1 sm:grid-cols-12 gap-0">

            <!-- Photo (left, fixed height) -->
            <div class="sm:col-span-4 relative">
              <div
                @click="openLightbox(pkg, activePhotoIndexes[pkg.id] ?? 0)"
                class="relative w-full aspect-[4/3] sm:aspect-auto sm:h-full min-h-[180px] bg-slate-100 overflow-hidden cursor-zoom-in group"
              >
                <img
                  :src="getActivePhoto(pkg)"
                  :alt="pkg.title"
                  loading="lazy"
                  decoding="async"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                />
                <!-- Badge -->
                <div class="absolute top-2.5 left-2.5 flex flex-col gap-1 pointer-events-none">
                  <span
                    v-if="pkg.badge"
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold text-white shadow"
                    :class="pkg.id === 'tour-hiace-premio' ? 'bg-indigo-600' : 'bg-blue-600'"
                  >
                    {{ isEnglish ? (pkg.badgeEn || pkg.badge) : pkg.badge }}
                  </span>
                  <span
                    v-if="pkg.rawOriginalPrice"
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-rose-600 text-white shadow-md animate-pulse"
                  >
                    🔥 {{ isEnglish ? `SAVE ${pkg.discountPercent}%` : `DISKON ${pkg.discountPercent}%` }}
                  </span>
                </div>
                <div class="absolute bottom-2 right-2 pointer-events-none">
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-black/50 text-white">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    {{ isEnglish ? (pkg.capacityEn || pkg.capacity) : pkg.capacity }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Info (right) -->
            <div class="sm:col-span-8 p-4 sm:p-5 flex flex-col justify-between gap-3">
              <!-- Title -->
              <div>
                <p class="text-[11px] font-bold text-blue-600 uppercase tracking-wider mb-0.5">
                  {{ isEnglish ? (pkg.vehicleEn || pkg.vehicle) : pkg.vehicle }} &bull; {{ isEnglish ? (pkg.durationEn || pkg.duration) : pkg.duration }}
                </p>
                <h2 class="text-base sm:text-lg font-black text-slate-900 leading-snug">{{ isEnglish ? (pkg.titleEn || pkg.title) : pkg.title }}</h2>
              </div>

              <!-- Price row -->
              <div class="flex items-center gap-3 flex-wrap">
                <div>
                  <!-- Strike-through Original Price & Savings Pill -->
                  <div v-if="pkg.rawOriginalPrice" class="flex items-center gap-1.5 mb-1">
                    <span class="text-xs font-bold text-slate-400 line-through">
                      {{ getOriginalPrice(pkg).formatted }}
                    </span>
                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-600 border border-rose-200 shadow-2xs">
                      🔥 {{ isEnglish ? `Save ${getSavings(pkg).formatted}` : `Hemat ${getSavings(pkg).formatted}` }}
                    </span>
                  </div>
                  <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider block">
                    {{ isEnglish ? (pkg.rawOriginalPrice ? 'SPECIAL PROMO PRICE' : (pkg.priceLabelEn || pkg.priceLabel)) : (pkg.rawOriginalPrice ? 'HARGA PROMO SPESIAL' : pkg.priceLabel) }}
                  </span>
                  <div class="flex items-baseline gap-1">
                    <span class="text-xl sm:text-2xl font-black text-blue-600 tracking-tight">{{ getPackagePrice(pkg).formatted }}</span>
                    <span v-if="getPackagePrice(pkg).amount > 0" class="text-xs text-slate-500 font-semibold">{{ t('common.perDay') }}</span>
                  </div>
                  <span v-if="getPackagePrice(pkg).isConverted && getPackagePrice(pkg).amount > 0" class="text-[10px] text-slate-400 font-medium block -mt-0.5">
                    ({{ getPackagePrice(pkg).originalFormatted }})
                  </span>
                </div>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                  {{ isEnglish ? 'Chauffeur & Fuel Included' : 'Include Supir & BBM' }}
                </span>
              </div>

              <!-- 3 Key stats -->
              <div class="grid grid-cols-3 gap-2">
                <div class="p-2 rounded-lg bg-slate-50 border border-slate-100 text-center">
                  <span class="text-[9px] text-slate-400 block font-semibold uppercase tracking-wider">{{ isEnglish ? 'Duration' : 'Durasi' }}</span>
                  <span class="text-[11px] font-black text-slate-800 block mt-0.5 truncate">{{ isEnglish ? (pkg.durationEn || pkg.duration) : (pkg.duration || '8–10 Jam') }}</span>
                </div>
                <div class="p-2 rounded-lg bg-slate-50 border border-slate-100 text-center">
                  <span class="text-[9px] text-slate-400 block font-semibold uppercase tracking-wider">{{ isEnglish ? 'Capacity' : 'Kapasitas' }}</span>
                  <span class="text-[11px] font-black text-slate-800 block mt-0.5 truncate">{{ isEnglish ? (pkg.capacityEn || pkg.capacity) : (pkg.capacity || '15 Kursi') }}</span>
                </div>
                <div class="p-2 rounded-lg bg-blue-50 border border-blue-100 text-center">
                  <span class="text-[9px] text-blue-500 block font-semibold uppercase tracking-wider">{{ isEnglish ? 'Facility' : 'Fasilitas' }}</span>
                  <span class="text-[11px] font-black text-blue-700 block mt-0.5 truncate">{{ isEnglish ? ((pkg.facilitiesEn && pkg.facilitiesEn[0]) || pkg.facilities?.[0] || 'Karaoke') : (pkg.facilities?.[0] || 'Karaoke') }}</span>
                </div>
              </div>

              <!-- Actions row -->
              <div class="flex items-center gap-2 flex-wrap">
                <a
                  :href="getTourWhatsAppUrl(pkg)"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-xs transition-all active:scale-95 shrink-0"
                >
                  <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                  </svg>
                  {{ isEnglish ? 'Book via WhatsApp' : 'Pesan via WhatsApp' }}
                </a>
                <button
                  @click="toggleDetails(pkg.id)"
                  type="button"
                  class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs transition-colors"
                >
                  {{ isExpanded(pkg.id) ? (isEnglish ? 'Hide Details' : 'Sembunyikan') : (isEnglish ? 'View Details' : 'Lihat Detail') }}
                  <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="isExpanded(pkg.id) ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- ── EXPANDED DETAIL (accordion) ──────────────────────── -->
          <div v-show="isExpanded(pkg.id)" class="border-t border-slate-100 p-4 sm:p-5 space-y-4">

            <!-- Description -->
            <p class="text-sm text-slate-600 leading-relaxed">{{ isEnglish ? (pkg.descriptionEn || pkg.description) : pkg.description }}</p>

            <!-- Route -->
            <div>
              <span class="text-[11px] font-bold text-slate-700 flex items-center gap-1.5 mb-2">
                <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                {{ isEnglish ? 'Tour Route & Stops:' : 'Rute Wisata yang Dikunjungi:' }}
              </span>
              <div class="flex flex-wrap items-center gap-1.5">
                <template v-for="(stop, sIdx) in getRouteStops(isEnglish ? (pkg.tourRouteEn || pkg.tourRoute) : pkg.tourRoute)" :key="sIdx">
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 text-[11px] font-semibold text-slate-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0"></span>{{ stop }}
                  </span>
                  <svg v-if="sIdx < getRouteStops(isEnglish ? (pkg.tourRouteEn || pkg.tourRoute) : pkg.tourRoute).length - 1" class="w-2.5 h-2.5 text-slate-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                  </svg>
                </template>
              </div>
            </div>

            <!-- Gallery thumbnails (inside detail) -->
            <div v-if="pkg.galleryPhotos && pkg.galleryPhotos.length > 1">
              <span class="text-[11px] font-bold text-slate-600 block mb-2">{{ isEnglish ? 'Vehicle Gallery:' : 'Galeri Foto Unit:' }}</span>
              <div class="flex items-center gap-1.5 overflow-x-auto pb-0.5 scrollbar-none">
                <button
                  v-for="(img, idx) in pkg.galleryPhotos"
                  :key="idx"
                  @click="setActivePhoto(pkg.id, idx)"
                  type="button"
                  :class="(activePhotoIndexes[pkg.id] ?? 0) === idx ? 'ring-2 ring-blue-500 opacity-100' : 'opacity-50 hover:opacity-80'"
                  class="w-16 h-11 rounded-lg overflow-hidden shrink-0 border border-slate-200 transition-all bg-slate-100"
                >
                  <img :src="img" class="w-full h-full object-cover" />
                </button>
              </div>
            </div>

            <!-- Comfort specs + facilities + itinerary in 2 cols on desktop -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- Facilities -->
              <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                <span class="text-xs font-bold text-slate-800 block mb-2">{{ isEnglish ? 'Vehicle Features:' : 'Fasilitas Kendaraan:' }}</span>
                <ul class="grid grid-cols-1 gap-1.5 text-xs text-slate-600">
                  <li v-for="(fac, fIdx) in (isEnglish ? (pkg.facilitiesEn || pkg.facilities) : pkg.facilities)" :key="fIdx" class="flex items-start gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ fac }}
                  </li>
                </ul>
              </div>

              <!-- Included / Excluded -->
              <div class="space-y-2.5">
                <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-100">
                  <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider block mb-1.5">{{ isEnglish ? 'Included:' : 'Termasuk:' }}</span>
                  <ul class="space-y-1 text-xs text-slate-600">
                    <li v-for="(inc, incIdx) in (isEnglish ? (pkg.includedEn || pkg.included) : pkg.included)" :key="incIdx" class="flex items-start gap-1.5">
                      <svg class="w-3 h-3 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                      {{ inc }}
                    </li>
                  </ul>
                </div>
                <div class="p-3 rounded-xl bg-slate-100 border border-slate-200">
                  <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider block mb-1.5">{{ isEnglish ? 'Not Included:' : 'Tidak Termasuk:' }}</span>
                  <ul class="space-y-1 text-xs text-slate-500">
                    <li v-for="(exc, excIdx) in (isEnglish ? (pkg.excludedEn || pkg.excluded) : pkg.excluded)" :key="excIdx" class="flex items-start gap-1.5">
                      <span class="text-slate-400 font-bold shrink-0">&bull;</span>{{ exc }}
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Itinerary -->
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
              <span class="text-xs font-bold text-slate-800 block mb-2">{{ isEnglish ? 'Tour Itinerary:' : 'Rencana Perjalanan (Itinerary):' }}</span>
              <ol class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 text-xs text-slate-600">
                <li v-for="(item, iIdx) in (isEnglish ? (pkg.itineraryEn || pkg.itinerary) : pkg.itinerary)" :key="iIdx" class="flex items-start gap-2">
                  <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">
                    {{ String(iIdx + 1).padStart(2, '0') }}
                  </span>
                  <span class="leading-relaxed">{{ item }}</span>
                </li>
              </ol>
            </div>

            <!-- Titik jemput footer -->
            <p class="text-[11px] text-slate-400">
              <span class="font-semibold text-slate-600">{{ isEnglish ? 'Pickup Point:' : 'Titik Penjemputan:' }}</span> {{ isEnglish ? 'Ferry Terminal / Airport / Any Hotel in Bintan' : 'Pelabuhan / Bandara / Hotel Bintan' }}
            </p>
          </div>

        </article>
      </div>


      <!-- 5. Quick Comparison Table: HiAce Commuter vs HiAce Premio -->
      <section v-reveal:fade-up class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 p-5 sm:p-8 mb-12 shadow-xs">
        <div class="max-w-2xl mb-6">
          <span class="text-xs font-bold text-blue-600 uppercase tracking-wider block mb-1">{{ isEnglish ? 'Selection Guide' : 'Panduan Memilih' }}</span>
          <h3 class="text-lg sm:text-2xl font-black text-slate-900 tracking-tight">
            {{ isEnglish ? 'HiAce Commuter vs HiAce Premio Luxury Comparison' : 'Perbandingan HiAce Commuter vs HiAce Premio Luxury' }}
          </h3>
          <p class="text-xs sm:text-sm text-slate-600 mt-1">
            {{ isEnglish ? 'Compare specifications, luxury amenities, and capacity to choose the perfect fleet for your group trip.' : 'Lihat perbedaan spesifikasi dan kenyamanan untuk menyesuaikan dengan kebutuhan rombongan Anda.' }}
          </p>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs sm:text-sm border-collapse">
            <thead>
              <tr class="border-b border-slate-200 text-slate-500">
                <th class="py-3 px-3 sm:px-4 font-semibold">{{ isEnglish ? 'Specs & Facilities' : 'Spesifikasi & Fasilitas' }}</th>
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
                <td class="py-3 px-3 sm:px-4 font-medium text-slate-600">{{ isEnglish ? 'Rental Rate (All-In)' : 'Tarif Sewa (All-In)' }}</td>
                <td class="py-3 px-3 sm:px-4 font-black text-slate-900 bg-slate-50/40">
                  <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="text-xs text-slate-400 line-through font-normal">{{ commuterOriginalPrice }}</span>
                    <span class="text-[10px] font-extrabold px-1.5 py-0.5 rounded-full bg-rose-50 text-rose-600 border border-rose-200">-22%</span>
                  </div>
                  <div class="text-blue-600 font-black">
                    <span>{{ commuterPrice }}</span> <span class="text-xs font-semibold text-slate-500">{{ t('common.perDay') }}</span>
                  </div>
                  <span v-if="currentCurrency !== 'IDR'" class="block text-[10px] text-slate-400 font-normal mt-0.5">
                    (Promo Rp 1.400.000, Normal Rp 1.800.000)
                  </span>
                </td>
                <td class="py-3 px-3 sm:px-4 font-black text-indigo-700 bg-indigo-50/30">
                  <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="text-xs text-slate-400 line-through font-normal">{{ premioOriginalPrice }}</span>
                    <span class="text-[10px] font-extrabold px-1.5 py-0.5 rounded-full bg-rose-50 text-rose-600 border border-rose-200">-25%</span>
                  </div>
                  <div class="text-indigo-700 font-black">
                    <span>{{ premioPrice }}</span> <span class="text-xs font-semibold text-slate-500">{{ t('common.perDay') }}</span>
                  </div>
                  <span v-if="currentCurrency !== 'IDR'" class="block text-[10px] text-slate-400 font-normal mt-0.5">
                    (Promo Rp 1.500.000, Normal Rp 2.000.000)
                  </span>
                </td>
              </tr>
              <tr>
                <td class="py-3 px-3 sm:px-4 font-medium text-slate-600">{{ isEnglish ? 'Seating Capacity' : 'Kapasitas Tempat Duduk' }}</td>
                <td class="py-3 px-3 sm:px-4 text-slate-800 bg-slate-50/40">{{ commuterCapacity }}</td>
                <td class="py-3 px-3 sm:px-4 text-slate-800 bg-indigo-50/30">{{ premioCapacity }}</td>
              </tr>
              <tr>
                <td class="py-3 px-3 sm:px-4 font-medium text-slate-600">{{ isEnglish ? 'Driver & Fuel' : 'Supir & BBM' }}</td>
                <td class="py-3 px-3 sm:px-4 text-emerald-700 font-bold bg-slate-50/40">{{ isEnglish ? 'Fully Included (All-In)' : 'Sudah Termasuk (All-In)' }}</td>
                <td class="py-3 px-3 sm:px-4 text-emerald-700 font-bold bg-indigo-50/30">{{ isEnglish ? 'Fully Included (All-In)' : 'Sudah Termasuk (All-In)' }}</td>
              </tr>
              <tr>
                <td class="py-3 px-3 sm:px-4 font-medium text-slate-600">{{ isEnglish ? 'Karaoke System' : 'Fasilitas Karaoke' }}</td>
                <td class="py-3 px-3 sm:px-4 text-slate-700 bg-slate-50/40">Sound System + Mic</td>
                <td class="py-3 px-3 sm:px-4 font-bold text-slate-900 bg-indigo-50/30">{{ isEnglish ? 'Ceiling Smart TV + Dual Wireless Mics' : 'Smart TV Plafon + Double Wireless Mic' }}</td>
              </tr>
              <tr>
                <td class="py-3 px-3 sm:px-4 font-medium text-slate-600">{{ isEnglish ? 'Interior & Comfort' : 'Interior & Kenyamanan' }}</td>
                <td class="py-3 px-3 sm:px-4 text-slate-700 bg-slate-50/40">{{ isEnglish ? 'Standard HiAce Comfortable & Clean' : 'Standar HiAce Nyaman & Bersih' }}</td>
                <td class="py-3 px-3 sm:px-4 font-semibold text-slate-900 bg-indigo-50/30">{{ isEnglish ? 'VIP Leather Captain Seats, Ambient Light, Wood Finish' : 'Kursi Kulit VIP, Ambient Light, Lantai Kayu' }}</td>
              </tr>
              <tr>
                <td class="py-3 px-3 sm:px-4 font-medium text-slate-600">{{ isEnglish ? 'Best Suited For' : 'Cocok Untuk' }}</td>
                <td class="py-3 px-3 sm:px-4 text-slate-600 bg-slate-50/40">{{ isEnglish ? 'Budget Family Trips & Travel Communities' : 'Wisata Keluarga Hemat & Komunitas' }}</td>
                <td class="py-3 px-3 sm:px-4 text-slate-700 bg-indigo-50/30">{{ isEnglish ? 'VIP Guests, Corporate Retreats & Luxury Tours' : 'Tamu VIP, Instansi Kedinasan, Liburan Mewah' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- 6. Three-Step Booking Flow -->
      <section v-reveal:fade-up class="mb-12">
        <div class="text-center max-w-xl mx-auto mb-8">
          <span class="text-xs font-bold text-blue-600 uppercase tracking-wider block mb-1">{{ isEnglish ? 'Effortless Service' : 'Kemudahan Layanan' }}</span>
          <h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
            {{ isEnglish ? 'Book Your Tour in 3 Simple Steps' : 'Cara Pesan Paket Tour dalam 3 Langkah' }}
          </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
          <!-- Step 1 -->
          <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs relative">
            <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-black text-xs flex items-center justify-center mb-3">
              1
            </div>
            <h4 class="text-sm font-bold text-slate-900 mb-1">{{ isEnglish ? 'Choose Package & Date' : 'Pilih Paket & Tanggal' }}</h4>
            <p class="text-xs text-slate-500 leading-relaxed">
              {{ isEnglish ? 'Select your preferred vehicle type (HiAce Commuter or Premio Luxury) and your preferred trip schedule in Bintan.' : 'Tentukan jenis armada (HiAce Commuter atau HiAce Premio) dan tanggal liburan Anda ke Pulau Bintan.' }}
            </p>
          </div>

          <!-- Step 2 -->
          <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs relative">
            <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-black text-xs flex items-center justify-center mb-3">
              2
            </div>
            <h4 class="text-sm font-bold text-slate-900 mb-1">{{ isEnglish ? 'Confirm via WhatsApp' : 'Konfirmasi via WhatsApp' }}</h4>
            <p class="text-xs text-slate-500 leading-relaxed">
              {{ isEnglish ? 'Reach our team directly via WhatsApp to verify availability, custom route requests, and group pickup location.' : 'Hubungi admin dengan satu klik untuk konfirmasi ketersediaan tanggal dan titik jemput rombongan.' }}
            </p>
          </div>

          <!-- Step 3 -->
          <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs relative">
            <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-black text-xs flex items-center justify-center mb-3">
              3
            </div>
            <h4 class="text-sm font-bold text-slate-900 mb-1">{{ isEnglish ? 'On-Time VIP Pickup' : 'Penjemputan Tepat Waktu' }}</h4>
            <p class="text-xs text-slate-500 leading-relaxed">
              {{ isEnglish ? 'Our professional chauffeur welcomes your group at Sri Bintan Pura Ferry Terminal, RHF Airport, or Lagoi Resorts on schedule.' : 'Supir profesional kami siap menyambut rombongan di Pelabuhan Sri Bintan Pura, Bandara RHF, atau Resort Lagoi.' }}
            </p>
          </div>
        </div>
      </section>

      <!-- 7. Consultation & Custom Route Banner -->
      <div v-reveal:zoom-in class="bg-slate-900 rounded-2xl sm:rounded-3xl p-6 sm:p-10 text-white text-center max-w-3xl mx-auto shadow-md">
        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-slate-800 border border-slate-700 text-slate-300 text-xs font-medium mb-3">
          <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
          </svg>
          <span>{{ isEnglish ? 'Free Route Consultation' : 'Konsultasi Bebas Biaya' }}</span>
        </span>
        <h3 class="text-xl sm:text-2xl font-black text-white mb-2">
          {{ isEnglish ? 'Need a Custom Route or Flexible Itinerary?' : 'Butuh Rute Khusus atau Penyesuaian Jadwal?' }}
        </h3>
        <p class="text-xs sm:text-sm text-slate-300 max-w-xl mx-auto mb-6 leading-relaxed">
          {{ isEnglish ? 'Our team is ready to tailor a personalized itinerary and schedule pickups from Sri Bintan Pura Ferry Terminal, RHF Airport, or Lagoi Resorts according to your arrival.' : 'Admin 3 Putri Mulya siap membantu mengatur jadwal penjemputan dari Pelabuhan Sri Bintan Pura, Bandara RHF, atau Resort Lagoi sesuai jam kedatangan Anda.' }}
        </p>
        <div class="flex flex-wrap items-center justify-center gap-3">
          <a
            :href="getWhatsAppUrl(isEnglish ? 'Hello 3 Putri Mulya, I would like to consult on custom routes and HiAce tour schedules in Bintan.' : 'Halo 3 Putri Mulya, saya ingin konsultasi rute khusus dan jadwal tour HiAce di Bintan.')"
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
            <span>{{ isEnglish ? 'Back to Home' : 'Kembali ke Beranda' }}</span>
          </RouterLink>
        </div>
      </div>

    </div>

    <!-- Fullscreen Lightbox / Zoom Modal -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isLightboxOpen && lightboxPkg"
        class="fixed inset-0 z-[100] bg-slate-950/95 backdrop-blur-xl flex flex-col justify-between select-none"
        @click.self="closeLightbox"
      >
        <!-- Lightbox Top Bar -->
        <div class="px-4 py-3 sm:px-6 sm:py-4 flex items-center justify-between border-b border-white/10 z-20 bg-slate-950/80 backdrop-blur-md">
          <div class="flex items-center gap-3">
            <div>
              <h3 class="text-sm sm:text-base font-bold text-white leading-tight">
                {{ isEnglish ? (lightboxPkg.titleEn || lightboxPkg.title) : lightboxPkg.title }}
              </h3>
              <p class="text-xs text-slate-400">
                {{ isEnglish ? (lightboxPkg.vehicleEn || lightboxPkg.vehicle) : lightboxPkg.vehicle }} &bull; {{ isEnglish ? `Photo ${lightboxPhotoIdx + 1} of ${currentLightboxPhotos.length}` : `Foto ${lightboxPhotoIdx + 1} dari ${currentLightboxPhotos.length}` }}
              </p>
            </div>
          </div>

          <!-- Controls: Zoom Controls & Close Button -->
          <div class="flex items-center gap-2">
            <!-- Zoom Controls -->
            <div class="flex items-center bg-white/10 rounded-xl p-0.5 border border-white/10">
              <button
                @click="zoomOut"
                type="button"
                :disabled="zoomLevel <= 1"
                class="p-1.5 sm:p-2 text-slate-300 hover:text-white disabled:opacity-30 disabled:hover:text-slate-300 transition-colors rounded-lg"
                :title="isEnglish ? 'Zoom Out (-)' : 'Perkecil (-)'"
              >
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/>
                </svg>
              </button>
              <span class="text-xs font-mono font-bold text-slate-200 px-1 sm:px-2 min-w-[42px] text-center">
                {{ Math.round(zoomLevel * 100) }}%
              </span>
              <button
                @click="zoomIn"
                type="button"
                :disabled="zoomLevel >= 3"
                class="p-1.5 sm:p-2 text-slate-300 hover:text-white disabled:opacity-30 disabled:hover:text-slate-300 transition-colors rounded-lg"
                :title="isEnglish ? 'Zoom In (+)' : 'Perbesar (+)'"
              >
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                </svg>
              </button>
            </div>

            <!-- Reset Zoom (if zoomed) -->
            <button
              v-if="zoomLevel > 1"
              @click="resetZoom"
              type="button"
              class="px-2.5 py-1.5 text-xs font-semibold rounded-xl bg-white/10 hover:bg-white/20 text-slate-200 transition-colors border border-white/10 hidden sm:inline-flex"
            >
              Reset
            </button>

            <!-- Close Button -->
            <button
              @click="closeLightbox"
              type="button"
              class="p-2 rounded-xl bg-white/10 hover:bg-red-500/80 text-white transition-colors border border-white/10 ml-1 sm:ml-2"
              :title="isEnglish ? 'Close (Esc)' : 'Tutup (Esc)'"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Lightbox Stage: Photo Display with Click to Zoom -->
        <div
          class="relative flex-1 flex items-center justify-center p-2 sm:p-6 overflow-hidden select-none"
          @click="toggleZoom"
        >
          <!-- Main Photo with Dynamic Transform Scale -->
          <div
            class="relative max-w-full max-h-full flex items-center justify-center transition-transform duration-200"
            :style="{ transform: `scale(${zoomLevel})`, cursor: zoomLevel > 1 ? 'zoom-out' : 'zoom-in' }"
          >
            <img
              :src="currentLightboxPhotos[lightboxPhotoIdx]"
              :alt="isEnglish ? (lightboxPkg.titleEn || lightboxPkg.title) : lightboxPkg.title"
              class="max-w-[92vw] max-h-[68vh] sm:max-h-[76vh] object-contain rounded-xl shadow-2xl transition-all"
            />
          </div>

          <!-- Prev & Next Floating Navigation Buttons -->
          <button
            v-if="currentLightboxPhotos.length > 1"
            @click.stop="prevLightboxPhoto"
            type="button"
            class="absolute left-2 sm:left-6 top-1/2 -translate-y-1/2 w-11 h-11 sm:w-13 sm:h-13 rounded-full bg-slate-900/80 hover:bg-blue-600 text-white backdrop-blur-md border border-white/20 shadow-xl flex items-center justify-center transition-all z-30 active:scale-95"
            :title="isEnglish ? 'Previous Photo' : 'Foto Sebelumnya'"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>

          <button
            v-if="currentLightboxPhotos.length > 1"
            @click.stop="nextLightboxPhoto"
            type="button"
            class="absolute right-2 sm:right-6 top-1/2 -translate-y-1/2 w-11 h-11 sm:w-13 sm:h-13 rounded-full bg-slate-900/80 hover:bg-blue-600 text-white backdrop-blur-md border border-white/20 shadow-xl flex items-center justify-center transition-all z-30 active:scale-95"
            :title="isEnglish ? 'Next Photo' : 'Foto Berikutnya'"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
          </button>

          <!-- Hint Pill at Bottom of Stage -->
          <div class="absolute bottom-3 left-1/2 -translate-x-1/2 pointer-events-none text-[11px] text-slate-400 bg-slate-950/60 backdrop-blur-xs px-3 py-1 rounded-full border border-white/10 hidden sm:block">
            {{ isEnglish ? `Click photo to ${zoomLevel > 1 ? 'zoom out' : 'zoom in'} • Use keyboard arrow keys ← →` : `Klik foto untuk ${zoomLevel > 1 ? 'memperkecil' : 'memperbesar (zoom)'} • Gunakan tombol panah keyboard ← →` }}
          </div>
        </div>

        <!-- Lightbox Bottom Bar: Thumbnails & Direct WhatsApp Booking -->
        <div class="px-4 py-3 sm:px-6 sm:py-3.5 border-t border-white/10 z-20 bg-slate-950/90 backdrop-blur-md flex flex-col sm:flex-row items-center justify-between gap-3">
          <!-- Thumbnails Strip -->
          <div class="flex items-center gap-2 overflow-x-auto max-w-full pb-1 sm:pb-0 scrollbar-none">
            <button
              v-for="(photo, idx) in currentLightboxPhotos"
              :key="idx"
              @click.stop="lightboxPhotoIdx = idx; resetZoom()"
              type="button"
              :class="lightboxPhotoIdx === idx ? 'ring-2 ring-blue-500 border-white scale-105 opacity-100' : 'opacity-50 hover:opacity-80 border-transparent'"
              class="w-14 h-10 sm:w-16 sm:h-11 rounded-lg overflow-hidden border-2 transition-all shrink-0 bg-slate-900"
            >
              <img :src="photo" class="w-full h-full object-cover" />
            </button>
          </div>

          <!-- Quick Booking CTA in Lightbox -->
          <div class="flex items-center gap-3 shrink-0">
            <span class="text-xs text-slate-300 hidden md:inline">{{ isEnglish ? 'Interested in this vehicle?' : 'Tertarik dengan unit ini?' }}</span>
            <a
              :href="getWhatsAppUrl(isEnglish ? `Hello 3 Putri Mulya, I would like to book the ${lightboxPkg.title} (${lightboxPkg.vehicle}) in Bintan.` : lightboxPkg.ctaWhatsappText)"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-md active:scale-95"
            >
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
              </svg>
              <span>{{ isEnglish ? 'Book via WhatsApp' : 'Pesan via WhatsApp' }}</span>
            </a>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>
