import type { PublicVehicle } from '@/types/fleet'

export interface VehiclePhotoAngle {
  id: string
  label: string
  url: string
}

/**
 * Returns available photo preview angles for each vehicle unit.
 * Designed to showcase multi-angle / twin photos ("yang kembar") directly in the catalog and hero.
 */
export function getVehicleAngles(car: PublicVehicle): VehiclePhotoAngle[] {
  if (!car.photo_url) return []

  const url = car.photo_url
  const nameLower = car.name.toLowerCase()

  // 1. Toyota All New Veloz (BP 1815 OQ - White)
  if (url.includes('veloz-putih-bp1815oq') || nameLower.includes('all new veloz')) {
    const mainUrl = url.includes('veloz-putih-bp1815oq-front')
      ? url.replace('veloz-putih-bp1815oq-front.jpg', 'veloz-putih-bp1815oq.jpg')
      : url
    const frontUrl = mainUrl.replace('veloz-putih-bp1815oq.jpg', 'veloz-putih-bp1815oq-front.jpg')

    return [
      { id: 'side', label: 'Samping', url: mainUrl },
      { id: 'front', label: 'Depan', url: frontUrl },
    ]
  }

  // 2. Toyota HiAce Commuter (Silver - Minibus 15 Seats)
  if (url.includes('hiace-commuter-silver') || nameLower.includes('hiace')) {
    const rightUrl = url.includes('hiace-commuter-silver-left')
      ? url.replace('hiace-commuter-silver-left.jpg', 'hiace-commuter-silver.jpg')
      : url
    const leftUrl = rightUrl.replace('hiace-commuter-silver.jpg', 'hiace-commuter-silver-left.jpg')

    return [
      { id: 'right', label: 'Sisi Kanan', url: rightUrl },
      { id: 'left', label: 'Sisi Kiri', url: leftUrl },
    ]
  }

  // 3. Toyota Avanza Veloz (BP 1649 YB - White Sporty)
  if (url.includes('avanza-veloz-putih-bp1649yb') || (nameLower.includes('veloz') && !nameLower.includes('all new'))) {
    let baseUrl = url
    if (url.includes('-front.jpg')) baseUrl = url.replace('-front.jpg', '.jpg')
    if (url.includes('-right.jpg')) baseUrl = url.replace('-right.jpg', '.jpg')
    if (url.includes('-left.jpg')) baseUrl = url.replace('-left.jpg', '.jpg')

    const frontUrl = baseUrl.replace('avanza-veloz-putih-bp1649yb.jpg', 'avanza-veloz-putih-bp1649yb-front.jpg')
    const rightUrl = baseUrl.replace('avanza-veloz-putih-bp1649yb.jpg', 'avanza-veloz-putih-bp1649yb-right.jpg')
    const leftUrl = baseUrl.replace('avanza-veloz-putih-bp1649yb.jpg', 'avanza-veloz-putih-bp1649yb-left.jpg')

    return [
      { id: 'front', label: 'Depan', url: frontUrl },
      { id: 'right', label: 'Sisi Kanan', url: rightUrl },
      { id: 'left', label: 'Sisi Kiri', url: leftUrl },
    ]
  }

  // Default single photo
  return [
    { id: 'main', label: 'Utama', url },
  ]
}
