<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
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

type ServiceTabKey = 'lepas-kunci' | 'driver' | 'tour' | 'transfer'
const activeTab = ref<ServiceTabKey>('lepas-kunci')

const mainServices = computed(() => isEnglish.value ? [
  {
    img: '/images/layanan-lepas-kunci.jpg',
    tag: 'Most Flexible',
    tagColor: 'text-amber-400',
    title: 'Self-Drive Car Rental',
    desc: 'Book your car, pick up the keys, and explore Bintan on your own schedule. City cars, family MPVs, to HiAce vans — transparent daily rates.',
    link: '/armada',
    linkLabel: 'View Fleet',
    isExternal: false,
    wa: 'Hello 3 Putri Mulya, I would like to inquire about self-drive car rental in Bintan.',
  },
  {
    img: '/images/layanan-driver.jpg',
    tag: 'Most Relaxing',
    tagColor: 'text-emerald-400',
    title: 'Rental with Chauffeur',
    desc: 'Sit back and enjoy the scenery. Our experienced local drivers are ready to escort your family or group all day with utmost hospitality.',
    link: '/armada',
    linkLabel: 'View Fleet',
    isExternal: false,
    wa: 'Hello 3 Putri Mulya, I would like to hire a car with chauffeur in Bintan.',
  },
  {
    img: '/images/layanan-paket-tour.jpg',
    tag: 'Best Value & All-In',
    tagColor: 'text-sky-400',
    title: 'Bintan Private Tour Packages',
    desc: 'Explore iconic destinations in a single day. Toyota HiAce 15 Seat + Chauffeur + Fuel fully included, plus on-board karaoke system.',
    link: '/paket-tour-bintan',
    linkLabel: 'Explore Tour Packages',
    isExternal: false,
    wa: 'Hello 3 Putri Mulya, I would like to inquire about Bintan private tour packages.',
  },
  {
    img: '/images/layanan-antar-jemput.jpg',
    tag: 'Punctual & Direct',
    tagColor: 'text-violet-400',
    title: 'Ferry & Airport Transfer',
    desc: 'Seamless pick-up from BBT Lagoi Ferry Terminal, Sri Bintan Pura, Roro Tj. Uban, or RHF Airport directly to your resort or hotel.',
    link: waUrl('Hello 3 Putri Mulya, I would like to book an airport or ferry terminal transfer in Bintan.'),
    linkLabel: 'Book Transfer via WA',
    isExternal: true,
    wa: 'Hello 3 Putri Mulya, I would like to book an airport or ferry terminal transfer in Bintan.',
  },
] : [
  {
    img: '/images/layanan-lepas-kunci.jpg',
    tag: 'Paling Fleksibel',
    tagColor: 'text-amber-400',
    title: 'Sewa Lepas Kunci',
    desc: 'Pesan mobil, ambil kunci, bebas kemana saja. Pilihan unit City Car hingga MPV (matic & manual) dengan harga harian transparan.',
    link: '/armada',
    linkLabel: 'Lihat Armada',
    isExternal: false,
    wa: 'Halo 3 Putri Mulya, saya ingin sewa mobil lepas kunci di Bintan.',
  },
  {
    img: '/images/layanan-driver.jpg',
    tag: 'Paling Santai',
    tagColor: 'text-emerald-400',
    title: 'Sewa dengan Supir',
    desc: 'Duduk santai tanpa capek menyetir. Driver lokal berpengalaman siap mengantar rombongan seharian penuh ke berbagai destinasi.',
    link: '/armada',
    linkLabel: 'Lihat Armada',
    isExternal: false,
    wa: 'Halo 3 Putri Mulya, saya ingin sewa mobil dengan supir untuk perjalanan di Bintan.',
  },
  {
    img: '/images/layanan-paket-tour.jpg',
    tag: 'All-In Terfavorit',
    tagColor: 'text-sky-400',
    title: 'Paket Tour Wisata Bintan',
    desc: 'Keliling destinasi ikonik (Gurun Pasir, Lagoi, Vihara 1000 Wajah, Trikora). HiAce 15 Seat + Supir + BBM + Karaoke on board include.',
    link: '/paket-tour-bintan',
    linkLabel: 'Lihat Paket Tour',
    isExternal: false,
    wa: 'Halo 3 Putri Mulya, saya ingin tanya info paket tour wisata Bintan.',
  },
  {
    img: '/images/layanan-antar-jemput.jpg',
    tag: 'Tepat Waktu',
    tagColor: 'text-violet-400',
    title: 'Antar-Jemput Bandara & Pelabuhan',
    desc: 'Layanan transfer tepat waktu dari Pelabuhan Ferry BBT Lagoi, Sri Bintan Pura, Roro Tj. Uban, & Bandara RHF langsung ke hotel/resort tujuan.',
    link: waUrl('Halo 3 Putri Mulya, saya ingin pesan layanan antar-jemput pelabuhan atau bandara di Bintan.'),
    linkLabel: 'Pesan Antar-Jemput',
    isExternal: true,
    wa: 'Halo 3 Putri Mulya, saya ingin pesan layanan antar-jemput pelabuhan atau bandara di Bintan.',
  },
])

const serviceTabList = computed(() => isEnglish.value ? [
  { id: 'lepas-kunci' as const, label: 'Self-Drive Rental', icon: '🔑' },
  { id: 'driver' as const, label: 'With Chauffeur', icon: '👨‍✈️' },
  { id: 'tour' as const, label: 'Private Tour Package', icon: '🌴' },
  { id: 'transfer' as const, label: 'Airport & Ferry Transfer', icon: '🚢' },
] : [
  { id: 'lepas-kunci' as const, label: 'Sewa Lepas Kunci', icon: '🔑' },
  { id: 'driver' as const, label: 'Sewa dengan Supir', icon: '👨‍✈️' },
  { id: 'tour' as const, label: 'Paket Tour Wisata', icon: '🌴' },
  { id: 'transfer' as const, label: 'Antar-Jemput Bandara & Pelabuhan', icon: '🚢' },
])

const serviceDetails = computed(() => {
  if (isEnglish.value) {
    return {
      'lepas-kunci': {
        badge: 'Self-Drive Rental',
        title: 'Total Driving Freedom Around Bintan',
        subtitle: 'For independent travelers, couples, or families who prefer full privacy and managing their own vacation schedule.',
        inclusions: [
          'Well-maintained vehicle with cold air conditioning & clean interior',
          'Flexible 24-hour rental period calculation per day',
          'Unlimited mileage across all of Bintan Island & Tanjung Pinang city',
          'Complete safety gear: spare tire, jack, tool kit, and valid vehicle registration (STNK)',
          '24/7 on-road emergency breakdown assistance',
          'Optional door-to-door delivery & return to your hotel or ferry terminal',
        ],
        terms: [
          'Valid Indonesian Driver License (SIM A) or International Driving Permit',
          'Original identity document (Passport / National ID)',
          'Proof of roundtrip ferry/flight booking or resort reservation voucher',
          'Refundable security deposit (immediately refunded upon safe vehicle return)',
        ],
        fleetReco: 'Toyota Agya (Compact City Car), Toyota Avanza / Veloz (Family MPV), Innova Reborn',
        waMessage: 'Hello 3 Putri Mulya, I would like to book a self-drive rental car in Bintan. Could you please share the availability and terms?',
        ctaLabel: 'Book Self-Drive via WA',
      },
      'driver': {
        badge: 'Rental with Chauffeur',
        title: 'Maximum Comfort, Zero Driving Fatigue',
        subtitle: 'Sit back, chat, and soak in the tropical view. Our polite local drivers take care of navigation, parking, and unfamiliar routes.',
        inclusions: [
          'Courteous, polite, non-smoking, and experienced local chauffeur',
          'In-depth knowledge of top scenic spots, scenic beach routes, and local culinary gems',
          'Full-day usage (10 to 12 hours duration per calendar day)',
          'Zero stress regarding parking spaces, fuel stations, or unfamiliar road directions',
          'Flexible stops anytime for photos, seaside coffee, or local seafood meals',
          'Choice between Car + Driver or All-In (Car + Driver + Fuel + Parking)',
        ],
        terms: [
          'Standard duration: 10–12 hours per calendar day',
          'Driver meals: either hosted with your group or standard driver allowance',
          'Transparent overtime rates if exceeding the 12-hour duration',
          'Simple booking with no complicated identity document guarantees required',
        ],
        fleetReco: 'Toyota Avanza, Veloz, Toyota Innova Reborn / Zenix Luxury',
        waMessage: 'Hello 3 Putri Mulya, I would like to hire a car with chauffeur in Bintan. Could you please share rates and unit availability?',
        ctaLabel: 'Book Chauffeur Service via WA',
      },
      'tour': {
        badge: 'Private Tour Package',
        title: 'All-In Bintan Sightseeing in Toyota HiAce',
        subtitle: 'The premier hassle-free way to explore the island for families, company outings, or groups of friends.',
        inclusions: [
          'Spacious Toyota HiAce Commuter (15 seats) or HiAce Premio (14 VIP seats)',
          'On-board high-clarity audio karaoke system for non-stop group entertainment',
          'Friendly local driver acting as your knowledgeable route guide',
          'All-in package: Vehicle + Driver + Fuel (BBM) + Destination Parking fees included',
          'Iconic highlights: Gurun Pasir Busung, Danau Biru, 1,000 Faces Temple, Lagoi Bay, Trikora Beach',
          'Complimentary luggage storage inside the van throughout the tour day',
        ],
        terms: [
          'Customizable itinerary based on your ferry arrival time and departure schedule',
          'Tour duration approx. 8 to 10 hours',
          'Advance booking recommended (H-1 or earlier) to guarantee HiAce availability',
          'Tourist entrance admission tickets can be coordinated via our team',
        ],
        fleetReco: 'Toyota HiAce Commuter (15-Seat), HiAce Premio Luxury',
        waMessage: 'Hello 3 Putri Mulya, I would like to inquire about the Private Tour Package with HiAce in Bintan. Can you share the route and itinerary?',
        ctaLabel: 'Inquire Tour Package via WA',
      },
      'transfer': {
        badge: 'Ferry & Airport Transfer',
        title: 'Punctual VIP Meet & Greet Direct to Resort',
        subtitle: 'Direct private transfer from arrival gates to your hotel lobby without queueing for taxis.',
        inclusions: [
          'Pickups from BBT Ferry Lagoi, Sri Bintan Pura Tanjungpinang, Roro Tj. Uban, or RHF Airport',
          'Chauffeur arrives 15–20 minutes early and monitors your ferry / flight status live',
          'Personalized greeting name-board at the arrival hall',
          'Luggage handling assistance directly from arrival door to the vehicle trunk',
          '100% private point-to-point transfer — never combined with other passengers',
          'Air-conditioned comfort directly to resorts in Lagoi, Trikora, or Tanjungpinang',
        ],
        terms: [
          'Please provide ferry departure time or flight number during booking',
          'Fixed point-to-point flat rates with no hidden fuel surcharges',
          'Free waiting time if your ferry or flight suffers delays',
          'Available 24 hours daily with advance reservation',
        ],
        fleetReco: 'Toyota Avanza (1–4 pax), Innova Reborn (4–6 pax), Toyota HiAce (7–15 pax)',
        waMessage: 'Hello 3 Putri Mulya, I need a private transfer from the Ferry Terminal / Airport to my hotel in Bintan. Here are my arrival details...',
        ctaLabel: 'Book Direct Transfer via WA',
      },
    }
  }

  return {
    'lepas-kunci': {
      badge: 'Sewa Lepas Kunci',
      title: 'Kebebasan Penuh Jelajahi Bintan Sendiri',
      subtitle: 'Solusi ideal bagi wisatawan mandiri, pasangan, atau keluarga kecil yang menginginkan privasi tinggi dan kebebasan menentukan waktu.',
      inclusions: [
        'Unit mobil terawat, bersih, wangi, dan ber-AC dingin',
        'Hitungan sewa fleksibel 24 jam penuh per hari',
        'Bebas keliling seluruh Pulau Bintan & Tanjungpinang (tanpa batas kilometer)',
        'Fasilitas ban serep, dongkrak, toolkit, dan kelengkapan STNK resmi',
        'Dukungan darurat di jalan (on-road assistance) 24/7 jika terjadi kendala',
        'Layanan antar & ambil unit ke hotel atau pelabuhan (opsional)',
      ],
      terms: [
        'E-KTP asli (atau Paspor untuk WNA)',
        'SIM A aktif yang masih berlaku',
        'Bukti tiket pulang-pergi (ferry / pesawat) atau voucher hotel',
        'Deposit jaminan sewa (dikembalikan utuh saat mobil kembali dalam kondisi baik)',
      ],
      fleetReco: 'Toyota Agya (Hemat), Toyota Avanza / Veloz (Keluarga), Innova Reborn',
      waMessage: 'Halo 3 Putri Mulya, saya ingin booking mobil sewa lepas kunci di Bintan. Mohon info ketersediaan unit dan persyaratannya.',
      ctaLabel: 'Pesan Lepas Kunci via WA',
    },
    'driver': {
      badge: 'Sewa dengan Supir',
      title: 'Santai Maksimal, Bebas Lelah & Bebas Nyasar',
      subtitle: 'Duduk santai di kabin nyaman bersama keluarga. Driver lokal kami yang ramah siap mengantar ke setiap sudut tujuan tanpa ribet.',
      inclusions: [
        'Supir lokal ramah, berpengalaman, sopan, dan tidak merokok di dalam unit',
        'Hafal seluruh jalan tikus, rute wisata, hingga resto kuliner lokal terbaik',
        'Durasi sewa seharian penuh (10 hingga 12 jam)',
        'Bebas lelah menyetir di pulau asing dan tidak perlu memikirkan tempat parkir',
        'Bebas mampir di mana saja untuk foto atau berwisata kuliner',
        'Pilihan sewa Unit + Supir saja atau Paket All-In (termasuk BBM & parkir)',
      ],
      terms: [
        'Durasi pemakaian standar: 10–12 jam per hari kalender',
        'Makan supir: bisa diajak makan bersama atau uang makan standar',
        'Biaya overtime berlaku transparan jika melebihi batas durasi 12 jam',
        'Tanpa syarat jaminan dokumen rumit (cukup data kontak & jadwal penjemputan)',
      ],
      fleetReco: 'Toyota Avanza, Veloz, Toyota Innova Reborn / Zenix Luxury',
      waMessage: 'Halo 3 Putri Mulya, saya ingin sewa mobil dengan supir di Bintan. Mohon info ketersediaan dan tarif paketnya.',
      ctaLabel: 'Pesan Jasa Supir via WA',
    },
    'tour': {
      badge: 'Paket Tour Wisata',
      title: 'Keliling Destinasi Ikonik All-In & Karaoke',
      subtitle: 'Pilihan paling praktis untuk liburan keluarga, rombongan kantor, atau reuni teman tanpa pusing mengatur rute perjalanan.',
      inclusions: [
        'Armada Toyota HiAce Commuter (15 seat) atau HiAce Premio Luxury (14 seat)',
        'Fasilitas sound audio & karaoke on-board untuk seru-seruan di perjalanan',
        'Supir berpengalaman yang merangkap sebagai pemandu rute lokal ramah',
        'Paket All-In lengkap: Mobil + Supir + BBM + Parkir destinasi',
        'Spot utama: Gurun Pasir Busung, Danau Biru, Vihara 1000 Wajah, Lagoi Bay, Pantai Trikora',
        'Gratis penitipan bagasi di mobil saat tour di hari kedatangan atau kepulangan',
      ],
      terms: [
        'Itinerary fleksibel: bisa disesuaikan dengan jam tiket kapal ferry / pesawat Anda',
        'Durasi tour harian sekitar 8–10 jam',
        'Disarankan reservasi minimal H-1 untuk kepastian ketersediaan unit HiAce',
        'Tiket masuk objek wisata dapat dibantu dikoordinasikan oleh tim kami',
      ],
      fleetReco: 'Toyota HiAce Commuter 15-Seat, HiAce Premio Luxury',
      waMessage: 'Halo 3 Putri Mulya, saya ingin tanya informasi Paket Tour Wisata Bintan menggunakan HiAce. Boleh minta rincian rute dan penawarannya?',
      ctaLabel: 'Tanya Paket Tour via WA',
    },
    'transfer': {
      badge: 'Antar-Jemput Pelabuhan & Bandara',
      title: 'Penjemputan Tepat Waktu Langsung ke Resort',
      subtitle: 'Layanan transfer privat tanpa antre dari pelabuhan atau bandara langsung ke pintu lobby hotel Anda.',
      inclusions: [
        'Titik jemput: Pelabuhan Ferry Lagoi (BBT), Sri Bintan Pura, Roro Tj. Uban, atau Bandara RHF',
        'Supir standby 15–20 menit sebelum jadwal kedatangan kapal/pesawat',
        'Layanan name-sign (papan nama tamu) di pintu keluar kedatangan',
        'Bantuan angkat koper dan bagasi oleh supir',
        '100% privat langsung ke resort tujuan Anda (tidak digabung rombongan lain)',
        'Kenyamanan kabin dingin ber-AC menuju kawasan Lagoi, Trikora, atau Tanjungpinang',
      ],
      terms: [
        'Mohon sertakan jam kedatangan kapal ferry atau nomor penerbangan saat booking',
        'Tarif flat antar-jemput langsung tanpa biaya siluman bensin',
        'Bebas biaya tunggu apabila kapal ferry atau pesawat mengalami keterlambatan',
        'Tersedia 24 jam dengan reservasi sebelumnya',
      ],
      fleetReco: 'Avanza (1–4 orang), Innova Reborn (4–6 orang), Toyota HiAce (7–15 orang)',
      waMessage: 'Halo 3 Putri Mulya, saya ingin pesan layanan antar-jemput privat dari Pelabuhan / Bandara ke hotel di Bintan. Berikut rincian kedatangan saya...',
      ctaLabel: 'Pesan Antar-Jemput via WA',
    },
  }
})

const currentDetail = computed(() => serviceDetails.value[activeTab.value])
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
          class="w-full h-full object-cover object-center"
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

    <!-- ═══════════ 4 LAYANAN UTAMA ═══════════ -->
    <section v-reveal:fade-up class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 sm:pt-16 pb-6">
      <div class="flex items-center justify-between mb-5">
        <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">{{ isEnglish ? 'Core Services' : 'Layanan Utama' }}</p>
        <span class="text-xs text-slate-400 hidden sm:inline">{{ isEnglish ? '4 Comprehensive Solutions' : '4 Pilihan Layanan Lengkap' }}</span>
      </div>

      <!-- MOBILE: Horizontal scroll -->
      <div class="sm:hidden -mx-4 px-4">
        <div class="flex gap-4 overflow-x-auto pb-3 snap-x snap-mandatory" style="scrollbar-width:none;">
          <div
            v-for="(svc, idx) in mainServices"
            :key="svc.title"
            v-reveal:fade-up="idx * 90"
            class="group relative flex-none snap-start rounded-2xl overflow-hidden bg-slate-900 flex flex-col justify-end"
            style="width:80vw;max-width:320px;height:380px;"
          >
            <img :src="svc.img" :alt="svc.title"
              class="absolute inset-0 w-full h-full object-cover group-active:scale-105 transition-transform duration-500"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/45 to-transparent"></div>
            <div class="relative z-10 p-5">
              <span class="text-[10px] font-bold uppercase tracking-widest mb-1.5 block" :class="svc.tagColor">{{ svc.tag }}</span>
              <h2 class="text-lg font-black text-white mb-1">{{ svc.title }}</h2>
              <p class="text-xs text-white/65 leading-relaxed mb-4 line-clamp-3">{{ svc.desc }}</p>
              <div class="flex items-center gap-3">
                <a
                  v-if="svc.isExternal"
                  :href="svc.link"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex items-center gap-1 px-4 py-2 rounded-full bg-white text-slate-900 font-bold text-xs hover:bg-slate-100 transition-colors"
                >
                  {{ svc.linkLabel }}
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                  </svg>
                </a>
                <RouterLink
                  v-else
                  :to="svc.link"
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

      <!-- DESKTOP: 2-col grid (2x2) -->
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
          <div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/45 to-transparent"></div>
          <div class="relative z-10 p-6 sm:p-7">
            <span class="text-[10px] font-bold uppercase tracking-widest mb-2 block" :class="svc.tagColor">{{ svc.tag }}</span>
            <h2 class="text-xl font-black text-white mb-1.5">{{ svc.title }}</h2>
            <p class="text-xs text-white/70 leading-relaxed mb-5">{{ svc.desc }}</p>
            <div class="flex items-center gap-3">
              <a
                v-if="svc.isExternal"
                :href="svc.link"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-white text-slate-900 font-bold text-xs hover:bg-slate-100 transition-colors"
              >
                {{ svc.linkLabel }}
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
              </a>
              <RouterLink
                v-else
                :to="svc.link"
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

    <!-- ═══════════ DETAIL & PANDUAN LENGKAP LAYANAN ═══════════ -->
    <section v-reveal:fade-up class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 border-t border-slate-100">
      <div class="mb-8">
        <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">
          {{ isEnglish ? 'In-Depth Service Guide' : 'Panduan & Detail Lengkap Layanan' }}
        </p>
        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
          {{ isEnglish ? 'Clear Guidelines & Inclusions for Peace of Mind' : 'Informasi Transparan & Fasilitas yang Anda Dapatkan' }}
        </h2>
        <p class="text-xs sm:text-sm text-slate-500 mt-1.5 max-w-2xl">
          {{ isEnglish
            ? 'Choose a service below to review full inclusions, requirements, recommended vehicles, and rental terms.'
            : 'Pilih layanan di bawah ini untuk melihat rincian fasilitas, syarat sewa, rekomendasi armada, dan ketentuannya.'
          }}
        </p>
      </div>

      <!-- Service Selection Tabs -->
      <div class="flex items-center gap-2 overflow-x-auto pb-2 mb-6 sm:mb-8" style="scrollbar-width:none;">
        <button
          v-for="tab in serviceTabList"
          :key="tab.id"
          type="button"
          @click="activeTab = tab.id"
          class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition-all border cursor-pointer"
          :class="activeTab === tab.id
            ? 'bg-slate-900 text-white border-slate-900 shadow-md scale-[1.02]'
            : 'bg-slate-50 hover:bg-slate-100 text-slate-600 border-slate-200'"
        >
          <span>{{ tab.icon }}</span>
          <span>{{ tab.label }}</span>
        </button>
      </div>

      <!-- Tab Content Card -->
      <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-sm">
        <!-- Tab Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
          <div>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-2">
              {{ currentDetail.badge }}
            </span>
            <h3 class="text-xl sm:text-2xl font-black text-slate-900">
              {{ currentDetail.title }}
            </h3>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
              {{ currentDetail.subtitle }}
            </p>
          </div>
          <a
            :href="waUrl(currentDetail.waMessage)"
            target="_blank" rel="noopener noreferrer"
            class="shrink-0 inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-emerald-600 hover:bg-emerald-500 text-white text-xs sm:text-sm font-bold shadow-md transition-all active:scale-95 whitespace-nowrap"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
            </svg>
            {{ currentDetail.ctaLabel }}
          </a>
        </div>

        <!-- 2 Column Breakdown (Inclusions & Terms) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6">
          <!-- Inclusions -->
          <div class="bg-slate-50/80 border border-slate-100 rounded-2xl p-5">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800 mb-3.5 flex items-center gap-2">
              <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-black">✓</span>
              {{ isEnglish ? 'What is Included (Fasilitas)' : 'Fasilitas & Apa yang Didapat' }}
            </h4>
            <ul class="space-y-2.5">
              <li
                v-for="(item, i) in currentDetail.inclusions"
                :key="i"
                class="flex items-start gap-2.5 text-xs sm:text-sm text-slate-600 leading-relaxed"
              >
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-2 shrink-0"></span>
                <span>{{ item }}</span>
              </li>
            </ul>
          </div>

          <!-- Requirements / Terms -->
          <div class="bg-slate-50/80 border border-slate-100 rounded-2xl p-5 flex flex-col justify-between">
            <div>
              <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800 mb-3.5 flex items-center gap-2">
                <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-black">ℹ</span>
                {{ isEnglish ? 'Requirements & Guidelines' : 'Ketentuan & Persyaratan Sewa' }}
              </h4>
              <ul class="space-y-2.5">
                <li
                  v-for="(term, j) in currentDetail.terms"
                  :key="j"
                  class="flex items-start gap-2.5 text-xs sm:text-sm text-slate-600 leading-relaxed"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-2 shrink-0"></span>
                  <span>{{ term }}</span>
                </li>
              </ul>
            </div>

            <!-- Fleet Recommendation Bar -->
            <div class="mt-5 pt-4 border-t border-slate-200/60 flex items-center justify-between gap-3 text-xs">
              <div>
                <span class="font-bold text-slate-700 block">{{ isEnglish ? 'Recommended Fleet:' : 'Rekomendasi Armada:' }}</span>
                <span class="text-slate-500 text-[11px]">{{ currentDetail.fleetReco }}</span>
              </div>
              <RouterLink
                :to="activeTab === 'tour' ? '/paket-tour-bintan' : '/armada'"
                class="shrink-0 text-blue-600 hover:text-blue-700 font-bold hover:underline"
              >
                {{ isEnglish ? 'Browse Fleet →' : 'Pilih Mobil →' }}
              </RouterLink>
            </div>
          </div>
        </div>
      </div>

      <!-- ════════ 3 LANGKAH MUDAH PEMESANAN ════════ -->
      <div class="mt-12 pt-10 border-t border-slate-100">
        <div class="text-center max-w-xl mx-auto mb-8">
          <span class="text-[11px] font-bold uppercase tracking-widest text-slate-400 block mb-1">
            {{ isEnglish ? 'How It Works' : 'Cara Pemesanan' }}
          </span>
          <h3 class="text-xl sm:text-2xl font-black text-slate-900">
            {{ isEnglish ? '3 Simple Steps to Start Your Journey' : '3 Langkah Mudah Memesan Layanan' }}
          </h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
          <!-- Step 1 -->
          <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 flex flex-col items-center text-center">
            <span class="w-10 h-10 rounded-full bg-blue-600 text-white font-black text-sm flex items-center justify-center mb-3 shadow-md">
              1
            </span>
            <h4 class="text-sm font-bold text-slate-900 mb-1">
              {{ isEnglish ? 'Choose Service & Vehicle' : 'Pilih Layanan & Armada' }}
            </h4>
            <p class="text-xs text-slate-500 leading-relaxed">
              {{ isEnglish
                ? 'Select whether you need self-drive, chauffeur, island tour, or airport/ferry transfer along with your preferred car type.'
                : 'Tentukan jenis layanan (lepas kunci, dengan supir, tour, atau antar-jemput) serta tipe armada yang sesuai rombongan Anda.'
              }}
            </p>
          </div>

          <!-- Step 2 -->
          <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 flex flex-col items-center text-center">
            <span class="w-10 h-10 rounded-full bg-emerald-600 text-white font-black text-sm flex items-center justify-center mb-3 shadow-md">
              2
            </span>
            <h4 class="text-sm font-bold text-slate-900 mb-1">
              {{ isEnglish ? 'Quick WhatsApp Confirmation' : 'Konfirmasi Cepat via WhatsApp' }}
            </h4>
            <p class="text-xs text-slate-500 leading-relaxed">
              {{ isEnglish
                ? 'Send your arrival date, pickup location, and time. Our team confirms availability and transparent rates in minutes.'
                : 'Kirim tanggal pemakaian, lokasi penjemputan, dan jadwal Anda. Admin kami merespons cepat dan memberikan total harga transparan.'
              }}
            </p>
          </div>

          <!-- Step 3 -->
          <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 flex flex-col items-center text-center">
            <span class="w-10 h-10 rounded-full bg-amber-500 text-white font-black text-sm flex items-center justify-center mb-3 shadow-md">
              3
            </span>
            <h4 class="text-sm font-bold text-slate-900 mb-1">
              {{ isEnglish ? 'Handover & Enjoy Trip' : 'Serah Kunci & Siap Berangkat' }}
            </h4>
            <p class="text-xs text-slate-500 leading-relaxed">
              {{ isEnglish
                ? 'Vehicle is delivered on time or our driver greets you at the terminal. Your smooth Bintan adventure begins!'
                : 'Mobil diantar tepat waktu atau supir menyambut kedatangan Anda di pintu terminal. Selamat menikmati keindahan Pulau Bintan!'
              }}
            </p>
          </div>
        </div>
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
