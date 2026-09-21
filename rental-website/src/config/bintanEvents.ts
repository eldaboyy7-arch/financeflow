import { siteConfig } from './site'

export interface BintanEvent {
  id: string
  title: string
  titleEn: string
  category: string
  categoryEn: string
  startDate: string // YYYY-MM-DD
  endDate: string   // YYYY-MM-DD
  dateDisplay: string
  dateDisplayEn: string
  location: string
  locationEn: string
  venue: string
  description: string
  descriptionEn: string
  recommendedFleet: string
  recommendedFleetEn: string
  image: string
  sourceCredit: string
  officialLink?: string
  tags: string[]
  highlightBadge?: string
  highlightBadgeEn?: string
}

// ─── Event Category Tags ────────────────────────────────────────────────────
// 'budaya' = Tradisi & Budaya Daerah Khas Bintan
// 'sport'  = Sport Tourism & Kejuaraan Internasional
// ─────────────────────────────────────────────────────────────────────────────

export const bintanEventsData: BintanEvent[] = [
  // ── 🎏 TRADISI & BUDAYA DAERAH ────────────────────────────────────────────
  {
    id: 'festival-jong-bintan',
    title: 'Festival Jong Bintan (Bintan Jong Race Festival)',
    titleEn: 'Festival Jong Bintan (Bintan Jong Race Festival)',
    category: 'Tradisi Bahari Khas Melayu · KEN',
    categoryEn: 'Malay Maritime Tradition · KEN',
    startDate: '2026-10-10',
    endDate: '2026-10-12',
    dateDisplay: '10 - 12 Oktober 2026',
    dateDisplayEn: '10 - 12 October 2026',
    location: 'Lagoi Bay / Pantai Trikora / Sebong Pereh, Bintan',
    locationEn: 'Lagoi Bay / Trikora Beach / Sebong Pereh, Bintan',
    venue: 'Pesisir Pantai Lagoi Bay & Sebong Pereh',
    description: 'Perlombaan miniatur perahu layar tradisional tanpa awak khas Melayu pesisir yang meluncur di laut memanfaatkan angin. Diikuti ratusan peserta dari Kepri hingga Malaysia/Singapura, diramaikan klinik pembuatan jong dan permainan rakyat (gasing & egrang). Masuk Karisma Event Nasional (KEN).',
    descriptionEn: 'Traditional unmanned miniature sailboat racing unique to Malay coastal culture, powered by sea winds. Hundreds of participants from Kepri, Malaysia & Singapore compete alongside traditional craft workshops and folk games (spinning tops & stilts). Listed in Indonesia\'s National Charisma Events (KEN).',
    recommendedFleet: 'Toyota Avanza / Veloz / Innova (Rombongan Keluarga & Wisatawan)',
    recommendedFleetEn: 'Toyota Avanza / Veloz / Innova (Family Groups & Visitors)',
    image: '/images/events/festival-jong-bintan.jpg',
    sourceCredit: 'Disparbud Kepri / Pemprov Kepri',
    officialLink: 'https://indonesia.travel',
    tags: ['Jong', 'Tradisi Melayu', 'KEN', 'Perahu Layar', 'Budaya Pesisir'],
    highlightBadge: 'Tradisi Melayu · KEN',
    highlightBadgeEn: 'Malay Tradition · KEN'
  },
  {
    id: 'hari-jadi-bintan',
    title: 'Hari Jadi Kab. Bintan & Pawai Budaya Adat',
    titleEn: 'Bintan Regency Anniversary & Cultural Parade',
    category: 'Pesta Rakyat & Pawai Budaya Adat',
    categoryEn: 'Cultural Parade & Public Celebration',
    startDate: '2026-12-01',
    endDate: '2026-12-01',
    dateDisplay: '1 Desember 2026',
    dateDisplayEn: '1 December 2026',
    location: 'Bandar Seri Bentan / Kijang / Tanjung Uban, Bintan',
    locationEn: 'Bandar Seri Bentan / Kijang / Tanjung Uban, Bintan',
    venue: 'Lapangan Utama Bandar Seri Bentan & Kijang',
    description: 'Pawai kebudayaan akbar dengan busana adat Melayu lengkap, atraksi silat pesisir, parade tarian tradisional, pasar rakyat, pameran UMKM Bintan Expo, dan pesta rakyat tahunan yang meriah.',
    descriptionEn: 'A grand cultural parade featuring full traditional Malay attire, coastal silat martial arts displays, traditional dance processions, local market, UMKM Bintan Expo exhibition, and vibrant public festivities.',
    recommendedFleet: 'Toyota Avanza / Innova Reborn / HiAce (Rombongan Pawai & Keluarga)',
    recommendedFleetEn: 'Toyota Avanza / Innova Reborn / HiAce (Parade Groups & Families)',
    image: '/images/events/hari-jadi-bintan.webp',
    sourceCredit: 'Pemkab Bintan / Bintan Resorts',
    officialLink: 'https://bintankab.go.id',
    tags: ['Hari Jadi Bintan', 'Pawai Budaya', 'Adat Melayu', 'Silat', 'UMKM'],
    highlightBadge: 'Pesta Rakyat Bintan',
    highlightBadgeEn: 'Bintan Cultural Fest'
  },
  {
    id: 'festival-lampu-colok',
    title: 'Festival Lampu Colok – Malam Tujuh Likur',
    titleEn: 'Festival Lampu Colok – Malam Tujuh Likur',
    category: 'Tradisi Budaya Religi Melayu',
    categoryEn: 'Malay Religious Cultural Tradition',
    startDate: '2027-03-24',
    endDate: '2027-03-24',
    dateDisplay: 'Malam ke-27 Ramadan 2027',
    dateDisplayEn: 'Night of 27 Ramadan 2027',
    location: 'Kijang, Bintan Timur & Seri Kuala Lobam, Bintan',
    locationEn: 'Kijang, East Bintan & Seri Kuala Lobam, Bintan',
    venue: 'Seluruh Desa & Kecamatan di Bintan',
    description: 'Tradisi ratusan tahun menyambut Lailatul Qadar. Ribuan pelita minyak tanah disusun masyarakat secara gotong royong membentuk gerbang gapura dan replika kubah masjid megah yang menyala berkilau di malam hari.',
    descriptionEn: 'A centuries-old tradition welcoming Lailatul Qadar night. Thousands of oil lamps are arranged communally to form illuminated arches and mosque dome replicas that glow majestically throughout the night.',
    recommendedFleet: 'Toyota Avanza / Innova (Sewa Malam & Kunjungan Antarkelurahan)',
    recommendedFleetEn: 'Toyota Avanza / Innova (Evening Rental & Village Hopping)',
    image: '/images/events/festival-lampu-colok.jpg',
    sourceCredit: 'Disparbud Kepri / Media Kepri',
    officialLink: 'https://indonesia.travel',
    tags: ['Lampu Colok', 'Ramadan', 'Tradisi Melayu', 'Lailatul Qadar', 'Religi'],
    highlightBadge: 'Tradisi Ramadan Melayu',
    highlightBadgeEn: 'Malay Ramadan Tradition'
  },

  // ── 🏆 SPORT TOURISM & KEJUARAAN INTERNASIONAL ───────────────────────────
  {
    id: 'dragon-boat-tanjungpinang',
    title: 'Tanjungpinang International Dragon Boat Race',
    titleEn: 'Tanjungpinang International Dragon Boat Race',
    category: 'Festival Budaya & Bahari',
    categoryEn: 'Maritime & Cultural Festival',
    startDate: '2026-10-23',
    endDate: '2026-10-25',
    dateDisplay: '23 - 25 Oktober 2026',
    dateDisplayEn: '23 - 25 October 2026',
    location: 'Sungai Carang & Tepi Laut, Tanjungpinang',
    locationEn: 'Carang River & Waterfront, Tanjungpinang',
    venue: 'Perairan Sungai Carang & Pelantar Tanjungpinang',
    description: 'Kejuaraan olahraga dayung perahu naga tradisional berstandar internasional yang diikuti tim dari berbagai negara sahabat serta festival budaya bahari pesisir Melayu.',
    descriptionEn: 'An international dragon boat paddling championship featuring teams from neighboring countries, accompanied by vibrant Malay coastal maritime cultural festivities.',
    recommendedFleet: 'Toyota Avanza / Innova Reborn (Keluarga & City Tour)',
    recommendedFleetEn: 'Toyota Avanza / Innova Reborn (Family & City Tour)',
    image: '/images/events/dragon-boat-tanjungpinang.jpg',
    sourceCredit: 'ANTARA Foto / Disparbud Kepri',
    officialLink: 'https://indonesia.travel',
    tags: ['Dragon Boat', 'Budaya Melayu', 'Sungai Carang', 'Sport Tourism'],
    highlightBadge: 'Festival Bahari',
    highlightBadgeEn: 'Maritime Fest'
  },
  {
    id: 'mandiri-bintan-marathon',
    title: 'Mandiri Bintan Marathon',
    titleEn: 'Mandiri Bintan Marathon',
    category: 'Marathon Internasional (AIMS)',
    categoryEn: 'International Marathon (AIMS)',
    startDate: '2026-11-07',
    endDate: '2026-11-08',
    dateDisplay: '7 - 8 November 2026',
    dateDisplayEn: '7 - 8 November 2026',
    location: 'Lagoi Bay, Kawasan Wisata Bintan Resorts',
    locationEn: 'Lagoi Bay, Bintan Resorts Area',
    venue: 'Plaza Lagoi & Scenic Coastal Running Course',
    description: 'Ajang lari marathon internasional bergengsi bersertifikasi AIMS dengan lintasan tepi pantai berpanorama alam eksotis Lagoi Bay, diikuti ribuan pelari dari mancanegara.',
    descriptionEn: 'A prestigious AIMS-certified international running event along the scenic coastal route of Lagoi Bay, attracting thousands of runners from across the globe.',
    recommendedFleet: 'Toyota Veloz / Innova Zenix / HiAce (Drop-off & Jemput Atlet)',
    recommendedFleetEn: 'Toyota Veloz / Innova Zenix / HiAce (Athlete Drop-off & Pickup)',
    image: '/images/events/bintan-marathon.jpg',
    sourceCredit: 'Bintan Marathon Official / Lagoi Bay',
    officialLink: 'https://bintan-marathon.com',
    tags: ['Marathon', 'AIMS Certified', 'Lagoi Bay', 'Eco-Tourism'],
    highlightBadge: 'AIMS Certified',
    highlightBadgeEn: 'AIMS Certified'
  },
  {
    id: 'bintan-triathlon',
    title: 'Bintan Triathlon & Gran Fondo',
    titleEn: 'Bintan Triathlon & Gran Fondo',
    category: 'Kejuaraan Triathlon Internasional',
    categoryEn: 'World-Class Triathlon Championship',
    startDate: '2027-05-15',
    endDate: '2027-05-16',
    dateDisplay: '15 - 16 Mei 2027',
    dateDisplayEn: '15 - 16 May 2027',
    location: 'Lagoi Bay Waterfront, Bintan Resorts',
    locationEn: 'Lagoi Bay Waterfront, Bintan Resorts',
    venue: 'Lagoi Bay Beach, Bintan Resorts',
    description: 'Pesta olahraga multi-disiplin kelas dunia (renang perairan terbuka, sepeda, dan lari) di surga tropis Lagoi Bay dengan standar fasilitas internasional.',
    descriptionEn: 'A world-class multi-discipline sports festival (open-water swim, cycling, and run) set in the tropical paradise of Lagoi Bay with international race standards.',
    recommendedFleet: 'Toyota HiAce Commuter / Premio (Muat Koper Sepeda & Tim Atlet)',
    recommendedFleetEn: 'Toyota HiAce Commuter / Premio (Fits Bike Cases & Athletic Teams)',
    image: '/images/events/bintan-triathlon.jpg',
    sourceCredit: 'Bintan Resorts / indonesia.travel',
    officialLink: 'https://indonesia.travel',
    tags: ['Triathlon', 'Open Water', 'World Championship', 'Lagoi Bay'],
    highlightBadge: 'Kejuaraan Dunia',
    highlightBadgeEn: 'World Class'
  },
  {
    id: 'tour-de-bintan',
    title: 'Tour de Bintan (UCI Gran Fondo World Series)',
    titleEn: 'Tour de Bintan (UCI Gran Fondo World Series)',
    category: 'Balap Sepeda Dunia UCI',
    categoryEn: 'UCI World Series Cycling',
    startDate: '2027-08-20',
    endDate: '2027-08-22',
    dateDisplay: '20 - 22 Agustus 2027',
    dateDisplayEn: '20 - 22 August 2027',
    location: 'Simpang Lagoi & Jalur Lintas Pulau Bintan',
    locationEn: 'Simpang Lagoi & Island-wide Bintan Route',
    venue: 'Grand Stand Simpang Lagoi & Bintan Resorts',
    description: 'Satu-satunya kualifikasi resmi UCI Gran Fondo World Series di Asia Tenggara, melintasi jalanan aspal mulus perbukitan hijau dan pesisir pantai Pulau Bintan.',
    descriptionEn: 'The only official UCI Gran Fondo World Series qualifying race in Southeast Asia, tracing smooth asphalt routes through lush hills and pristine coastlines.',
    recommendedFleet: 'Toyota HiAce Premio Luxury (Armada Khusus Tim Pesepeda & Support Car)',
    recommendedFleetEn: 'Toyota HiAce Premio Luxury (Dedicated for Cyclist Teams & Support Car)',
    image: '/images/events/tour-de-bintan.webp',
    sourceCredit: 'Tour de Bintan Official (UCI)',
    officialLink: 'https://tourdebintan.id',
    tags: ['UCI Gran Fondo', 'Road Cycling', 'Asia Qualifier', 'Bintan Hills'],
    highlightBadge: 'UCI World Series',
    highlightBadgeEn: 'UCI World Series'
  }
]

export type EventStatus = 'live' | 'upcoming' | 'past'

export function getEventStatus(event: BintanEvent, refDate: Date = new Date()): EventStatus {
  const todayStr = refDate.toISOString().split('T')[0]
  if (todayStr >= event.startDate && todayStr <= event.endDate) {
    return 'live'
  }
  if (todayStr < event.startDate) {
    return 'upcoming'
  }
  return 'past'
}

export function getDaysUntil(startDateStr: string, refDate: Date = new Date()): number {
  const today = new Date(refDate)
  today.setHours(0, 0, 0, 0)
  const target = new Date(startDateStr + 'T00:00:00')
  const diffMs = target.getTime() - today.getTime()
  return Math.ceil(diffMs / (1000 * 60 * 60 * 24))
}

export function getActiveUpcomingEvents(limit: number = 4): BintanEvent[] {
  const now = new Date()
  const todayStr = now.toISOString().split('T')[0]

  // Tags that classify as sport tourism vs budaya (tradisi daerah)
  const SPORT_IDS = ['dragon-boat-tanjungpinang', 'mandiri-bintan-marathon', 'bintan-triathlon', 'tour-de-bintan']
  const BUDAYA_IDS = ['festival-jong-bintan', 'hari-jadi-bintan', 'festival-lampu-colok', 'festival-desa-pengudang', 'festival-durian-masiran', 'pek-cun-sembahyang-laut']

  const isSport  = (e: BintanEvent) => SPORT_IDS.includes(e.id)
  const isBudaya = (e: BintanEvent) => BUDAYA_IDS.includes(e.id)

  // Sort helper: live first, then nearest upcoming date
  const sortByNearest = (list: BintanEvent[]) =>
    list.slice().sort((a, b) => {
      const aLive = getEventStatus(a, now) === 'live'
      const bLive = getEventStatus(b, now) === 'live'
      if (aLive && !bLive) return -1
      if (!aLive && bLive) return 1
      return a.startDate.localeCompare(b.startDate)
    })

  // Filter active (not yet ended) events per category
  let activeBudaya = bintanEventsData.filter(e => isBudaya(e) && e.endDate >= todayStr)
  let activeSport  = bintanEventsData.filter(e => isSport(e)  && e.endDate >= todayStr)

  // Fallback: if all events of a category have passed, use full list of that category
  if (activeBudaya.length === 0) activeBudaya = bintanEventsData.filter(isBudaya)
  if (activeSport.length  === 0) activeSport  = bintanEventsData.filter(isSport)

  // Build balanced 2+2 mix (for limit=4)
  if (limit === 4) {
    const topBudaya = sortByNearest(activeBudaya).slice(0, 2)
    const topSport  = sortByNearest(activeSport).slice(0, 2)

    // Merge and sort combined list by date (live first, then nearest)
    return sortByNearest([...topBudaya, ...topSport])
  }

  // Generic path for other limit values: merge all active, sort, slice
  const allActive = [
    ...bintanEventsData.filter(e => e.endDate >= todayStr),
  ]
  if (allActive.length === 0) return sortByNearest(bintanEventsData).slice(0, limit)
  return sortByNearest(allActive).slice(0, limit)
}

export function generateEventWhatsAppUrl(
  event: BintanEvent,
  phone: string = siteConfig.rentalPhone,
  lang: 'en' | 'id' = 'id'
): string {
  const cleanPhone = phone.replace(/\D/g, '')
  const isEn = lang === 'en'
  const title = isEn ? event.titleEn : event.title
  const date = isEn ? event.dateDisplayEn : event.dateDisplay
  const loc = isEn ? event.locationEn : event.location
  const fleet = isEn ? event.recommendedFleetEn : event.recommendedFleet

  const text = isEn
    ? `Hello ${siteConfig.rentalName}, I am planning to visit Bintan for the event:
*${title}*
Date: ${date}
Location: ${loc}
Recommended Fleet: ${fleet}

I would like to inquire about car rental / charter availability and rates for this event. Thank you!`
    : `Halo ${siteConfig.rentalName}, saya berencana ke Bintan untuk menghadiri acara:
*${title}*
Jadwal: ${date}
Lokasi: ${loc}
Rekomendasi Armada: ${fleet}

Saya ingin tanya ketersediaan sewa mobil / carter untuk acara ini. Boleh info tarif dan ketersediaannya? Terima kasih!`

  return `https://wa.me/${cleanPhone}?text=${encodeURIComponent(text)}`
}
