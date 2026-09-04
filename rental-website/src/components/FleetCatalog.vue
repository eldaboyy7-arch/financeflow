<script setup lang="ts">
import { ref } from 'vue'
import type { PublicVehicle } from '@/types/fleet'
import { siteConfig } from '@/config/site'
import { generateVehicleWhatsAppUrl } from '@/utils/whatsapp'

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
          <span>Data langsung dari sistem operasional garasi</span>
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
          <div class="relative aspect-[16/10] bg-slate-100 overflow-hidden">
            <img
              v-if="car.photo_url"
              :src="car.photo_url"
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

            <!-- Video Preview Button (Top-Right, if safe embed URL available) -->
            <button
              v-if="car.safe_video_embed_url"
              @click="openVideoModal(car.safe_video_embed_url, car.name)"
              type="button"
              class="absolute top-2 right-2 z-10 w-7 h-7 rounded-full bg-slate-900/80 hover:bg-slate-900 text-white flex items-center justify-center transition-colors shadow-sm"
              title="Tonton video unit"
            >
              <svg class="w-3.5 h-3.5 ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z"/>
              </svg>
            </button>
          </div>

          <!-- Card Content -->
          <div class="p-2.5 sm:p-4 flex-1 flex flex-col justify-between">
            <div>
              <!-- Brand & Model Year -->
              <div class="text-[11px] sm:text-xs text-slate-500 font-medium tracking-wide">
                <span v-if="car.brand">{{ car.brand }} &bull; </span>
                <span>Tahun {{ car.model_year }}</span>
              </div>

              <!-- Car Name -->
              <h3 class="text-xs sm:text-base font-bold text-slate-900 leading-snug line-clamp-1 mt-0.5">
                {{ car.name }}
              </h3>

              <!-- Compact Specs Pill -->
              <div class="flex items-center gap-2 text-[11px] sm:text-xs text-slate-500 mt-2">
                <span class="inline-flex items-center gap-1 bg-slate-100 px-1.5 py-0.5 rounded">
                  <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                  </svg>
                  {{ car.capacity }} Seat
                </span>

                <span class="inline-flex items-center gap-1 bg-slate-100 px-1.5 py-0.5 rounded">
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
  </section>
</template>
