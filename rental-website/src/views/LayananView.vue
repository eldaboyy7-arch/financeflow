<script setup lang="ts">
import { computed, onMounted, watch } from 'vue'
import { RouterLink } from 'vue-router'
import { siteConfig } from '@/config/site'
import { useLanguage } from '@/composables/useLanguage'

const { isEnglish } = useLanguage()

const updatePageTitle = () => {
  document.title = isEnglish.value
    ? 'Car Rental Services & Tour Transportation Bintan | 3 Putri Mulya'
    : 'Layanan Rental Mobil & Transportasi Wisata Bintan | 3 Putri Mulya'
}

onMounted(() => {
  updatePageTitle()
})

watch(isEnglish, () => {
  updatePageTitle()
})

const waUrl = (text: string) => {
  const clean = siteConfig.rentalPhone.replace(/\D/g, '')
  return `https://wa.me/${clean}?text=${encodeURIComponent(text)}`
}

const mainServices = computed(() => isEnglish.value ? [
  {
    img: '/images/layanan-lepas-kunci.jpg',
    tag: 'Most Flexible',
    tagColor: 'text-amber-400',
    title: 'Self-Drive Car Rental',
    desc: 'Book your car, pick up the keys, and explore Bintan on your own schedule. City cars, family MPVs, to HiAce vans — transparent daily rates.',
    link: '/armada',
    linkLabel: 'View Fleet',
    wa: 'Hello 3 Putri Mulya, I would like to inquire about self-drive car rental in Bintan.',
  },
  {
    img: '/images/layanan-driver.jpg',
    tag: 'Most Relaxing',
    tagColor: 'text-emerald-400',
    title: 'Rental with Chauffeur',
    desc: 'Sit back and enjoy the scenery. Our experienced local drivers are ready to escort your family or group all day.',
    link: '/paket-tour-bintan',
    linkLabel: 'View Tour Packages',
    wa: 'Hello 3 Putri Mulya, I would like to hire a car with chauffeur in Bintan.',
  },
] : [
  {
    img: '/images/layanan-lepas-kunci.jpg',
    tag: 'Paling Fleksibel',
    tagColor: 'text-amber-400',
    title: 'Sewa Lepas Kunci',
    desc: 'Pesan mobil, ambil kunci, bebas kemana saja. City Car, MPV, hingga HiAce — harga harian transparan.',
    link: '/armada',
    linkLabel: 'Lihat Armada',
    wa: 'Halo 3 Putri Mulya, saya ingin sewa mobil lepas kunci di Bintan.',
  },
  {
    img: '/images/layanan-driver.jpg',
    tag: 'Paling Santai',
    tagColor: 'text-emerald-400',
    title: 'Sewa dengan Supir',
    desc: 'Duduk santai, nikmati perjalanan. Driver berpengalaman siap mengantar rombongan seharian penuh.',
    link: '/paket-tour-bintan',
    linkLabel: 'Lihat Paket',
    wa: 'Halo 3 Putri Mulya, saya ingin sewa mobil dengan supir untuk perjalanan di Bintan.',
  },
])
</script>

<template>
  <div class="min-h-screen bg-white font-sans">

    <!-- ═══════════ HERO — RESPONSIVE (MOBILE & DESKTOP) ═══════════ -->
    <section class="relative h-screen min-h-[560px] max-h-[900px] flex items-end overflow-hidden">
      <picture class="absolute inset-0 w-full h-full">
        <source media="(max-width: 639px)" srcset="/images/layanan-hero-mobile.jpg" />
        <img
          src="/images/layanan-hero.jpg"
          alt="Layanan Rental Mobil Bintan"
          class="w-full h-full object-cover object-bottom sm:object-center"
        />
      </picture>

      <!-- Gradient overlay -->
      <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/45 to-slate-950/10 pointer-events-none"></div>

      <div v-reveal:fade-up class="relative z-10 w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-20 sm:pb-32">
        <p class="text-xs font-bold uppercase tracking-[0.15em] text-amber-400 mb-3">
          3 Putri Mulya · Bintan &amp; Tanjung Pinang
        </p>
        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight leading-tight mb-3 max-w-xl">
          {{ isEnglish ? 'Services for Every Journey You Take' : 'Layanan untuk Setiap Perjalanan Anda' }}
        </h1>
        <p class="text-sm sm:text-base text-white/70 sm:text-white/55 mb-8 max-w-sm">
          {{ isEnglish ? 'Car Rental · Chauffeur · Tour Packages · Airport Transfer' : 'Rental Mobil · Driver · Tour · Antar-Jemput' }}
        </p>
        <div class="flex flex-wrap items-center gap-3">
          <a
            :href="waUrl(isEnglish ? 'Hello 3 Putri Mulya, I would like to inquire about available services in Bintan.' : 'Halo 3 Putri Mulya, saya ingin tanya informasi layanan yang tersedia.')"
            target="_blank" rel="noopener noreferrer"
            class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-emerald-500 hover:bg-emerald-400 text-white font-bold text-sm transition-all active:scale-95"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
            </svg>
            {{ isEnglish ? 'Free Consultation' : 'Konsultasi Gratis' }}
          </a>
          <RouterLink
            to="/armada"
            class="inline-flex items-center gap-1.5 px-6 py-3 rounded-full border border-white/30 text-white font-bold text-sm hover:bg-white/10 transition-all"
          >
            {{ isEnglish ? 'View Fleet' : 'Lihat Armada' }}
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
          </RouterLink>
        </div>
      </div>

      <!-- Scroll down arrow (desktop only) -->
      <div class="hidden sm:block absolute bottom-6 left-1/2 -translate-x-1/2 z-10 animate-bounce">
        <svg class="w-5 h-5 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
      </div>
    </section>

    <!-- ═══════════ 2 LAYANAN UTAMA ═══════════ -->
    <section v-reveal:fade-up class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 sm:pt-16 pb-5">
      <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-5">{{ isEnglish ? 'Core Services' : 'Layanan Utama' }}</p>

      <!-- MOBILE: Horizontal scroll -->
      <div class="sm:hidden -mx-4 px-4">
        <div class="flex gap-4 overflow-x-auto pb-3 snap-x snap-mandatory" style="scrollbar-width:none;">
          <div
            v-for="(svc, idx) in mainServices"
            :key="svc.title"
            v-reveal:fade-up="idx * 90"
            class="group relative flex-none snap-start rounded-2xl overflow-hidden bg-slate-900 flex flex-col justify-end"
            style="width:80vw;max-width:320px;height:360px;"
          >
            <img :src="svc.img" :alt="svc.title"
              class="absolute inset-0 w-full h-full object-cover group-active:scale-105 transition-transform duration-500"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/40 to-transparent"></div>
            <div class="relative z-10 p-5">
              <span class="text-[10px] font-bold uppercase tracking-widest mb-1.5 block" :class="svc.tagColor">{{ svc.tag }}</span>
              <h2 class="text-lg font-black text-white mb-1">{{ svc.title }}</h2>
              <p class="text-xs text-white/60 leading-relaxed mb-4">{{ svc.desc }}</p>
              <div class="flex items-center gap-3">
                <RouterLink :to="svc.link"
                  class="inline-flex items-center gap-1 px-4 py-2 rounded-full bg-white text-slate-900 font-bold text-xs hover:bg-slate-100 transition-colors"
                >
                  {{ svc.linkLabel }}
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                  </svg>
                </RouterLink>
                <a :href="waUrl(svc.wa)" target="_blank" rel="noopener noreferrer"
                  class="text-xs font-bold text-white/60 hover:text-white transition-colors"
                >{{ isEnglish ? 'Inquire via WA' : 'Tanya Admin' }}</a>
              </div>
            </div>
          </div>
          <div class="flex-none w-4"></div>
        </div>
        <div class="flex justify-center gap-1.5 mt-2.5">
          <span v-for="(_, i) in mainServices" :key="i"
            class="h-1.5 rounded-full"
            :class="i === 0 ? 'w-4 bg-slate-800' : 'w-1.5 bg-slate-300'"
          />
        </div>
      </div>

      <!-- DESKTOP: 2-col grid -->
      <div class="hidden sm:grid grid-cols-2 gap-5">
        <div
          v-for="(svc, idx) in mainServices"
          :key="svc.title"
          v-reveal:fade-up="idx * 90"
          class="group relative rounded-2xl overflow-hidden bg-slate-900 flex flex-col justify-end"
          style="min-height:380px;"
        >
          <img :src="svc.img" :alt="svc.title"
            class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
          />
          <div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/40 to-transparent"></div>
          <div class="relative z-10 p-6 sm:p-7">
            <span class="text-[10px] font-bold uppercase tracking-widest mb-2 block" :class="svc.tagColor">{{ svc.tag }}</span>
            <h2 class="text-xl font-black text-white mb-1.5">{{ svc.title }}</h2>
            <p class="text-xs text-white/65 leading-relaxed mb-5">{{ svc.desc }}</p>
            <div class="flex items-center gap-3">
              <RouterLink :to="svc.link"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-white text-slate-900 font-bold text-xs hover:bg-slate-100 transition-colors"
              >
                {{ svc.linkLabel }}
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
              </RouterLink>
              <a :href="waUrl(svc.wa)" target="_blank" rel="noopener noreferrer"
                class="text-xs font-bold text-white/70 hover:text-white transition-colors"
              >{{ isEnglish ? 'Inquire via WA' : 'Tanya Admin' }}</a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════ PAKET TOUR — Full Width ═══════════ -->
    <section v-reveal:zoom-in class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
      <div class="group relative rounded-2xl overflow-hidden bg-slate-900 flex items-end" style="min-height:260px;">
        <img src="/images/destinations/busung.jpg" alt="Paket Tour Bintan"
          class="absolute inset-0 w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500"
        />
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/95 via-slate-950/55 to-slate-950/10"></div>
        <div class="relative z-10 p-6 sm:p-8 max-w-lg">
          <span class="text-[10px] font-bold uppercase tracking-widest text-amber-400 mb-2 block">All-In</span>
          <h2 class="text-xl sm:text-2xl font-black text-white mb-2">{{ isEnglish ? 'Bintan Private Tour Packages' : 'Paket Tour Bintan' }}</h2>
          <p class="text-xs sm:text-sm text-white/65 leading-relaxed mb-5 max-w-sm">
            {{ isEnglish
              ? 'Explore top iconic destinations in a single day. Toyota HiAce + Chauffeur + Fuel fully included. On-board karaoke system. Just arrive and enjoy.'
              : 'Keliling destinasi ikonik dalam satu hari. HiAce + Supir + BBM sudah include. Karaoke on board. Tinggal datang dan nikmati.'
            }}
          </p>
          <RouterLink to="/paket-tour-bintan"
            class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-full bg-amber-400 hover:bg-amber-300 text-slate-900 font-bold text-xs transition-colors"
          >
            {{ isEnglish ? 'Explore Tour Packages' : 'Lihat Paket Tour' }}
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
          </RouterLink>
        </div>
      </div>
    </section>

    <!-- ═══════════ ANTAR JEMPUT ═══════════ -->
    <section v-reveal:fade-up class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border border-slate-200 rounded-2xl px-6 py-6">
        <div>
          <span class="text-[10px] font-bold uppercase tracking-widest text-violet-500 block mb-1.5">{{ isEnglish ? 'Punctual & Reliable' : 'Tepat Waktu' }}</span>
          <h2 class="text-base sm:text-lg font-black text-slate-900 mb-1">{{ isEnglish ? 'Airport & Ferry Terminal Transfer' : 'Antar Jemput Bandara & Ferry' }}</h2>
          <p class="text-sm text-slate-500">
            {{ isEnglish ? 'RHF Airport · Sri Bintan Pura Ferry Terminal · Solo to Large Groups' : 'Bandara RHF · Pelabuhan Sri Bintan Pura · Personal hingga rombongan besar' }}
          </p>
        </div>
        <a
          :href="waUrl(isEnglish ? 'Hello 3 Putri Mulya, I would like to book an airport or ferry terminal transfer in Bintan.' : 'Halo 3 Putri Mulya, saya ingin pesan layanan antar/jemput bandara atau pelabuhan di Bintan.')"
          target="_blank" rel="noopener noreferrer"
          class="shrink-0 inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-slate-900 hover:bg-slate-700 text-white font-bold text-sm transition-all whitespace-nowrap"
        >
          <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
          </svg>
          {{ isEnglish ? 'Inquire via WA' : 'Tanya Admin WA' }}
        </a>
      </div>
    </section>

    <!-- ══════════════ KENAPA KAMI ══════════════ -->
    <section v-reveal:fade-up class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-12 sm:pt-14 sm:pb-16">
      <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-8">{{ isEnglish ? 'Why Choose Us?' : 'Kenapa Memilih Kami?' }}</p>
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-5 sm:gap-6">

        <!-- Armada Terawat -->
        <div v-reveal:fade-up="0" class="flex flex-col gap-3">
          <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-black text-slate-900 mb-1">{{ isEnglish ? 'Well-Maintained Fleet' : 'Armada Terawat' }}</h3>
            <p class="text-xs text-slate-500 leading-relaxed">{{ isEnglish ? 'All vehicles are clean, feature cold A/C, and pass multi-point checks before dispatch.' : 'Unit selalu bersih, ber-AC dingin, dan diperiksa sebelum perjalanan.' }}</p>
          </div>
        </div>

        <!-- Driver Berpengalaman -->
        <div v-reveal:fade-up="80" class="flex flex-col gap-3">
          <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-black text-slate-900 mb-1">{{ isEnglish ? 'Experienced Chauffeurs' : 'Driver Berpengalaman' }}</h3>
            <p class="text-xs text-slate-500 leading-relaxed">{{ isEnglish ? 'Knowledgeable on every corner of Bintan & Tanjung Pinang, courteous, and always punctual.' : 'Hafal seluruh sudut Bintan & Tanjung Pinang, ramah dan tepat waktu.' }}</p>
          </div>
        </div>

        <!-- Harga Transparan -->
        <div v-reveal:fade-up="160" class="flex flex-col gap-3">
          <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-black text-slate-900 mb-1">{{ isEnglish ? 'Transparent Pricing' : 'Harga Transparan' }}</h3>
            <p class="text-xs text-slate-500 leading-relaxed">{{ isEnglish ? 'No hidden surcharges. Everything is crystal clear upfront prior to booking.' : 'Tidak ada biaya tersembunyi. Semua sudah jelas di awal sebelum booking.' }}</p>
          </div>
        </div>

        <!-- Respon Cepat -->
        <div v-reveal:fade-up="240" class="flex flex-col gap-3">
          <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-black text-slate-900 mb-1">{{ isEnglish ? 'Fast Response' : 'Respon Cepat' }}</h3>
            <p class="text-xs text-slate-500 leading-relaxed">{{ isEnglish ? 'Direct WhatsApp replies — typically within minutes.' : 'Chat WhatsApp langsung dibalas — rata-rata dalam hitungan menit.' }}</p>
          </div>
        </div>

      </div>
    </section>

    <!-- ═══════════ BOTTOM CTA ═══════════ -->
    <section v-reveal:zoom-in class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
      <div class="bg-slate-900 rounded-2xl px-7 sm:px-10 py-9 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5">
        <div>
          <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-1.5">{{ isEnglish ? 'Need Advice?' : 'Butuh Saran?' }}</p>
          <h3 class="text-lg sm:text-xl font-black text-white">{{ isEnglish ? 'Unsure which service suits you best?' : 'Tidak yakin pilih layanan mana?' }}</h3>
          <p class="text-sm text-slate-400 mt-1">{{ isEnglish ? 'Free consultation, we help match your group needs and budget perfectly.' : 'Konsultasi gratis, kami bantu sesuaikan kebutuhan dan budget kamu.' }}</p>
        </div>
        <a
          :href="waUrl(isEnglish ? 'Hello 3 Putri Mulya, I would like to consult on selecting the best rental service for my trip in Bintan.' : 'Halo 3 Putri Mulya, saya ingin konsultasi untuk memilih layanan sewa yang sesuai dengan kebutuhan perjalanan saya di Bintan.')"
          target="_blank" rel="noopener noreferrer"
          class="shrink-0 inline-flex items-center gap-2 px-6 py-3 rounded-full bg-emerald-500 hover:bg-emerald-400 text-white font-bold text-sm transition-all active:scale-95 whitespace-nowrap"
        >
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
          </svg>
          {{ isEnglish ? 'Chat WhatsApp Now' : 'Chat WhatsApp Sekarang' }}
        </a>
      </div>
    </section>

  </div>
</template>
