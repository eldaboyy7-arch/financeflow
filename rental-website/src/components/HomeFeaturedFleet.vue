<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { RouterLink } from 'vue-router'
import type { PublicVehicle } from '@/types/fleet'
import { siteConfig } from '@/config/site'
import { generateVehicleWhatsAppUrl } from '@/utils/whatsapp'
import { getVehicleAngles, type VehiclePhotoAngle } from '@/utils/vehiclePhotos'
import { getVehicleCategoryBracket } from '@/config/fleetCategories'

withDefaults(
  defineProps<{
    vehicles: PublicVehicle[]
    loading: boolean
    error: string | null
    totalFleetCount?: number
  }>(),
  {
    totalFleetCount: 0
  }
)

const emit = defineEmits<{
  (e: 'retry'): void
}>()

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

const openPhotoModal = (car: PublicVehicle, initialAngleIdx = 0) => {
  previewVehicle.value = car
  previewAngleIdx.value = initialAngleIdx
}

const closePhotoModal = () => {
  previewVehicle.value = null
  previewAngleIdx.value = 0
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
</script>

<template>
  <section id="armada" class="py-14 sm:py-20 bg-slate-50/60 border-t border-slate-200/80 scroll-mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <!-- Header Section: Tanpa Sidebar / Filter Kontrol (Full Width Travel Showcase) -->
      <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
        <div>
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-xs font-bold mb-3">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
            <span>Armada Pilihan Terpopuler</span>
          </div>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">
            Unit Favorit Perjalanan di Bintan
          </h2>
          <p class="text-sm text-slate-500 mt-1.5 max-w-xl">
            Paling diminati untuk keliling santai, wisata keluarga, maupun dinas. Unit terawat, AC dingin, dan siap jalan.
          </p>
        </div>

        <!-- Link Desktop ke Etalase Penuh -->
        <RouterLink
          to="/armada"
          class="hidden sm:inline-flex items-center gap-1.5 text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors shrink-0 group"
        >
          <span>Lihat Semua Armada ({{ totalFleetCount > 0 ? `${totalFleetCount} Unit` : 'Lengkap' }})</span>
          <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
          </svg>
        </RouterLink>
      </div>

      <!-- Loading State -->
      <div
        v-if="loading && vehicles.length === 0"
        class="flex gap-4 overflow-x-auto pb-4 pt-1 snap-x snap-mandatory -mx-4 px-4 sm:mx-0 sm:px-0 lg:grid lg:grid-cols-3 lg:gap-6 lg:overflow-visible lg:pb-0 scrollbar-none"
      >
        <div
          v-for="i in 3"
          :key="i"
          class="snap-start shrink-0 w-[78vw] max-w-[310px] sm:w-[320px] lg:w-auto bg-white rounded-2xl border border-slate-200 overflow-hidden animate-pulse"
        >
          <div class="aspect-[16/10] bg-slate-200"></div>
          <div class="p-4 sm:p-5 space-y-3">
            <div class="h-4 bg-slate-200 rounded w-3/4"></div>
            <div class="h-3 bg-slate-100 rounded w-1/2"></div>
            <div class="h-9 bg-slate-200 rounded-xl w-full mt-4"></div>
          </div>
        </div>
      </div>

      <!-- Error State -->
      <div
        v-else-if="error"
        class="rounded-2xl bg-red-50 border border-red-200 p-8 text-center text-red-700 my-6"
      >
        <p class="font-bold text-base mb-2">{{ error }}</p>
        <p class="text-xs text-red-600 mb-4">Pastikan server backend sedang berjalan.</p>
        <button
          @click="emit('retry')"
          type="button"
          class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors"
        >
          Coba Muat Ulang
        </button>
      </div>

      <!-- 3 Featured Vehicle Cards: Horizontal Scroll di Mobile (Mirip Inspirasi Destinasi) & Grid 3 Kolom di Desktop -->
      <div
        v-else
        class="flex gap-4 overflow-x-auto pb-4 pt-1 snap-x snap-mandatory -mx-4 px-4 sm:mx-0 sm:px-0 lg:grid lg:grid-cols-3 lg:gap-6 lg:overflow-visible lg:pb-0 scrollbar-none"
      >
        <article
          v-for="car in vehicles"
          :key="car.id"
          class="snap-start shrink-0 w-[78vw] max-w-[310px] sm:w-[320px] lg:w-auto bg-white rounded-2xl border border-slate-200/90 overflow-hidden flex flex-col justify-between hover:border-slate-300 hover:shadow-lg transition-all duration-200 group"
        >
          <!-- Vehicle Photo Stage -->
          <div class="relative aspect-[16/10] bg-slate-100 overflow-hidden cursor-pointer select-none"
            @click="openPhotoModal(car, activeCardAngles[car.id] ?? 0)"
            title="Klik untuk melihat foto resolusi penuh"
          >
            <img
              v-if="getCardPhotoUrl(car)"
              :src="getCardPhotoUrl(car)!"
              :alt="car.name"
              loading="lazy"
              decoding="async"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            />
            <div
              v-else
              class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-slate-50"
            >
              <svg class="w-10 h-10 mb-1 text-slate-300" fill="currentColor" viewBox="0 0 24 24">
                <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
              </svg>
              <span class="text-[11px]">Foto unit segera hadir</span>
            </div>

            <!-- Status Badge (Top-Left) -->
            <div class="absolute top-2.5 left-2.5 z-10">
              <span
                v-if="car.status === 'available'"
                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold bg-emerald-600/95 text-white shadow-xs backdrop-blur-xs"
              >
                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                <span>Tersedia</span>
              </span>
              <span
                v-else-if="car.status === 'maintenance'"
                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold bg-amber-500 text-white shadow-xs backdrop-blur-xs"
              >
                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                <span>Perawatan</span>
              </span>
              <span
                v-else
                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-semibold bg-slate-800/90 text-slate-200 shadow-xs backdrop-blur-xs"
              >
                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                <span>{{ car.status_label || 'Tidak Tersedia' }}</span>
              </span>
            </div>

            <!-- Multi-Angle Badge Switcher (Bottom-Right) -->
            <div
              v-if="getVehicleAngles(car).length > 1"
              class="absolute bottom-2 right-2 z-10 flex items-center gap-1 bg-slate-950/75 backdrop-blur-md px-1.5 py-0.5 rounded-lg border border-white/15"
              @click.stop
            >
              <button
                v-for="(angle, aIdx) in getVehicleAngles(car)"
                :key="angle.id"
                @click.stop="setCardAngle(car.id, aIdx)"
                type="button"
                :class="[
                  (activeCardAngles[car.id] ?? 0) === aIdx
                    ? 'bg-blue-600 text-white font-bold'
                    : 'text-slate-300 hover:text-white font-medium',
                  'px-1.5 sm:px-2 py-0.5 rounded text-[9px] sm:text-[10px] leading-tight transition-all'
                ]"
              >
                {{ angle.label }}
              </button>
            </div>

            <!-- Zoom Icon Button (Top-Right) -->
            <button
              @click.stop="openPhotoModal(car, activeCardAngles[car.id] ?? 0)"
              type="button"
              class="absolute top-2.5 right-2.5 z-10 w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-black/50 hover:bg-black/80 text-white flex items-center justify-center backdrop-blur-xs transition-colors shadow-xs"
              title="Perbesar foto unit"
            >
              <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
              </svg>
            </button>
          </div>

          <!-- Card Content Body -->
          <div class="p-4 sm:p-5 flex flex-col justify-between flex-1">
            <div>
              <!-- Category & Year Sub-tag -->
              <div class="flex items-center justify-between text-xs text-slate-400 mb-1">
                <span class="font-bold text-[10px] sm:text-[11px] uppercase tracking-wider text-blue-600">
                  {{ getVehicleCategoryBracket(car.capacity).shortLabel }}
                </span>
                <span class="text-[10px] sm:text-[11px] text-slate-500 font-medium">
                  Tahun {{ car.model_year }}
                </span>
              </div>

              <!-- Vehicle Name -->
              <h3 class="font-display text-base sm:text-lg font-extrabold text-slate-900 tracking-tight leading-snug line-clamp-1 group-hover:text-blue-600 transition-colors">
                {{ car.name }}
              </h3>

              <!-- Specification Badges -->
              <div class="mt-2.5 sm:mt-3 flex flex-wrap items-center gap-1.5 sm:gap-2 text-[11px] sm:text-xs text-slate-600">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-md bg-slate-100 font-medium">
                  <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                  <span>{{ car.capacity }} Kursi</span>
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-md bg-slate-100 font-medium">
                  <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                  </svg>
                  <span>{{ car.transmission_label }}</span>
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-md bg-slate-100 font-medium">
                  <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                  </svg>
                  <span>{{ car.fuel_type_label }}</span>
                </span>
              </div>
            </div>

            <!-- Price & Booking CTA -->
            <div class="mt-4 sm:mt-6 pt-3 sm:pt-4 border-t border-slate-100">
              <div class="flex items-baseline justify-between mb-2 sm:mb-3">
                <span class="text-[11px] sm:text-xs text-slate-400 font-medium">Mulai dari</span>
                <div class="text-right">
                  <span class="font-display text-base sm:text-lg font-black text-slate-900 tracking-tight">{{ car.daily_rate_formatted }}</span>
                  <span class="text-[11px] sm:text-xs text-slate-500 font-normal"> /hari</span>
                </div>
              </div>

              <!-- Action Button -->
              <a
                v-if="car.status === 'available'"
                :href="generateVehicleWhatsAppUrl(car, siteConfig.rentalPhone, siteConfig.rentalName)"
                target="_blank"
                rel="noopener noreferrer"
                class="w-full h-9 sm:h-11 px-3 sm:px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold inline-flex items-center justify-center gap-1.5 shadow-xs hover:shadow-emerald-600/25 transition-all active:scale-98"
              >
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                </svg>
                <span>Pesan via WhatsApp</span>
              </a>

              <button
                v-else-if="car.status === 'maintenance'"
                disabled
                class="w-full h-9 sm:h-11 px-3 sm:px-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-xs font-bold inline-flex items-center justify-center gap-1.5 cursor-not-allowed select-none"
              >
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                <span>Perawatan</span>
              </button>

              <button
                v-else
                disabled
                class="w-full h-9 sm:h-11 px-3 sm:px-4 rounded-xl bg-slate-100 text-slate-400 text-xs font-medium inline-flex items-center justify-center cursor-not-allowed"
              >
                <span>{{ car.status_label || 'Tidak Tersedia' }}</span>
              </button>
            </div>
          </div>
        </article>
      </div>

      <!-- Mobile Hint & Link (di bawah slider mobile, mirip Inspirasi Destinasi) -->
      <div class="lg:hidden mt-3 flex items-center justify-between text-xs text-slate-500 px-1">
        <span class="inline-flex items-center gap-1.5 text-[11px] text-slate-400 font-medium">
          <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
          </svg>
          <span>Geser untuk melihat unit</span>
        </span>
        <RouterLink
          to="/armada"
          class="font-bold text-blue-600 hover:text-blue-800 text-xs inline-flex items-center gap-1 whitespace-nowrap"
        >
          <span>Lihat Semua Armada</span>
          <span>&rarr;</span>
        </RouterLink>
      </div>

      <!-- Bottom Conversion Banner: Ajak User ke Halaman /armada -->
      <div class="mt-8 sm:mt-10 p-4 sm:p-6 rounded-2xl bg-gradient-to-r from-slate-900 via-slate-850 to-slate-900 text-white flex flex-col sm:flex-row items-center justify-between gap-4 sm:gap-5 shadow-lg border border-slate-800">
        <div class="flex items-center gap-3.5 sm:gap-4 w-full sm:w-auto">
          <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-blue-600/20 border border-blue-500/30 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm sm:text-base font-bold text-white leading-snug">Butuh kapasitas lebih besar atau bus pariwisata?</h3>
            <p class="text-xs sm:text-sm text-slate-300 mt-0.5">
              Jelajahi seluruh koleksi City Car, MPV, HiAce 15 Seat, hingga Bus Pariwisata di etalase resmi kami.
            </p>
          </div>
        </div>

        <RouterLink
          to="/armada"
          class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 sm:px-6 py-2.5 sm:py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs sm:text-sm font-bold shadow-md hover:shadow-blue-600/30 transition-all shrink-0 active:scale-95"
        >
          <span>Lihat Semua Armada {{ totalFleetCount > 0 ? `(${totalFleetCount} Unit)` : '' }}</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
          </svg>
        </RouterLink>
      </div>

    </div>

    <!-- Vehicle Details & Multi-Angle Showroom Modal Dialog -->
    <div
      v-if="previewVehicle"
      class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4 md:p-6 bg-slate-950/80 backdrop-blur-sm transition-all"
      @click.self="closePhotoModal"
    >
      <div
        class="bg-white rounded-2xl sm:rounded-3xl overflow-hidden w-full max-w-4xl lg:max-w-5xl xl:max-w-6xl border border-slate-200 shadow-2xl text-slate-900 flex flex-col md:flex-row md:max-h-[88vh] max-h-[94vh] animate-in fade-in zoom-in-95 duration-200"
      >
        <!-- Modal Left Column: Media Stage -->
        <div class="md:w-7/12 lg:w-3/5 bg-slate-950 flex flex-col justify-between relative overflow-hidden p-4 sm:p-5 lg:p-6 select-none border-b md:border-b-0 md:border-r border-slate-800 shrink-0">
          <div class="relative z-10 flex items-center justify-between gap-2">
            <span
              v-if="previewVehicle.status === 'available'"
              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-600 text-white shadow-md"
            >
              <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
              <span>Tersedia Siap Jalan</span>
            </span>
            <span
              v-else-if="previewVehicle.status === 'maintenance'"
              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500 text-white shadow-md"
            >
              <span class="w-2 h-2 rounded-full bg-white"></span>
              <span>Perawatan</span>
            </span>
            <span
              v-else
              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-800 text-slate-200"
            >
              <span>{{ previewVehicle.status_label || 'Tidak Tersedia' }}</span>
            </span>

            <button
              @click="closePhotoModal"
              type="button"
              class="md:hidden w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>

          <!-- Main High-Res Photo Container -->
          <div class="relative w-full flex-1 min-h-[220px] sm:min-h-[300px] md:min-h-[360px] lg:min-h-[420px] my-3 sm:my-4 flex items-center justify-center overflow-hidden">
            <img
              v-if="activePreviewUrl"
              :src="activePreviewUrl"
              :alt="previewVehicle.name"
              class="max-h-full max-w-full w-auto h-auto object-contain rounded-xl drop-shadow-2xl transition-all duration-300"
            />
            <div v-else class="text-center text-slate-500">
              <svg class="w-16 h-16 mx-auto mb-2 text-slate-700" fill="currentColor" viewBox="0 0 24 24"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg>
              <p class="text-xs text-slate-400">Foto unit tidak tersedia</p>
            </div>

            <!-- Left & Right Arrow Buttons -->
            <button
              v-if="previewAngles.length > 1"
              @click="prevModalAngle"
              class="absolute left-2 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-black/60 hover:bg-black/90 text-white flex items-center justify-center transition-all active:scale-90"
              title="Sudut foto sebelumnya"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <button
              v-if="previewAngles.length > 1"
              @click="nextModalAngle"
              class="absolute right-2 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-black/60 hover:bg-black/90 text-white flex items-center justify-center transition-all active:scale-90"
              title="Sudut foto berikutnya"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
          </div>

          <!-- Bottom Angle Thumbnails -->
          <div v-if="previewAngles.length > 1" class="relative z-10 flex items-center justify-center gap-2 pt-2 border-t border-slate-800/80">
            <button
              v-for="(angle, idx) in previewAngles"
              :key="angle.id"
              @click="previewAngleIdx = idx"
              type="button"
              :class="[
                previewAngleIdx === idx
                  ? 'border-blue-500 bg-blue-600/30 text-white font-bold'
                  : 'border-slate-800 bg-slate-900/80 text-slate-400 hover:text-white',
                'px-3 py-1.5 rounded-lg border text-xs transition-all flex items-center gap-1.5'
              ]"
            >
              <span class="w-1.5 h-1.5 rounded-full" :class="previewAngleIdx === idx ? 'bg-blue-400' : 'bg-slate-600'"></span>
              <span>{{ angle.label }}</span>
            </button>
          </div>
        </div>

        <!-- Modal Right Column: Specs & Booking -->
        <div class="md:w-5/12 lg:w-2/5 p-5 sm:p-6 lg:p-7 flex flex-col justify-between overflow-y-auto max-h-[50vh] md:max-h-none">
          <div>
            <div class="hidden md:flex justify-end mb-2">
              <button
                @click="closePhotoModal"
                type="button"
                class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-900 flex items-center justify-center transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </div>

            <span class="inline-block px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 text-xs font-bold mb-2 uppercase tracking-wide">
              {{ getVehicleCategoryBracket(previewVehicle.capacity).shortLabel }}
            </span>
            <h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight leading-snug">
              {{ previewVehicle.name }}
            </h3>
            <p class="text-xs text-slate-500 mt-1">Tahun Produksi {{ previewVehicle.model_year }}</p>

            <!-- 4 Specs Grid -->
            <div class="grid grid-cols-2 gap-2.5 mt-5">
              <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-[11px] text-slate-400 block font-medium">Kapasitas</span>
                <span class="text-xs sm:text-sm font-bold text-slate-800 mt-0.5 block">{{ previewVehicle.capacity }} Kursi</span>
              </div>
              <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-[11px] text-slate-400 block font-medium">Transmisi</span>
                <span class="text-xs sm:text-sm font-bold text-slate-800 mt-0.5 block">{{ previewVehicle.transmission_label }}</span>
              </div>
              <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-[11px] text-slate-400 block font-medium">Bahan Bakar</span>
                <span class="text-xs sm:text-sm font-bold text-slate-800 mt-0.5 block">{{ previewVehicle.fuel_type_label }}</span>
              </div>
              <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-[11px] text-slate-400 block font-medium">Kondisi Unit</span>
                <span
                  class="text-xs sm:text-sm font-bold mt-0.5 block"
                  :class="previewVehicle.status === 'maintenance' ? 'text-amber-600' : 'text-emerald-600'"
                >
                  {{ previewVehicle.status_label }}
                </span>
              </div>
            </div>

            <!-- Description -->
            <p class="text-xs text-slate-600 mt-4 leading-relaxed bg-slate-50/50 p-3.5 rounded-xl border border-slate-100">
              {{ previewVehicle.description || 'Unit prima dalam kondisi bersih, mesin responsif, dan siap melayani kebutuhan perjalanan wisata dan bisnis di Tanjungpinang & Bintan.' }}
            </p>
          </div>

          <!-- Bottom Pricing & CTA -->
          <div class="mt-6 pt-5 border-t border-slate-200">
            <div class="flex items-baseline justify-between mb-4">
              <span class="text-xs text-slate-500">Harga Sewa Harian</span>
              <div>
                <span class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ previewVehicle.daily_rate_formatted }}</span>
                <span class="text-xs text-slate-500 font-normal"> /hari</span>
              </div>
            </div>

            <a
              v-if="previewVehicle.status === 'available'"
              :href="generateVehicleWhatsAppUrl(previewVehicle, siteConfig.rentalPhone, siteConfig.rentalName)"
              target="_blank"
              rel="noopener noreferrer"
              class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-md hover:shadow-emerald-600/30 transition-all active:scale-98"
            >
              <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/></svg>
              <span>Booking via WhatsApp</span>
            </a>

            <button
              v-else-if="previewVehicle.status === 'maintenance'"
              disabled
              class="w-full py-3 px-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 font-bold text-xs sm:text-sm flex items-center justify-center gap-2 cursor-not-allowed select-none"
            >
              <span class="w-2 h-2 rounded-full bg-amber-500"></span>
              <span>Perawatan</span>
            </button>

            <button
              v-else
              disabled
              class="w-full py-3 px-4 rounded-xl bg-slate-100 text-slate-400 font-medium text-xs sm:text-sm flex items-center justify-center cursor-not-allowed select-none"
            >
              <span>{{ previewVehicle.status_label || 'Tidak Tersedia' }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
