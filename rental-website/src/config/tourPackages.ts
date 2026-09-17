export interface TourPackage {
  id: string
  slug: string
  title: string
  titleEn?: string
  subtitle: string
  subtitleEn?: string
  badge?: string
  badgeEn?: string
  description: string
  descriptionEn?: string
  duration: string
  durationEn?: string
  price: string
  rawPrice?: number
  priceLabel: string
  priceLabelEn?: string
  vehicle: string
  vehicleEn?: string
  capacity: string
  capacityEn?: string
  vehiclePhoto: string
  galleryPhotos?: string[]
  facilities: string[]
  facilitiesEn?: string[]
  tourRoute: string
  tourRouteEn?: string
  itinerary: string[]
  itineraryEn?: string[]
  included: string[]
  includedEn?: string[]
  excluded: string[]
  excludedEn?: string[]
  ctaWhatsappText: string
  ctaWhatsappTextEn?: string
}

/**
 * Data Resmi Paket Tour Bintan (Khusus Armada Toyota HiAce 3 Putri Mulya).
 * Disesuaikan 1-to-1 dengan data operasional klien:
 * - HiAce Commuter (Rp 1.400.000)
 * - HiAce Premio (Rp 1.500.000)
 * Keduanya sudah termasuk Supir & BBM dengan kapasitas 11-15 Person serta Karaoke System.
 */
export const tourPackages: TourPackage[] = [
  {
    id: 'tour-hiace-commuter',
    slug: 'tour-bintan-hiace-commuter',
    title: 'Tour Bintan — HiAce Commuter',
    titleEn: 'Bintan Island Tour — HiAce Commuter',
    subtitle: 'Hiace Commuter, Include Supir & BBM, 15 Person',
    subtitleEn: 'HiAce Commuter, Chauffeur & Fuel Included, 15 Persons',
    badge: 'Paling Populer',
    badgeEn: 'Most Popular',
    description: 'Paket tour keliling destinasi favorit Pulau Bintan menggunakan armada Toyota HiAce Commuter 15 kursi yang nyaman, bersih, dan dilengkapi fasilitas hiburan karaoke sepanjang perjalanan.',
    descriptionEn: "A full-day island exploration covering Bintan's favorite highlights in a clean, spacious 15-seater Toyota HiAce Commuter equipped with an onboard karaoke entertainment system.",
    duration: 'Full Day Tour (8 - 10 Jam)',
    durationEn: 'Full Day Tour (8 - 10 Hours)',
    price: 'Rp 1.400.000',
    rawPrice: 1400000,
    priceLabel: 'HARGA MULAI',
    priceLabelEn: 'STARTING FROM',
    vehicle: 'Toyota HiAce Commuter (15 Kursi)',
    vehicleEn: 'Toyota HiAce Commuter (15 Seats)',
    capacity: '15 Person',
    capacityEn: '15 Persons',
    vehiclePhoto: '/images/fleet/hiace-commuter-silver.jpg',
    galleryPhotos: [
      '/images/fleet/hiace-commuter-silver.jpg',
      '/images/fleet/hiace-commuter-interior.jpg',
      '/images/fleet/hiace-commuter-silver-right.jpg',
      '/images/fleet/hiace-commuter-silver-left.jpg'
    ],
    facilities: [
      'Mobil dilengkapi Karaoke System',
      'Full AC Dingin merata di setiap baris kursi',
      'Kabin bersih, wangi, & terawat rutin',
      'Suspensi empuk nyaman untuk jarak jauh'
    ],
    facilitiesEn: [
      'Equipped with on-board Karaoke Entertainment System',
      'Dual blower AC with balanced cooling to every seat row',
      'Spotless, fresh, and routinely detailed cabin',
      'Plush suspension engineered for smooth long-distance travel'
    ],
    tourRoute: 'Sleeping Budha - Danau Biru - Lagoi - Patung Gonggong - Patung Seribu',
    tourRouteEn: 'Sleeping Buddha - Blue Lake & Dunes - Lagoi Bay - Gonggong Landmark - 500 Lohan Temple',
    itinerary: [
      'Penjemputan rombongan di Pelabuhan Sri Bintan Pura / Bandara RHF / Hotel',
      'Kunjungan Vihara Dharma Shanti (Sleeping Buddha)',
      'Eksplorasi keindahan Danau Biru & Gurun Pasir Busung',
      'Kawasan Wisata Lagoi & Pantai Bintan Resorts',
      'Ikon Kota Tanjung Pinang: Gedung Gonggong & Tepi Laut',
      'Kunjungan Vihara Ksitigarbha Bodhisattva (Patung Seribu Wajah)',
      'Waktu belanja oleh-oleh & kuliner khas Bintan',
      'Pengantaran kembali ke pelabuhan / bandara / hotel'
    ],
    itineraryEn: [
      'Morning guest pickup at Sri Bintan Pura Ferry Terminal / RHF Airport / Hotel',
      'Visit Dharma Shanti Monastery (Reclining Buddha)',
      'Explore the breathtaking Blue Lake & Busung Sand Dunes',
      'Lagoi Tourism Hub & scenic Bintan Resorts Beach',
      'Tanjung Pinang waterfront landmark: Gonggong Building',
      'Visit Ksitigarbha Bodhisattva Monastery (500 Lohan Temple)',
      'Shopping stop for authentic local Bintan snacks and souvenirs',
      'Punctual drop-off back to ferry terminal / airport / hotel'
    ],
    included: [
      'Armada Toyota HiAce Commuter 15 Kursi',
      'Supir (Driver) ramah & berpengalaman',
      'Bahan Bakar Minyak (BBM)',
      'Karaoke System on board'
    ],
    includedEn: [
      'Toyota HiAce Commuter 15-Seater Van',
      'Courteous & experienced local chauffeur',
      'Fuel (BBM) for the designated tour route',
      'On-board Karaoke Entertainment System'
    ],
    excluded: [
      'Tiket masuk objek wisata (jika berbayar)',
      'Makan / minum peserta tour',
      'Pengeluaran pribadi di luar rute'
    ],
    excludedEn: [
      'Attraction entrance tickets (if applicable)',
      'Meals / beverages for tour participants',
      'Personal expenses outside the itinerary'
    ],
    ctaWhatsappText: 'Halo 3 Putri Mulya, saya ingin booking Paket Tour Bintan (HiAce Commuter 15 Person - Rp 1.400.000). Mohon informasi ketersediaan tanggal.',
    ctaWhatsappTextEn: 'Hello 3 Putri Mulya, I would like to book the Bintan Tour Package (HiAce Commuter 15 Persons - Rp 1,400,000). Please check date availability.'
  },
  {
    id: 'tour-hiace-premio',
    slug: 'tour-bintan-hiace-premio',
    title: 'Tour Bintan — HiAce Premio Luxury',
    titleEn: 'Bintan Luxury Tour — HiAce Premio VIP',
    subtitle: 'Hiace Premio, Include Supir & BBM, 11 - 14 Person',
    subtitleEn: 'HiAce Premio, Chauffeur & Fuel Included, 11 - 14 Persons',
    badge: 'Luxury VIP',
    badgeEn: 'Luxury VIP',
    description: 'Pengalaman wisata premium berkelas dengan Toyota HiAce Premio (BP 7024 BU). Interior mewah bergaya VIP dengan ambient lighting modern, Smart TV plafon, double wireless microphone untuk karaoke, kursi kulit empuk, dan lantai kayu elegan.',
    descriptionEn: 'An executive VIP travel experience with our flagship Toyota HiAce Premio (BP 7024 BU). Features modern ambient ceiling lighting, ceiling Smart TV, double wireless microphones for karaoke, plush leather recliners, and wood-panel flooring.',
    duration: 'Full Day Tour (8 - 10 Jam)',
    durationEn: 'Full Day Tour (8 - 10 Hours)',
    price: 'Rp 1.500.000',
    rawPrice: 1500000,
    priceLabel: 'HARGA MULAI',
    priceLabelEn: 'STARTING FROM',
    vehicle: 'Toyota HiAce Premio Luxury (BP 7024 BU)',
    vehicleEn: 'Toyota HiAce Premio Luxury VIP (BP 7024 BU)',
    capacity: '11 - 14 Person',
    capacityEn: '11 - 14 Persons',
    vehiclePhoto: '/images/fleet/hiace-premio-gold-bp7024bu.jpg',
    galleryPhotos: [
      '/images/fleet/hiace-premio-gold-bp7024bu.jpg',
      '/images/fleet/hiace-premio-interior-tv.jpg',
      '/images/fleet/hiace-premio-interior-seats.jpg',
      '/images/fleet/hiace-premio-interior-side.jpg'
    ],
    facilities: [
      'Smart TV Plafon & Karaoke System canggih',
      'Double Wireless Microphone on-board',
      'Interior Mewah: Kursi Kulit Custom & Lantai Kayu',
      'Custom Ambient Roof Lighting & Sound System Premium',
      'AC Dingin Maksimal & Kabin Sangat Senyap'
    ],
    facilitiesEn: [
      'Ceiling Smart TV & advanced Karaoke System',
      'Double on-board wireless microphones',
      'Luxury Interior: Custom Leather Captain Seats & Wood Flooring',
      'Custom ambient ceiling starlight & premium sound system',
      'Maximum AC cooling & ultra-quiet insulated cabin'
    ],
    tourRoute: 'Sleeping Budha - Danau Biru - Lagoi - Patung Gonggong - Patung Seribu',
    tourRouteEn: 'Sleeping Buddha - Blue Lake & Dunes - Lagoi Bay - Gonggong Landmark - 500 Lohan Temple',
    itinerary: [
      'Penjemputan VIP di Pelabuhan Sri Bintan Pura / Bandara RHF / Hotel / Resort',
      'Kunjungan Vihara Dharma Shanti (Sleeping Buddha)',
      'Spot foto Danau Biru & Gurun Pasir Busung',
      'Kawasan Wisata Lagoi & Pantai Eksotis Bintan',
      'Spot ikonik Patung Gonggong tepi laut Tanjung Pinang',
      'Wisata religi & budaya Vihara Patung Seribu Wajah',
      'Kuliner khas & belanja oleh-oleh lokal',
      'Pengantaran kembali dengan pelayanan ramah'
    ],
    itineraryEn: [
      'VIP morning pickup at Sri Bintan Pura / BBT Lagoi Ferry / RHF Airport / Resort',
      'Visit Dharma Shanti Vihara (Sleeping Buddha)',
      'Photo stop at Busung Sand Dunes & Blue Lake',
      'Lagoi Tourism Area & pristine Bintan white-sand beaches',
      'Iconic Gonggong seaside landmark in Tanjung Pinang',
      'Cultural & heritage exploration at 500 Lohan Temple',
      'Local culinary tasting & specialty souvenir boutique',
      'Relaxing drop-off with hospitable island hospitality'
    ],
    included: [
      'Armada Mewah Toyota HiAce Premio Luxury',
      'Supir (Driver) profesional & ramah',
      'Bahan Bakar Minyak (BBM)',
      'Karaoke System (Smart TV Plafon + Double Wireless Mic)',
      'Full AC & Sound System Premium'
    ],
    includedEn: [
      'Luxury Toyota HiAce Premio VIP Vehicle',
      'Professional & courteous private chauffeur',
      'Fuel (BBM) included',
      'Karaoke System (Smart Ceiling TV + Double Wireless Mics)',
      'Full Climate-Controlled AC & Premium Audio'
    ],
    excluded: [
      'Tiket masuk objek wisata (jika berbayar)',
      'Makan / minum peserta tour',
      'Pengeluaran pribadi di luar rute'
    ],
    excludedEn: [
      'Attraction entrance fees (if applicable)',
      'Participant meals / drinks',
      'Personal expenses outside the itinerary'
    ],
    ctaWhatsappText: 'Halo 3 Putri Mulya, saya ingin booking Paket Tour Bintan Luxury (HiAce Premio - Rp 1.500.000). Mohon cek ketersediaan jadwal.',
    ctaWhatsappTextEn: 'Hello 3 Putri Mulya, I would like to book the Bintan Luxury Tour Package (HiAce Premio - Rp 1,500,000). Please check schedule availability.'
  },
  {
    id: 'tour-hiace-custom',
    slug: 'charter-hiace-bintan-custom',
    title: 'Custom Route Charter — HiAce Bintan',
    titleEn: 'Custom Route Charter — HiAce Bintan',
    subtitle: 'HiAce Commuter / Premio, Include Supir & BBM, Rute Bebas',
    subtitleEn: 'HiAce Commuter / Premio, Chauffeur & Fuel Included, Custom Route',
    badge: 'Rute Bebas Sesuai Request',
    badgeEn: 'Flexible Custom Itinerary',
    description: 'Ingin mengunjungi destinasi tertentu di luar rute reguler (seperti Pantai Trikora, Barelang, ziarah, atau agenda kedinasan instansi)? Kami siap melayani rute khusus sesuai kebutuhan agenda Anda.',
    descriptionEn: "Wish to explore destinations outside regular circuits (such as Trikora Beach, Barelang, spiritual pilgrimages, or corporate government agendas)? We are ready to serve customized itineraries tailored specifically to your group's schedule.",
    duration: 'Fleksibel (Harian / Multi-Hari)',
    durationEn: 'Flexible (Single-Day / Multi-Day)',
    price: 'Mulai Rp 1.400.000',
    rawPrice: 1400000,
    priceLabel: 'TARIF NEGO FLEKSIBEL',
    priceLabelEn: 'FLEXIBLE NEGOTIABLE RATE',
    vehicle: 'Pilihan HiAce Commuter / HiAce Premio',
    vehicleEn: 'Choice of HiAce Commuter / HiAce Premio',
    capacity: 'Hingga 15 Person',
    capacityEn: 'Up to 15 Persons',
    vehiclePhoto: '/images/fleet/hiace-premio-interior-tv.jpg',
    galleryPhotos: [
      '/images/fleet/hiace-premio-interior-tv.jpg',
      '/images/fleet/hiace-premio-gold-bp7024bu.jpg',
      '/images/fleet/hiace-commuter-silver.jpg'
    ],
    facilities: [
      'Mobil dilengkapi Karaoke System',
      'Rute dan jadwal perjalanan bebas ditentukan penyewa',
      'Bisa untuk keperluan wisata, gathering kantor, atau dinas instansi',
      'Driver ramah & siap mendampingi sepanjang hari'
    ],
    facilitiesEn: [
      'Equipped with on-board Karaoke System',
      'Itinerary and schedule 100% determined by your group',
      'Perfect for family vacations, corporate retreats, or government visits',
      'Courteous, knowledgeable chauffeur accompanying your party all day'
    ],
    tourRoute: 'Rute Bebas: Disesuaikan dengan Permintaan & Kesepakatan Anda',
    tourRouteEn: 'Flexible Route: Fully customized according to your group preferences',
    itinerary: [
      'Titik penjemputan bebas sesuai lokasi pemesan',
      'Rute fleksibel mengelilingi destinasi pilihan Anda di Pulau Bintan & Tanjung Pinang',
      'Waktu perjalanan dan titik singgah dapat diatur mandiri'
    ],
    itineraryEn: [
      'Custom pickup location anywhere in Bintan Island',
      'Flexible routing visiting your choice of attractions in Bintan & Tanjung Pinang',
      'Self-paced stop durations and custom sightseeing schedules'
    ],
    included: [
      'Armada Toyota HiAce (Commuter / Premio)',
      'Supir berpengalaman menguasai seluruh penjuru Bintan',
      'BBM (sesuai rute yang disepakati)',
      'Karaoke on board & Full AC'
    ],
    includedEn: [
      'Toyota HiAce Fleet (Commuter or Premio)',
      'Experienced chauffeur familiar with every corner of Bintan',
      'Fuel (BBM) for the agreed custom itinerary',
      'On-board Karaoke & Full AC'
    ],
    excluded: [
      'Tiket objek wisata & konsumsi pribadi'
    ],
    excludedEn: [
      'Attraction admission tickets & personal meals'
    ],
    ctaWhatsappText: 'Halo 3 Putri Mulya, saya ingin konsultasi Custom Route Charter Toyota HiAce untuk kebutuhan perjalanan rombongan kami di Bintan.',
    ctaWhatsappTextEn: 'Hello 3 Putri Mulya, I would like to consult on a Custom Route Charter with Toyota HiAce for our group trip in Bintan.'
  }
]
