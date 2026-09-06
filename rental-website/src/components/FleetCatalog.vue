<script setup lang="ts">
import { ref, computed } from 'vue'
import type { PublicVehicle } from '@/types/fleet'
import { siteConfig } from '@/config/site'
import { generateVehicleWhatsAppUrl } from '@/utils/whatsapp'
import { getVehicleAngles, type VehiclePhotoAngle } from '@/utils/vehiclePhotos'

defineProps<{
  vehicles: PublicVehicle[]
  loading: boolean
  error: string | null
}>()

const emit = defineEmits<{
  (e: 'retry'): void
}>()

// Modal Video state
const activeVideoUrl = ref<string | null>(null)
const activeVideoTitle = ref<string>('')

const openVideoModal = (url: string, title: string) => {
  activeVideoUrl.value = url
  activeVideoTitle.value = title
}

const closeVideoModal = () => {
  activeVideoUrl.value = null
  activeVideoTitle.value = ''
}

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
</script>

<template>
  <section id="armada" class="py-12 sm:py-16 scroll-mt-16">
    <span id="katalog" class="sr-only"></span>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <!-- Desktop Layout: Sidebar Kiri + Grid Kanan -->
      <div class="flex flex-col lg:flex-row gap-8 lg:gap-10 items-start">

        <!-- Sidebar Kiri: Section Header + Filter -->
        <div class="w-full lg:w-56 xl:w-64 shrink-0">
          <!-- Section Header -->
          <div class="mb-6">
            <p class="text-xs font-bold uppercase tracking-widest text-blue-600 mb-2">Armada Tersedia</p>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight leading-snug mb-1">
              Pilihan Armada Kami
            </h2>
            <p class="text-xs text-slate-500 leading-relaxed">
              Terjangkau &amp; Terpercaya. Status unit selalu ter-update sesuai ketersediaan di garasi.
            </p>
          </div>

          <!-- Filter Slot -->
          <slot name="filter"></slot>

          <!-- Refresh CTA -->
          <div class="mt-3 flex items-center gap-2 text-xs text-slate-400">
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            <span>Unit dicek berkala</span>
            <button
              @click="emit('retry')"
              type="button"
              class="inline-flex items-center gap-1 text-[11px] text-blue-600 hover:text-blue-800 font-medium transition-colors"
              title="Muat ulang data armada terbaru"
            >
              <svg class="w-3 h-3" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              Cek Status
            </button>
          </div>
        </div>

        <!-- Konten Kanan: Grid Armada -->
        <div class="flex-1 min-w-0">

          <!-- Error State -->
          <div
            v-if="error"
            class="rounded-xl bg-red-50 border border-red-200 p-6 text-center text-red-700 my-6"
          >
            <p class="font-semibold text-sm mb-2">{{ error }}</p>
            <p class="text-xs text-red-600 mb-4">Pastikan koneksi internet atau server backend sedang berjalan.</p>
            <button
              @click="emit('retry')"
              type="button"
              class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors"
            >
              Coba Muat Ulang
            </button>
          </div>

          <!-- Loading Skeleton -->
          <div
            v-else-if="loading && vehicles.length === 0"
            class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-5"
          >
            <div
              v-for="i in 4"
              :key="i"
              class="bg-white rounded-xl border border-slate-200 overflow-hidden animate-pulse"
            >
              <div class="aspect-[16/10] bg-slate-200"></div>
              <div class="p-3 sm:p-4 space-y-2.5">
                <div class="h-4 bg-slate-200 rounded w-3/4"></div>
                <div class="h-3 bg-slate-100 rounded w-1/2"></div>
                <div class="pt-2 flex justify-between items-center">
                  <div class="h-4 bg-slate-200 rounded w-1/3"></div>
                  <div class="h-8 bg-slate-200 rounded w-1/3"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div
            v-else-if="vehicles.length === 0"
            class="bg-white rounded-xl border border-slate-200 p-8 sm:p-12 text-center my-6"
          >
            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
              </svg>
            </div>
            <h3 class="text-base font-semibold text-slate-800 mb-1">Armada Tidak Ditemukan</h3>
            <p class="text-xs sm:text-sm text-slate-500 max-w-sm mx-auto mb-4">
              Tidak ada kendaraan yang sesuai dengan kriteria filter saat ini.
            </p>
          </div>

          <!-- Fleet Grid: 2 kolom mobile, 2-3-4 desktop -->
          <div
            v-else
            class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-5"
          >
        <article
          v-for="car in vehicles"
          :key="car.id"
          class="bg-white rounded-2xl border border-slate-200/90 overflow-hidden flex flex-col justify-between hover:border-slate-300 hover:shadow-md transition-all group"
        >
          <!-- Vehicle Photo Container -->
          <div
            class="relative aspect-[16/10] bg-slate-100 overflow-hidden cursor-pointer"
            @click="openPhotoModal(car, activeCardAngles[car.id] ?? 0)"
            title="Klik untuk melihat preview foto unit"
          >
            <img
              v-if="getCardPhotoUrl(car)"
              :src="getCardPhotoUrl(car)!"
              :alt="car.name"
              loading="lazy"
              decoding="async"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            />
            <!-- Fallback Illustration -->
            <div
              v-else
              class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-slate-50"
            >
              <svg class="w-10 h-10 mb-1 text-slate-300" fill="currentColor" viewBox="0 0 24 24">
                <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
              </svg>
              <span class="text-[11px] text-slate-400 font-medium">Foto Belum Tersedia</span>
            </div>

            <!-- Status Badge (Top-Left) -->
            <div class="absolute top-2.5 left-2.5 z-10">
              <span
                v-if="car.status === 'available'"
                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-600 text-white shadow-sm"
              >
                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                <span>Tersedia</span>
              </span>
              <span
                v-else-if="car.status === 'rented'"
                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-900/80 text-slate-200 backdrop-blur-xs shadow-sm"
              >
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                <span>Sedang Disewa</span>
              </span>
              <span
                v-else
                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-900/70 text-slate-300 backdrop-blur-xs shadow-sm"
              >
                <span>{{ car.status_label || 'Tidak Tersedia' }}</span>
              </span>
            </div>

            <!-- Top-Right Action Cluster: Video & Photo Gallery Zoom -->
            <div class="absolute top-2.5 right-2.5 z-10 flex items-center gap-1.5">
              <!-- Video Preview Button (if safe embed URL available) -->
              <button
                v-if="car.safe_video_embed_url"
                @click.stop="openVideoModal(car.safe_video_embed_url, car.name)"
                type="button"
                class="w-7 h-7 rounded-full bg-slate-900/80 hover:bg-slate-900 text-white flex items-center justify-center transition-colors shadow-sm"
                title="Tonton video unit"
              >
                <svg class="w-3.5 h-3.5 ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M8 5v14l11-7z"/>
                </svg>
              </button>

              <!-- Photo Gallery Preview Icon Button -->
              <button
                @click.stop="openPhotoModal(car, activeCardAngles[car.id] ?? 0)"
                type="button"
                class="w-7 h-7 rounded-full bg-slate-900/80 hover:bg-slate-900 text-white flex items-center justify-center transition-colors shadow-sm"
                title="Perbesar foto unit"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                </svg>
              </button>
            </div>

            <!-- Multi-angle Switcher Pills (Bottom Right of photo) -->
            <div
              v-if="getVehicleAngles(car).length > 1"
              class="absolute bottom-2 right-2 z-10 flex items-center gap-1 bg-slate-900/85 backdrop-blur-xs p-0.5 rounded-md border border-slate-700/80 shadow-sm"
            >
              <button
                v-for="(angle, aIdx) in getVehicleAngles(car)"
                :key="angle.id"
                @click.stop="setCardAngle(car.id, aIdx)"
                type="button"
                :class="(activeCardAngles[car.id] ?? 0) === aIdx ? 'bg-blue-600 text-white font-bold' : 'text-slate-300 hover:text-white'"
                class="px-1.5 py-0.5 rounded text-[9px] sm:text-[10px] transition-colors leading-none"
                :title="'Sudut ' + angle.label"
              >
                {{ angle.label }}
              </button>
            </div>
          </div>

          <!-- Card Content -->
          <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between">
            <div>
              <!-- Car Name -->
              <h3
                class="text-base sm:text-lg font-bold text-slate-900 leading-snug line-clamp-1 cursor-pointer hover:text-blue-600 transition-colors"
                @click="openPhotoModal(car, activeCardAngles[car.id] ?? 0)"
                title="Lihat detail lengkap unit"
              >
                {{ car.name }}
              </h3>

              <!-- Brand & Year -->
              <p class="text-xs text-slate-500 font-medium mt-0.5">
                <span v-if="car.brand">{{ car.brand }} &bull; </span>
                <span>Tahun {{ car.model_year }}</span>
              </p>

              <!-- Specifications -->
              <div class="flex items-center gap-3 text-xs text-slate-600 mt-3 pt-2.5 border-t border-slate-100">
                <span class="inline-flex items-center gap-1 font-medium">
                  <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                  </svg>
                  <span>{{ car.capacity }} Kursi</span>
                </span>

                <span class="text-slate-300">&bull;</span>

                <span class="inline-flex items-center gap-1 font-medium">
                  <span>{{ car.transmission_label }}</span>
                </span>
              </div>
            </div>

            <!-- Price & Button Area -->
            <div class="pt-3.5 mt-3.5 border-t border-slate-100 flex flex-col gap-2.5">
              <!-- Price Row -->
              <div class="flex items-baseline justify-between">
                <span class="text-[11px] text-slate-500 font-medium">Tarif sewa</span>
                <div class="text-sm sm:text-base font-extrabold text-slate-900">
                  {{ car.daily_rate_formatted }}
                  <span class="text-[11px] font-normal text-slate-500">/hari</span>
                </div>
              </div>

              <!-- Full Width Action Button -->
              <a
                v-if="car.status === 'available'"
                :href="generateVehicleWhatsAppUrl(car, siteConfig.rentalPhone, siteConfig.rentalName)"
                target="_blank"
                rel="noopener noreferrer"
                class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs sm:text-sm font-bold transition-all shadow-xs hover:shadow-sm active:scale-98"
              >
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                </svg>
                <span>Pesan via WhatsApp</span>
              </a>

              <a
                v-else-if="car.status === 'rented'"
                :href="generateVehicleWhatsAppUrl(car, siteConfig.rentalPhone, siteConfig.rentalName)"
                target="_blank"
                rel="noopener noreferrer"
                class="w-full inline-flex items-center justify-center gap-1 py-2.5 px-3 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-semibold transition-colors"
              >
                <span>Tanya Jadwal Lain</span>
              </a>

              <button
                v-else
                disabled
                class="w-full inline-flex items-center justify-center py-2.5 px-3 rounded-xl bg-slate-100 text-slate-400 text-xs sm:text-sm font-medium cursor-not-allowed"
              >
                <span>{{ car.status_label || 'Sedang Diservis' }}</span>
              </button>
            </div>
          </div>
        </article>
      </div>

      <!-- Lihat semua armada link + Disclaimer (bawah grid kanan) -->
      <div class="mt-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pt-4 border-t border-slate-100">
        <p class="text-xs text-slate-500 leading-relaxed">
          *Ketersediaan armada diperbarui berkala. Titik antar-jemput dikonfirmasi admin via WhatsApp.
        </p>
        <a href="#armada" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors shrink-0">
          Lihat semua armada &rarr;
        </a>
      </div>

        </div>
        <!-- /Konten Kanan -->

      </div>
      <!-- /Desktop Layout Sidebar -->

    </div>

    <!-- Video Modal Dialog -->
    <div
      v-if="activeVideoUrl"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-xs"
      @click.self="closeVideoModal"
    >
      <div class="bg-slate-900 rounded-xl overflow-hidden max-w-2xl w-full border border-slate-800 shadow-2xl">
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-800 text-white">
          <span class="text-sm font-semibold truncate">{{ activeVideoTitle }}</span>
          <button
            @click="closeVideoModal"
            class="text-slate-400 hover:text-white p-1"
            type="button"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <div class="aspect-video w-full bg-black">
          <iframe
            :src="activeVideoUrl"
            class="w-full h-full"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen
          ></iframe>
        </div>
      </div>
    </div>

    <!-- Vehicle Details & Gallery Modal Dialog -->
    <div
      v-if="previewVehicle"
      class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-slate-950/75 backdrop-blur-xs transition-all"
      @click.self="closePhotoModal"
    >
      <div class="bg-white rounded-t-3xl sm:rounded-3xl overflow-hidden max-w-xl w-full border border-slate-200 shadow-2xl text-slate-900 flex flex-col max-h-[92vh] sm:max-h-[90vh] animate-in fade-in slide-in-from-bottom-4 duration-200">
        <!-- 1. Media Viewport (Photo & Video) -->
        <div class="relative aspect-[16/10] bg-slate-100 overflow-hidden shrink-0">
          <!-- Photo View -->
          <div v-if="activeModalTab === 'photo'" class="w-full h-full relative flex items-center justify-center bg-slate-50">
            <img
              v-if="activePreviewUrl"
              :src="activePreviewUrl"
              :alt="previewVehicle.name"
              class="w-full h-full object-cover"
            />
            <div v-else class="text-slate-400 text-xs flex flex-col items-center justify-center p-4">
              <svg class="w-10 h-10 text-slate-300 mb-1" fill="currentColor" viewBox="0 0 24 24">
                <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
              </svg>
              <span>Foto unit belum tersedia</span>
            </div>

            <!-- Angle Switcher Pills (Bottom Center) -->
            <div
              v-if="previewAngles.length > 1"
              class="absolute bottom-3 left-1/2 -translate-x-1/2 z-10 flex items-center gap-1 bg-slate-900/80 backdrop-blur-xs p-1 rounded-full border border-slate-700/60 shadow-lg"
            >
              <button
                v-for="(angle, idx) in previewAngles"
                :key="angle.id"
                @click="previewAngleIdx = idx"
                type="button"
                :class="previewAngleIdx === idx ? 'bg-blue-600 text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white'"
                class="px-2.5 py-0.5 rounded-full text-[11px] transition-colors leading-tight"
              >
                {{ angle.label }}
              </button>
            </div>
          </div>

          <!-- Video View -->
          <div v-else-if="activeModalTab === 'video' && previewVehicle.safe_video_embed_url" class="w-full h-full bg-black">
            <iframe
              :src="previewVehicle.safe_video_embed_url"
              class="w-full h-full"
              frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowfullscreen
            ></iframe>
          </div>

          <!-- Top-Left Status Badge -->
          <div class="absolute top-3 left-3 z-10">
            <span
              v-if="previewVehicle.status === 'available'"
              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-600 text-white shadow-md"
            >
              <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
              Tersedia Siap Jalan
            </span>
            <span
              v-else-if="previewVehicle.status === 'maintenance'"
              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-700 text-slate-100 shadow-md"
            >
              <span class="w-2 h-2 rounded-full bg-slate-400"></span>
              Dalam Perawatan
            </span>
            <span
              v-else-if="previewVehicle.status === 'rented'"
              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-600 text-white shadow-md"
            >
              <span class="w-2 h-2 rounded-full bg-white"></span>
              Sedang Disewa
            </span>
            <span
              v-else
              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-700 text-white shadow-md"
            >
              {{ previewVehicle.status_label || 'Tidak Tersedia' }}
            </span>
          </div>

          <!-- Top-Right Actions: Media Toggle & Close -->
          <div class="absolute top-3 right-3 z-10 flex items-center gap-2">
            <!-- Media Toggle if Video available -->
            <div
              v-if="previewVehicle.safe_video_embed_url"
              class="flex items-center bg-slate-900/80 backdrop-blur-xs p-0.5 rounded-full border border-slate-700/60 shadow-md text-[11px]"
            >
              <button
                @click="activeModalTab = 'photo'"
                :class="activeModalTab === 'photo' ? 'bg-white text-slate-900 font-bold' : 'text-slate-300 hover:text-white'"
                class="px-2.5 py-0.5 rounded-full transition-colors"
                type="button"
              >
                Foto
              </button>
              <button
                @click="activeModalTab = 'video'"
                :class="activeModalTab === 'video' ? 'bg-white text-slate-900 font-bold' : 'text-slate-300 hover:text-white'"
                class="px-2.5 py-0.5 rounded-full transition-colors flex items-center gap-1"
                type="button"
              >
                <svg class="w-3 h-3 text-red-500 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                Video
              </button>
            </div>

            <!-- Close Button -->
            <button
              @click="closePhotoModal"
              class="w-8 h-8 rounded-full bg-white/90 hover:bg-white text-slate-700 hover:text-slate-900 shadow-md backdrop-blur-xs flex items-center justify-center transition-transform hover:scale-105"
              type="button"
              title="Tutup dialog"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- 2. Scrollable Details Body -->
        <div class="overflow-y-auto px-5 py-4 space-y-4 flex-1">
          <!-- Title & Meta Subtitle -->
          <div>
            <h3 class="text-xl sm:text-2xl font-black text-slate-900 leading-tight">
              {{ previewVehicle.name }}
            </h3>
            <div class="text-xs sm:text-sm text-slate-500 font-medium mt-1 flex items-center gap-2">
              <span>Tahun {{ previewVehicle.model_year }}</span>
              <span v-if="previewVehicle.brand">&bull; {{ previewVehicle.brand }}</span>
              <span v-if="previewVehicle.fuel_type_label">&bull; Bahan Bakar {{ previewVehicle.fuel_type_label }}</span>
            </div>
          </div>

          <!-- Quick Specifications 4-Card Grid -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
            <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 flex flex-col justify-center">
              <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Kapasitas</span>
              <div class="flex items-center gap-1.5 mt-1">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span class="text-xs sm:text-sm font-bold text-slate-800">{{ previewVehicle.capacity }} Kursi</span>
              </div>
            </div>

            <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 flex flex-col justify-center">
              <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Transmisi</span>
              <div class="flex items-center gap-1.5 mt-1">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                <span class="text-xs sm:text-sm font-bold text-slate-800">{{ previewVehicle.transmission_label }}</span>
              </div>
            </div>

            <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 flex flex-col justify-center">
              <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Bahan Bakar</span>
              <div class="flex items-center gap-1.5 mt-1">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span class="text-xs sm:text-sm font-bold text-slate-800">{{ previewVehicle.fuel_type_label }}</span>
              </div>
            </div>

            <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 flex flex-col justify-center">
              <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Kondisi</span>
              <div class="flex items-center gap-1.5 mt-1">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-xs sm:text-sm font-bold text-slate-800">Siap Jalan</span>
              </div>
            </div>
          </div>

          <!-- Description Box (Operational Notes) -->
          <div v-if="previewVehicle.description" class="bg-blue-50/50 border border-blue-100/80 rounded-xl p-3.5">
            <h5 class="text-[11px] font-bold text-blue-900 uppercase tracking-wider mb-1 flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
              </svg>
              Catatan &amp; Deskripsi Unit
            </h5>
            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">{{ previewVehicle.description }}</p>
          </div>

          <!-- Included Service & Reassurance Checklist -->
          <div class="rounded-xl border border-slate-200/80 p-3.5 space-y-2.5 bg-slate-50/50">
            <h5 class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Fasilitas &amp; Jaminan Layanan</h5>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-slate-600">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Unit bersih, harum, &amp; disanitasi</span>
              </div>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>AC sejuk &amp; servis berkala rutin</span>
              </div>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Opsi sewa lepas kunci / dengan driver</span>
              </div>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Koordinasi serah terima fleksibel</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 3. Sticky Bottom Action Bar -->
        <div class="px-5 py-3.5 bg-white border-t border-slate-200 flex items-center justify-between gap-3 shrink-0">
          <div>
            <span class="text-[10px] text-slate-400 block font-medium">Tarif Sewa Harian</span>
            <div class="text-base sm:text-lg font-extrabold text-slate-900">
              {{ previewVehicle.daily_rate_formatted }}
              <span class="text-xs font-normal text-slate-500">/hari</span>
            </div>
          </div>

          <!-- Dynamic Action Button -->
          <a
            v-if="previewVehicle.status === 'available'"
            :href="generateVehicleWhatsAppUrl(previewVehicle, siteConfig.rentalPhone, siteConfig.rentalName)"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-md transition-all active:scale-95"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
            </svg>
            Sewa via WhatsApp
          </a>

          <div
            v-else-if="previewVehicle.status === 'maintenance'"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 text-slate-500 border border-slate-200 text-xs font-semibold"
          >
            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
            Dalam Perawatan
          </div>

          <a
            v-else
            :href="generateVehicleWhatsAppUrl(previewVehicle, siteConfig.rentalPhone, siteConfig.rentalName)"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold text-xs transition-all shadow-xs"
          >
            Tanya Jadwal Lain
          </a>
        </div>
      </div>
    </div>
  </section>
</template>
