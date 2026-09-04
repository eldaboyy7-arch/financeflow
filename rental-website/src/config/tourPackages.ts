export interface TourPackage {
  id: string
  slug: string
  title: string
  badge?: string
  description: string
  duration: string
  price: string | null // Dikosongkan (null) jika belum ditentukan oleh klien
  priceNote: string
  vehicle: string
  capacity: string
  vehiclePhoto?: string
  itinerary: string[]
  included: string[]
  excluded: string[]
  ctaWhatsappText: string
}

/**
 * Data Konfigurasi Paket Tour Travel Bintan (3 Putri Mulya).
 * Sesuai prinsip kehati-hatian: harga dan kebijakan yang belum dikonfirmasi klien
 * ditandai sebagai 'Belum Ditentukan / Konfirmasi WhatsApp' agar pemilik rental dapat mengisinya kemudian.
 */
export const tourPackages: TourPackage[] = [
  {
    id: 'tour-hiace-bintan',
    slug: 'paket-tour-hiace-bintan',
    title: 'Paket Tour Bintan Rombongan (Toyota HiAce)',
    badge: 'Rombongan & Instansi',
    description: 'Layanan perjalanan wisata dan dinas menggunakan armada minibus Toyota HiAce Commuter (15 Kursi). Pilihan tepat untuk rombongan keluarga besar, instansi kantor, maupun wisatawan dari pelabuhan ferry atau bandara.',
    duration: 'Full Day (8 - 10 Jam) / Sesuai Kesepakatan',
    price: null, // Belum ditentukan oleh klien
    priceNote: 'Tarif fleksibel & terjangkau — hubungi admin via WhatsApp untuk penawaran terbaik',
    vehicle: 'Toyota HiAce Commuter (15 Kursi)',
    capacity: 'Hingga 15 Penumpang',
    vehiclePhoto: '/images/fleet/hiace-commuter-silver.jpg',
    itinerary: [
      'Penjemputan rombongan di Pelabuhan Sri Bintan Pura / Bandara RHF / Hotel',
      'Rute wisata sesuai request (Kawasan Lagoi, Pantai Bintan, atau City Tour Tanjung Pinang)',
      'Waktu istirahat, makan siang, dan belanja oleh-oleh lokal',
      'Pengantaran kembali ke titik penjemputan dengan tepat waktu'
    ],
    included: [
      'Armada Toyota HiAce Commuter 15 Kursi siap jalan',
      'Pengemudi (driver) profesional & ramah',
      'Kabin bersih, higienis, dan AC dingin merata',
      'BBM / Solar (dapat disesuaikan dengan opsi paket)'
    ],
    excluded: [
      'Tiket masuk objek wisata (jika berbayar)',
      'Konsumsi / makan peserta',
      'Pengeluaran pribadi di luar rute kesepakatan'
    ],
    ctaWhatsappText: 'Halo 3 Putri Mulya, saya tertarik dengan Paket Tour Bintan Rombongan (Toyota HiAce). Mohon informasi ketersediaan jadwal dan penawaran harganya.'
  },
  {
    id: 'tour-keluarga-bintan',
    slug: 'paket-tour-bintan-keluarga',
    title: 'Paket Tour Wisata Bintan Keluarga (MPV Nyaman)',
    badge: 'Keluarga & Grup Kecil',
    description: 'Perjalanan wisata santai dan privat keliling spot menarik di Pulau Bintan menggunakan mobil keluarga terawat (Toyota All New Veloz atau Avanza). Nyaman untuk keluarga kecil 4–7 orang.',
    duration: 'Full Day (8 - 10 Jam) / Sesuai Kesepakatan',
    price: null, // Belum ditentukan oleh klien
    priceNote: 'Tarif disesuaikan dengan rute dan durasi — konfirmasi langsung ke admin',
    vehicle: 'Toyota All New Veloz / Avanza (7 Kursi)',
    capacity: '4 - 7 Penumpang',
    vehiclePhoto: '/images/fleet/veloz-putih-bp1815oq.jpg',
    itinerary: [
      'Penjemputan di hotel / pelabuhan / bandara',
      'Eksplorasi destinasi santai pilihan keluarga',
      'Kuliner khas Tanjung Pinang & Bintan',
      'Pengantaran kembali dengan aman dan nyaman'
    ],
    included: [
      'Unit mobil terawat (All New Veloz / Avanza)',
      'Driver berpengalaman',
      'AC dingin ganda & kabin wangi'
    ],
    excluded: [
      'Tiket masuk destinasi & pengeluaran pribadi peserta'
    ],
    ctaWhatsappText: 'Halo 3 Putri Mulya, saya ingin konsultasi Paket Tour Wisata Bintan Keluarga dengan mobil Veloz/Avanza.'
  },
  {
    id: 'tour-custom-bintan',
    slug: 'paket-custom-tour-bintan',
    title: 'Custom Tour & Charter Rute Bebas',
    badge: 'Fleksibel',
    description: 'Rancang sendiri rute perjalanan dan destinasi Anda di Pulau Bintan. Kami siapkan kendaraan resmi terawat beserta driver berpengalaman yang siap mengantar agenda wisata, dinas kerja, maupun ziarah.',
    duration: 'Fleksibel (Harian / Multi-Hari)',
    price: null, // Belum ditentukan oleh klien
    priceNote: 'Dihitung transparan berdasarkan durasi sewa dan rute perjalanan',
    vehicle: 'Pilihan Bebas (City Car / MPV / HiAce Commuter)',
    capacity: 'Disesuaikan dengan pilihan armada',
    vehiclePhoto: '/images/fleet/avanza-hitam-bp1645fb.jpg',
    itinerary: [
      'Rute dan titik singgah ditentukan sepenuhnya oleh penyewa',
      'Jadwal penjemputan dan kepulangan fleksibel'
    ],
    included: [
      'Kendaraan resmi terawat 3 Putri Mulya',
      'Driver yang paham rute Pulau Bintan & Tanjung Pinang',
      'Konsultasi estimasi rute gratis via WhatsApp'
    ],
    excluded: [
      'Biaya parkir khusus / retribusi rute (jika ada)',
      'Tiket dan keperluan pribadi'
    ],
    ctaWhatsappText: 'Halo 3 Putri Mulya, saya ingin tanya Custom Tour / Charter Kendaraan untuk rute khusus di Bintan.'
  }
]
