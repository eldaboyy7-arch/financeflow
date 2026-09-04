export interface RentalPackage {
  id: string;
  title: string;
  description: string;
  highlights: string[];
}

export interface RentalRequirementCategory {
  category: string;
  items: string[];
}

/**
 * Kebijakan bisnis rental (Paket sewa & Persyaratan).
 * Dikosongkan secara default untuk menghindari asumsi/klaim sepihak.
 * Komponen hanya akan merender section ini jika array diisi dengan kebijakan riil dari pemilik rental.
 */
export const rentalPackages: RentalPackage[] = []

export const rentalRequirements: RentalRequirementCategory[] = []
