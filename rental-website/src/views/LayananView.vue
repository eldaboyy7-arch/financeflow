<script setup lang="ts">
import { RouterLink } from 'vue-router'
import { siteConfig } from '@/config/site'

const waUrl = (text: string) => {
  const clean = siteConfig.rentalPhone.replace(/\D/g, '')
  return `https://wa.me/${clean}?text=${encodeURIComponent(text)}`
}

const services = [
  {
    number: '01',
    accent: 'bg-blue-500',
    tag: 'Paling Fleksibel',
    tagColor: 'text-blue-600 bg-blue-50 ring-blue-100',
    title: 'Sewa Lepas Kunci',
    desc: 'Pesan mobil, ambil kunci, bebas kemana saja. Tersedia City Car, MPV, hingga HiAce untuk trip pribadi atau keluarga di Bintan.',
    features: ['City Car, MPV, HiAce tersedia', 'Tarif harian transparan', 'Unit bersih & ber-AC prima'],
    link: '/armada',
    linkLabel: 'Lihat Armada',
    wa: 'Halo 3 Putri Mulya, saya ingin tanya ketersediaan unit sewa lepas kunci di Bintan.',
  },
  {
    number: '02',
    accent: 'bg-emerald-500',
    tag: 'Paling Santai',
    tagColor: 'text-emerald-600 bg-emerald-50 ring-emerald-100',
    title: 'Sewa dengan Supir',
    desc: 'Duduk santai, driver kami yang hafal seluruh penjuru Bintan siap mengantar rombongan ke mana saja seharian penuh.',
    features: ['Driver profesional & ramah', 'HiAce Commuter / Premio (15 kursi)', 'Jemput dari bandara / pelabuhan / hotel'],
    link: '/paket-tour-bintan',
    linkLabel: 'Lihat Paket',
    wa: 'Halo 3 Putri Mulya, saya ingin sewa mobil dengan supir untuk perjalanan di Bintan.',
  },
  {
    number: '03',
    accent: 'bg-amber-400',
    tag: 'All-In',
    tagColor: 'text-amber-700 bg-amber-50 ring-amber-100',
    title: 'Paket Tour Bintan',
    desc: 'Keliling destinasi ikonik dalam satu hari. Armada HiAce + supir + BBM sudah include — tinggal datang dan nikmati.',
    features: ['Sleeping Buddha · Lagoi · Gonggong · Patung Seribu', 'Include supir & BBM', 'Karaoke on board · Full Day 8–10 jam'],
    link: '/paket-tour-bintan',
    linkLabel: 'Lihat Paket Tour',
    wa: 'Halo 3 Putri Mulya, saya tertarik Paket Tour Bintan. Mohon info harga.',
  },
  {
    number: '04',
    accent: 'bg-violet-500',
    tag: 'Tepat Waktu',
    tagColor: 'text-violet-600 bg-violet-50 ring-violet-100',
    title: 'Antar Jemput Bandara & Ferry',
    desc: 'Tidak perlu antri taksi. Layanan jemput & antar ke Bandara RHF dan Pelabuhan Sri Bintan Pura untuk personal maupun rombongan.',
    features: ['Bandara RHF & Pelabuhan Sri Bintan Pura', 'Personal hingga rombongan besar', 'Bisa pesan H-1 atau lebih awal'],
    link: null,
    linkLabel: null,
    wa: 'Halo 3 Putri Mulya, saya ingin pesan layanan antar/jemput bandara atau pelabuhan di Bintan.',
  },
]
</script>

<template>
  <div class="min-h-screen bg-white font-sans">

    <!-- ── HERO ─────────────────────────────────────────── -->
    <section class="relative overflow-hidden">
      <!-- Background photo -->
      <img
        src="/images/layanan-hero.jpg"
        alt="Layanan Rental Mobil Bintan — 3 Putri Mulya"
        class="absolute inset-0 w-full h-full object-cover object-center"
      />
      <!-- Overlay: gelap di kiri untuk teks, transparan di kanan -->
      <div class="absolute inset-0 bg-gradient-to-r from-slate-950/90 via-slate-950/60 to-slate-950/10 pointer-events-none"></div>
      <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 to-transparent pointer-events-none"></div>

      <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-28 pb-20 sm:pt-36 sm:pb-28">
        <p class="text-xs font-bold uppercase tracking-[0.15em] text-amber-400 mb-4">
          3 Putri Mulya · Bintan & Tanjung Pinang
        </p>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight leading-[1.05] mb-5 max-w-lg">
          Layanan<br/> Kami
        </h1>
        <p class="text-sm sm:text-base text-white/70 leading-relaxed max-w-sm mb-8">
          Sewa lepas kunci, dengan supir, paket wisata, hingga antar-jemput — semua tersedia untuk perjalanan nyaman di Bintan.
        </p>
        <a
          :href="waUrl('Halo 3 Putri Mulya, saya ingin tanya informasi layanan yang tersedia.')"
          target="_blank"
          rel="noopener noreferrer"
          class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-emerald-500 hover:bg-emerald-400 text-white font-bold text-sm transition-all active:scale-95"
        >
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
          </svg>
          Konsultasi via WhatsApp
        </a>
      </div>
    </section>

    <!-- ── SERVICE CARDS ──────────────────────────────────── -->
    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">

      <!-- MOBILE: Horizontal scroll -->
      <div class="sm:hidden -mx-4 px-4">
        <div class="flex gap-4 overflow-x-auto pb-4 snap-x snap-mandatory" style="scrollbar-width:none;">
          <div
            v-for="svc in services"
            :key="svc.number"
            class="flex-none snap-start w-[78vw] max-w-[300px] flex flex-col bg-white border border-slate-200 rounded-2xl p-5 shadow-sm"
          >
            <div class="w-8 h-1 rounded-full mb-4" :class="svc.accent"></div>
            <div class="flex items-center justify-between mb-3">
              <span class="text-2xl font-black text-slate-100 select-none">{{ svc.number }}</span>
              <span class="text-[10px] font-bold px-2 py-0.5 rounded-full ring-1" :class="svc.tagColor">
                {{ svc.tag }}
              </span>
            </div>
            <h2 class="text-base font-black text-slate-900 mb-1.5">{{ svc.title }}</h2>
            <p class="text-xs text-slate-500 leading-relaxed mb-4 flex-1">{{ svc.desc }}</p>
            <ul class="space-y-1 mb-5">
              <li v-for="f in svc.features" :key="f" class="flex items-start gap-1.5 text-xs text-slate-600">
                <span class="mt-1 w-1.5 h-1.5 rounded-full shrink-0" :class="svc.accent"></span>
                {{ f }}
              </li>
            </ul>
            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
              <RouterLink
                v-if="svc.link"
                :to="svc.link"
                class="text-xs font-bold text-slate-900 hover:text-blue-600 inline-flex items-center gap-1 transition-colors"
              >
                {{ svc.linkLabel }}
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
              </RouterLink>
              <span v-if="svc.link" class="text-slate-200 text-xs">|</span>
              <a
                :href="waUrl(svc.wa)"
                target="_blank"
                rel="noopener noreferrer"
                class="text-xs font-bold text-emerald-600 hover:text-emerald-700 inline-flex items-center gap-1 transition-colors"
              >
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                </svg>
                Tanya Admin
              </a>
            </div>
          </div>
          <div class="flex-none w-4"></div>
        </div>
        <!-- Dot indicator -->
        <div class="flex justify-center gap-1.5 mt-2">
          <span v-for="(_, i) in services" :key="i"
            class="h-1.5 rounded-full transition-all"
            :class="i === 0 ? 'w-4 bg-slate-800' : 'w-1.5 bg-slate-300'"
          />
        </div>
      </div>

      <!-- DESKTOP: 2x2 Grid -->
      <div class="hidden sm:grid grid-cols-2 gap-5">
        <div
          v-for="svc in services"
          :key="svc.number"
          class="flex flex-col bg-white border border-slate-200 rounded-2xl p-7 hover:border-slate-300 hover:shadow-lg transition-all duration-200"
        >
          <div class="w-10 h-1 rounded-full mb-6" :class="svc.accent"></div>
          <div class="flex items-center justify-between mb-4">
            <span class="text-3xl font-black text-slate-100 select-none">{{ svc.number }}</span>
            <span class="text-[11px] font-bold px-2.5 py-1 rounded-full ring-1" :class="svc.tagColor">
              {{ svc.tag }}
            </span>
          </div>
          <h2 class="text-lg font-black text-slate-900 mb-2">{{ svc.title }}</h2>
          <p class="text-sm text-slate-500 leading-relaxed mb-5 flex-1">{{ svc.desc }}</p>
          <ul class="space-y-1.5 mb-6">
            <li v-for="f in svc.features" :key="f" class="flex items-start gap-2 text-[13px] text-slate-600">
              <span class="mt-1 w-1.5 h-1.5 rounded-full shrink-0" :class="svc.accent"></span>
              {{ f }}
            </li>
          </ul>
          <div class="flex items-center gap-3 pt-5 border-t border-slate-100">
            <RouterLink
              v-if="svc.link"
              :to="svc.link"
              class="text-sm font-bold text-slate-900 hover:text-blue-600 inline-flex items-center gap-1 transition-colors"
            >
              {{ svc.linkLabel }}
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
              </svg>
            </RouterLink>
            <span v-if="svc.link" class="text-slate-200">|</span>
            <a
              :href="waUrl(svc.wa)"
              target="_blank"
              rel="noopener noreferrer"
              class="text-sm font-bold text-emerald-600 hover:text-emerald-700 inline-flex items-center gap-1.5 transition-colors"
            >
              <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
              </svg>
              Tanya Admin
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- ── BOTTOM CTA ──────────────────────────────────────── -->
    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
      <div class="bg-slate-900 rounded-2xl px-7 sm:px-10 py-9 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5">
        <div>
          <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-1.5">Butuh Saran?</p>
          <h3 class="text-lg sm:text-xl font-black text-white">Tidak yakin pilih layanan mana?</h3>
          <p class="text-sm text-slate-400 mt-1">Konsultasi gratis — kami bantu sesuaikan dengan kebutuhan kamu.</p>
        </div>
        <a
          :href="waUrl('Halo 3 Putri Mulya, saya ingin konsultasi untuk memilih layanan sewa yang sesuai dengan kebutuhan perjalanan saya di Bintan.')"
          target="_blank"
          rel="noopener noreferrer"
          class="shrink-0 inline-flex items-center gap-2 px-6 py-3 rounded-full bg-emerald-500 hover:bg-emerald-400 text-white font-bold text-sm transition-all active:scale-95 whitespace-nowrap"
        >
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
          </svg>
          Chat WhatsApp Sekarang
        </a>
      </div>
    </section>

  </div>
</template>
