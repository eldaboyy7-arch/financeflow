<script setup lang="ts">
import { RouterLink } from 'vue-router'
import { siteConfig } from '@/config/site'

// page: Layanan

const getWaUrl = (text: string) => {
  const clean = siteConfig.rentalPhone.replace(/\D/g, '')
  return `https://wa.me/${clean}?text=${encodeURIComponent(text)}`
}

const services = [
  {
    id: 'lepas-kunci',
    icon: '🚗',
    color: 'blue',
    badge: 'Paling Fleksibel',
    title: 'Sewa Lepas Kunci',
    subtitle: 'Tanpa Supir — Bebas Kemana Saja',
    description: 'Pesan mobil dan bawa sendiri ke mana pun kamu mau di Bintan & Tanjung Pinang. Cocok untuk traveler mandiri, trip keluarga, atau perjalanan bisnis yang butuh privasi penuh.',
    features: [
      'Unit bersih, terawat & ber-AC dingin',
      'Harga harian transparan tanpa biaya tersembunyi',
      'Tersedia City Car, MPV, hingga HiAce Minibus',
      'Bebas pilih rute & jadwal sendiri',
      'Antar-jemput unit bisa diatur (area tertentu)',
    ],
    cta: 'Lihat Armada Tersedia',
    ctaLink: '/armada',
    ctaWa: 'Halo 3 Putri Mulya, saya ingin tanya ketersediaan unit untuk sewa lepas kunci di Bintan. Mohon info harga dan unit yang tersedia.',
    image: '/images/fleet/avanza-veloz-putih-bp1649yb.jpg',
  },
  {
    id: 'dengan-supir',
    icon: '👨‍✈️',
    color: 'emerald',
    badge: 'Paling Santai',
    title: 'Sewa dengan Supir',
    subtitle: 'Driver Berpengalaman — Kamu Tinggal Nikmati',
    description: 'Nikmati perjalanan tanpa repot nyetir. Driver kami ramah, hafal seluruh sudut Bintan, dan siap menemani dari pagi hingga malam. Cocok untuk wisata keluarga, rombongan kantor, atau tamu VIP.',
    features: [
      'Driver ramah, berpengalaman & hafal jalan Bintan',
      'Tersedia unit HiAce Commuter & Premio (15 Kursi)',
      'Include BBM sesuai paket',
      'Bisa request rute wisata favorit',
      'Penjemputan dari pelabuhan, bandara, atau hotel',
    ],
    cta: 'Lihat Paket Tour',
    ctaLink: '/paket-tour-bintan',
    ctaWa: 'Halo 3 Putri Mulya, saya ingin info sewa mobil dengan supir untuk perjalanan di Bintan. Mohon info harga dan ketersediaan.',
    image: '/images/fleet/hiace-premio-gold-bp7024bu.jpg',
  },
  {
    id: 'paket-tour',
    icon: '🏝️',
    color: 'amber',
    badge: 'Terlengkap',
    title: 'Paket Tour Bintan',
    subtitle: 'Keliling Destinasi Favorit — Satu Harga Sudah All-In',
    description: 'Paket wisata seharian keliling destinasi ikonik Pulau Bintan. Berangkat pagi, pulang sore/malam, semua sudah termasuk armada HiAce + supir + BBM. Tinggal datang dan nikmati!',
    features: [
      'Armada Toyota HiAce Commuter / Premio (15 Kursi)',
      'Include supir & BBM — tidak ada biaya tambahan',
      'Rute ikonik: Sleeping Buddha, Lagoi, Gonggong, Patung Seribu',
      'Karaoke system on board',
      'Penjemputan dari pelabuhan, bandara, atau hotel',
    ],
    cta: 'Lihat Paket Tour',
    ctaLink: '/paket-tour-bintan',
    ctaWa: 'Halo 3 Putri Mulya, saya tertarik dengan Paket Tour Bintan. Mohon info paket dan harga yang tersedia.',
    image: '/images/destinations/lagoi.jpg',
  },
  {
    id: 'antar-jemput',
    icon: '✈️',
    color: 'violet',
    badge: 'Praktis',
    title: 'Antar Jemput Bandara & Ferry',
    subtitle: 'Tepat Waktu — Nyaman Sejak Pertama Tiba',
    description: 'Layanan jemput atau antar ke Bandara Raja Haji Fisabilillah (RHF) dan Pelabuhan Sri Bintan Pura. Tidak perlu antri taksi atau bingung cari transportasi saat baru tiba di Bintan.',
    features: [
      'Jemput / antar ke Bandara RHF & Pelabuhan Sri Bintan Pura',
      'Tepat waktu, tidak ada drama ketinggalan',
      'Tersedia untuk grup kecil hingga rombongan besar',
      'Bisa pesan H-1 atau beberapa hari sebelumnya',
      'Harga sesuai jarak & jumlah penumpang',
    ],
    cta: 'Chat WhatsApp',
    ctaLink: null,
    ctaWa: 'Halo 3 Putri Mulya, saya ingin pesan layanan antar/jemput bandara atau pelabuhan di Bintan. Mohon info harga dan ketersediaan.',
    image: '/images/bintan-roadtrip-consultation.jpg',
  },
]

const colorMap: Record<string, { badge: string; icon: string; border: string; btn: string; dot: string }> = {
  blue:   { badge: 'bg-blue-50 text-blue-600 border-blue-200',    icon: 'bg-blue-100 text-blue-600',    border: 'border-blue-500',   btn: 'bg-blue-600 hover:bg-blue-500 shadow-blue-600/25',   dot: 'bg-blue-500' },
  emerald:{ badge: 'bg-emerald-50 text-emerald-700 border-emerald-200', icon: 'bg-emerald-100 text-emerald-600', border: 'border-emerald-500', btn: 'bg-emerald-600 hover:bg-emerald-500 shadow-emerald-600/25', dot: 'bg-emerald-500' },
  amber:  { badge: 'bg-amber-50 text-amber-700 border-amber-200', icon: 'bg-amber-100 text-amber-600',  border: 'border-amber-500',  btn: 'bg-amber-500 hover:bg-amber-400 shadow-amber-500/25',   dot: 'bg-amber-500' },
  violet: { badge: 'bg-violet-50 text-violet-700 border-violet-200', icon: 'bg-violet-100 text-violet-600', border: 'border-violet-500', btn: 'bg-violet-600 hover:bg-violet-500 shadow-violet-600/25', dot: 'bg-violet-500' },
}
</script>

<template>
  <div class="bg-slate-50 font-sans min-h-screen">

    <!-- Hero Header -->
    <section class="relative bg-slate-900 text-white pt-20 pb-16 px-4 sm:px-6 lg:px-8 overflow-hidden">
      <!-- Background image -->
      <img
        src="/images/bintan-travel-hero.jpg"
        alt="Layanan Rental Mobil Bintan"
        class="absolute inset-0 w-full h-full object-cover object-center filter brightness-[0.35]"
      />
      <div class="absolute inset-0 bg-gradient-to-b from-slate-900/60 to-slate-900/80 pointer-events-none"></div>

      <div class="max-w-4xl mx-auto relative z-10 text-center">
        <!-- Eyebrow -->
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/25 text-white text-xs font-semibold mb-6 backdrop-blur-md">
          <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
          <span>3 PUTRI MULYA — BINTAN & TANJUNG PINANG</span>
        </div>

        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white mb-4 leading-tight">
          Layanan Kami
        </h1>
        <p class="max-w-xl mx-auto text-sm sm:text-base text-white/75 leading-relaxed mb-8">
          Dari sewa lepas kunci harian, sewa dengan supir, paket tour wisata, hingga antar-jemput bandara & ferry — semua tersedia untuk kebutuhan perjalanan Anda di Bintan.
        </p>

        <!-- Quick CTA -->
        <a
          :href="getWaUrl('Halo 3 Putri Mulya, saya ingin tanya informasi layanan yang tersedia.')"
          target="_blank"
          rel="noopener noreferrer"
          class="inline-flex items-center gap-2.5 px-6 py-3 rounded-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-lg shadow-emerald-900/40 transition-all active:scale-95"
        >
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
          </svg>
          Konsultasi Gratis via WhatsApp
        </a>
      </div>
    </section>

    <!-- Services Section -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 space-y-10 sm:space-y-14">

      <!-- Service Cards -->
      <div
        v-for="(svc, idx) in services"
        :key="svc.id"
        class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden"
      >
        <div
          class="grid grid-cols-1 lg:grid-cols-2 items-stretch"
          :class="idx % 2 === 1 ? 'lg:flex-row-reverse' : ''"
        >
          <!-- Image Side -->
          <div
            class="relative min-h-[220px] sm:min-h-[280px] lg:min-h-full overflow-hidden bg-slate-100"
            :class="idx % 2 === 1 ? 'lg:order-2' : 'lg:order-1'"
          >
            <img
              :src="svc.image"
              :alt="svc.title"
              class="w-full h-full object-cover"
            />
            <!-- Color accent overlay subtle -->
            <div class="absolute inset-0 opacity-10"
              :class="{
                'bg-blue-600': svc.color === 'blue',
                'bg-emerald-600': svc.color === 'emerald',
                'bg-amber-500': svc.color === 'amber',
                'bg-violet-600': svc.color === 'violet',
              }"
            ></div>
          </div>

          <!-- Content Side -->
          <div
            class="p-7 sm:p-10 flex flex-col justify-center"
            :class="idx % 2 === 1 ? 'lg:order-1' : 'lg:order-2'"
          >
            <!-- Icon + Badge row -->
            <div class="flex items-center gap-3 mb-5">
              <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl shrink-0"
                :class="colorMap[svc.color].icon"
              >
                {{ svc.icon }}
              </div>
              <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border"
                :class="colorMap[svc.color].badge"
              >
                {{ svc.badge }}
              </span>
            </div>

            <!-- Title -->
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-tight mb-1">
              {{ svc.title }}
            </h2>
            <p class="text-sm font-semibold mb-4"
              :class="{
                'text-blue-600': svc.color === 'blue',
                'text-emerald-600': svc.color === 'emerald',
                'text-amber-600': svc.color === 'amber',
                'text-violet-600': svc.color === 'violet',
              }"
            >
              {{ svc.subtitle }}
            </p>
            <p class="text-sm text-slate-600 leading-relaxed mb-6">
              {{ svc.description }}
            </p>

            <!-- Features -->
            <ul class="space-y-2 mb-8">
              <li v-for="f in svc.features" :key="f" class="flex items-start gap-2.5 text-sm text-slate-700">
                <span class="w-5 h-5 rounded-full flex items-center justify-center shrink-0 mt-0.5"
                  :class="colorMap[svc.color].icon"
                >
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                  </svg>
                </span>
                {{ f }}
              </li>
            </ul>

            <!-- CTA Buttons -->
            <div class="flex flex-wrap gap-3">
              <!-- Primary: internal link or WA -->
              <RouterLink
                v-if="svc.ctaLink"
                :to="svc.ctaLink"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-white font-bold text-sm shadow-lg transition-all active:scale-95"
                :class="colorMap[svc.color].btn"
              >
                {{ svc.cta }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
              </RouterLink>
              <a
                v-else
                :href="getWaUrl(svc.ctaWa)"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-white font-bold text-sm shadow-lg transition-all active:scale-95"
                :class="colorMap[svc.color].btn"
              >
                {{ svc.cta }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
              </a>

              <!-- Secondary: WA -->
              <a
                v-if="svc.ctaLink"
                :href="getWaUrl(svc.ctaWa)"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-slate-700 font-bold text-sm border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 transition-all"
              >
                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                </svg>
                Tanya Admin
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom CTA banner -->
      <div class="bg-slate-900 rounded-3xl p-8 sm:p-10 text-center text-white">
        <p class="text-xs font-bold uppercase tracking-widest text-amber-400 mb-3">TIDAK YAKIN PILIH LAYANAN MANA?</p>
        <h3 class="text-2xl sm:text-3xl font-black mb-3">Konsultasi Gratis dengan Admin Kami</h3>
        <p class="text-sm text-slate-400 mb-7 max-w-lg mx-auto">
          Ceritakan kebutuhan perjalanan kamu, kami bantu rekomendasikan layanan terbaik sesuai budget dan jumlah rombongan.
        </p>
        <a
          :href="getWaUrl('Halo 3 Putri Mulya, saya ingin konsultasi untuk memilih layanan sewa yang sesuai dengan kebutuhan saya.')"
          target="_blank"
          rel="noopener noreferrer"
          class="inline-flex items-center gap-3 px-7 py-3.5 rounded-full bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-sm shadow-xl shadow-emerald-900/30 transition-all active:scale-95"
        >
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
          </svg>
          Chat WhatsApp Sekarang
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
          </svg>
        </a>
      </div>

    </main>
  </div>
</template>
