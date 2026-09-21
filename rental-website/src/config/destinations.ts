export interface DestinationItem {
  id: string
  name: string
  nameEn?: string
  category: 'pantai' | 'religi' | 'budaya' | 'ikonik' | 'ekowisata'
  categoryLabel: string
  categoryLabelEn?: string
  location: string
  locationEn?: string
  driveTime: string
  driveTimeEn?: string
  recommendedFleet: string
  recommendedFleetEn?: string
  badge: string
  badgeEn?: string
  description: string
  descriptionEn?: string
  highlights: string[]
  highlightsEn?: string[]
  officialSource: string
  image: string
  photoCredit: string
  photoCreditFull: string
  featured: boolean
  waText: string
  waTextEn?: string
}

export const destinationsList: DestinationItem[] = [
  {
    id: 'lagoi',
    name: 'Kawasan Wisata Lagoi & Lagoi Bay',
    nameEn: 'Lagoi Tourism Area & Lagoi Bay',
    category: 'pantai',
    categoryLabel: 'Pantai & Resort',
    categoryLabelEn: 'Beach & Resorts',
    location: 'Teluk Sebong, Bintan Utara',
    locationEn: 'Teluk Sebong, North Bintan',
    driveTime: '±60 menit dari Tanjungpinang / ±15 menit dari Pelabuhan BBT',
    driveTimeEn: '±60 mins from Tanjungpinang / ±15 mins from BBT Ferry Terminal',
    recommendedFleet: 'Avanza, Veloz, HiAce Commuter & Premio',
    recommendedFleetEn: 'Avanza, Veloz, HiAce Commuter & Premio',
    badge: 'Pantai & Resort Eksklusif',
    badgeEn: 'Exclusive Beach & Resorts',
    description: 'Pusat pariwisata internasional Bintan dengan pasir putih membentang, Lagoi Bay Lantern Park, dan deretan resort tepi pantai berkelas dunia.',
    descriptionEn: "Bintan's premier international tourism enclave featuring expansive white sand beaches, Lagoi Bay Lantern Park, and world-class beachfront luxury resorts.",
    highlights: [
      'Akses gratis ke pantai publik Lagoi Bay berpasir putih bersih',
      'Spot foto Plaza Lagoi, Rumah Imaji 3D & Taman Lampion',
      'Dekat dengan Danau Lagoi dan pusat kuliner tepi laut'
    ],
    highlightsEn: [
      'Free public access to the spotless white sand beach of Lagoi Bay',
      'Instagrammable photo spots at Plaza Lagoi, Rumah Imaji 3D & Lantern Park',
      'Close to Lake Lagoi and seaside seafood dining promenades'
    ],
    officialSource: 'Kemenparekraf RI & Dinas Kebudayaan & Pariwisata Kab. Bintan',
    image: '/images/destinations/lagoi.jpg',
    photoCredit: 'Dok. Kemenparekraf',
    photoCreditFull: 'Dokumentasi Resmi Kemenparekraf RI (Indonesia.travel)',
    featured: true,
    waText: 'Halo 3 Putri Mulya, saya ingin sewa mobil atau paket tour untuk rute wisata ke Kawasan Lagoi & Lagoi Bay.',
    waTextEn: 'Hello 3 Putri Mulya, I would like to book a car rental or tour package for Lagoi & Lagoi Bay.'
  },
  {
    id: 'busung',
    name: 'Gurun Pasir & Danau Biru Busung',
    nameEn: 'Busung Sand Dunes & Blue Lake',
    category: 'ikonik',
    categoryLabel: 'Spot Foto Ikonik',
    categoryLabelEn: 'Iconic Photo Spot',
    location: 'Desa Busung, Kec. Seri Kuala Lobam',
    locationEn: 'Busung Village, Seri Kuala Lobam',
    driveTime: '±45 menit dari Tanjungpinang / ±25 menit dari Tanjung Uban',
    driveTimeEn: '±45 mins from Tanjungpinang / ±25 mins from Tanjung Uban Port',
    recommendedFleet: 'Agya, Avanza, Veloz, Innova Reborn',
    recommendedFleetEn: 'Agya, Avanza, Veloz, Innova Reborn',
    badge: 'Spot Foto Ikonik & Viral',
    badgeEn: 'Iconic & Viral Photo Spot',
    description: 'Lanskap bukit pasir putih bergelombang eks-tambang bauksit yang berpadu dengan telaga air hujan berwarna biru toska yang sangat fotogenik.',
    descriptionEn: 'A picturesque landscape of rolling white sand dunes from former bauxite quarries paired with vibrant turquoise-blue rainwater lakes.',
    highlights: [
      'Gundukan pasir eksotis ala padang pasir Timur Tengah',
      'Danau toska jernih dengan latar perbukitan pasir putih',
      'Wahana foto perahu bambu, ayunan, dan spot selfie estetik'
    ],
    highlightsEn: [
      'Exotic desert dunes reminiscent of Middle Eastern landscapes',
      'Crystal-clear turquoise lake set against white sandy slopes',
      'Aesthetic photo props: bamboo rafts, swings, and scenic selfie decks'
    ],
    officialSource: 'Dinas Pariwisata Pemprov Kepulauan Riau',
    image: '/images/destinations/busung.jpg',
    photoCredit: 'Dok. Dispar Kepri',
    photoCreditFull: 'Dokumentasi Resmi Dinas Pariwisata Pemprov Kepulauan Riau',
    featured: true,
    waText: 'Halo 3 Putri Mulya, saya ingin booking kendaraan untuk perjalanan wisata ke Gurun Pasir Busung & Danau Biru.',
    waTextEn: 'Hello 3 Putri Mulya, I would like to book transport for Busung Sand Dunes & Blue Lake.'
  },
  {
    id: 'galang-batang',
    name: 'Vihara Jin Gang Shan (Galang Batang)',
    nameEn: 'Jin Gang Shan Monastery (Galang Batang)',
    category: 'religi',
    categoryLabel: 'Religi & Arsitektur Megah',
    categoryLabelEn: 'Grand Buddhist Heritage',
    location: 'Galang Batang, Kec. Gunung Kijang, Bintan',
    locationEn: 'Galang Batang, Gunung Kijang, Bintan',
    driveTime: '±35 menit dari Tanjungpinang / ±20 menit dari Pantai Trikora',
    driveTimeEn: '±35 mins from Tanjungpinang / ±20 mins from Trikora Beach',
    recommendedFleet: 'Avanza, Veloz, Innova Reborn, HiAce Commuter',
    recommendedFleetEn: 'Avanza, Veloz, Innova Reborn, HiAce Commuter',
    badge: 'Kawasan 38 Ha • Tiket Masuk Gratis',
    badgeEn: '38-Hectare Complex • Free Entry',
    description: 'Kompleks vihara seluas 38 hektare dengan kemegahan arsitektur kuil bercorak Thailand berbalut ornamen emas di perbukitan Galang Batang yang viral dan memikat pengunjung.',
    descriptionEn: 'A sprawling 38-hectare temple complex showcasing majestic Thai-inspired golden architecture nestled on the scenic hills of Galang Batang.',
    highlights: [
      'Kompleks vihara seluas 38 hektare dengan arsitektur kuil emas megah ala Thailand',
      'Tiket masuk gratis tanpa dipungut biaya retribusi (Bintan Tourism official)',
      'Spot patung Buddha emas raksasa, pagoda bertingkat, dan panorama alam asri',
      'Satu koridor strategis dengan rute pesisir Pantai Trikora dan Bintan Timur'
    ],
    highlightsEn: [
      'Sprawling 38-hectare temple featuring magnificent Thai-style golden architecture',
      'Free admission with no entrance or retribution fees (Official Bintan Tourism)',
      'Giant golden Buddha statue, multi-tiered pagodas, and scenic mountain views',
      'Conveniently connected along the coastal route of Trikora Beach and East Bintan'
    ],
    officialSource: 'Dinas Kebudayaan & Pariwisata Kab. Bintan (Bintan Tourism)',
    image: '/images/destinations/galang-batang.jpg',
    photoCredit: 'Dok. Disbudpar Bintan',
    photoCreditFull: 'Dokumentasi Resmi Dinas Kebudayaan & Pariwisata Kabupaten Bintan (bintantourism.com)',
    featured: true,
    waText: 'Halo 3 Putri Mulya, saya ingin sewa armada mobil untuk kunjungan wisata ke Vihara Jin Gang Shan (Galang Batang).',
    waTextEn: 'Hello 3 Putri Mulya, I would like to rent a vehicle for Jin Gang Shan Monastery in Galang Batang.'
  },
  {
    id: 'trikora',
    name: 'Pantai Trikora & Pesisir Timur',
    nameEn: 'Trikora Beach & Eastern Coastline',
    category: 'pantai',
    categoryLabel: 'Pantai & Pesisir',
    categoryLabelEn: 'Beach & Coastal',
    location: 'Desa Malang Rapat, Bintan Pesisir',
    locationEn: 'Malang Rapat Village, Coastal Bintan',
    driveTime: '±45 menit dari Tanjungpinang / ±50 menit dari Bandara RHF',
    driveTimeEn: '±45 mins from Tanjungpinang / ±50 mins from RHF Airport',
    recommendedFleet: 'Semua Tipe (Agya, Avanza, Veloz, HiAce)',
    recommendedFleetEn: 'All Vehicle Types (Agya, Avanza, Veloz, HiAce)',
    badge: 'Pesisir Alami Granit',
    badgeEn: 'Natural Granite Coast',
    description: 'Garis pantai legendaris sepanjang puluhan kilometer dengan hamparan pasir putih alami, formasi batu granit raksasa, pondok santai, dan air laut jernih.',
    descriptionEn: 'A legendary coastline stretching for kilometers, adorned with natural white sands, colossal granite boulders, breezy beach huts, and crystal-clear waters.',
    highlights: [
      'Garis pantai Trikora 1 hingga 4 yang asri dan teduh oleh pohon kelapa',
      'Formasi batu granit alami megah di tepi laut',
      'Sentra kuliner seafood segar lokal, kelong apung, dan otak-otak khas pesisir'
    ],
    highlightsEn: [
      'Scenic Trikora Beaches 1 through 4 shaded by whispering coconut palms',
      'Monumental natural granite boulder formations along the coast',
      'Fresh local seafood stalls, floating kelongs, and traditional grilled fish cake (otak-otak)'
    ],
    officialSource: 'Dinas Kebudayaan & Pariwisata Kab. Bintan',
    image: '/images/destinations/trikora.jpg',
    photoCredit: 'CC BY-SA 4.0',
    photoCreditFull: 'Foto oleh Andrian Vernandes (Wikimedia Commons, Lisensi CC BY-SA 4.0)',
    featured: false,
    waText: 'Halo 3 Putri Mulya, saya ingin sewa mobil harian untuk rute santai keliling Pantai Trikora.',
    waTextEn: 'Hello 3 Putri Mulya, I would like to rent a car for a scenic drive along Trikora Beach.'
  },
  {
    id: 'sleeping-buddha',
    name: 'Sleeping Buddha (Vihara Dharma Shanti)',
    nameEn: 'Sleeping Buddha (Dharma Shanti Vihara)',
    category: 'religi',
    categoryLabel: 'Ikon Religi & Budaya',
    categoryLabelEn: 'Religious & Cultural Icon',
    location: 'Tanjung Uban, Kec. Bintan Utara, Kab. Bintan',
    locationEn: 'Tanjung Uban, North Bintan',
    driveTime: '±15 menit dari Pelabuhan ASDP Tanjung Uban / ±30 menit dari Lagoi',
    driveTimeEn: '±15 mins from ASDP Tanjung Uban Port / ±30 mins from Lagoi',
    recommendedFleet: 'Agya, Avanza, Veloz, Innova Reborn',
    recommendedFleetEn: 'Agya, Avanza, Veloz, Innova Reborn',
    badge: 'Buddha Tidur 16,8 M • Gratis',
    badgeEn: '16.8M Reclining Buddha • Free',
    description: 'Ikon wisata religi kebanggaan Bintan Utara berupa patung Buddha Tidur (Sleeping Buddha) berlapis emas sepanjang 16,8 meter dan tinggi 4 meter yang memancarkan ketenangan nirwana.',
    descriptionEn: "North Bintan's spiritual landmark featuring a magnificent 16.8-meter-long, 4-meter-tall gold-plated Reclining Buddha statue radiating tranquility.",
    highlights: [
      'Patung Buddha Tidur berlapis emas sepanjang 16,8 meter & tinggi 4 meter',
      'Dinding relief artistik yang mengisahkan perjalanan kehidupan Sang Buddha',
      'Tiket masuk gratis bagi seluruh wisatawan dan pengunjung umum',
      'Lokasi strategis di Tanjung Uban, sangat dekat dengan penyeberangan Roro Batam-Bintan dan resort Lagoi'
    ],
    highlightsEn: [
      '16.8-meter-long & 4-meter-tall gold-plated Reclining Buddha sculpture',
      'Artistic relief murals depicting the life journey of Gautama Buddha',
      'Free admission for all international and domestic visitors',
      'Strategic location in Tanjung Uban near Batam-Bintan ferry port and Lagoi resorts'
    ],
    officialSource: 'Dinas Kebudayaan & Pariwisata Kab. Bintan (Bintan Tourism)',
    image: '/images/destinations/sleeping-buddha.jpg',
    photoCredit: 'Dok. Disbudpar Bintan',
    photoCreditFull: 'Dokumentasi Resmi Dinas Kebudayaan & Pariwisata Kabupaten Bintan (bintantourism.com)',
    featured: true,
    waText: 'Halo 3 Putri Mulya, saya ingin sewa mobil untuk rute wisata religi Sleeping Buddha Vihara Dharma Shanti Tanjung Uban.',
    waTextEn: 'Hello 3 Putri Mulya, I would like to rent a car for Sleeping Buddha at Dharma Shanti Vihara.'
  },
  {
    id: 'patung-seribu',
    name: 'Vihara Patung Seribu (Ksitigarbha Bodhisattva)',
    nameEn: '500 Lohan Temple (Ksitigarbha Bodhisattva)',
    category: 'religi',
    categoryLabel: 'Religi & Budaya',
    categoryLabelEn: 'Heritage & Culture',
    location: 'Km 14, Tanjungpinang Timur (Batas Kab. Bintan)',
    locationEn: 'Km 14, East Tanjungpinang (Bintan Border)',
    driveTime: '±20 menit dari Pusat Kota Tanjungpinang / ±15 menit dari Bandara RHF',
    driveTimeEn: '±20 mins from Tanjungpinang City Center / ±15 mins from RHF Airport',
    recommendedFleet: 'Semua Tipe (Agya, Avanza, Innova, HiAce)',
    recommendedFleetEn: 'All Types (Agya, Avanza, Innova, HiAce)',
    badge: 'Religi & 500 Arhat',
    badgeEn: 'Spiritual & 500 Arhats',
    description: 'Kompleks vihara megah dengan benteng batu bergaya Tiongkok kuno yang menaungi 500 patung batu seukuran manusia (Lohan/Arhat) dengan ekspresi wajah berbeda.',
    descriptionEn: 'A majestic monastery enclosed by stone fortress walls reminiscent of ancient China, sheltering 500 life-sized stone Arhat statues each with a unique facial expression.',
    highlights: [
      'Gerbang benteng megah bak Tembok Besar Tiongkok',
      '500 patung Arahat batu pahatan tangan dengan busana dan ekspresi unik',
      'Patung Bodhisattva Ksitigarbha setinggi belasan meter yang agung'
    ],
    highlightsEn: [
      'Imposing stone fortress gateway reminiscent of the Great Wall of China',
      '500 hand-sculpted life-sized stone Arhat statues with distinctive expressions',
      'Colossal Ksitigarbha Bodhisattva statue standing regally in the courtyard'
    ],
    officialSource: 'Dinas Pariwisata Pemprov Kepulauan Riau',
    image: '/images/destinations/patung-seribu.jpg',
    photoCredit: 'Dok. Dispar Kepri',
    photoCreditFull: 'Dokumentasi Resmi Dinas Pariwisata Pemprov Kepulauan Riau',
    featured: false,
    waText: 'Halo 3 Putri Mulya, saya ingin reservasi transportasi untuk mengunjungi Vihara Patung Seribu.',
    waTextEn: 'Hello 3 Putri Mulya, I would like to arrange transport to visit the 500 Lohan Temple.'
  },
  {
    id: 'penyengat',
    name: 'Pulau Penyengat & Masjid Raya Sultan Riau',
    nameEn: 'Penyengat Island & Grand Mosque of Sultan Riau',
    category: 'budaya',
    categoryLabel: 'Cagar Budaya Nasional',
    categoryLabelEn: 'National Cultural Heritage',
    location: 'Dermaga Penyeberangan Tanjungpinang (10 menit via pompong)',
    locationEn: 'Tanjungpinang Boat Pier (10-min boat ride)',
    driveTime: '±15 menit dari Tanjungpinang ke Pelabuhan Pompong Penyengat',
    driveTimeEn: '±15 mins from Tanjungpinang to Penyengat boat dock',
    recommendedFleet: 'Antar-Jemput Pelabuhan: Avanza, Innova, HiAce',
    recommendedFleetEn: 'Port Transfers: Avanza, Innova, HiAce',
    badge: 'Warisan Sejarah Nasional',
    badgeEn: 'National Historic Heritage',
    description: 'Pusat kejayaan Kerajaan Melayu Riau-Lingga dan mahkota bahasa Indonesia, terkenal dengan Masjid Raya Sultan Riau berputih telur dan makam pahlawan Raja Ali Haji.',
    descriptionEn: 'The historical crown of the Riau-Lingga Malay Sultanate and birthplace of standard Indonesian language, renowned for the egg-white-bonded Grand Mosque and royal tombs.',
    highlights: [
      'Masjid bersejarah warna kuning hijau yang dibangun dengan perekat putih telur',
      'Kompleks Makam Pahlawan Nasional Raja Ali Haji (penggagas dasar Bahasa Indonesia)',
      'Situs Benteng Pertahanan Bukit Kursi dan Gedung Mesiu Kerajaan'
    ],
    highlightsEn: [
      'Historic yellow-and-green royal mosque built using egg whites as mortar binder',
      'Burial complex of National Hero Raja Ali Haji (father of Bahasa Indonesia)',
      'Historic Bukit Kursi defense battery fortress and royal powder pavilion'
    ],
    officialSource: 'Kemendikbudristek RI Cagar Budaya & Dispar Kepri',
    image: '/images/destinations/penyengat.jpg',
    photoCredit: 'Wikimedia Commons CC',
    photoCreditFull: 'Dokumentasi Warisan Budaya Nasional (Wikimedia Commons)',
    featured: false,
    waText: 'Halo 3 Putri Mulya, saya butuh transportasi antar-jemput ke dermaga penyeberangan Pulau Penyengat.',
    waTextEn: 'Hello 3 Putri Mulya, I need car transfer to the boat dock for Penyengat Island.'
  },
  {
    id: 'avalokitesvara',
    name: 'Vihara Avalokitesvara Graha (Guan Yin)',
    nameEn: 'Guan Yin Temple (Avalokitesvara Graha)',
    category: 'religi',
    categoryLabel: 'Religi & Arsitektur',
    categoryLabelEn: 'Temple Architecture',
    location: 'Jl. WR Supratman Km 14, Tanjungpinang - Bintan',
    locationEn: 'Jl. WR Supratman Km 14, Tanjungpinang - Bintan',
    driveTime: '±15 menit dari Bandara RHF / ±25 menit dari Pelabuhan SBP',
    driveTimeEn: '±15 mins from RHF Airport / ±25 mins from SBP Ferry Port',
    recommendedFleet: 'Semua Tipe (Agya, Avanza, Veloz, HiAce)',
    recommendedFleetEn: 'All Vehicle Types (Agya, Avanza, Veloz, HiAce)',
    badge: 'Guan Yin Terbesar se-Asia Tenggara',
    badgeEn: 'Largest Guan Yin in SE Asia',
    description: 'Salah satu vihara Buddha terbesar di Asia Tenggara dengan patung Dewi Kwan Im berlapis emas setinggi 16,8 meter di dalam aula utama yang megah dan taman asri.',
    descriptionEn: 'One of the largest Buddhist monasteries in Southeast Asia housing a 16.8-meter gold-plated Goddess of Mercy (Guan Yin) statue inside a grandiose hall with scenic dragon fruit orchards.',
    highlights: [
      'Patung Dewi Kwan Im berlapis emas terdaftar dalam Rekor MURI',
      'Halaman luas berpagar patung arhat dan taman buah naga yang menyejukkan',
      'Arsitektur vihara megah dengan latar perbukitan hijau'
    ],
    highlightsEn: [
      'Gold-plated Guan Yin statue recognized in the Indonesian Record Museum (MURI)',
      'Expansive grounds bordered by statues and tranquil dragon fruit orchards',
      'Majestic monastery architecture set against lush green hills'
    ],
    officialSource: 'Dinas Pariwisata Pemprov Kepulauan Riau',
    image: '/images/destinations/avalokitesvara.jpg',
    photoCredit: 'Wikimedia Commons CC',
    photoCreditFull: 'Dokumentasi Vihara Avalokitesvara Graha (Wikimedia Commons)',
    featured: false,
    waText: 'Halo 3 Putri Mulya, saya ingin sewa kendaraan untuk rute wisata ke Vihara Avalokitesvara Graha.',
    waTextEn: 'Hello 3 Putri Mulya, I want to rent a vehicle for a tour to Guan Yin Temple (Avalokitesvara Graha).'
  },
  {
    id: 'gonggong',
    name: 'Gedung Gonggong & Tepi Laut Laman Boenda',
    nameEn: 'Gonggong Building & Waterfront Laman Boenda',
    category: 'ikonik',
    categoryLabel: 'Landmark Ikonik',
    categoryLabelEn: 'Iconic City Landmark',
    location: 'Tepi Laut Kota Tanjungpinang',
    locationEn: 'Waterfront (Tepi Laut), Tanjungpinang City',
    driveTime: '±5 menit dari Pelabuhan Sri Bintan Pura / ±25 menit dari Bandara',
    driveTimeEn: '±5 mins from Sri Bintan Pura Ferry Port / ±25 mins from Airport',
    recommendedFleet: 'Agya, Avanza, Veloz, Innova',
    recommendedFleetEn: 'Agya, Avanza, Veloz, Innova',
    badge: 'Ikon Landmark Kepri',
    badgeEn: 'Riau Islands Landmark',
    description: 'Pusat rekreasi tepi laut dengan arsitektur menyerupai siput laut Gonggong khas Kepri, menawarkan pemandangan matahari terbenam spektakuler menghadap Selat Riau.',
    descriptionEn: 'A seaside recreational pavilion built in the shape of Bintan’s signature Gonggong sea snail, offering spectacular golden sunset vistas facing the Riau Strait.',
    highlights: [
      'Arsitektur ikonik berbentuk Gonggong (kuliner khas siput laut Bintan)',
      'Kawasan pedestrian tepi laut yang bersih, ramah jalan kaki, dan kuliner sore',
      'Spot terbaik menikmati sunset di ufuk barat Tanjungpinang'
    ],
    highlightsEn: [
      'Iconic architectural landmark shaped like the famous Gonggong sea snail',
      'Clean waterfront promenade perfect for evening strolls and street food',
      'Prime vantage point for watching picturesque sunsets over the western horizon'
    ],
    officialSource: 'Dinas Pariwisata Kota Tanjungpinang & Pemprov Kepri',
    image: '/images/destinations/gonggong.jpg',
    photoCredit: 'Wikimedia Commons CC',
    photoCreditFull: 'Dokumentasi Alun-Alun Tepi Laut (Wikimedia Commons)',
    featured: false,
    waText: 'Halo 3 Putri Mulya, saya ingin sewa mobil untuk keliling kawasan Tepi Laut & Gedung Gonggong.',
    waTextEn: 'Hello 3 Putri Mulya, I would like to hire a car to tour the Waterfront & Gonggong Building.'
  },
  {
    id: 'treasure-bay',
    name: 'Treasure Bay Bintan (Crystal Lagoon)',
    nameEn: 'Treasure Bay Bintan (Crystal Lagoon)',
    category: 'ikonik',
    categoryLabel: 'Laguna & Water Sports',
    categoryLabelEn: 'Lagoon & Water Sports',
    location: 'Kawasan Wisata Lagoi, Teluk Sebong, Bintan',
    locationEn: 'Lagoi Tourism Enclave, Teluk Sebong, Bintan',
    driveTime: '±60 menit dari Tanjungpinang / ±10 menit dari Pelabuhan BBT Lagoi',
    driveTimeEn: '±60 mins from Tanjungpinang / ±10 mins from BBT Ferry Port',
    recommendedFleet: 'Avanza, Veloz, Innova Reborn, HiAce Premio',
    recommendedFleetEn: 'Avanza, Veloz, Innova Reborn, HiAce Premio',
    badge: 'Laguna 6,3 Ha Terbesar Asia',
    badgeEn: "Asia's Largest 6.3 Ha Lagoon",
    description: 'Resort seluas 338 hektare dengan Crystal Lagoon seluas 6,3 hektare — kolam air laut buatan terbesar se-Asia Tenggara dengan wahana air kelas dunia dan glamping.',
    descriptionEn: "A 338-hectare resort enclave featuring the 6.3-hectare Crystal Lagoon — Southeast Asia's largest man-made saltwater lagoon with world-class watersports and luxury glamping.",
    highlights: [
      'Crystal Lagoon 6,3 hektare dengan air toska jernih setara 50 kolam renang olimpiade',
      'Wahana watersport lengkap: kayak, paddle board, water slide, cable ski, dan jetovator',
      'Aktivitas darat seru: tur ATV off-road hutan mangrove, segway, dan skuter listrik',
      'Fasilitas glamping mewah tepi pantai dengan restoran berstandar internasional'
    ],
    highlightsEn: [
      '6.3-hectare turquoise saltwater lagoon equivalent to 50 Olympic swimming pools',
      'Complete watersport activities: kayaking, paddleboarding, water slides, cable ski',
      'Exciting land adventures: mangrove ATV off-road trails, segways, and e-scooters',
      'Beachfront glamping resorts and international culinary dining'
    ],
    officialSource: 'Dinas Kebudayaan & Pariwisata Kab. Bintan (Bintan Tourism)',
    image: '/images/destinations/treasure-bay.jpg',
    photoCredit: 'Dok. Disbudpar Bintan',
    photoCreditFull: 'Dokumentasi Resmi Dinas Kebudayaan & Pariwisata Kabupaten Bintan (bintantourism.com)',
    featured: true,
    waText: 'Halo 3 Putri Mulya, saya ingin reservasi transportasi rental mobil untuk kunjungan wisata ke Treasure Bay Bintan.',
    waTextEn: 'Hello 3 Putri Mulya, I would like to book private transport to visit Treasure Bay Bintan.'
  },
  {
    id: 'masjid-pink',
    name: 'Masjid Pink Bintan (Masjid Raya An-Nur)',
    nameEn: 'Bintan Pink Mosque (An-Nur Grand Mosque)',
    category: 'religi',
    categoryLabel: 'Religi & Arsitektur Islami',
    categoryLabelEn: 'Islamic Heritage & Architecture',
    location: 'Simpang Lagoi, Kec. Teluk Sebong, Bintan',
    locationEn: 'Simpang Lagoi, Teluk Sebong, Bintan',
    driveTime: '±50 menit dari Tanjungpinang / ±20 menit dari Kawasan Lagoi',
    driveTimeEn: '±50 mins from Tanjungpinang / ±20 mins from Lagoi Area',
    recommendedFleet: 'Semua Tipe (Agya, Avanza, Veloz, HiAce)',
    recommendedFleetEn: 'All Types (Agya, Avanza, Veloz, HiAce)',
    badge: 'Ikon Religi Merah Muda',
    badgeEn: 'Iconic Pink Dome Mosque',
    description: 'Masjid megah bernuansa merah muda pastel memukau dengan perpaduan arsitektur Timur Tengah kontemporer dan taman tropis yang sangat fotogenik di gerbang Lagoi.',
    descriptionEn: 'A magnificent pastel-pink mosque blending contemporary Middle Eastern architecture with tropical greenery, located right at the entrance gate to Lagoi.',
    highlights: [
      'Kubah dan dinding luar bercat merah muda khas yang anggun dan memesona',
      'Ruang sholat utama berpendingin udara yang sejuk dengan mihrab keemasan elegan',
      'Spot foto favorit wisatawan saat melintas di jalur utama penghubung Tanjungpinang - Lagoi',
      'Area parkir luas yang nyaman untuk rombongan mobil keluarga maupun bus tour'
    ],
    highlightsEn: [
      'Distinctive pastel-pink domes and exterior facade offering scenic photo backdrops',
      'Air-conditioned prayer hall with elegant golden ornamental mihrab',
      'Popular scenic stopover along the main Tanjungpinang - Lagoi highway',
      'Spacious parking accommodating private rental cars and tour buses'
    ],
    officialSource: 'Dinas Kebudayaan & Pariwisata Kab. Bintan (Bintan Tourism)',
    image: '/images/destinations/masjid-pink.jpg',
    photoCredit: 'Dok. Disbudpar Bintan',
    photoCreditFull: 'Dokumentasi Resmi Dinas Kebudayaan & Pariwisata Kabupaten Bintan (bintantourism.com)',
    featured: false,
    waText: 'Halo 3 Putri Mulya, saya ingin sewa kendaraan untuk rute wisata religi ke Masjid Pink Bintan.',
    waTextEn: 'Hello 3 Putri Mulya, I want to rent a vehicle for a visit to the Pink Mosque in Bintan.'
  },
  {
    id: 'safari-lagoi',
    name: 'Safari Lagoi & Eco Farm Bintan',
    nameEn: 'Safari Lagoi & Bintan Eco Farm',
    category: 'ekowisata',
    categoryLabel: 'Konservasi & Edukasi Satwa',
    categoryLabelEn: 'Wildlife Sanctuary & Eco Farm',
    location: 'Baru City, Teluk Sebong, Bintan Resorts',
    locationEn: 'Baru City, Teluk Sebong, Bintan Resorts',
    driveTime: '±65 menit dari Tanjungpinang / ±15 menit dari Lagoi Bay',
    driveTimeEn: '±65 mins from Tanjungpinang / ±15 mins from Lagoi Bay',
    recommendedFleet: 'Avanza, Veloz, Innova Reborn, HiAce Commuter',
    recommendedFleetEn: 'Avanza, Veloz, Innova Reborn, HiAce Commuter',
    badge: 'Suaka Satwa & Eco Farm 17 Ha',
    badgeEn: '17 Ha Wildlife Sanctuary & Farm',
    description: 'Pusat suaka perlindungan satwa langka endemik Indonesia (harimau Sumatera, beruang madu, komodo, orangutan) berpadu dengan kebun buah organik seluas 17 hektare.',
    descriptionEn: 'A wildlife sanctuary protecting endangered Indonesian species (Sumatran tigers, sun bears, Komodo dragons, orangutans) alongside a 17-hectare organic fruit and vegetable farm.',
    highlights: [
      'Melihat langsung satwa langka terlindungi dalam habitat asri yang terjaga',
      'Tur jalan kaki ramah anak di kebun organik seluas 17 hektare (petik buah & sayur)',
      'Edukasi budidaya lebah madu kelulut dan konservasi keanekaragaman hayati',
      'Destinasi wisata keluarga favorit bernuansa edukatif dan rekreatif di Pulau Bintan'
    ],
    highlightsEn: [
      'Up-close encounters with rescued endangered wildlife in protected natural enclosures',
      'Family-friendly walking tours across 17 hectares of fruit orchards and gardens',
      'Educational stingless bee (kelulut) honey harvesting and biodiversity workshops',
      'Premier family-friendly educational eco-tourism attraction in Bintan Island'
    ],
    officialSource: 'Dinas Kebudayaan & Pariwisata Kab. Bintan (Bintan Tourism)',
    image: '/images/destinations/safari-lagoi.jpg',
    photoCredit: 'Dok. Disbudpar Bintan',
    photoCreditFull: 'Dokumentasi Resmi Dinas Kebudayaan & Pariwisata Kabupaten Bintan (bintantourism.com)',
    featured: false,
    waText: 'Halo 3 Putri Mulya, saya ingin rental mobil keluarga untuk berkunjung ke Safari Lagoi & Eco Farm.',
    waTextEn: 'Hello 3 Putri Mulya, I want to rent a family car to visit Safari Lagoi & Eco Farm.'
  },
  {
    id: 'air-terjun-gunung-bintan',
    name: 'Air Terjun Gunung Bintan & Hutan Tropis',
    nameEn: 'Mount Bintan Waterfall & Rainforest',
    category: 'ekowisata',
    categoryLabel: 'Wisata Alam & Air Terjun',
    categoryLabelEn: 'Nature & Waterfall Hike',
    location: 'Kampung Bekapur, Desa Bintan Buyu, Kec. Teluk Bintan',
    locationEn: 'Kampung Bekapur, Bintan Buyu, Teluk Bintan',
    driveTime: '±40 menit dari Tanjungpinang / ±35 menit dari Lagoi',
    driveTimeEn: '±40 mins from Tanjungpinang / ±35 mins from Lagoi',
    recommendedFleet: 'Avanza, Veloz, Innova Reborn (Ground Clearance Prima)',
    recommendedFleetEn: 'Avanza, Veloz, Innova Reborn (High Ground Clearance)',
    badge: 'Puncak Tertinggi Bintan 340 M',
    badgeEn: 'Highest Peak in Bintan (340M)',
    description: 'Pemandian air terjun alami berair sejuk jernih di kaki Gunung Bintan (titik tertinggi pulau 340 mdpl) yang diselimuti hutan hujan tropis rimbun dan sentra kebun durian.',
    descriptionEn: "A refreshing natural waterfall cascade at the foothills of Mount Bintan (the island's highest peak at 340m), surrounded by lush rainforest and durian orchards.",
    highlights: [
      'Kolam alami bertingkat dengan air pegunungan segar yang jernih dan bebas polusi',
      'Jalur pendakian (trekking) teduh melintasi pohon-pohon raksasa hutan lindung',
      'Pemandangan panorama seluruh Pulau Bintan dari puncak menara pantau Gunung Bintan',
      'Sentra pesta buah durian musiman khas Gunung Bintan yang terkenal manis legit'
    ],
    highlightsEn: [
      'Tiered natural rock pools with clean, crisp, pollution-free mountain spring water',
      'Shaded hiking trails winding beneath giant trees of protected rainforest',
      'Panoramic 360-degree views of Bintan Island from the mountaintop watchtower',
      'Epicenter of the seasonal sweet-and-creamy Gunung Bintan durian harvest'
    ],
    officialSource: 'Dinas Kebudayaan & Pariwisata Kab. Bintan (Bintan Tourism)',
    image: '/images/destinations/air-terjun-gunung-bintan.jpg',
    photoCredit: 'Dok. Disbudpar Bintan',
    photoCreditFull: 'Dokumentasi Resmi Dinas Kebudayaan & Pariwisata Kabupaten Bintan (bintantourism.com)',
    featured: false,
    waText: 'Halo 3 Putri Mulya, saya ingin sewa mobil untuk trip wisata petualangan ke Air Terjun Gunung Bintan.',
    waTextEn: 'Hello 3 Putri Mulya, I want to rent a car for an adventure hike to Mount Bintan Waterfall.'
  },
  {
    id: 'desa-ekang',
    name: "Desa Wisata Ekang Anculai (D'Bamboo Kamp)",
    nameEn: "Ekang Tourism Village (D'Bamboo Kamp)",
    category: 'ekowisata',
    categoryLabel: 'Desa Wisata ADWI & Glamping',
    categoryLabelEn: 'Award-Winning Eco Village & Glamping',
    location: 'Desa Ekang Anculai, Kec. Teluk Sebong, Bintan',
    locationEn: 'Ekang Anculai Village, Teluk Sebong, Bintan',
    driveTime: '±45 menit dari Tanjungpinang / ±25 menit dari Lagoi',
    driveTimeEn: '±45 mins from Tanjungpinang / ±25 mins from Lagoi',
    recommendedFleet: 'Agya, Avanza, Veloz, Innova Reborn',
    recommendedFleetEn: 'Agya, Avanza, Veloz, Innova Reborn',
    badge: 'Juara ADWI Kemenparekraf • 13 Ha',
    badgeEn: 'Ministry Award Winner • 13 Ha',
    description: 'Kawasan desa wisata binaan ADWI Kemenparekraf seluas 13 hektare yang memadukan keasrian pedesaan, tenda glamping terapung di danau, petualangan ATV, dan susur mangrove.',
    descriptionEn: 'A 13-hectare eco-tourism village recognized by the Ministry of Tourism, combining peaceful rural charm, floating lake glamping tents, ATV trails, and mangrove exploration.',
    highlights: [
      'Pondok bambu dan tenda glamping kayu eksotis terapung di atas danau tenang',
      'Jalur petualangan ATV melintasi perkebunan nanas, karet, dan belantara tropis',
      'Aktivitas berkuda, memancing air tawar, dan tur edukasi perkebunan madu kelulut',
      'Ekowisata peraih penghargaan Anugerah Desa Wisata Indonesia (ADWI)'
    ],
    highlightsEn: [
      'Exotic bamboo huts and wooden glamping tents floating over a peaceful lake',
      'Exciting ATV off-road tracks through pineapple farms, rubber trees, and jungle',
      'Horseback riding, freshwater fishing, and stingless bee honey farm tours',
      'Winner of the Indonesian Tourism Village Award (ADWI) by Ministry of Tourism'
    ],
    officialSource: 'Kemenparekraf RI & Dinas Kebudayaan & Pariwisata Kab. Bintan',
    image: '/images/destinations/desa-ekang.jpg',
    photoCredit: 'Dok. Disbudpar Bintan',
    photoCreditFull: 'Dokumentasi Resmi Dinas Kebudayaan & Pariwisata Kabupaten Bintan (bintantourism.com)',
    featured: false,
    waText: 'Halo 3 Putri Mulya, saya ingin rental mobil untuk trip liburan ke Desa Wisata Ekang Anculai.',
    waTextEn: 'Hello 3 Putri Mulya, I want to rent a car for a holiday trip to Ekang Tourism Village.'
  },
  {
    id: 'museum-bahari',
    name: 'Museum Bahari Bintan',
    nameEn: 'Bintan Maritime Museum',
    category: 'budaya',
    categoryLabel: 'Museum & Edukasi Maritim',
    categoryLabelEn: 'Maritime Heritage & Museum',
    location: 'Desa Teluk Bakau, Kec. Gunung Kijang, Bintan Pesisir',
    locationEn: 'Teluk Bakau Village, Gunung Kijang, Coastal Bintan',
    driveTime: '±40 menit dari Tanjungpinang / ±5 menit dari Pantai Trikora',
    driveTimeEn: '±40 mins from Tanjungpinang / ±5 mins from Trikora Beach',
    recommendedFleet: 'Semua Tipe (Agya, Avanza, Veloz, HiAce)',
    recommendedFleetEn: 'All Types (Agya, Avanza, Veloz, HiAce)',
    badge: 'Museum Bahari Bentuk Kapal',
    badgeEn: 'Warship-Shaped Maritime Museum',
    description: 'Museum kelautan satu-satunya di Bintan berarsitektur unik menyerupai kapal perang raksasa di tepi pantai, memamerkan ribuan artefak sejarah bahari dan kapal karam kuno.',
    descriptionEn: 'The sole maritime museum in Bintan uniquely shaped like a naval battleship along the coastline, exhibiting marine artifacts and ancient shipwreck treasures.',
    highlights: [
      'Gedung museum berbentuk lambung kapal perang dengan pemandangan langsung ke laut',
      'Pameran artefak muatan kapal tenggelam kuno (BMKT) seperti porselen dinasti Tiongkok',
      'Koleksi alat tangkap tradisional, miniatur sampan Melayu, dan fosil fauna laut langka',
      'Lokasi tepat di jalur wisata pantai pesisir timur Trikora'
    ],
    highlightsEn: [
      'Warship-shaped museum architecture offering panoramic ocean views',
      'Exhibition of antique ceramics salvaged from ancient sunken merchant ships',
      'Traditional Malay fishing gears, boat miniatures, and rare marine specimens',
      'Located directly along the eastern coastal sightseeing route of Trikora'
    ],
    officialSource: 'Dinas Kebudayaan & Pariwisata Kab. Bintan (Bintan Tourism)',
    image: '/images/destinations/museum-bahari.jpg',
    photoCredit: 'Dok. Disbudpar Bintan',
    photoCreditFull: 'Dokumentasi Resmi Dinas Kebudayaan & Pariwisata Kabupaten Bintan (bintantourism.com)',
    featured: false,
    waText: 'Halo 3 Putri Mulya, saya ingin sewa mobil untuk rute edukasi maritim ke Museum Bahari Bintan.',
    waTextEn: 'Hello 3 Putri Mulya, I would like to rent a vehicle to visit Bintan Maritime Museum.'
  },
  {
    id: 'mangrove-sebong',
    name: 'Bintan Mangrove Discovery & Fireflies Tour (Sungai Sebong)',
    nameEn: 'Bintan Mangrove Discovery & Fireflies Tour (Sebong River)',
    category: 'ekowisata',
    categoryLabel: 'Ekowisata & Petualangan Susur Sungai',
    categoryLabelEn: 'Eco-Tourism & River Safari',
    location: 'Kawasan Sungai Sebong, Teluk Sebong, Bintan Utara',
    locationEn: 'Sebong River Enclave, Teluk Sebong, North Bintan',
    driveTime: '±55 menit dari Tanjungpinang / ±15 menit dari Kawasan Lagoi',
    driveTimeEn: '±55 mins from Tanjungpinang / ±15 mins from Lagoi Resorts',
    recommendedFleet: 'Avanza, Veloz, Innova Reborn, HiAce Premio',
    recommendedFleetEn: 'Avanza, Veloz, Innova Reborn, HiAce Premio',
    badge: 'Susur Sungai 8 Km • Fireflies Magis',
    badgeEn: '8 Km River Safari • Magical Fireflies',
    description: 'Petualangan susur hutan bakau purba sepanjang 8 km menelusuri Sungai Sebong di siang hari, serta tur malam magis menyaksikan ribuan kunang-kunang menyala di rimbun pepohonan mangrove.',
    descriptionEn: 'An 8-kilometer ancient mangrove river safari cruising Sebong River by day, and an enchanting night tour witnessing thousands of glowing fireflies illuminating the tropical canopies.',
    highlights: [
      'Tur susur sungai bakau peraih penghargaan ekowisata internasional',
      'Melihat satwa liar endemik: monyet silver leaf, ular bakau, burung raja-udang, dan biawak',
      'Atraksi tur malam kunang-kunang (Fireflies Tour) favorit wisatawan Singapura dan mancanegara',
      'Pemandu lokal berpengalaman dengan armada perahu kayu tradisional yang aman dan nyaman'
    ],
    highlightsEn: [
      'Award-winning international eco-tourism river cruise through pristine mangroves',
      'Encounters with native wildlife: silvered leaf monkeys, mangrove snakes, kingfishers, and monitors',
      'Magical evening Firefly Night Cruise widely favored by Singaporean & global tourists',
      'Licensed expert boatmen and safe traditional motorized longboats with life vests'
    ],
    officialSource: 'Dinas Pariwisata Pemprov Kepulauan Riau & Bintan Resorts',
    image: '/images/destinations/mangrove-sebong.webp',
    photoCredit: 'Dok. Bintan Resorts',
    photoCreditFull: 'Dokumentasi Resmi Kawasan Wisata Bintan Mangrove (bintan-resorts.com)',
    featured: true,
    waText: 'Halo 3 Putri Mulya, saya ingin rental mobil untuk trip wisata ke Bintan Mangrove & Fireflies Tour Sungai Sebong.',
    waTextEn: 'Hello 3 Putri Mulya, I would like to book a car rental for Bintan Mangrove & Fireflies Tour at Sebong River.'
  },
  {
    id: 'banyan-tree-temple',
    name: 'Kelenteng Pohon Beringin (Banyan Tree Temple Senggarang)',
    nameEn: 'Senggarang Banyan Tree Temple',
    category: 'budaya',
    categoryLabel: 'Situs Budaya & Sejarah Religi',
    categoryLabelEn: 'Historic Heritage & Tree Temple',
    location: 'Desa Senggarang, Kec. Tanjungpinang Kota',
    locationEn: 'Senggarang Village, Tanjungpinang City',
    driveTime: '±25 menit dari Pusat Kota Tanjungpinang / ±50 menit dari Trikora',
    driveTimeEn: '±25 mins from Tanjungpinang City / ±50 mins from Trikora',
    recommendedFleet: 'Agya, Avanza, Veloz, Innova Reborn',
    recommendedFleetEn: 'Agya, Avanza, Veloz, Innova Reborn',
    badge: 'Kuil Bersejarah 1811 • Dililit Beringin',
    badgeEn: 'Historic 1811 Temple • Enclosed by Banyan',
    description: 'Situs cagar budaya unik berusia lebih dari 200 tahun di perkampungan nelayan Tionghoa tertua Senggarang, di mana seluruh dinding kuil kuno dililit dan ditopang akar pohon beringin raksasa.',
    descriptionEn: 'A unique 200-year-old historic shrine nestled in Bintan’s oldest Chinese water settlement of Senggarang, where ancient brick walls are completely encased and supported by colossal sacred banyan roots.',
    highlights: [
      'Struktur kuil peninggalan tahun 1811 yang menyatu secara alami dengan pohon beringin raksasa',
      'Kampung pecinan tertua di Pulau Bintan dengan jejeran rumah panggung kayu di atas laut',
      'Spot fotografi arsitektur dan budaya bernuansa mistis eksotis yang terkenal ke mancanegara',
      'Dekat dengan Kompleks Vihara Dharma Sasana yang memiliki kelenteng kuno bernilai sejarah tinggi'
    ],
    highlightsEn: [
      'Historic 1811 Taoist shrine naturally embraced and anchored by majestic banyan root networks',
      'Oldest Chinese settlement in Bintan with traditional timber stilt houses over coastal waters',
      'Internationally renowned photography destination blending sacred history and natural wonder',
      'Close to the historic Dharma Sasana temple complex featuring century-old maritime shrines'
    ],
    officialSource: 'Dinas Kebudayaan & Pariwisata Kota Tanjungpinang',
    image: '/images/destinations/banyan-tree-temple.webp',
    photoCredit: 'Dok. Google Maps',
    photoCreditFull: 'Dokumentasi Pengunjung Google Maps (The Banyan Tree Shrine Senggarang)',
    featured: false,
    waText: 'Halo 3 Putri Mulya, saya ingin sewa mobil untuk wisata sejarah ke Kelenteng Pohon Beringin Senggarang.',
    waTextEn: 'Hello 3 Putri Mulya, I want to rent a car to visit the Senggarang Banyan Tree Temple.'
  },
  {
    id: 'lorong-bintan',
    name: 'Kawasan Kota Lama & Street Art Lorong Bintan',
    nameEn: 'Old Town Heritage & Lorong Bintan Street Art',
    category: 'budaya',
    categoryLabel: 'Cagar Budaya & Wisata Jalanan',
    categoryLabelEn: 'Heritage Quarter & Street Art',
    location: 'Jl. Merdeka & Lorong Bintan, Tanjungpinang Kota',
    locationEn: 'Jl. Merdeka & Lorong Bintan, Tanjungpinang Old Town',
    driveTime: '±5 menit dari Pelabuhan SBP / ±20 menit dari Bandara RHF',
    driveTimeEn: '±5 mins from SBP Ferry Port / ±20 mins from RHF Airport',
    recommendedFleet: 'Agya, Avanza, Veloz, Innova Reborn',
    recommendedFleetEn: 'Agya, Avanza, Veloz, Innova Reborn',
    badge: 'Kota Pusaka • Mural 3D & Kopi Legendaris',
    badgeEn: 'Heritage City • 3D Murals & Historic Coffee',
    description: 'Kawasan heritage bersejarah tempo dulu dengan deretan ruko kolonial vintage, lorong mural seni 3D interaktif yang estetik, serta sentra warung kopi tarik legendaris khas Kepulauan Riau.',
    descriptionEn: 'The historic heart of Tanjungpinang lined with vintage colonial shophouses, colorful 3D street art mural alleys, and legendary heritage coffee shops serving traditional hand-pulled coffee.',
    highlights: [
      'Kawasan Cagar Budaya Kota Pusaka yang ditata rapi dan ramah pejalan kaki',
      'Deretan lukisan mural dinding 3D kreatif yang menggambarkan sejarah dan kearifan lokal Melayu-Tionghoa',
      'Surganya pencinta kopi dan kuliner: Kedai Kopi Hawaii (sejak 1968), Prata, Bakpau, dan Mie Tarempa',
      'Akses sangat dekat dengan Pelabuhan Internasional Sri Bintan Pura dan pesisir laut'
    ],
    highlightsEn: [
      'Pedestrian-friendly historic heritage quarter preserving centuries-old architectural roots',
      'Creative 3D street art murals capturing maritime legends and multicultural Malay-Peranakan life',
      'Gastronomic haven: Legendary Kedai Kopi Hawaii (est. 1968), fresh roti prata, and Mie Tarempa',
      'Located just a 3-minute stroll from Sri Bintan Pura International Passenger Ferry Terminal'
    ],
    officialSource: 'Dinas Pariwisata Kota Tanjungpinang & Pemprov Kepri',
    image: '/images/destinations/lorong-bintan.jpg',
    photoCredit: 'Dok. Google Maps',
    photoCreditFull: 'Dokumentasi Pengunjung Google Maps (Street Art Lorong Bintan)',
    featured: false,
    waText: 'Halo 3 Putri Mulya, saya ingin sewa mobil untuk rute eksplorasi kuliner & Kota Lama Lorong Bintan.',
    waTextEn: 'Hello 3 Putri Mulya, I want to rent a car for exploring Tanjungpinang Old Town & Lorong Bintan.'
  },
  {
    id: 'pantai-dugong',
    name: 'Pantai Dugong & Pesisir Trikora Km 52',
    nameEn: 'Dugong Beach & Trikora Km 52 Coastline',
    category: 'pantai',
    categoryLabel: 'Pantai Bahari & Kuliner Pesisir',
    categoryLabelEn: 'White Sand Beach & Dining',
    location: 'Desa Malang Rapat, Kec. Gunung Kijang, Bintan Pesisir',
    locationEn: 'Malang Rapat Village, Gunung Kijang, Coastal Bintan',
    driveTime: '±50 menit dari Tanjungpinang / ±55 menit dari Lagoi',
    driveTimeEn: '±50 mins from Tanjungpinang / ±55 mins from Lagoi',
    recommendedFleet: 'Semua Tipe (Agya, Avanza, Veloz, HiAce)',
    recommendedFleetEn: 'All Types (Agya, Avanza, Veloz, HiAce)',
    badge: 'SK Gubernur Kepri • Spot Dugong Ikonik',
    badgeEn: 'Govt Designated Beach • Dugong Monument',
    description: 'Destinasi pantai pasir putih terindah di Trikora Km 52 dengan ikon patung Putri Duyung (Dugong), barisan pohon kelapa teduh, gazebo bersantai, dan sentra pizza bakar pesisir Bintan.',
    descriptionEn: 'One of the most pristine white sandy beaches at Trikora Km 52 featuring the iconic Dugong sea cow monument, shaded coconut groves, relaxing huts, and famous seaside wood-fired pizza.',
    highlights: [
      'Ditetapkan resmi dalam SK Gubernur Kepri sebagai Daya Tarik Wisata Unggulan Bintan',
      'Ikon patung Ikan Duyung (Dugong) megah di atas bebatuan tepi pantai yang sangat fotogenik',
      'Garis pantai landai berpasir putih bersih dengan air laut toska jernih yang tenang untuk berenang',
      'Dekat sentra kuliner pizza bakar Italia tepi pantai (Pizzeria Trikora) dan aneka kelong seafood'
    ],
    highlightsEn: [
      'Officially designated prime tourist attraction under Riau Islands Gubernatorial Decree',
      'Scenic Dugong sea cow statue atop seaside granite rocks offering iconic photo backgrounds',
      'Gently sloping pristine white sand coastline with calm crystal-clear turquoise waters for swimming',
      'Steps away from famous beachfront wood-fired pizza ovens and traditional seafood kelongs'
    ],
    officialSource: 'SK Gubernur Kepri No. 1263 & Dinas Kebudayaan & Pariwisata Kab. Bintan',
    image: '/images/destinations/pantai-dugong.webp',
    photoCredit: 'Dok. Google Maps',
    photoCreditFull: 'Dokumentasi Pengunjung Google Maps (Pantai Dugong Trikora Km 52)',
    featured: false,
    waText: 'Halo 3 Putri Mulya, saya ingin reservasi mobil sewa untuk trip santai ke Pantai Dugong Trikora Km 52.',
    waTextEn: 'Hello 3 Putri Mulya, I would like to rent a vehicle for a beach day at Dugong Beach Trikora Km 52.'
  }
]

