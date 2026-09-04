export interface TourPackage {
  id: string
  slug: string
  title: string
  subtitle: string
  badge?: string
  description: string
  duration: string
  price: string
  priceLabel: string
  vehicle: string
  capacity: string
  vehiclePhoto: string
  galleryPhotos?: string[]
  facilities: string[]
  tourRoute: string
  itinerary: string[]
  included: string[]
  excluded: string[]
  ctaWhatsappText: string
}

/**
 * Data Resmi Paket Tour Bintan (Khusus Armada Toyota HiAce 3 Putri Mulya).
 * Disesuaikan 1-to-1 dengan data operasional klien:
 * - HiAce Commuter (Rp 1.000.000)
 * - HiAce Premio (Rp 1.200.000)
 * Keduanya sudah termasuk Supir & BBM dengan kapasitas 15 Person serta Karaoke System.
 */
export const tourPackages: TourPackage[] = [
  {
    id: 'tour-hiace-commuter',
    slug: 'tour-bintan-hiace-commuter',
    title: 'Tour Bintan — HiAce Commuter',
    subtitle: 'Hiace Commuter, Include Supir & BBM, 15 Person',
    badge: 'Paling Populer',
    description: 'Paket tour keliling destinasi favorit Pulau Bintan menggunakan armada Toyota HiAce Commuter 15 kursi yang nyaman, bersih, dan dilengkapi fasilitas hiburan karaoke sepanjang perjalanan.',
    duration: 'Full Day Tour (8 - 10 Jam)',
    price: 'Rp 1.000.000',
    priceLabel: 'HARGA MULAI',
    vehicle: 'Toyota HiAce Commuter (15 Kursi)',
    capacity: '15 Person',
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
    tourRoute: 'Sleeping Budha - Danau Biru - Lagoi - Patung Gonggong - Patung Seribu',
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
    included: [
      'Armada Toyota HiAce Commuter 15 Kursi',
      'Supir (Driver) ramah & berpengalaman',
      'Bahan Bakar Minyak (BBM)',
      'Karaoke System on board'
    ],
    excluded: [
      'Tiket masuk objek wisata (jika berbayar)',
      'Makan / minum peserta tour',
      'Pengeluaran pribadi di luar rute'
    ],
    ctaWhatsappText: 'Halo 3 Putri Mulya, saya ingin booking Paket Tour Bintan (HiAce Commuter 15 Person - Rp 1.000.000). Mohon informasi ketersediaan tanggal.'
  },
  {
    id: 'tour-hiace-premio',
    slug: 'tour-bintan-hiace-premio',
    title: 'Tour Bintan — HiAce Premio Luxury',
    subtitle: 'Hiace Premio, Include Supir & BBM, 15 Person',
    badge: 'Luxury VIP',
    description: 'Pengalaman wisata premium berkelas dengan Toyota HiAce Premio (BP 7024 BU). Interior mewah bergaya VIP dengan ambient lighting modern, Smart TV plafon, double wireless microphone untuk karaoke, kursi kulit empuk, dan lantai kayu elegan.',
    duration: 'Full Day Tour (8 - 10 Jam)',
    price: 'Rp 1.200.000',
    priceLabel: 'HARGA MULAI',
    vehicle: 'Toyota HiAce Premio Luxury (BP 7024 BU)',
    capacity: '15 Person',
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
    tourRoute: 'Sleeping Budha - Danau Biru - Lagoi - Patung Gonggong - Patung Seribu',
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
    included: [
      'Armada Mewah Toyota HiAce Premio Luxury (15 Kursi)',
      'Supir (Driver) profesional & ramah',
      'Bahan Bakar Minyak (BBM)',
      'Karaoke System (Smart TV Plafon + Double Wireless Mic)',
      'Full AC & Sound System Premium'
    ],
    excluded: [
      'Tiket masuk objek wisata (jika berbayar)',
      'Makan / minum peserta tour',
      'Pengeluaran pribadi di luar rute'
    ],
    ctaWhatsappText: 'Halo 3 Putri Mulya, saya ingin booking Paket Tour Bintan Luxury (HiAce Premio 15 Person - Rp 1.200.000). Mohon cek ketersediaan jadwal.'
  },
  {
    id: 'tour-hiace-custom',
    slug: 'charter-hiace-bintan-custom',
    title: 'Custom Route Charter — HiAce Bintan',
    subtitle: 'HiAce Commuter / Premio, Include Supir & BBM, Rute Bebas',
    badge: 'Rute Bebas Sesuai Request',
    description: 'Ingin mengunjungi destinasi tertentu di luar rute reguler (seperti Pantai Trikora, Barelang, ziarah, atau agenda kedinasan instansi)? Kami siap melayani rute khusus sesuai kebutuhan agenda Anda.',
    duration: 'Fleksibel (Harian / Multi-Hari)',
    price: 'Mulai Rp 1.000.000',
    priceLabel: 'TARIF NEGO FLEKSIBEL',
    vehicle: 'Pilihan HiAce Commuter / HiAce Premio',
    capacity: 'Hingga 15 Person',
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
    tourRoute: 'Rute Bebas: Disesuaikan dengan Permintaan & Kesepakatan Anda',
    itinerary: [
      'Titik penjemputan bebas sesuai lokasi pemesan',
      'Rute fleksibel mengelilingi destinasi pilihan Anda di Pulau Bintan & Tanjung Pinang',
      'Waktu perjalanan dan titik singgah dapat diatur mandiri'
    ],
    included: [
      'Armada Toyota HiAce (Commuter / Premio)',
      'Supir berpengalaman menguasai seluruh penjuru Bintan',
      'BBM (sesuai rute yang disepakati)',
      'Karaoke on board & Full AC'
    ],
    excluded: [
      'Tiket objek wisata & konsumsi pribadi'
    ],
    ctaWhatsappText: 'Halo 3 Putri Mulya, saya ingin konsultasi Custom Route Charter Toyota HiAce untuk kebutuhan perjalanan rombongan kami di Bintan.'
  }
]
