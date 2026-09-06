<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useFleet } from '@/composables/useFleet'
import { FLEET_BRACKETS, getVehicleCategoryBracket, isDriverMandatory, TOUR_PACKAGE_ROUTE } from '@/config/fleetCategories'
import { siteConfig } from '@/config/site'
import { generateVehicleWhatsAppUrl, generateGeneralWhatsAppUrl } from '@/utils/whatsapp'
import { getVehicleAngles, type VehiclePhotoAngle } from '@/utils/vehiclePhotos'
import type { PublicVehicle } from '@/types/fleet'

const {
  filteredVehicles,
  loading,
  error,
  searchQuery,
  transmissionFilter,
  categoryFilter,
  stats,
  fetchVehicles
} = useFleet()

// Active photo angle per vehicle card in the grid
const activeCardAngles = ref<Record<number, number>>({})

const getCardPhotoUrl = (car: PublicVehicle): string | null => {
  const angles = getVehicleAngles(car)
  if (angles.length === 0) return car.photo_url
  const idx = activeCardAngles.value[car.id] ?? 0
  return angles[idx]?.url || car.photo_url
}

const setCardAngle = (carId: number, idx: number) => {
  activeCardAngles.value[carId] = idx
}

// Modal Photo & Detail Preview state
const previewVehicle = ref<PublicVehicle | null>(null)
const previewAngleIdx = ref<number>(0)
const activeModalTab = ref<'photo' | 'video'>('photo')

const openPhotoModal = (car: PublicVehicle, initialAngleIdx = 0, tab: 'photo' | 'video' = 'photo') => {
  previewVehicle.value = car
  previewAngleIdx.value = initialAngleIdx
  activeModalTab.value = tab
}

const closePhotoModal = () => {
  previewVehicle.value = null
  previewAngleIdx.value = 0
  activeModalTab.value = 'photo'
}

const previewAngles = computed<VehiclePhotoAngle[]>(() => {
  if (!previewVehicle.value) return []
  return getVehicleAngles(previewVehicle.value)
})

const activePreviewUrl = computed<string | null>(() => {
  if (!previewVehicle.value) return null
  if (previewAngles.value.length > 0 && previewAngles.value[previewAngleIdx.value]) {
    return previewAngles.value[previewAngleIdx.value].url
  }
  return previewVehicle.value.photo_url
})

const nextModalAngle = () => {
  if (previewAngles.value.length <= 1) return
  previewAngleIdx.value = (previewAngleIdx.value + 1) % previewAngles.value.length
}

const prevModalAngle = () => {
  if (previewAngles.value.length <= 1) return
  previewAngleIdx.value = (previewAngleIdx.value - 1 + previewAngles.value.length) % previewAngles.value.length
}

const handleKeydown = (e: KeyboardEvent) => {
  if (e.key === 'Escape') {
    if (previewVehicle.value) closePhotoModal()
  } else if (previewVehicle.value && previewAngles.value.length > 1) {
    if (e.key === 'ArrowRight') nextModalAngle()
    if (e.key === 'ArrowLeft') prevModalAngle()
  }
}

watch(previewVehicle, (vehicle) => {
  if (typeof document === 'undefined') return
  if (vehicle) {
    document.body.classList.add('overflow-hidden')
  } else {
    document.body.classList.remove('overflow-hidden')
  }
})

onMounted(() => {
  document.title = 'Katalog Lengkap Armada — Sewa Mobil & Bus Pariwisata Bintan | 3 Putri Mulya'
  window.scrollTo({ top: 0, behavior: 'smooth' })
  fetchVehicles()
  if (typeof window !== 'undefined') {
    window.addEventListener('keydown', handleKeydown)
  }
})

onUnmounted(() => {
  if (typeof window !== 'undefined') {
    window.removeEventListener('keydown', handleKeydown)
    document.body.classList.remove('overflow-hidden')
  }
})

const generalWaUrl = computed(() => generateGeneralWhatsAppUrl(siteConfig.rentalPhone, siteConfig.rentalName))
</script>

<template>
  <div class="bg-slate-50 min-h-screen py-5 sm:py-12">
    <div class="max-w-7xl mx-auto px-3.5 sm:px-6 lg:px-8">

      <!-- Breadcrumb Navigation -->
      <nav class="flex items-center gap-1.5 sm:gap-2 text-[11px] sm:text-xs text-slate-500 mb-3 sm:mb-6" aria-label="Breadcrumb">
        <RouterLink to="/" class="hover:text-blue-600 transition-colors">Beranda</RouterLink>
        <span>/</span>
        <span class="text-slate-900 font-semibold">Katalog Armada</span>
      </nav>

      <!-- Page Header -->
      <header class="max-w-3xl mb-4 sm:mb-8">
        <div class="inline-flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-[11px] sm:text-xs font-semibold mb-2 sm:mb-3">
          <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
          </svg>
          <span>Etalase Lengkap Armada 3 Putri Mulya</span>
        </div>

        <h1 class="text-xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-[1.2] mb-1.5 sm:mb-3">
          Pilihan Sewa Mobil &amp; Bus Pariwisata
        </h1>

        <p class="text-xs sm:text-base text-slate-600 leading-relaxed">
          Temukan kendaraan yang tepat untuk kebutuhan Anda di Pulau Bintan. Tersedia city car hemat, MPV keluarga lega, hingga minibus HiAce dan bus pariwisata untuk rombongan besar.
        </p>
      </header>

      <!-- 1. Category Filter Tabs (Single Source of Truth: FLEET_BRACKETS) -->
      <div class="mb-6 overflow-x-auto pb-1">
        <div class="flex items-center gap-2 min-w-max">
          <button
            @click="categoryFilter = 'all'"
            type="button"
            :class="categoryFilter === 'all'
              ? 'bg-blue-600 text-white font-bold shadow-sm'
              : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
            class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all shrink-0"
          >
            Semua Armada ({{ stats.total }})
          </button>

          <button
            v-for="bracket in FLEET_BRACKETS"
            :key="bracket.id"
            @click="categoryFilter = bracket.id"
            type="button"
            :class="categoryFilter === bracket.id
              ? 'bg-blue-600 text-white font-bold shadow-sm'
              : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
            class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all shrink-0 flex items-center gap-1.5"
          >
            <span>{{ bracket.label }}</span>
            <span
              v-if="bracket.driverPolicy === 'with_driver'"
              class="text-[10px] px-1.5 py-0.5 rounded-full"
              :class="categoryFilter === bracket.id ? 'bg-blue-700 text-white' : 'bg-amber-100 text-amber-800'"
            >
              +Supir
            </span>
          </button>
        </div>
      </div>

      <!-- 2. Search & Secondary Transmission Filter Bar -->
      <div class="bg-white rounded-xl sm:rounded-2xl border border-slate-200 p-3 sm:p-5 mb-5 sm:mb-8 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-2.5 sm:gap-3.5">
        <!-- Search Box -->
        <div class="relative w-full sm:w-80">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </div>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari tipe mobil (misal: Veloz, Agya, HiAce)..."
            class="w-full pl-8 sm:pl-9 pr-3 sm:pr-4 py-1.5 sm:py-2 rounded-lg sm:rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
          />
        </div>

        <!-- Filter Dropdown & Status Counter -->
        <div class="w-full sm:w-auto flex items-center justify-between sm:justify-end gap-2 sm:gap-3 shrink-0">
          <!-- Transmission Select -->
          <div class="flex items-center gap-1.5 sm:gap-2 text-xs">
            <span class="text-slate-500 font-medium hidden md:inline">Transmisi:</span>
            <select
              v-model="transmissionFilter"
              class="px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-lg sm:rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-xs sm:text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
            >
              <option value="all">Semua Transmisi</option>
              <option value="matic">Matic</option>
              <option value="manual">Manual</option>
            </select>
          </div>

          <!-- Refresh / Live Tracker -->
          <button
            @click="fetchVehicles(true)"
            type="button"
            class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-lg sm:rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:text-blue-600 hover:bg-slate-50 transition-colors"
            title="Perbarui status armada"
          >
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Refresh</span>
          </button>
        </div>
      </div>

      <!-- 3. Fleet Grid Section -->
      <div>
        <!-- Loading State -->
        <div v-if="loading" class="py-16 text-center">
          <div class="inline-block w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mb-3"></div>
          <p class="text-sm font-semibold text-slate-600">Memuat data armada...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="py-12 px-6 bg-red-50 border border-red-200 rounded-2xl text-center max-w-lg mx-auto">
          <p class="text-sm font-semibold text-red-700 mb-2">{{ error }}</p>
          <button
            @click="fetchVehicles(true)"
            type="button"
            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold transition-colors"
          >
            Coba Lagi
          </button>
        </div>

        <!-- Empty Filter State -->
        <div v-else-if="filteredVehicles.length === 0" class="py-16 text-center bg-white rounded-3xl border border-slate-200 p-8">
          <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
          </svg>
          <h3 class="text-base font-bold text-slate-800 mb-1">Unit Tidak Ditemukan</h3>
          <p class="text-xs text-slate-500 max-w-sm mx-auto mb-4">
            Tidak ada unit yang sesuai dengan kriteria pencarian atau kategori ini.
          </p>
          <button
            @click="searchQuery = ''; transmissionFilter = 'all'; categoryFilter = 'all'"
            type="button"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-colors"
          >
            Reset Filter
          </button>
        </div>

        <!-- Vehicles Grid (Etalase 2-Kolom di Mobile, 3-Kolom di Desktop) -->
        <div v-else class="grid grid-cols-2 lg:grid-cols-3 gap-2.5 sm:gap-6">
          <article
            v-for="car in filteredVehicles"
            :key="car.id"
            class="bg-white rounded-xl sm:rounded-2xl border border-slate-200/90 overflow-hidden flex flex-col justify-between hover:border-slate-300 hover:shadow-md transition-all group"
          >
            <!-- Vehicle Photo Container -->
            <div
              class="relative aspect-[4/3] sm:aspect-[16/10] bg-slate-100 overflow-hidden cursor-pointer"
              @click="openPhotoModal(car, activeCardAngles[car.id] ?? 0)"
              title="Klik untuk melihat detail foto unit"
            >
              <img
                v-if="getCardPhotoUrl(car)"
                :src="getCardPhotoUrl(car)!"
                :alt="car.name"
                loading="lazy"
                decoding="async"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
              <!-- Fallback -->
              <div
                v-else
                class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-slate-50"
              >
                <svg class="w-8 h-8 sm:w-10 sm:h-10 mb-1 text-slate-300" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                </svg>
                <span class="text-[9px] sm:text-[11px] text-slate-400 font-medium">Foto Belum Tersedia</span>
              </div>

              <!-- Top-Left Badges: Status & Driver Policy -->
              <div class="absolute top-1.5 left-1.5 sm:top-2.5 sm:left-2.5 z-10 flex flex-col gap-1 items-start">
                <!-- Status Badge -->
                <span
                  v-if="car.status === 'available'"
                  class="inline-flex items-center gap-1 sm:gap-1.5 px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded-full text-[9px] sm:text-xs font-bold bg-emerald-600 text-white shadow-xs"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                  <span>Tersedia</span>
                </span>
                <span
                  v-else-if="car.status === 'maintenance'"
                  class="inline-flex items-center gap-1 sm:gap-1.5 px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded-full text-[9px] sm:text-xs font-bold bg-amber-500 text-white shadow-xs"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                  <span>Perawatan</span>
                </span>
                <span
                  v-else-if="car.status === 'rented'"
                  class="inline-flex items-center gap-1 sm:gap-1.5 px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded-full text-[9px] sm:text-xs font-semibold bg-slate-900/80 text-slate-200 backdrop-blur-xs shadow-xs"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                  <span>Disewa</span>
                </span>

                <!-- Mandatory Driver Badge for HiAce & Bus -->
                <span
                  v-if="isDriverMandatory(car.capacity)"
                  class="inline-flex items-center gap-0.5 sm:gap-1 px-1.5 py-0.5 rounded text-[8px] sm:text-[10px] font-bold bg-indigo-600 text-white shadow-xs"
                >
                  <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                  <span>+Supir</span>
                </span>
              </div>

              <!-- Multi-angle Switcher Pills (Bottom Right of photo) -->
              <div
                v-if="getVehicleAngles(car).length > 1"
                class="absolute bottom-1.5 right-1.5 sm:bottom-2 sm:right-2 z-10 flex items-center gap-0.5 sm:gap-1 bg-slate-900/85 backdrop-blur-xs p-0.5 rounded sm:rounded-md border border-slate-700/80 shadow-xs"
              >
                <button
                  v-for="(angle, aIdx) in getVehicleAngles(car)"
                  :key="angle.id"
                  @click.stop="setCardAngle(car.id, aIdx)"
                  type="button"
                  :class="(activeCardAngles[car.id] ?? 0) === aIdx ? 'bg-blue-600 text-white font-bold' : 'text-slate-300 hover:text-white'"
                  class="px-1 sm:px-1.5 py-0.5 rounded text-[8px] sm:text-[10px] transition-colors leading-none"
                  :title="'Sudut ' + angle.label"
                >
                  {{ angle.label }}
                </button>
              </div>
            </div>

            <!-- Card Content -->
            <div class="p-2.5 sm:p-5 flex-1 flex flex-col justify-between">
              <div>
                <!-- Category Tag & Year -->
                <div class="flex items-center justify-between text-[9px] sm:text-[11px] text-slate-500 mb-1 font-medium">
                  <span class="text-blue-600 font-bold uppercase tracking-wider line-clamp-1">
                    {{ getVehicleCategoryBracket(car.capacity).shortLabel }}
                  </span>
                  <span class="text-slate-400 shrink-0 ml-1">’{{ String(car.model_year).slice(-2) }}</span>
                </div>

                <!-- Car Name -->
                <h3
                  class="text-xs sm:text-lg font-bold text-slate-900 leading-snug line-clamp-1 cursor-pointer hover:text-blue-600 transition-colors"
                  @click="openPhotoModal(car, activeCardAngles[car.id] ?? 0)"
                  :title="car.name"
                >
                  {{ car.name }}
                </h3>

                <!-- Specifications Chips -->
                <div class="flex items-center flex-wrap gap-1 sm:gap-2 text-[10px] sm:text-xs text-slate-500 mt-1.5 sm:mt-2.5 pt-1.5 sm:pt-2 border-t border-slate-100">
                  <span class="inline-flex items-center gap-1 font-medium">
                    <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>{{ car.capacity }} Kursi</span>
                  </span>

                  <span class="text-slate-300">&bull;</span>

                  <span class="inline-flex items-center gap-1 font-medium">
                    <span>{{ car.transmission_label }}</span>
                  </span>

                  <span class="hidden sm:inline text-slate-300">&bull;</span>

                  <span class="hidden sm:inline-flex items-center gap-1 font-medium">
                    <span>{{ car.fuel_type_label }}</span>
                  </span>
                </div>

                <!-- Cross-selling Banner for HiAce/Bus (Compact on mobile) -->
                <div
                  v-if="isDriverMandatory(car.capacity)"
                  class="mt-2 sm:mt-3 p-1.5 sm:p-2.5 rounded-lg sm:rounded-xl bg-blue-50/70 border border-blue-100 text-[10px] sm:text-[11px] text-blue-900 leading-tight"
                >
                  <p class="font-medium hidden sm:block">
                    Butuh paket wisata keliling Bintan lengkap Supir + BBM + Karaoke?
                  </p>
                  <RouterLink
                    :to="TOUR_PACKAGE_ROUTE"
                    class="font-bold text-blue-700 hover:text-blue-900 inline-flex items-center gap-0.5 underline text-[10px] sm:text-[11px]"
                  >
                    <span>Paket Tour All-In &rarr;</span>
                  </RouterLink>
                </div>
              </div>

              <!-- Price & Button Area -->
              <div class="pt-2 sm:pt-3 mt-2 sm:mt-3 border-t border-slate-100 flex flex-col gap-1.5 sm:gap-2.5">
                <!-- Price Row -->
                <div class="flex flex-col">
                  <div class="flex items-baseline gap-1">
                    <span class="text-xs sm:text-xl font-black text-slate-900 tracking-tight leading-none">
                      {{ car.daily_rate_formatted }}
                    </span>
                    <span class="text-[9px] sm:text-xs text-slate-500 font-normal leading-none">/hari</span>
                  </div>
                  <span v-if="isDriverMandatory(car.capacity)" class="text-[9px] sm:text-[10px] text-slate-400 mt-0.5 sm:mt-1 font-medium line-clamp-1">
                    *Termasuk supir
                  </span>
                </div>

                <!-- Action Button -->
                <a
                  v-if="car.status === 'available'"
                  :href="generateVehicleWhatsAppUrl(car, siteConfig.rentalPhone, siteConfig.rentalName)"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="w-full h-8 sm:h-10 px-2 sm:px-3 rounded-lg sm:rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] sm:text-xs font-bold whitespace-nowrap inline-flex items-center justify-center gap-1 sm:gap-1.5 transition-all shadow-xs hover:shadow-sm active:scale-95"
                >
                  <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                  </svg>
                  <span class="sm:hidden">Pesan</span>
                  <span class="hidden sm:inline">Pesan via WhatsApp</span>
                </a>

                <button
                  v-else-if="car.status === 'maintenance'"
                  disabled
                  class="w-full h-8 sm:h-10 px-2 sm:px-3 rounded-lg sm:rounded-xl bg-slate-100 text-slate-400 border border-slate-200 text-[10px] sm:text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center cursor-not-allowed"
                >
                  <span>Perawatan</span>
                </button>

                <a
                  v-else-if="car.status === 'rented'"
                  :href="generateVehicleWhatsAppUrl(car, siteConfig.rentalPhone, siteConfig.rentalName)"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="w-full h-8 sm:h-10 px-2 sm:px-3 rounded-lg sm:rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-[10px] sm:text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center transition-colors"
                >
                  <span class="sm:hidden">Jadwal Lain</span>
                  <span class="hidden sm:inline">Tanya Jadwal Lain</span>
                </a>
              </div>
            </div>
          </article>
        </div>
      </div>

      <!-- 4. Bottom Cross-Selling Charter & Tour Banner -->
      <div class="mt-12 sm:mt-16 bg-gradient-to-br from-blue-900 via-slate-900 to-indigo-950 rounded-3xl p-6 sm:p-10 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-2xl">
          <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-bold border border-blue-400/30 uppercase tracking-wider">
            Solusi Rombongan &amp; Acara Khusus
          </span>
          <h3 class="text-2xl sm:text-3xl font-extrabold text-white mt-3 leading-snug">
            Butuh Sewa HiAce atau Bus Pariwisata untuk Rombongan?
          </h3>
          <p class="text-sm sm:text-base text-slate-300 mt-2.5 leading-relaxed">
            Kami siap melayani kebutuhan gathering kantor, study tour sekolah, antar-jemput kontingen, hingga paket wisata keliling Bintan dengan fasilitas Karaoke System dan supir berpengalaman.
          </p>

          <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3.5 mt-6">
            <RouterLink
              :to="TOUR_PACKAGE_ROUTE"
              class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs sm:text-sm font-bold shadow-md transition-all text-center"
            >
              Lihat Paket Tour Bintan All-In &rarr;
            </RouterLink>

            <a
              :href="generalWaUrl"
              target="_blank"
              rel="noopener noreferrer"
              class="px-6 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs sm:text-sm font-semibold border border-white/20 transition-all text-center flex items-center justify-center gap-2"
            >
              <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/></svg>
              <span>Konsultasi Bus / Rombongan</span>
            </a>
          </div>
        </div>
      </div>

    </div>

    <!-- Vehicle Details & Photo Gallery Modal Dialog -->
    <div
      v-if="previewVehicle"
      class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4 md:p-6 bg-slate-950/80 backdrop-blur-sm transition-all"
      @click.self="closePhotoModal"
    >
      <div
        class="bg-white rounded-2xl sm:rounded-3xl overflow-hidden w-full max-w-4xl lg:max-w-5xl xl:max-w-6xl border border-slate-200 shadow-2xl text-slate-900 flex flex-col md:flex-row md:max-h-[88vh] max-h-[94vh] animate-in fade-in zoom-in-95 duration-200"
      >
        <!-- 1. LEFT COLUMN: Media Showcase Stage -->
        <div class="md:w-7/12 lg:w-3/5 bg-slate-950 flex flex-col justify-between relative overflow-hidden p-4 sm:p-5 lg:p-6 select-none border-b md:border-b-0 md:border-r border-slate-800 shrink-0">
          
          <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-slate-800/40 via-slate-950 to-black pointer-events-none"></div>

          <!-- Top Stage Bar -->
          <div class="relative z-10 flex items-center justify-between gap-2">
            <div>
              <span
                v-if="previewVehicle.status === 'available'"
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-600/95 text-white backdrop-blur-md shadow-md"
              >
                <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                <span>Tersedia Siap Jalan</span>
              </span>
              <span
                v-else-if="previewVehicle.status === 'maintenance'"
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500 text-white backdrop-blur-md shadow-md"
              >
                <span class="w-2 h-2 rounded-full bg-white"></span>
                <span>Perawatan</span>
              </span>
              <span
                v-else-if="previewVehicle.status === 'rented'"
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-800/90 text-slate-200 backdrop-blur-md shadow-md"
              >
                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                <span>Sedang Disewa</span>
              </span>
            </div>

            <div class="flex items-center gap-2">
              <a
                v-if="activePreviewUrl"
                :href="activePreviewUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="w-8 h-8 rounded-full bg-slate-900/80 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-700/60 backdrop-blur-md flex items-center justify-center transition-colors"
                title="Buka foto resolusi penuh"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
              </a>

              <button
                @click="closePhotoModal"
                class="md:hidden w-8 h-8 rounded-full bg-slate-900/80 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-700/60 backdrop-blur-md flex items-center justify-center transition-colors"
                type="button"
                title="Tutup dialog"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Photo Stage Center -->
          <div class="relative z-10 my-auto py-2 sm:py-5 flex items-center justify-center min-h-[220px] sm:min-h-[300px] md:min-h-[360px] lg:min-h-[420px]">
            <div class="relative w-full h-full flex items-center justify-center group">
              <img
                v-if="activePreviewUrl"
                :src="activePreviewUrl"
                :alt="previewVehicle.name"
                class="w-full h-full max-h-[260px] sm:max-h-[340px] md:max-h-[420px] object-contain drop-shadow-[0_20px_35px_rgba(0,0,0,0.6)] transition-all duration-300"
              />

              <button
                v-if="previewAngles.length > 1"
                @click="prevModalAngle"
                type="button"
                class="absolute left-0 sm:left-1 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-slate-900/80 hover:bg-slate-800 text-white backdrop-blur-md flex items-center justify-center transition-all border border-slate-700/80 shadow-lg"
                title="Sudut sebelumnya"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
              </button>
              <button
                v-if="previewAngles.length > 1"
                @click="nextModalAngle"
                type="button"
                class="absolute right-0 sm:right-1 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-slate-900/80 hover:bg-slate-800 text-white backdrop-blur-md flex items-center justify-center transition-all border border-slate-700/80 shadow-lg"
                title="Sudut berikutnya"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
              </button>
            </div>
          </div>

          <!-- Bottom Gallery Navigation -->
          <div class="relative z-10 pt-3 border-t border-slate-800/80 flex flex-col items-center gap-1.5">
            <div v-if="previewAngles.length > 1" class="flex items-center justify-center gap-2 flex-wrap">
              <button
                v-for="(angle, idx) in previewAngles"
                :key="angle.id"
                @click="previewAngleIdx = idx"
                type="button"
                :class="previewAngleIdx === idx
                  ? 'border-blue-500 bg-blue-600 text-white font-bold shadow-md ring-2 ring-blue-500/50'
                  : 'border-slate-700/80 bg-slate-900/80 text-slate-300 hover:text-white hover:border-slate-600'"
                class="px-3 py-1 rounded-lg border text-xs font-semibold flex items-center gap-1.5 transition-all"
              >
                <span>{{ angle.label }}</span>
              </button>
            </div>
            <div class="text-[11px] text-slate-400 font-medium text-center">
              <span v-if="previewAngles.length > 1">Pilih sudut untuk melihat foto dari sisi lain</span>
              <span v-else>Foto asli armada kami &bull; Unit terawat siap jalan</span>
            </div>
          </div>
        </div>

        <!-- 2. RIGHT COLUMN: Details & Actions -->
        <div class="md:w-5/12 lg:w-2/5 flex flex-col justify-between bg-white overflow-hidden">
          <div class="p-5 sm:p-6 pb-4 border-b border-slate-100 flex items-start justify-between gap-3 shrink-0">
            <div>
              <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[11px] font-bold uppercase tracking-wider">
                  {{ getVehicleCategoryBracket(previewVehicle.capacity).shortLabel }}
                </span>
                <span class="text-xs text-slate-400 font-medium">&bull; Tahun {{ previewVehicle.model_year }}</span>
              </div>
              <h3 class="text-xl sm:text-2xl font-black text-slate-900 leading-tight mt-1">
                {{ previewVehicle.name }}
              </h3>
            </div>

            <button
              @click="closePhotoModal"
              class="hidden md:flex w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 items-center justify-center transition-colors shrink-0"
              type="button"
              title="Tutup dialog (Esc)"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>

          <!-- Body -->
          <div class="overflow-y-auto p-5 sm:p-6 space-y-4 flex-1">
            <!-- 2x2 Specs Grid -->
            <div class="grid grid-cols-2 gap-2.5">
              <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 flex flex-col justify-center">
                <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Kapasitas</span>
                <div class="flex items-center gap-1.5 mt-1">
                  <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                  <span class="text-xs sm:text-sm font-bold text-slate-800">{{ previewVehicle.capacity }} Kursi</span>
                </div>
              </div>

              <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 flex flex-col justify-center">
                <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Transmisi</span>
                <div class="flex items-center gap-1.5 mt-1">
                  <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                  <span class="text-xs sm:text-sm font-bold text-slate-800">{{ previewVehicle.transmission_label }}</span>
                </div>
              </div>

              <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 flex flex-col justify-center">
                <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Bahan Bakar</span>
                <div class="flex items-center gap-1.5 mt-1">
                  <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                  <span class="text-xs sm:text-sm font-bold text-slate-800">{{ previewVehicle.fuel_type_label }}</span>
                </div>
              </div>

              <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 flex flex-col justify-center">
                <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Kondisi</span>
                <div class="flex items-center gap-1.5 mt-1">
                  <svg v-if="previewVehicle.status === 'maintenance'" class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  <svg v-else class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  <span class="text-xs sm:text-sm font-bold" :class="previewVehicle.status === 'maintenance' ? 'text-amber-700' : 'text-slate-800'">
                    {{ previewVehicle.status === 'maintenance' ? 'Perawatan' : 'Siap Jalan' }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Description Box -->
            <div v-if="previewVehicle.description" class="bg-blue-50/50 border border-blue-100/80 rounded-xl p-3.5">
              <h5 class="text-[11px] font-bold text-blue-900 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                Catatan &amp; Deskripsi Unit
              </h5>
              <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">{{ previewVehicle.description }}</p>
            </div>

            <!-- Cross Link if HiAce/Bus -->
            <div v-if="isDriverMandatory(previewVehicle.capacity)" class="rounded-xl border border-indigo-200 bg-indigo-50/60 p-3.5 text-xs text-indigo-950">
              <p class="font-bold mb-1">Butuh Paket Tour Wisata All-In?</p>
              <p class="text-indigo-800 leading-relaxed">
                Armada ini tersedia dalam paket tour wisata Pulau Bintan sudah termasuk Supir, BBM, dan fasilitas hiburan Karaoke System.
              </p>
              <RouterLink :to="TOUR_PACKAGE_ROUTE" class="inline-flex items-center gap-1 font-bold text-indigo-700 hover:text-indigo-900 mt-2 underline">
                Lihat Detail Paket Tour Bintan &rarr;
              </RouterLink>
            </div>
          </div>

          <!-- Bottom Action Bar -->
          <div class="p-4 sm:p-5 bg-white border-t border-slate-100 flex items-center justify-between gap-3 shrink-0">
            <div>
              <span class="text-[10px] text-slate-400 block font-medium">Tarif Sewa Harian</span>
              <div class="text-base sm:text-lg font-extrabold text-slate-900 leading-tight">
                {{ previewVehicle.daily_rate_formatted }}
                <span class="text-xs font-normal text-slate-500">/hari</span>
              </div>
            </div>

            <a
              v-if="previewVehicle.status === 'available'"
              :href="generateVehicleWhatsAppUrl(previewVehicle, siteConfig.rentalPhone, siteConfig.rentalName)"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs sm:text-sm shadow-sm transition-all active:scale-95"
            >
              <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/></svg>
              <span>Pesan via WhatsApp</span>
            </a>

            <div
              v-else-if="previewVehicle.status === 'maintenance'"
              class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-100 text-slate-600 border border-slate-200 text-xs font-semibold"
            >
              <span class="w-2 h-2 rounded-full bg-amber-500"></span>
              <span>Perawatan</span>
            </div>

            <a
              v-else
              :href="generateVehicleWhatsAppUrl(previewVehicle, siteConfig.rentalPhone, siteConfig.rentalName)"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold text-xs transition-colors"
            >
              <span>Tanya Jadwal Lain</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
