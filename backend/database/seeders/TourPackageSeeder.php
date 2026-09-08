<?php

namespace Database\Seeders;

use App\Models\TourPackage;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class TourPackageSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::where('email', 'tigaputrimulya03@gmail.com')->value('id') ?? User::first()?->id;

        $hiaceCommuterVehicle = Vehicle::where('name', 'like', '%Commuter%')->first();
        $hiacePremioVehicle = Vehicle::where('name', 'like', '%Premio%')->first();

        $packages = [
            [
                'user_id'           => $userId,
                'vehicle_id'        => $hiaceCommuterVehicle?->id,
                'title'             => 'Tour Bintan — HiAce Commuter',
                'slug'              => 'tour-bintan-hiace-commuter',
                'subtitle'          => 'Include Supir & BBM, Kapasitas 15 Kursi',
                'badge'             => 'Paling Populer',
                'badge_color'       => 'amber',
                'price'             => 1000000,
                'price_label'       => 'HARGA MULAI',
                'duration'          => 'Full Day Tour (8 - 10 Jam)',
                'capacity'          => '15 Penumpang',
                'vehicle_name'      => 'Toyota HiAce Commuter (15 Kursi)',
                'description'       => 'Pilihan paling favorit untuk rombongan keluarga besar, reuni kantor, atau kunjungan instansi. Kabin luas, AC dingin merata di setiap baris, suspensi empuk, dan supir lokal berpengalaman yang menguasai seluruh destinasi Bintan.',
                'tour_route'        => 'Sleeping Buddha - Danau Biru & Gurun Pasir - Kawasan Wisata Lagoi - Patung Penyu & Gonggong - Vihara Patung 1000 Wajah',
                'cover_photo_path'  => '/images/fleet/hiace-commuter-silver.jpg',
                'gallery_photos'    => [
                    '/images/fleet/hiace-commuter-silver.jpg',
                    '/images/fleet/hiace-commuter-interior.jpg',
                    '/images/fleet/hiace-commuter-silver-right.jpg',
                    '/images/fleet/hiace-commuter-silver-left.jpg',
                ],
                'facilities'        => [
                    'Toyota HiAce Commuter 15 Kursi AC Dingin',
                    'Supir Lokal Ramah & Berpengalaman',
                    'BBM (Bahan Bakar Minyak) Selama Tur',
                    'Air Mineral Dingin',
                    'Biaya Parkir Destinasi Utama',
                    'Penjemputan & Pengantaran Hotel / Pelabuhan',
                ],
                'itinerary'         => [
                    '08:00 - 08:30: Penjemputan di Hotel / Pelabuhan (Bandar Bentan Telani / Sri Bintan Pura)',
                    '08:30 - 09:45: Mengunjungi Vihara Dharma Shanti & Patung Sleeping Buddha',
                    '09:45 - 11:30: Wisata Eksotis Gurun Pasir Busung & Spot Foto Danau Biru',
                    '11:30 - 13:00: Istirahat Makan Siang di Restoran Seafood Lokal khas Melayu (Biaya Mandiri)',
                    '13:00 - 15:00: Eksplorasi Kawasan Wisata Lagoi Bay & Danau Lagoi',
                    '15:00 - 16:30: Singgah di Tugu Daun Sirih & Patung Gonggong Tepi Laut',
                    '16:30 - 17:30: Mengunjungi Kemegahan Vihara Ksitigarbha Bodhisattva (Patung 1000 Wajah)',
                    '17:30 - 18:30: Belanja Oleh-Oleh Khas Bintan & Pengantaran Kembali ke Hotel / Pelabuhan',
                ],
                'included'          => [
                    'Unit HiAce Commuter full AC bersih & wangi',
                    'Supir profesional merangkap pemandu ramah',
                    'BBM (bensin/solar) selama operasional tur',
                    'Air mineral botol untuk seluruh peserta',
                    'Biaya parkir di titik-titik kunjungan utama',
                ],
                'excluded'          => [
                    'Tiket masuk destinasi wisata berbayar',
                    'Makan siang/malam peserta & supir',
                    'Pengeluaran pribadi & belanja oleh-oleh',
                    'Tipping sukarela untuk driver',
                ],
                'cta_whatsapp_text' => 'Halo Admin Bintan Travel, saya ingin booking Paket Tour HiAce Commuter untuk tanggal...',
                'sort_order'        => 1,
                'is_active'         => true,
            ],
            [
                'user_id'           => $userId,
                'vehicle_id'        => $hiacePremioVehicle?->id,
                'title'             => 'Tour Bintan — HiAce Premio Luxury',
                'slug'              => 'tour-bintan-hiace-premio-luxury',
                'subtitle'          => 'Executive Luxury Comfort, Include Supir & BBM, 11 - 14 Kursi',
                'badge'             => 'Luxury VIP',
                'badge_color'       => 'indigo',
                'price'             => 1300000,
                'price_label'       => 'HARGA MULAI',
                'duration'          => 'Full Day Tour (8 - 10 Jam)',
                'capacity'          => '11 - 14 Penumpang',
                'vehicle_name'      => 'Toyota HiAce Premio Luxury',
                'description'       => 'Pengalaman perjalanan wisata berkelas VIP. Dilengkapi interior mewah, kursi captain/reclining ergonomis, kabin kedap suara, audio premium, serta suspensi super nyaman untuk kenyamanan maksimal menjelajahi Bintan.',
                'tour_route'        => 'Sleeping Buddha - Danau Biru & Gurun Pasir - Kawasan Wisata Lagoi - Patung Penyu & Gonggong - Vihara Patung 1000 Wajah',
                'cover_photo_path'  => '/images/fleet/hiace-premio-gold-bp7024bu.jpg',
                'gallery_photos'    => [
                    '/images/fleet/hiace-premio-gold-bp7024bu.jpg',
                    '/images/fleet/hiace-premio-interior-seats.jpg',
                    '/images/fleet/hiace-premio-interior-tv.jpg',
                    '/images/fleet/hiace-premio-interior-side.jpg',
                ],
                'facilities'        => [
                    'Toyota HiAce Premio Luxury Interior VIP',
                    'Supir Profesional Berpakaian Rapi & Bersertifikat',
                    'BBM Selama Tur Penuh',
                    'Air Mineral Premium & Tissue Dingin',
                    'Biaya Parkir Semua Destinasi',
                    'Free Penjemputan VIP Pelabuhan / Resort',
                ],
                'itinerary'         => [
                    '08:00 - 08:30: VIP Pickup di Lobby Hotel / Terminal Kedatangan Ferry Pelabuhan',
                    '08:30 - 09:45: Wisata Budaya Vihara Dharma Shanti & Sleeping Buddha',
                    '09:45 - 11:30: Sesi Foto Premium di Gurun Pasir Busung & Telaga Biru',
                    '11:30 - 13:00: Makan Siang Rekomendasi di Kelong Seafood Terapung (Biaya Mandiri)',
                    '13:00 - 15:00: Relaksasi di Lagoi Bay, Pantai Pasir Putih & Plaza Lagoi',
                    '15:00 - 16:30: Menikmati Semilir Angin di Landmark Patung Gonggong Tepi Laut',
                    '16:30 - 17:30: Menikmati Kemegahan Arsitektur Vihara Patung 1000 Wajah',
                    '17:30 - 18:30: Singgah Pusat Oleh-Oleh Kerupuk Atom & Pengantaran Kembali ke Hotel',
                ],
                'included'          => [
                    'Unit Toyota HiAce Premio Luxury VIP',
                    'Supir profesional berpengalaman & berbahasa santun',
                    'BBM penuh selama perjalanan',
                    'Air mineral premium & refresh amenities',
                    'Parkir dan retribusi jalan',
                ],
                'excluded'          => [
                    'Tiket wahana permainan / atraksi khusus',
                    'Makan dan minum peserta tur',
                    'Biaya dokumentasi tambahan',
                    'Tip sukarela untuk driver',
                ],
                'cta_whatsapp_text' => 'Halo Admin Bintan Travel, saya ingin reservasi Paket Tour HiAce Premio Luxury untuk tanggal...',
                'sort_order'        => 2,
                'is_active'         => true,
            ],
            [
                'user_id'           => $userId,
                'vehicle_id'        => null,
                'title'             => 'Custom Charter & Itinerary Fleksibel',
                'slug'              => 'custom-charter-bintan',
                'subtitle'          => 'Bebas Tentukan Destinasi, Durasi & Pilihan Armada Anda',
                'badge'             => 'Fleksibel',
                'badge_color'       => 'blue',
                'price'             => 0,
                'price_label'       => 'KONSULTASI GRATIS',
                'duration'          => 'Sesuai Kebutuhan (Half Day / Full Day / Multi-Day)',
                'capacity'          => 'Innova Reborn, HiAce, Alphard / Bus',
                'vehicle_name'      => 'Pilihan Bebas (Innova Reborn / HiAce / Alphard)',
                'description'       => 'Punya agenda khusus, jadwal rapat dinas, tour golf, pre-wedding, atau rute impian sendiri? Konsultasikan kepada tim kami. Kami siap menyusunkan rute optimal, memilihkan armada terbaik, dan memberikan penawaran harga paling transparan.',
                'tour_route'        => 'Rute Fleksibel (Resort Lagoi, Trikora Beach, Tanjungpinang Heritage, Wisata Belanja)',
                'cover_photo_path'  => '/images/fleet/avanza-veloz-putih-bp1649yb.jpg',
                'gallery_photos'    => [
                    '/images/fleet/avanza-veloz-putih-bp1649yb.jpg',
                    '/images/fleet/hiace-premio-gold-bp7024bu.jpg',
                ],
                'facilities'        => [
                    'Armada Sesuai Pilihan (Innova, HiAce, Alphard)',
                    'Supir Merangkap Local Guide',
                    'Bahan Bakar Minyak Sesuai Rute',
                    'Konsultasi Rute & Jadwal Gratis',
                    'Fleksibilitas Jam Penjemputan',
                ],
                'itinerary'         => [
                    'Fleksibel: Waktu dan lokasi penjemputan disesuaikan tiket kedatangan Anda',
                    'Fleksibel: Bebas menentukan urutan destinasi tanpa terikat jadwal kaku',
                    'Fleksibel: Pilihan rute mencakup Tanjungpinang, Bintan Utara, Bintan Timur, hingga Lagoi',
                    'Fleksibel: Durasi dapat disesuaikan untuk setengah hari (half-day) maupun multi-hari',
                ],
                'included'          => [
                    'Unit armada ber-AC sesuai pilihan kontrak',
                    'Supir berpengalaman siap melayani rute Anda',
                    'Konsultasi rencana perjalanan tanpa biaya',
                    'BBM sesuai kesepakatan rute perjalanan',
                ],
                'excluded'          => [
                    'Tiket masuk destinasi di luar kesepakatan awal',
                    'Konsumsi peserta tour',
                    'Akomodasi supir (untuk luar pulau / multi-day)',
                ],
                'cta_whatsapp_text' => 'Halo Admin Bintan Travel, saya ingin konsultasi Custom Charter untuk rombongan kami...',
                'sort_order'        => 3,
                'is_active'         => true,
            ],
        ];

        foreach ($packages as $data) {
            TourPackage::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
