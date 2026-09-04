<script setup lang="ts">
import { computed } from 'vue'
import type { PublicVehicle } from '@/types/fleet'
import { siteConfig } from '@/config/site'
import { generateGeneralWhatsAppUrl, generateVehicleWhatsAppUrl } from '@/utils/whatsapp'

const props = defineProps<{
  featuredVehicle?: PublicVehicle | null
}>()

const waGeneralUrl = computed(() => generateGeneralWhatsAppUrl(siteConfig.rentalPhone, siteConfig.rentalName))

const featuredWaUrl = computed(() => {
  if (!props.featuredVehicle) return waGeneralUrl.value
  return generateVehicleWhatsAppUrl(props.featuredVehicle, siteConfig.rentalPhone, siteConfig.rentalName)
})
</script>

<template>
  <section class="relative bg-white border-b border-slate-200 pt-8 pb-12 sm:pt-12 sm:pb-16 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
        <!-- Left Column: Customer-Oriented Copy & CTAs -->
        <div class="lg:col-span-7">
          <!-- Status Pill -->
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-medium mb-5 border border-slate-200">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>Armada Terawat & Bersih</span>
          </div>

          <!-- Natural, Customer-Centric Headline -->
          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-[1.15] mb-4">
            Sewa Mobil Nyaman Lepas Kunci & Driver untuk Perjalanan Anda
          </h1>

          <!-- Clear Customer Subcopy -->
          <p class="text-base sm:text-lg text-slate-600 mb-8 leading-relaxed max-w-2xl">
            Pilihan armada terawat dengan tarif harian transparan. Telusuri katalog lengkap kami, cek status ketersediaan unit, dan konsultasikan jadwal sewa Anda langsung via WhatsApp.
          </p>

          <!-- Action Buttons -->
          <div class="flex flex-wrap items-center gap-3.5 mb-8">
            <a
              href="#katalog"
              class="inline-flex items-center justify-center px-6 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-sm transition-colors"
            >
              Lihat Katalog Armada
              <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </a>

            <a
              :href="waGeneralUrl"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center justify-center px-5 py-3 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-sm transition-colors"
            >
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
              </svg>
              Konsultasi WhatsApp
            </a>
          </div>

          <!-- Reassuring Feature Points -->
          <div class="flex flex-wrap items-center gap-y-2 gap-x-6 text-xs font-medium text-slate-500 pt-4 border-t border-slate-100">
            <div class="flex items-center gap-1.5">
              <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              <span>Tarif Harian Transparan</span>
            </div>
            <div class="flex items-center gap-1.5">
              <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              <span>Unit Siap Jalan</span>
            </div>
            <div class="flex items-center gap-1.5">
              <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              <span>Konfirmasi Cepat via WhatsApp</span>
            </div>
          </div>
        </div>

        <!-- Right Column: Featured Vehicle Showcase (Tangible Automotive Hero) -->
        <div class="lg:col-span-5">
          <div
            v-if="featuredVehicle"
            class="bg-slate-900 rounded-2xl overflow-hidden border border-slate-800 shadow-xl text-white relative group"
          >
            <!-- Card Header Tag -->
            <div class="flex items-center justify-between px-5 pt-4 pb-2">
              <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                Unit Rekomendasi
              </span>
              <span
                v-if="featuredVehicle.status === 'available'"
                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30"
              >
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                Tersedia Sekarang
              </span>
              <span
                v-else
                class="text-xs text-slate-400 font-medium"
              >
                {{ featuredVehicle.status_label }}
              </span>
            </div>

            <!-- Vehicle Visual Container -->
            <div class="relative aspect-[16/10] bg-slate-950/60 overflow-hidden flex items-center justify-center p-4">
              <img
                v-if="featuredVehicle.photo_url"
                :src="featuredVehicle.photo_url"
                :alt="featuredVehicle.name"
                class="w-full h-full object-cover rounded-lg"
              />
              <div
                v-else
                class="w-full h-full flex flex-col items-center justify-center text-slate-500"
              >
                <!-- Sleek Car Silhouette -->
                <svg class="w-24 h-24 text-slate-700 mb-2" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                </svg>
                <span class="text-xs text-slate-400 font-medium">Foto Unit dari Sistem Armada</span>
              </div>
            </div>

            <!-- Vehicle Details Bar -->
            <div class="p-5 bg-slate-900 border-t border-slate-800">
              <div class="flex items-start justify-between gap-3 mb-3">
                <div>
                  <h3 class="text-lg font-bold text-white leading-snug">
                    {{ featuredVehicle.name }}
                  </h3>
                  <div class="text-xs text-slate-400 mt-0.5">
                    Tahun {{ featuredVehicle.model_year }} &bull; {{ featuredVehicle.transmission_label }} &bull; {{ featuredVehicle.capacity }} Kursi
                  </div>
                </div>
                <div class="text-right shrink-0">
                  <span class="text-[10px] text-slate-400 block uppercase tracking-wider">Tarif Sewa</span>
                  <span class="text-base font-extrabold text-white">
                    {{ featuredVehicle.daily_rate_formatted }}
                  </span>
                  <span class="text-[10px] text-slate-400">/hari</span>
                </div>
              </div>

              <a
                :href="featuredWaUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition-colors shadow-xs"
              >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                </svg>
                Pesan Unit Ini via WhatsApp
              </a>
            </div>
          </div>

          <!-- Fallback subtle badge if no vehicles loaded yet -->
          <div
            v-else
            class="aspect-[16/10] bg-slate-100 rounded-2xl border border-slate-200 flex flex-col items-center justify-center p-6 text-center"
          >
            <svg class="w-16 h-16 text-slate-300 mb-3" fill="currentColor" viewBox="0 0 24 24">
              <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
            </svg>
            <span class="text-sm font-semibold text-slate-600">Armada Rental Terawat</span>
            <span class="text-xs text-slate-400 mt-1">Lihat katalog lengkap di bawah</span>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
