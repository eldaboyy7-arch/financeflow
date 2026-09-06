export type DriverPolicy = 'optional' | 'with_driver'

export interface FleetCategoryBracket {
  id: string
  label: string
  shortLabel: string
  badgeLabel: string
  minCapacity: number
  maxCapacity: number
  driverPolicy: DriverPolicy
  description: string
}

/**
 * Single source of truth untuk pembagian kategori armada berdasarkan kapasitas kursi.
 * Terbuka tanpa batas atas (maxCapacity: Infinity pada bracket Bus) agar bus besar 50-65+ kursi
 * tidak pernah salah masuk ke kategori City Car.
 */
export const FLEET_BRACKETS: FleetCategoryBracket[] = [
  {
    id: 'city-car',
    label: 'City Car (4–5 Kursi)',
    shortLabel: 'City Car',
    badgeLabel: 'City Car Hemat',
    minCapacity: 1,
    maxCapacity: 5,
    driverPolicy: 'optional',
    description: 'Pilihan ekonomis, lincah, dan hemat bahan bakar untuk kebutuhan keliling kota.'
  },
  {
    id: 'family-mpv',
    label: 'MPV Keluarga (6–8 Kursi)',
    shortLabel: 'MPV Keluarga',
    badgeLabel: 'Favorit Keluarga',
    minCapacity: 6,
    maxCapacity: 8,
    driverPolicy: 'optional',
    description: 'Kabin lega dan nyaman untuk perjalanan keluarga, dinas kerja, atau liburan santai.'
  },
  {
    id: 'minibus',
    label: 'Minibus / HiAce (9–19 Kursi)',
    shortLabel: 'HiAce / Minibus',
    badgeLabel: 'Termasuk Supir',
    minCapacity: 9,
    maxCapacity: 19,
    driverPolicy: 'with_driver',
    description: 'Armada medium khusus rombongan keluarga besar, tamu VIP, atau trip rombongan wisata.'
  },
  {
    id: 'bus',
    label: 'Bus Pariwisata (20+ Kursi)',
    shortLabel: 'Bus Pariwisata',
    badgeLabel: 'Charter Bus + Kru',
    minCapacity: 20,
    maxCapacity: Infinity,
    driverPolicy: 'with_driver',
    description: 'Kapasitas besar untuk gathering perusahaan, study tour sekolah, rombongan instansi & event.'
  }
]

/**
 * Mengembalikan bracket kategori berdasarkan jumlah kapasitas kursi kendaraan.
 * Menggunakan pendekatan defensif: jika melebihi batas atau tidak ditemukan,
 * otomatis jatuh ke bracket terakhir (Bus Pariwisata), bukan ke City Car.
 */
export function getVehicleCategoryBracket(capacity: number): FleetCategoryBracket {
  const cap = Number(capacity) || 0
  if (cap <= 0) {
    return FLEET_BRACKETS[0]
  }

  const matched = FLEET_BRACKETS.find(b => cap >= b.minCapacity && cap <= b.maxCapacity)
  return matched || FLEET_BRACKETS[FLEET_BRACKETS.length - 1]
}

/**
 * Memeriksa apakah suatu armada wajib menggunakan supir (HiAce & Bus).
 */
export function isDriverMandatory(capacity: number): boolean {
  return getVehicleCategoryBracket(capacity).driverPolicy === 'with_driver'
}

/**
 * Route resmi paket tour wisata untuk cross-selling armada rombongan (HiAce & Bus).
 */
export const TOUR_PACKAGE_ROUTE = '/paket-tour-bintan'
