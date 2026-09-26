<script setup lang="ts">
import { ref, onMounted, computed, watch, defineAsyncComponent } from 'vue'
import { useLanguage } from '@/composables/useLanguage'
import { useFleet } from '@/composables/useFleet'
import type { BookingFilterParams } from '@/utils/whatsapp'
// Above-fold: eager load
import HeroSection from '@/components/HeroSection.vue'
import TrustPillarsBar from '@/components/TrustPillarsBar.vue'
import HomeFeaturedFleet from '@/components/HomeFeaturedFleet.vue'
import HomeUpcomingEvents from '@/components/HomeUpcomingEvents.vue'
import HomePopularTours from '@/components/HomePopularTours.vue'
// Below-fold: lazy load untuk tidak membebani first paint
const TripInspirations  = defineAsyncComponent(() => import('@/components/TripInspirations.vue'))
const LocationSection   = defineAsyncComponent(() => import('@/components/LocationSection.vue'))
const FaqSection        = defineAsyncComponent(() => import('@/components/FaqSection.vue'))
const FinalCtaSection   = defineAsyncComponent(() => import('@/components/FinalCtaSection.vue'))

const { isEnglish } = useLanguage()

const {
  vehicles,
  featuredVehicles,
  featuredLoading,
  featuredError,
  totalFleetCount,
  fetchVehicles,
  fetchFeaturedVehicles
} = useFleet()

const activeSearchFilter = ref<BookingFilterParams | null>(null)

function handleSearch(params: BookingFilterParams) {
  activeSearchFilter.value = params
  // Pastikan seluruh data armada telah dimuat jika pencarian aktif
  if (vehicles.value.length === 0) {
    fetchVehicles()
  }
}

function handleResetFilter() {
  activeSearchFilter.value = null
}

const displayedVehicles = computed(() => {
  if (!activeSearchFilter.value) {
    return featuredVehicles.value
  }

  const pool = vehicles.value.length > 0 ? vehicles.value : featuredVehicles.value
  const { vehicleType, passengers } = activeSearchFilter.value

  return pool.filter(v => {
    // 1. Filter tipe armada
    if (vehicleType && vehicleType !== 'all') {
      if (vehicleType === 'city-car') {
        if (v.capacity > 5 && !v.name.toLowerCase().includes('agya')) return false
      } else if (vehicleType === 'mpv') {
        if (v.capacity < 6 || v.capacity > 8) return false
      } else if (vehicleType === 'hiace') {
        if (v.capacity < 9 && !v.name.toLowerCase().includes('hiace')) return false
      }
    }

    // 2. Filter jumlah penumpang
    if (passengers) {
      if (passengers === 15) {
        if (v.capacity < 9) return false
      } else if (passengers === 7) {
        if (v.capacity < 6) return false
      } else if (passengers === 4) {
        if (v.capacity < 4) return false
      }
    }

    return true
  })
})

const featuredVehicle = computed(() => {
  return featuredVehicles.value.find(v => v.name.toLowerCase().includes('veloz') && v.status === 'available')
    || featuredVehicles.value.find(v => v.status === 'available')
    || featuredVehicles.value[0]
    || null
})

function updateDocTitle() {
  document.title = isEnglish.value
    ? 'Rental Mobil Bintan - Car Rental Bintan | 3 Putri Mulya'
    : 'Rental Mobil Bintan - Sewa Mobil & Tour Bintan | 3 Putri Mulya'
}

onMounted(() => {
  updateDocTitle()
  fetchFeaturedVehicles()
  fetchVehicles() // Muat seluruh armada agar pencarian instan
})

watch(isEnglish, () => {
  updateDocTitle()
})
</script>

<template>
  <div>
    <!-- 1. Hero Section (Includes Floating Quick Search Widget) -->
    <HeroSection :featured-vehicle="featuredVehicle" @search="handleSearch" />

    <!-- 2. Trust Pillars Bar (4 Nilai Layanan: Armada Terawat, Driver, Harga Transparan, 24 Jam) -->
    <TrustPillarsBar />

    <!-- 3. Pilihan Armada Pilihan (Featured 4 Unit Live, Clean Grid / Filtered Results) -->
    <HomeFeaturedFleet
      :vehicles="displayedVehicles"
      :loading="featuredLoading"
      :error="featuredError"
      :total-fleet-count="totalFleetCount"
      :active-filter="activeSearchFilter"
      @retry="fetchFeaturedVehicles(true)"
      @reset-filter="handleResetFilter"
    />

    <!-- 4. Agenda & Kalender Event Bintan Mendatang (Official Sports & Cultural Events) -->
    <HomeUpcomingEvents />

    <!-- 5. Paket Tour Populer (3 Scenic Highlight Cards) -->
    <HomePopularTours />

    <!-- 5. Destinasi Favorit di Bintan (Inspirasi Perjalanan) -->
    <TripInspirations />

    <!-- 6 & 7. Side-by-Side: Area Layanan & Titik Koordinasi + Pertanyaan Umum (FAQ) -->
    <section id="faq" class="py-14 sm:py-20 bg-slate-50 border-t border-slate-200 scroll-mt-16 sm:scroll-mt-20">
      <span id="lokasi-faq" class="sr-only"></span>
      <span id="lokasi" class="sr-only"></span>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-10 items-stretch">
          <div id="lokasi-card" v-reveal:fade-right>
            <LocationSection />
          </div>
          <div id="faq-card" class="scroll-mt-20" v-reveal:fade-left>
            <FaqSection />
          </div>
        </div>
      </div>
    </section>

    <!-- 8. Final CTA Banner (Dark Navy Banner + WhatsApp) -->
    <div v-reveal:zoom-in>
      <FinalCtaSection />
    </div>
  </div>
</template>
