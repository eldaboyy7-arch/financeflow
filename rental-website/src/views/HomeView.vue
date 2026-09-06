<script setup lang="ts">
import { onMounted, computed } from 'vue'
import { useFleet } from '@/composables/useFleet'
import HeroSection from '@/components/HeroSection.vue'
import TravelOptions from '@/components/TravelOptions.vue'
import FleetFilter from '@/components/FleetFilter.vue'
import FleetCatalog from '@/components/FleetCatalog.vue'
import TripInspirations from '@/components/TripInspirations.vue'
import TrustGuarantees from '@/components/TrustGuarantees.vue'
import LocationSection from '@/components/LocationSection.vue'
import FaqSection from '@/components/FaqSection.vue'
import FinalCtaSection from '@/components/FinalCtaSection.vue'

const {
  featuredVehicles,
  featuredLoading,
  featuredError,
  totalFleetCount,
  searchQuery,
  transmissionFilter,
  fetchFeaturedVehicles
} = useFleet()

// Filter 3 unit pilihan jika user melakukan pencarian cepat di homepage
const displayVehicles = computed(() => {
  return featuredVehicles.value.filter((vehicle) => {
    if (searchQuery.value.trim()) {
      const q = searchQuery.value.toLowerCase().trim()
      const matchName = vehicle.name.toLowerCase().includes(q)
      const matchBrand = vehicle.brand ? vehicle.brand.toLowerCase().includes(q) : false
      if (!matchName && !matchBrand) return false
    }
    if (transmissionFilter.value !== 'all') {
      if (vehicle.transmission !== transmissionFilter.value) return false
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

onMounted(() => {
  document.title = '3 Putri Mulya - Rental Mobil & Tour Bintan'
  fetchFeaturedVehicles()
})

const resetFilters = () => {
  searchQuery.value = ''
  transmissionFilter.value = 'all'
}
</script>

<template>
  <div>
    <!-- 1. Hero Section -->
    <HeroSection :featured-vehicle="featuredVehicle" />

    <!-- 2. Pilih Cara Perjalanan (Rental Mobil Harian vs Paket Tour HiAce) -->
    <TravelOptions />

    <!-- 3. Pilihan Armada Pilihan (Featured 3 Unit Live dari Public Fleet API FinanceFlow) -->
    <FleetCatalog
      :vehicles="displayVehicles"
      :loading="featuredLoading"
      :error="featuredError"
      :total-fleet-count="totalFleetCount"
      @retry="fetchFeaturedVehicles(true)"
    >
      <template #filter>
        <FleetFilter
          :search-query="searchQuery"
          :transmission-filter="transmissionFilter"
          :total-units="totalFleetCount || featuredVehicles.length"
          :available-units="featuredVehicles.filter(v => v.status === 'available').length"
          @update:search-query="searchQuery = $event"
          @update:transmission-filter="transmissionFilter = $event"
          @reset="resetFilters"
        />
      </template>
    </FleetCatalog>

    <!-- 4. Inspirasi Perjalanan di Bintan (4 Destinasi) -->
    <TripInspirations />

    <!-- 5. Kenapa Memilih 3 Putri Mulya (4 Nilai Konkret) -->
    <TrustGuarantees />

    <!-- 6 & 7. Side-by-Side: Area Layanan & Titik Koordinasi + Pertanyaan Umum (FAQ) -->
    <section id="lokasi-faq" class="py-14 sm:py-20 bg-slate-50 border-t border-slate-200 scroll-mt-16">
      <span id="lokasi" class="sr-only"></span>
      <span id="faq" class="sr-only"></span>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-10 items-stretch">
          <LocationSection />
          <FaqSection />
        </div>
      </div>
    </section>

    <!-- 8. Final CTA (WhatsApp) -->
    <FinalCtaSection />
  </div>
</template>
