import type { PublicVehicle } from '@/types/fleet'

export function cleanPhoneNumber(phone: string): string {
  // Menghapus karakter selain angka
  return phone.replace(/\D/g, '')
}

export function generateVehicleWhatsAppUrl(vehicle: PublicVehicle, phone: string, rentalName: string): string {
  const cleanPhone = cleanPhoneNumber(phone)
  if (!cleanPhone) return '#'

  let text = ''
  if (vehicle.status === 'available') {
    text = `Halo ${rentalName}, saya ingin menanyakan ketersediaan armada:
• Unit: ${vehicle.name} (${vehicle.model_year})
• Transmisi: ${vehicle.transmission_label} (${vehicle.capacity} Kursi)
• Tarif: ${vehicle.daily_rate_formatted} / hari

Apakah unit ini tersedia untuk tanggal sewa tertentu? Terima kasih.`
  } else if (vehicle.status === 'rented') {
    text = `Halo ${rentalName}, saya melihat di website unit ${vehicle.name} (${vehicle.model_year}) saat ini sedang disewa.
Apakah saya bisa booking unit ini untuk jadwal tanggal berikutnya? Terima kasih.`
  } else {
    text = `Halo ${rentalName}, saya tertarik dengan armada ${vehicle.name} (${vehicle.model_year}).
Apakah ada unit sejenis yang sedang siap pakai? Terima kasih.`
  }

  return `https://wa.me/${cleanPhone}?text=${encodeURIComponent(text)}`
}

export function generateGeneralWhatsAppUrl(phone: string, rentalName: string): string {
  const cleanPhone = cleanPhoneNumber(phone)
  if (!cleanPhone) return '#'

  const text = `Halo ${rentalName}, saya ingin menanyakan informasi sewa mobil. Terima kasih.`
  return `https://wa.me/${cleanPhone}?text=${encodeURIComponent(text)}`
}
