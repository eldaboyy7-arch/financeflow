<script setup lang="ts">
import { onMounted, computed } from 'vue'
import { useFleet } from '@/composables/useFleet'
import TheNavbar from '@/components/TheNavbar.vue'
import HeroSection from '@/components/HeroSection.vue'
import FleetFilter from '@/components/FleetFilter.vue'
import FleetCatalog from '@/components/FleetCatalog.vue'
import RentalPackages from '@/components/RentalPackages.vue'
import RentalTerms from '@/components/RentalTerms.vue'
import TrustGuarantees from '@/components/TrustGuarantees.vue'
import LocationSection from '@/components/LocationSection.vue'
import FaqSection from '@/components/FaqSection.vue'
import TheFooter from '@/components/TheFooter.vue'
import FloatingWhatsappBar from '@/components/FloatingWhatsappBar.vue'

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
  fetchVehicles()
})

const resetFilters = () => {
  searchQuery.value = ''
  transmissionFilter.value = 'all'
}
</script>

<template>
  <div class="min-h-screen flex flex-col bg-slate-50 text-slate-800">
    <!-- Navbar -->
    <TheNavbar />

    <!-- Main Content -->
    <main class="flex-1">
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

      <!-- Paket Layanan (Hanya tampil jika ada kebijakan yang dikonfigurasi) -->
      <RentalPackages />

      <!-- Ketentuan & Persyaratan (Hanya tampil jika ada kebijakan yang dikonfigurasi) -->
      <RentalTerms />

      <!-- Jaminan Kualitas Layanan -->
      <TrustGuarantees />

      <!-- Lokasi & Koordinasi Serah Terima -->
      <LocationSection />

      <!-- Pertanyaan Umum (FAQ) -->
      <FaqSection />
    </main>

    <!-- Footer -->
    <TheFooter />

    <!-- Sticky Floating WhatsApp Bar (Mobile only) -->
    <FloatingWhatsappBar />
  </div>
</template>
