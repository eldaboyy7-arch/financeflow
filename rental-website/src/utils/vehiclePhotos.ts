import type { PublicVehicle } from '@/types/fleet'

export interface VehiclePhotoAngle {
  id: string
  label: string
  url: string
}

/**
 * Returns photo preview angle for vehicle unit.
 * Client provides single front / main photo for each vehicle.
 */
export function getVehicleAngles(car: PublicVehicle): VehiclePhotoAngle[] {
  if (!car.photo_url) return []

  return [
    { id: 'main', label: 'Foto Depan', url: car.photo_url },
  ]
}
