export interface DestinationItem {
  id: string
  name: string
  category: 'pantai' | 'religi' | 'budaya' | 'ikonik' | 'ekowisata'
  categoryLabel: string
  location: string
  driveTime: string
  recommendedFleet: string
  badge: string
  description: string
  highlights: string[]
  officialSource: string
  image: string
  photoCredit: string
  photoCreditFull: string
  featured: boolean
  waText: string
}

export const destinationsList: DestinationItem[] = [
  {
    id: 'lagoi',
    name: 'Kawasan Wisata Lagoi & Lagoi Bay',
    category: 'pantai',
    categoryLabel: 'Pantai & Resort',
    location: 'Teluk Sebong, Bintan Utara',
    driveTime: '±60 menit dari Tanjungpinang / ±15 menit dari Pelabuhan BBT',
    recommendedFleet: 'Avanza, Veloz, HiAce Commuter & Premio',
    badge: 'Pantai & Resort Eksklusif',
    description: 'Pusat pariwisata internasional Bintan dengan pasir putih membentang, Lagoi Bay Lantern Park, dan deretan resort tepi pantai berkelas dunia.',
    highlights: [
      'Akses gratis ke pantai publik Lagoi Bay berpasir putih bersih',
      'Spot foto Plaza Lagoi, Rumah Imaji 3D & Taman Lampion',
      'Dekat dengan Danau Lagoi dan pusat kuliner tepi laut'
    ],
    officialSource: 'Kemenparekraf RI & Dinas Kebudayaan & Pariwisata Kab. Bintan',
    image: '/images/destinations/lagoi.jpg',
    photoCredit: 'Dok. Kemenparekraf',
    photoCreditFull: 'Dokumentasi Resmi Kemenparekraf RI (Indonesia.travel)',
    featured: true,
    waText: 'Halo 3 Putri Mulya, saya ingin sewa mobil atau paket tour untuk rute wisata ke Kawasan Lagoi & Lagoi Bay.'
  },
  {
    id: 'busung',
    name: 'Gurun Pasir & Danau Biru Busung',
    category: 'ikonik',
    categoryLabel: 'Spot Foto Ikonik',
    location: 'Desa Busung, Kec. Seri Kuala Lobam',
    driveTime: '±45 menit dari Tanjungpinang / ±25 menit dari Tanjung Uban',
    recommendedFleet: 'Agya, Avanza, Veloz, Innova Reborn',
    badge: 'Spot Foto Ikonik & Viral',
    description: 'Lanskap bukit pasir putih bergelombang eks-tambang bauksit yang berpadu dengan telaga air hujan berwarna biru toska yang sangat fotogenik.',
    highlights: [
      'Gundukan pasir eksotis ala padang pasir Timur Tengah',
      'Danau toska jernih dengan latar perbukitan pasir putih',
      'Wahana foto perahu bambu, ayunan, dan spot selfie estetik'
    ],
    officialSource: 'Dinas Pariwisata Pemprov Kepulauan Riau',
    image: '/images/destinations/busung.jpg',
    photoCredit: 'Dok. Dispar Kepri',
    photoCreditFull: 'Dokumentasi Resmi Dinas Pariwisata Pemprov Kepulauan Riau',
    featured: true,
    waText: 'Halo 3 Putri Mulya, saya ingin booking kendaraan untuk perjalanan wisata ke Gurun Pasir Busung & Danau Biru.'
  },
  {
    id: 'galang-batang',
    name: 'Vihara Jin Gang Shan (Galang Batang)',
    category: 'religi',
    categoryLabel: 'Religi & Arsitektur Megah',
    location: 'Galang Batang, Kec. Gunung Kijang, Bintan',
    driveTime: '±35 menit dari Tanjungpinang / ±20 menit dari Pantai Trikora',
    recommendedFleet: 'Avanza, Veloz, Innova Reborn, HiAce Commuter',
    badge: 'Kawasan 38 Ha • Tiket Masuk Gratis',
    description: 'Kompleks vihara seluas 38 hektare dengan kemegahan arsitektur kuil bercorak Thailand berbalut ornamen emas di perbukitan Galang Batang yang viral dan memikat pengunjung.',
    highlights: [
      'Kompleks vihara seluas 38 hektare dengan arsitektur kuil emas megah ala Thailand',
      'Tiket masuk gratis tanpa dipungut biaya retribusi (Bintan Tourism official)',
      'Spot patung Buddha emas raksasa, pagoda bertingkat, dan panorama alam asri',
      'Satu koridor strategis dengan rute pesisir Pantai Trikora dan Bintan Timur'
    ],
    officialSource: 'Dinas Kebudayaan & Pariwisata Kab. Bintan (Bintan Tourism)',
    image: '/images/destinations/galang-batang.jpg',
    photoCredit: 'Dok. Disbudpar Bintan',
    photoCreditFull: 'Dokumentasi Resmi Dinas Kebudayaan & Pariwisata Kabupaten Bintan (bintantourism.com)',
    featured: true,
    waText: 'Halo 3 Putri Mulya, saya ingin sewa armada mobil untuk kunjungan wisata ke Vihara Jin Gang Shan (Galang Batang).'
  },
  {
    id: 'trikora',
    name: 'Pantai Trikora & Pesisir Timur',
    category: 'pantai',
    categoryLabel: 'Pantai & Pesisir',
    location: 'Desa Malang Rapat, Bintan Pesisir',
    driveTime: '±45 menit dari Tanjungpinang / ±50 menit dari Bandara RHF',
    recommendedFleet: 'Semua Tipe (Agya, Avanza, Veloz, HiAce)',
    badge: 'Pesisir Alami Granit',
    description: 'Garis pantai legendaris sepanjang puluhan kilometer dengan hamparan pasir putih alami, formasi batu granit raksasa, pondok santai, dan air laut jernih.',
    highlights: [
      'Garis pantai Trikora 1 hingga 4 yang asri dan teduh oleh pohon kelapa',
      'Formasi batu granit alami megah di tepi laut',
      'Sentra kuliner seafood segar lokal, kelong apung, dan otak-otak khas pesisir'
    ],
    officialSource: 'Dinas Kebudayaan & Pariwisata Kab. Bintan',
    image: '/images/destinations/trikora.jpg',
    photoCredit: 'CC BY-SA 4.0',
    photoCreditFull: 'Foto oleh Andrian Vernandes (Wikimedia Commons, Lisensi CC BY-SA 4.0)',
    featured: false,
    waText: 'Halo 3 Putri Mulya, saya ingin sewa mobil harian untuk rute santai keliling Pantai Trikora.'
  },
  {
    id: 'sleeping-buddha',
    name: 'Sleeping Buddha (Vihara Dharma Shanti)',
    category: 'religi',
    categoryLabel: 'Ikon Religi & Budaya',
    location: 'Tanjung Uban, Kec. Bintan Utara, Kab. Bintan',
    driveTime: '±15 menit dari Pelabuhan ASDP Tanjung Uban / ±30 menit dari Lagoi',
    recommendedFleet: 'Agya, Avanza, Veloz, Innova Reborn',
    badge: 'Buddha Tidur 16,8 M • Gratis',
    description: 'Ikon wisata religi kebanggaan Bintan Utara berupa patung Buddha Tidur (Sleeping Buddha) berlapis emas sepanjang 16,8 meter dan tinggi 4 meter yang memancarkan ketenangan nirwana.',
    highlights: [
      'Patung Buddha Tidur berlapis emas sepanjang 16,8 meter & tinggi 4 meter',
      'Dinding relief artistik yang mengisahkan perjalanan kehidupan Sang Buddha',
      'Tiket masuk gratis bagi seluruh wisatawan dan pengunjung umum',
      'Lokasi strategis di Tanjung Uban, sangat dekat dengan penyeberangan Roro Batam-Bintan dan resort Lagoi'
    ],
    officialSource: 'Dinas Kebudayaan & Pariwisata Kab. Bintan (Bintan Tourism)',
    image: '/images/destinations/sleeping-buddha.jpg',
    photoCredit: 'Dok. Disbudpar Bintan',
    photoCreditFull: 'Dokumentasi Resmi Dinas Kebudayaan & Pariwisata Kabupaten Bintan (bintantourism.com)',
    featured: true,
    waText: 'Halo 3 Putri Mulya, saya ingin sewa mobil untuk rute wisata religi Sleeping Buddha Vihara Dharma Shanti Tanjung Uban.'
  },
  {
    id: 'patung-seribu',
    name: 'Vihara Patung Seribu (Ksitigarbha Bodhisattva)',
    category: 'religi',
    categoryLabel: 'Religi & Budaya',
    location: 'Km 14, Tanjungpinang Timur (Batas Kab. Bintan)',
    driveTime: '±20 menit dari Pusat Kota Tanjungpinang / ±15 menit dari Bandara RHF',
    recommendedFleet: 'Semua Tipe (Agya, Avanza, Innova, HiAce)',
    badge: 'Religi & 500 Arhat',
    description: 'Kompleks vihara megah dengan benteng batu bergaya Tiongkok kuno yang menaungi 500 patung batu seukuran manusia (Lohan/Arhat) dengan ekspresi wajah berbeda.',
    highlights: [
      'Gerbang benteng megah bak Tembok Besar Tiongkok',
      '500 patung Arahat batu pahatan tangan dengan busana dan ekspresi unik',
      'Patung Bodhisattva Ksitigarbha setinggi belasan meter yang agung'
    ],
    officialSource: 'Dinas Pariwisata Pemprov Kepulauan Riau',
    image: '/images/destinations/patung-seribu.jpg',
    photoCredit: 'Dok. Dispar Kepri',
    photoCreditFull: 'Dokumentasi Resmi Dinas Pariwisata Pemprov Kepulauan Riau',
    featured: false,
    waText: 'Halo 3 Putri Mulya, saya ingin reservasi transportasi untuk mengunjungi Vihara Patung Seribu.'
  },
  {
    id: 'penyengat',
    name: 'Pulau Penyengat & Masjid Raya Sultan Riau',
    category: 'budaya',
    categoryLabel: 'Cagar Budaya Nasional',
    location: 'Dermaga Penyeberangan Tanjungpinang (10 menit via pompong)',
    driveTime: '±15 menit dari Tanjungpinang ke Pelabuhan Pompong Penyengat',
    recommendedFleet: 'Antar-Jemput Pelabuhan: Avanza, Innova, HiAce',
    badge: 'Warisan Sejarah Nasional',
    description: 'Pusat kejayaan Kerajaan Melayu Riau-Lingga dan mahkota bahasa Indonesia, terkenal dengan Masjid Raya Sultan Riau berputih telur dan makam pahlawan Raja Ali Haji.',
    highlights: [
      'Masjid bersejarah warna kuning hijau yang dibangun dengan perekat putih telur',
      'Kompleks Makam Pahlawan Nasional Raja Ali Haji (penggagas dasar Bahasa Indonesia)',
      'Situs Benteng Pertahanan Bukit Kursi dan Gedung Mesiu Kerajaan'
    ],
    officialSource: 'Kemendikbudristek RI Cagar Budaya & Dispar Kepri',
    image: '/images/destinations/penyengat.jpg',
    photoCredit: 'Wikimedia Commons CC',
    photoCreditFull: 'Dokumentasi Warisan Budaya Nasional (Wikimedia Commons)',
    featured: false,
    waText: 'Halo 3 Putri Mulya, saya butuh transportasi antar-jemput ke dermaga penyeberangan Pulau Penyengat.'
  },
  {
    id: 'avalokitesvara',
    name: 'Vihara Avalokitesvara Graha (Guan Yin)',
    category: 'religi',
    categoryLabel: 'Religi & Arsitektur',
    location: 'Jl. WR Supratman Km 14, Tanjungpinang - Bintan',
    driveTime: '±15 menit dari Bandara RHF / ±25 menit dari Pelabuhan SBP',
    recommendedFleet: 'Semua Tipe (Agya, Avanza, Veloz, HiAce)',
    badge: 'Guan Yin Terbesar se-Asia Tenggara',
    description: 'Salah satu vihara Buddha terbesar di Asia Tenggara dengan patung Dewi Kwan Im berlapis emas setinggi 16,8 meter di dalam aula utama yang megah dan taman asri.',
    highlights: [
      'Patung Dewi Kwan Im berlapis emas terdaftar dalam Rekor MURI',
      'Halaman luas berpagar patung arhat dan taman buah naga yang menyejukkan',
      'Arsitektur vihara megah dengan latar perbukitan hijau'
    ],
    officialSource: 'Dinas Pariwisata Pemprov Kepulauan Riau',
    image: '/images/destinations/avalokitesvara.jpg',
    photoCredit: 'Wikimedia Commons CC',
    photoCreditFull: 'Dokumentasi Vihara Avalokitesvara Graha (Wikimedia Commons)',
    featured: false,
    waText: 'Halo 3 Putri Mulya, saya ingin sewa kendaraan untuk rute wisata ke Vihara Avalokitesvara Graha.'
  },
  {
    id: 'gonggong',
    name: 'Gedung Gonggong & Tepi Laut Laman Boenda',
    category: 'ikonik',
    categoryLabel: 'Landmark Ikonik',
    location: 'Tepi Laut Kota Tanjungpinang',
    driveTime: '±5 menit dari Pelabuhan Sri Bintan Pura / ±25 menit dari Bandara',
    recommendedFleet: 'Agya, Avanza, Veloz, Innova',
    badge: 'Ikon Landmark Kepri',
    description: 'Pusat rekreasi tepi laut dengan arsitektur menyerupai siput laut Gonggong khas Kepri, menawarkan pemandangan matahari terbenam spektakuler menghadap Selat Riau.',
    highlights: [
      'Arsitektur ikonik berbentuk Gonggong (kuliner khas siput laut Bintan)',
      'Kawasan pedestrian tepi laut yang bersih, ramah jalan kaki, dan kuliner sore',
      'Spot terbaik menikmati sunset di ufuk barat Tanjungpinang'
    ],
    officialSource: 'Dinas Pariwisata Kota Tanjungpinang & Pemprov Kepri',
    image: '/images/destinations/gonggong.jpg',
    photoCredit: 'Wikimedia Commons CC',
    photoCreditFull: 'Dokumentasi Alun-Alun Tepi Laut (Wikimedia Commons)',
    featured: false,
    waText: 'Halo 3 Putri Mulya, saya ingin sewa mobil untuk keliling kawasan Tepi Laut & Gedung Gonggong.'
  },
  {
    id: 'treasure-bay',
    name: 'Treasure Bay Bintan (Crystal Lagoon)',
    category: 'ikonik',
    categoryLabel: 'Laguna & Water Sports',
    location: 'Kawasan Wisata Lagoi, Teluk Sebong, Bintan',
    driveTime: '±60 menit dari Tanjungpinang / ±10 menit dari Pelabuhan BBT Lagoi',
    recommendedFleet: 'Avanza, Veloz, Innova Reborn, HiAce Premio',
    badge: 'Laguna 6,3 Ha Terbesar Asia',
    description: 'Resort seluas 338 hektare dengan Crystal Lagoon seluas 6,3 hektare — kolam air laut buatan terbesar se-Asia Tenggara dengan wahana air kelas dunia dan glamping.',
    highlights: [
      'Crystal Lagoon 6,3 hektare dengan air toska jernih setara 50 kolam renang olimpiade',
      'Wahana watersport lengkap: kayak, paddle board, water slide, cable ski, dan jetovator',
      'Aktivitas darat seru: tur ATV off-road hutan mangrove, segway, dan skuter listrik',
      'Fasilitas glamping mewah tepi pantai dengan restoran berstandar internasional'
    ],
    officialSource: 'Dinas Kebudayaan & Pariwisata Kab. Bintan (Bintan Tourism)',
    image: '/images/destinations/treasure-bay.jpg',
    photoCredit: 'Dok. Disbudpar Bintan',
    photoCreditFull: 'Dokumentasi Resmi Dinas Kebudayaan & Pariwisata Kabupaten Bintan (bintantourism.com)',
    featured: true,
    waText: 'Halo 3 Putri Mulya, saya ingin reservasi transportasi rental mobil untuk kunjungan wisata ke Treasure Bay Bintan.'
  },
  {
    id: 'masjid-pink',
    name: 'Masjid Pink Bintan (Masjid Raya An-Nur)',
    category: 'religi',
    categoryLabel: 'Religi & Arsitektur Islami',
    location: 'Simpang Lagoi, Kec. Teluk Sebong, Bintan',
    driveTime: '±50 menit dari Tanjungpinang / ±20 menit dari Kawasan Lagoi',
    recommendedFleet: 'Semua Tipe (Agya, Avanza, Veloz, HiAce)',
    badge: 'Ikon Religi Merah Muda',
    description: 'Masjid megah bernuansa merah muda pastel memukau dengan perpaduan arsitektur Timur Tengah kontemporer dan taman tropis yang sangat fotogenik di gerbang Lagoi.',
    highlights: [
      'Kubah dan dinding luar bercat merah muda khas yang anggun dan memesona',
      'Ruang sholat utama berpendingin udara yang sejuk dengan mihrab keemasan elegan',
      'Spot foto favorit wisatawan saat melintas di jalur utama penghubung Tanjungpinang - Lagoi',
      'Area parkir luas yang nyaman untuk rombongan mobil keluarga maupun bus tour'
    ],
    officialSource: 'Dinas Kebudayaan & Pariwisata Kab. Bintan (Bintan Tourism)',
    image: '/images/destinations/masjid-pink.jpg',
    photoCredit: 'Dok. Disbudpar Bintan',
    photoCreditFull: 'Dokumentasi Resmi Dinas Kebudayaan & Pariwisata Kabupaten Bintan (bintantourism.com)',
    featured: false,
    waText: 'Halo 3 Putri Mulya, saya ingin sewa kendaraan untuk rute wisata religi ke Masjid Pink Bintan.'
  },
  {
    id: 'safari-lagoi',
    name: 'Safari Lagoi & Eco Farm Bintan',
    category: 'ekowisata',
    categoryLabel: 'Konservasi & Edukasi Satwa',
    location: 'Baru City, Teluk Sebong, Bintan Resorts',
    driveTime: '±65 menit dari Tanjungpinang / ±15 menit dari Lagoi Bay',
    recommendedFleet: 'Avanza, Veloz, Innova Reborn, HiAce Commuter',
    badge: 'Suaka Satwa & Eco Farm 17 Ha',
    description: 'Pusat suaka perlindungan satwa langka endemik Indonesia (harimau Sumatera, beruang madu, komodo, orangutan) berpadu dengan kebun buah organik seluas 17 hektare.',
    highlights: [
      'Melihat langsung satwa langka terlindungi dalam habitat asri yang terjaga',
      'Tur jalan kaki ramah anak di kebun organik seluas 17 hektare (petik buah & sayur)',
      'Edukasi budidaya lebah madu kelulut dan konservasi keanekaragaman hayati',
      'Destinasi wisata keluarga favorit bernuansa edukatif dan rekreatif di Pulau Bintan'
    ],
    officialSource: 'Dinas Kebudayaan & Pariwisata Kab. Bintan (Bintan Tourism)',
    image: '/images/destinations/safari-lagoi.jpg',
    photoCredit: 'Dok. Disbudpar Bintan',
    photoCreditFull: 'Dokumentasi Resmi Dinas Kebudayaan & Pariwisata Kabupaten Bintan (bintantourism.com)',
    featured: false,
    waText: 'Halo 3 Putri Mulya, saya ingin rental mobil keluarga untuk berkunjung ke Safari Lagoi & Eco Farm.'
  },
  {
    id: 'air-terjun-gunung-bintan',
    name: 'Air Terjun Gunung Bintan & Hutan Tropis',
    category: 'ekowisata',
    categoryLabel: 'Wisata Alam & Air Terjun',
    location: 'Kampung Bekapur, Desa Bintan Buyu, Kec. Teluk Bintan',
    driveTime: '±40 menit dari Tanjungpinang / ±35 menit dari Lagoi',
    recommendedFleet: 'Avanza, Veloz, Innova Reborn (Ground Clearance Prima)',
    badge: 'Puncak Tertinggi Bintan 340 M',
    description: 'Pemandian air terjun alami berair sejuk jernih di kaki Gunung Bintan (titik tertinggi pulau 340 mdpl) yang diselimuti hutan hujan tropis rimbun dan sentra kebun durian.',
    highlights: [
      'Kolam alami bertingkat dengan air pegunungan segar yang jernih dan bebas polusi',
      'Jalur pendakian (trekking) teduh melintasi pohon-pohon raksasa hutan lindung',
      'Pemandangan panorama seluruh Pulau Bintan dari puncak menara pantau Gunung Bintan',
      'Sentra pesta buah durian musiman khas Gunung Bintan yang terkenal manis legit'
    ],
    officialSource: 'Dinas Kebudayaan & Pariwisata Kab. Bintan (Bintan Tourism)',
    image: '/images/destinations/air-terjun-gunung-bintan.jpg',
    photoCredit: 'Dok. Disbudpar Bintan',
    photoCreditFull: 'Dokumentasi Resmi Dinas Kebudayaan & Pariwisata Kabupaten Bintan (bintantourism.com)',
    featured: false,
    waText: 'Halo 3 Putri Mulya, saya ingin sewa mobil untuk trip wisata petualangan ke Air Terjun Gunung Bintan.'
  },
  {
    id: 'desa-ekang',
    name: "Desa Wisata Ekang Anculai (D'Bamboo Kamp)",
    category: 'ekowisata',
    categoryLabel: 'Desa Wisata ADWI & Glamping',
    location: 'Desa Ekang Anculai, Kec. Teluk Sebong, Bintan',
    driveTime: '±45 menit dari Tanjungpinang / ±25 menit dari Lagoi',
    recommendedFleet: 'Agya, Avanza, Veloz, Innova Reborn',
    badge: 'Juara ADWI Kemenparekraf • 13 Ha',
    description: 'Kawasan desa wisata binaan ADWI Kemenparekraf seluas 13 hektare yang memadukan keasrian pedesaan, tenda glamping terapung di danau, petualangan ATV, dan susur mangrove.',
    highlights: [
      'Pondok bambu dan tenda glamping kayu eksotis terapung di atas danau tenang',
      'Jalur petualangan ATV melintasi perkebunan nanas, karet, dan belantara tropis',
      'Aktivitas berkuda, memancing air tawar, dan tur edukasi perkebunan madu kelulut',
      'Ekowisata peraih penghargaan Anugerah Desa Wisata Indonesia (ADWI)'
    ],
    officialSource: 'Kemenparekraf RI & Dinas Kebudayaan & Pariwisata Kab. Bintan',
    image: '/images/destinations/desa-ekang.jpg',
    photoCredit: 'Dok. Disbudpar Bintan',
    photoCreditFull: 'Dokumentasi Resmi Dinas Kebudayaan & Pariwisata Kabupaten Bintan (bintantourism.com)',
    featured: false,
    waText: 'Halo 3 Putri Mulya, saya ingin rental mobil untuk trip liburan ke Desa Wisata Ekang Anculai.'
  },
  {
    id: 'museum-bahari',
    name: 'Museum Bahari Bintan',
    category: 'budaya',
    categoryLabel: 'Museum & Edukasi Maritim',
    location: 'Desa Teluk Bakau, Kec. Gunung Kijang, Bintan Pesisir',
    driveTime: '±40 menit dari Tanjungpinang / ±5 menit dari Pantai Trikora',
    recommendedFleet: 'Semua Tipe (Agya, Avanza, Veloz, HiAce)',
    badge: 'Museum Bahari Bentuk Kapal',
    description: 'Museum kelautan satu-satunya di Bintan berarsitektur unik menyerupai kapal perang raksasa di tepi pantai, memamerkan ribuan artefak sejarah bahari dan kapal karam kuno.',
    highlights: [
      'Gedung museum berbentuk lambung kapal perang dengan pemandangan langsung ke laut',
      'Pameran artefak muatan kapal tenggelam kuno (BMKT) seperti porselen dinasti Tiongkok',
      'Koleksi alat tangkap tradisional, miniatur sampan Melayu, dan fosil fauna laut langka',
      'Lokasi tepat di jalur wisata pantai pesisir timur Trikora'
    ],
    officialSource: 'Dinas Kebudayaan & Pariwisata Kab. Bintan (Bintan Tourism)',
    image: '/images/destinations/museum-bahari.jpg',
    photoCredit: 'Dok. Disbudpar Bintan',
    photoCreditFull: 'Dokumentasi Resmi Dinas Kebudayaan & Pariwisata Kabupaten Bintan (bintantourism.com)',
    featured: false,
    waText: 'Halo 3 Putri Mulya, saya ingin sewa mobil untuk rute edukasi maritim ke Museum Bahari Bintan.'
  }
]
