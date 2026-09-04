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
  vehicles,
  filteredVehicles,
  loading,
  error,
  searchQuery,
  transmissionFilter,
  stats,
  fetchVehicles
} = useFleet()

// Unit unggulan hero yang diambil langsung dari armada live (utamakan Veloz unit kembar yang tersedia)
const featuredVehicle = computed(() => {
  return vehicles.value.find(v => v.name.toLowerCase().includes('veloz') && v.status === 'available')
    || vehicles.value.find(v => v.status === 'available')
    || vehicles.value[0]
    || null
})

onMounted(() => {
  document.title = '3 Putri Mulya - Rental Mobil & Tour Bintan'
  fetchVehicles()
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

    <!-- 3. Pilihan Armada (Katalog Live dari Public Fleet API FinanceFlow) -->
    <FleetCatalog
      :vehicles="filteredVehicles"
      :loading="loading"
      :error="error"
      @retry="fetchVehicles(true)"
    >
      <template #filter>
        <FleetFilter
          :search-query="searchQuery"
          :transmission-filter="transmissionFilter"
          :total-units="stats.total"
          :available-units="stats.available"
          @update:search-query="searchQuery = $event"
          @update:transmission-filter="transmissionFilter = $event"
          @reset="resetFilters"
        />
      </template>
    </FleetCatalog>

    <!-- 4. Inspirasi Perjalanan di Bintan (4 Destinasi Pendukung) -->
    <TripInspirations />

    <!-- 5. Kenapa Memilih 3 Putri Mulya (4 Nilai Konkret) -->
    <TrustGuarantees />

    <!-- 6. Area Layanan & Titik Koordinasi -->
    <LocationSection />

    <!-- 7. Pertanyaan Umum (FAQ 6 Topik) -->
    <FaqSection />

    <!-- 8. Final CTA (WhatsApp) -->
    <FinalCtaSection />
  </div>
</template>
