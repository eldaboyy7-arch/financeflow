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

// Modal Photo Preview / Gallery state
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
</script>

<template>
  <section id="katalog" class="py-10 sm:py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Section Header -->
      <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-6 gap-3">
        <div>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
            Katalog Armada
          </h2>
          <p class="text-sm text-slate-500 mt-1">
            Pilihan armada resmi terawat siap untuk disewa.
          </p>
        </div>
        <div class="text-xs text-slate-500 flex items-center gap-1.5">
          <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
          <span>Informasi ketersediaan armada diperbarui berkala</span>
        </div>
      </div>

      <!-- Filter Slot -->
      <slot name="filter"></slot>

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

      <!-- Loading Skeleton (Marketplace Grid) -->
      <div
        v-else-if="loading && vehicles.length === 0"
        class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-2.5 sm:gap-5"
      >
        <div
          v-for="i in 6"
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

      <!-- Marketplace Fleet Grid: 2 Columns on Mobile, 3-4 Columns on Desktop -->
      <div
        v-else
        class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-2.5 sm:gap-5"
      >
        <article
          v-for="car in vehicles"
          :key="car.id"
          class="bg-white rounded-xl border border-slate-200 overflow-hidden flex flex-col hover:border-slate-300 hover:shadow-sm transition-all group"
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
            <div class="absolute top-2 left-2 z-10">
              <span
                v-if="car.status === 'available'"
                class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm"
              >
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Tersedia
              </span>
              <span
                v-else-if="car.status === 'rented'"
                class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-slate-800/85 text-white backdrop-blur-xs shadow-sm"
              >
                Sedang Disewa
              </span>
              <span
                v-else
                class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200 shadow-sm"
              >
                Servis
              </span>
            </div>

            <!-- Top-Right Action Cluster: Video & Photo Gallery Zoom -->
            <div class="absolute top-2 right-2 z-10 flex items-center gap-1">
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

            <!-- Twin / Multi-angle Switcher Pills (Bottom Right of photo) -->
            <div
              v-if="getVehicleAngles(car).length > 1"
              class="absolute bottom-1.5 right-1.5 z-10 flex items-center gap-1 bg-slate-900/85 backdrop-blur-xs p-0.5 rounded-md border border-slate-700/80 shadow-sm"
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
          <div class="p-2.5 sm:p-4 flex-1 flex flex-col justify-between">
            <div>
              <!-- 1. Car Name (Primary Identifier) -->
              <h3 class="text-sm sm:text-base font-bold text-slate-900 leading-snug line-clamp-1">
                {{ car.name }}
              </h3>

              <!-- 2. Year & Brand -->
              <div class="text-[11px] sm:text-xs text-slate-500 font-medium tracking-wide mt-0.5">
                <span>Tahun {{ car.model_year }}</span>
                <span v-if="car.brand"> &bull; {{ car.brand }}</span>
              </div>

              <!-- 3. Key Specifications -->
              <div class="flex items-center gap-1.5 text-[11px] sm:text-xs text-slate-600 mt-2.5">
                <span class="inline-flex items-center gap-1 bg-slate-100 px-2 py-0.5 rounded font-medium">
                  <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                  </svg>
                  {{ car.capacity }} Kursi
                </span>

                <span class="inline-flex items-center gap-1 bg-slate-100 px-2 py-0.5 rounded font-medium">
                  {{ car.transmission_label }}
                </span>
              </div>
            </div>

            <!-- Price & Direct WhatsApp CTA -->
            <div class="pt-3 mt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
              <div>
                <span class="text-[10px] sm:text-[11px] text-slate-500 block leading-tight">Mulai dari</span>
                <div class="text-xs sm:text-sm font-extrabold text-slate-900">
                  {{ car.daily_rate_formatted }}
                  <span class="text-[10px] font-normal text-slate-500">/hari</span>
                </div>
              </div>

              <!-- Contextual WhatsApp Action Button -->
              <a
                v-if="car.status === 'available'"
                :href="generateVehicleWhatsAppUrl(car, siteConfig.rentalPhone, siteConfig.rentalName)"
                target="_blank"
                rel="noopener noreferrer"
                class="w-full sm:w-auto inline-flex items-center justify-center px-2.5 py-1.5 sm:px-3 sm:py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition-colors shadow-xs"
              >
                <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                </svg>
                Chat Sewa
              </a>

              <a
                v-else-if="car.status === 'rented'"
                :href="generateVehicleWhatsAppUrl(car, siteConfig.rentalPhone, siteConfig.rentalName)"
                target="_blank"
                rel="noopener noreferrer"
                class="w-full sm:w-auto inline-flex items-center justify-center px-2 py-1.5 sm:px-2.5 sm:py-2 rounded-lg border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-medium transition-colors"
              >
                Jadwal Lain
              </a>

              <button
                v-else
                disabled
                class="w-full sm:w-auto inline-flex items-center justify-center px-2 py-1.5 sm:px-2.5 sm:py-2 rounded-lg bg-slate-100 text-slate-400 text-xs font-medium cursor-not-allowed"
              >
                Perawatan
              </button>
            </div>
          </div>
        </article>
      </div>

      <!-- Mandatory Transparency Disclaimer -->
      <div class="mt-8 pt-4 border-t border-slate-200 text-center text-xs text-slate-500 leading-relaxed max-w-2xl mx-auto">
        Status ketersediaan armada diperbarui secara berkala dari sistem operasional. Kepastian jadwal dan serah terima unit dikonfirmasi langsung oleh admin melalui WhatsApp.
      </div>
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

    <!-- Photo Preview / Gallery Modal Dialog -->
    <div
      v-if="previewVehicle"
      class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/85 backdrop-blur-xs"
      @click.self="closePhotoModal"
    >
      <div class="bg-slate-900 rounded-2xl overflow-hidden max-w-2xl w-full border border-slate-800 shadow-2xl text-white flex flex-col max-h-[92vh]">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-4 sm:px-5 py-3 border-b border-slate-800 shrink-0">
          <div>
            <h4 class="text-sm sm:text-base font-bold text-white">{{ previewVehicle.name }}</h4>
            <p class="text-xs text-slate-400">
              Tahun {{ previewVehicle.model_year }} &bull; {{ previewVehicle.transmission_label }} &bull; {{ previewVehicle.capacity }} Kursi
            </p>
          </div>
          <button
            @click="closePhotoModal"
            class="text-slate-400 hover:text-white p-1 rounded-lg transition-colors"
            type="button"
            title="Tutup preview"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Large Photo Viewport -->
        <div class="relative bg-slate-950 flex-1 flex items-center justify-center overflow-hidden min-h-[260px] sm:min-h-[380px] p-2">
          <img
            v-if="activePreviewUrl"
            :src="activePreviewUrl"
            :alt="previewVehicle.name"
            class="w-full h-full max-h-[60vh] object-contain rounded-lg"
          />
          <div v-else class="text-slate-500 text-sm">
            Foto tidak tersedia
          </div>

          <!-- Angle Indicator Badges / Switcher in Modal -->
          <div
            v-if="previewAngles.length > 1"
            class="absolute bottom-4 left-1/2 -translate-x-1/2 z-10 flex items-center gap-1.5 bg-slate-900/90 backdrop-blur-xs px-3 py-1 rounded-full border border-slate-700/80 shadow-lg"
          >
            <span class="text-[11px] text-slate-400 mr-1 hidden sm:inline">Sudut:</span>
            <button
              v-for="(angle, idx) in previewAngles"
              :key="angle.id"
              @click="previewAngleIdx = idx"
              type="button"
              :class="previewAngleIdx === idx ? 'bg-blue-600 text-white font-bold' : 'text-slate-300 hover:text-white'"
              class="px-2.5 py-0.5 rounded-full text-xs transition-colors"
            >
              {{ angle.label }}
            </button>
          </div>
        </div>

        <!-- Modal Footer with CTA & Price -->
        <div class="px-4 sm:px-5 py-3.5 bg-slate-900 border-t border-slate-800 flex items-center justify-between gap-3 shrink-0">
          <div>
            <span class="text-[10px] sm:text-xs text-slate-400 block">Tarif Sewa</span>
            <div class="text-sm sm:text-base font-extrabold text-white">
              {{ previewVehicle.daily_rate_formatted }}
              <span class="text-xs font-normal text-slate-400">/hari</span>
            </div>
          </div>

          <a
            :href="generateVehicleWhatsAppUrl(previewVehicle, siteConfig.rentalPhone, siteConfig.rentalName)"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition-colors shadow-xs"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
            </svg>
            Tanya Unit via WhatsApp
          </a>
        </div>
      </div>
    </div>
  </section>
</template>
