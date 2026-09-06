<script setup lang="ts">
import { onMounted, computed } from 'vue'
import { useFleet } from '@/composables/useFleet'
import HeroSection from '@/components/HeroSection.vue'
import TravelOptions from '@/components/TravelOptions.vue'
import HomeFeaturedFleet from '@/components/HomeFeaturedFleet.vue'
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
  fetchFeaturedVehicles
} = useFleet()

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
</script>

<template>
  <div>
    <!-- 1. Hero Section -->
    <HeroSection :featured-vehicle="featuredVehicle" />

    <!-- 2. Pilih Cara Perjalanan (Rental Mobil Harian vs Paket Tour HiAce) -->
    <TravelOptions />

    <!-- 3. Pilihan Armada Pilihan (Featured 3 Unit Live, Ringkas & Bersih) -->
    <HomeFeaturedFleet
      :vehicles="featuredVehicles"
      :loading="featuredLoading"
      :error="featuredError"
      :total-fleet-count="totalFleetCount"
      @retry="fetchFeaturedVehicles(true)"
    />

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
