<script setup lang="ts">
import { onMounted, computed } from 'vue'
import { useFleet } from '@/composables/useFleet'
import HeroSection from '@/components/HeroSection.vue'
import FleetFilter from '@/components/FleetFilter.vue'
import FleetCatalog from '@/components/FleetCatalog.vue'
import TourPackages from '@/components/TourPackages.vue'
import RentalPackages from '@/components/RentalPackages.vue'
import RentalTerms from '@/components/RentalTerms.vue'
import TrustGuarantees from '@/components/TrustGuarantees.vue'
import LocationSection from '@/components/LocationSection.vue'
import FaqSection from '@/components/FaqSection.vue'

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
  document.title = '3 Putri Mulya - Rental Mobil Lepas Kunci & Driver Bintan'
  fetchVehicles()
})

const resetFilters = () => {
  searchQuery.value = ''
  transmissionFilter.value = 'all'
}
</script>

<template>
  <div>
    <!-- Hero Section -->
    <HeroSection :featured-vehicle="featuredVehicle" />

    <!-- Fleet Catalog & Filter -->
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

    <!-- Paket Tour & Travel Bintan (Section Baru Phase 1) -->
    <TourPackages />

    <!-- Paket Layanan Rental Harian (Lepas Kunci & Driver - Hanya tampil jika dikonfigurasi) -->
    <RentalPackages />

    <!-- Ketentuan & Persyaratan Rental (Hanya tampil jika dikonfigurasi) -->
    <RentalTerms />

    <!-- Jaminan Kualitas Layanan -->
    <TrustGuarantees />

    <!-- Lokasi & Koordinasi Serah Terima -->
    <LocationSection />

    <!-- Pertanyaan Umum (FAQ) -->
    <FaqSection />
  </div>
</template>
