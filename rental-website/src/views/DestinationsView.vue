<script setup lang="ts">
import { ref, computed } from 'vue'
import { RouterLink } from 'vue-router'
import { destinationsList } from '@/config/destinations'
import { siteConfig } from '@/config/site'

const searchQuery = ref('')
const selectedCategory = ref<string>('all')

const categories = [
  { id: 'all', label: 'Semua Destinasi' },
  { id: 'pantai', label: 'Pantai & Pesisir' },
  { id: 'ikonik', label: 'Spot Foto Ikonik' },
  { id: 'religi', label: 'Religi & Arsitektur' },
  { id: 'ekowisata', label: 'Ekowisata & Alam' },
  { id: 'budaya', label: 'Cagar Budaya & Museum' }
]

const filteredDestinations = computed(() => {
  return destinationsList.filter((dest) => {
    const matchesCategory = selectedCategory.value === 'all' || dest.category === selectedCategory.value
    const query = searchQuery.value.toLowerCase().trim()
    const matchesSearch = !query || 
      dest.name.toLowerCase().includes(query) ||
      dest.location.toLowerCase().includes(query) ||
      dest.description.toLowerCase().includes(query) ||
      dest.categoryLabel.toLowerCase().includes(query)
    return matchesCategory && matchesSearch
  })
})

const getWhatsAppUrl = (text: string) => {
  const phone = siteConfig.rentalPhone.replace(/\D/g, '')
  return `https://wa.me/${phone}?text=${encodeURIComponent(text)}`
}

const waGeneralConsultUrl = computed(() => {
  const phone = siteConfig.rentalPhone.replace(/\D/g, '')
  const text = 'Halo 3 Putri Mulya, saya ingin konsultasi rute wisata keliling Pulau Bintan dan rekomendasi armada yang cocok.'
  return `https://wa.me/${phone}?text=${encodeURIComponent(text)}`
})
</script>

<template>
  <div class="bg-slate-50 font-sans">
    <!-- Hero Header with Breathtaking Scenic Destination Photography -->
    <section class="relative text-white pt-16 sm:pt-20 pb-20 sm:pb-24 px-4 sm:px-6 lg:px-8 overflow-hidden bg-slate-950">
      <!-- Background Scenic Image -->
      <img
        src="/images/bintan-travel-hero.jpg"
        alt="Wisata Bahari dan Pesisir Pulau Bintan"
        class="absolute inset-0 w-full h-full object-cover object-center scale-105 filter brightness-[0.85]"
      />
      <!-- Gradient Overlays for High Legibility & Premium Deep Atmosphere -->
      <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/75 to-slate-950/80 pointer-events-none"></div>
      <div class="absolute inset-0 bg-blue-950/30 mix-blend-multiply pointer-events-none"></div>

      <div class="max-w-7xl mx-auto relative z-10 text-center">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-900/80 border border-white/20 text-blue-300 text-xs font-semibold mb-5 backdrop-blur-md shadow-lg">
          <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
          <span>Dokumentasi Resmi &amp; Pariwisata Bintan</span>
        </div>

        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white mb-4 drop-shadow-md">
          Panduan &amp; Inspirasi Destinasi Wisata Bintan
        </h1>

        <p class="max-w-2xl mx-auto text-sm sm:text-base text-slate-200 leading-relaxed mb-8 drop-shadow-sm">
          Eksplorasi pantai pasir putih, cagar sejarah Melayu, dan vihara megah berkelas dunia. Nikmati kebebasan rute dengan sewa mobil lepas kunci atau kenyamanan paket supir 3 Putri Mulya.
        </p>

        <!-- Quick Summary Stats -->
        <div class="inline-flex flex-wrap items-center justify-center gap-2.5 sm:gap-4 text-xs text-white">
          <div class="px-3.5 py-1.5 rounded-xl bg-slate-900/80 border border-white/20 backdrop-blur-md flex items-center gap-2 shadow-sm">
            <span class="text-blue-400 font-extrabold">{{ destinationsList.length }}</span> Destinasi Terdata
          </div>
          <div class="px-3.5 py-1.5 rounded-xl bg-slate-900/80 border border-white/20 backdrop-blur-md flex items-center gap-2 shadow-sm">
            <span class="text-emerald-400 font-extrabold">100%</span> Rute Bebas Fleksibel
          </div>
          <div class="px-3.5 py-1.5 rounded-xl bg-slate-900/80 border border-white/20 backdrop-blur-md flex items-center gap-2 shadow-sm">
            <span class="text-amber-400 font-extrabold">Lepas Kunci</span> / Driver All-In
          </div>
        </div>
      </div>
    </section>

    <!-- Main Content Section: Filter Bar & Cards -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
      <!-- Search & Category Filters -->
      <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-sm mb-8 space-y-4">
        <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between">
          <!-- Search Bar -->
          <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari destinasi, lokasi, atau kata kunci (misal: Gurun, Lagoi, Vihara)..."
              class="w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 text-slate-800 placeholder-slate-400 transition-all"
            />
          </div>

          <!-- Counter Info -->
          <div class="text-xs text-slate-500 shrink-0 self-center">
            Menampilkan <span class="font-bold text-slate-800">{{ filteredDestinations.length }}</span> destinasi
          </div>
        </div>

        <!-- Filter Category Pills -->
        <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto pb-1 pt-1 scrollbar-none">
          <button
            v-for="cat in categories"
            :key="cat.id"
            type="button"
            @click="selectedCategory = cat.id"
            class="px-3.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all"
            :class="selectedCategory === cat.id
              ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/20'
              : 'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-800'"
          >
            {{ cat.label }}
          </button>
        </div>
      </div>

      <!-- Destinations Grid -->
      <div v-if="filteredDestinations.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <article
          v-for="dest in filteredDestinations"
          :key="dest.id"
          class="bg-white rounded-2xl border border-slate-200/90 overflow-hidden shadow-sm hover:shadow-md hover:border-slate-300 transition-all flex flex-col group"
        >
          <!-- Destination Photo Header -->
          <div class="relative aspect-[16/10] overflow-hidden bg-slate-900">
            <img
              :src="dest.image"
              :alt="dest.name"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
              loading="lazy"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-transparent pointer-events-none"></div>

            <!-- Category Badge -->
            <div class="absolute top-3 left-3 z-10">
              <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-900/85 text-slate-100 border border-white/20 backdrop-blur-sm shadow-sm">
                {{ dest.badge }}
              </span>
            </div>

            <!-- Official Photo Credit Badge -->
            <div class="absolute top-3 right-3 z-10">
              <span
                :title="'Sumber foto: ' + dest.photoCreditFull"
                class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-medium bg-slate-950/70 text-slate-200 backdrop-blur-sm border border-white/15"
              >
                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="truncate max-w-[100px]">{{ dest.photoCredit }}</span>
              </span>
            </div>

            <!-- Title & Location Overlay -->
            <div class="absolute bottom-3 left-4 right-4 z-10">
              <h2 class="text-base sm:text-lg font-bold text-white leading-tight drop-shadow-md group-hover:text-blue-200 transition-colors line-clamp-1">
                {{ dest.name }}
              </h2>
              <p class="text-xs text-slate-300 flex items-center gap-1 mt-1">
                <svg class="w-3.5 h-3.5 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="truncate">{{ dest.location }}</span>
              </p>
            </div>
          </div>

          <!-- Card Body -->
          <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between space-y-4">
            <!-- Description -->
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
              {{ dest.description }}
            </p>

            <!-- Key Highlights List -->
            <div class="space-y-1.5 bg-slate-50 p-3 rounded-xl border border-slate-100 text-xs text-slate-700">
              <div class="text-[11px] font-bold text-slate-900 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                <span class="w-1 h-1 rounded-full bg-blue-600"></span>
                Daya Tarik Utama:
              </div>
              <ul class="space-y-1">
                <li v-for="(highlight, hIdx) in dest.highlights" :key="hIdx" class="flex items-start gap-1.5 text-[11px] leading-snug">
                  <span class="text-blue-600 font-bold shrink-0">•</span>
                  <span>{{ highlight }}</span>
                </li>
              </ul>
            </div>

            <!-- Route & Recommended Fleet Info -->
            <div class="pt-2 border-t border-slate-100 space-y-1.5 text-xs">
              <div class="flex items-center gap-2 text-slate-500 text-[11px]">
                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ dest.driveTime }}</span>
              </div>
              <div class="flex items-center gap-2 text-slate-500 text-[11px]">
                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                </svg>
                <span>Armada Pas: <strong class="text-slate-700">{{ dest.recommendedFleet }}</strong></span>
              </div>
            </div>

            <!-- Action CTA Buttons -->
            <div class="pt-2 flex items-center gap-2">
              <a
                :href="getWhatsAppUrl(dest.waText)"
                target="_blank"
                rel="noopener noreferrer"
                class="flex-1 py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold inline-flex items-center justify-center gap-1.5 shadow-sm transition-all active:scale-95"
              >
                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                </svg>
                <span>Sewa Mobil ke Sini</span>
              </a>

              <RouterLink
                to="/paket-tour-bintan"
                class="py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold inline-flex items-center justify-center transition-colors"
                title="Lihat Paket Tour HiAce"
              >
                Paket Tour
              </RouterLink>
            </div>
          </div>
        </article>
      </div>

      <!-- Empty State when search returns nothing -->
      <div v-else class="bg-white rounded-2xl border border-slate-200 p-12 text-center max-w-md mx-auto space-y-3">
        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <h3 class="text-sm font-bold text-slate-800">Tidak ada destinasi yang cocok</h3>
        <p class="text-xs text-slate-500">Coba ubah kata kunci pencarian atau pilih kategori "Semua Destinasi".</p>
        <button
          type="button"
          @click="searchQuery = ''; selectedCategory = 'all'"
          class="px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-bold hover:bg-blue-700 transition-colors"
        >
          Reset Pencarian
        </button>
      </div>

      <!-- Bottom Banner: Custom Itinerary & Consultation -->
      <section class="mt-14 p-6 sm:p-8 rounded-3xl bg-gradient-to-br from-slate-900 via-slate-900 to-blue-950 text-white border border-slate-800 shadow-xl flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="space-y-2 text-center md:text-left">
          <span class="inline-block text-[11px] font-bold uppercase tracking-wider text-blue-400">Konsultasi Rute Wisata</span>
          <h3 class="text-xl sm:text-2xl font-black text-white tracking-tight">Ingin Rute Kustom atau Keliling Banyak Tempat?</h3>
          <p class="text-xs sm:text-sm text-slate-300 max-w-xl leading-relaxed">
            Tim 3 Putri Mulya siap merekomendasikan urutan rute paling efisien, estimasi BBM, dan armada yang paling nyaman untuk rombongan Anda.
          </p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
          <a
            :href="waGeneralConsultUrl"
            target="_blank"
            rel="noopener noreferrer"
            class="px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold inline-flex items-center gap-2 shadow-lg shadow-emerald-950/40 transition-all active:scale-95"
          >
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
            </svg>
            <span>Konsultasi Itinerary Gratis via WA</span>
          </a>
        </div>
      </section>
    </main>
  </div>
</template>
