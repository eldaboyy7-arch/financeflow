import type { PublicVehicle } from '@/types/fleet'

export function cleanPhoneNumber(phone: string): string {
  // Menghapus karakter selain angka
  return phone.replace(/\D/g, '')
}

export interface BookingFilterParams {
  vehicleType?: string
  startDate?: string
  endDate?: string
  passengers?: number
}

export function generateVehicleWhatsAppUrl(
  vehicle: PublicVehicle,
  phone: string,
  rentalName: string,
  bookingInfo?: BookingFilterParams | null
): string {
  const cleanPhone = cleanPhoneNumber(phone)
  if (!cleanPhone) return '#'

  let customBookingInfo = ''
  if (bookingInfo) {
    const lines: string[] = []
    if (bookingInfo.startDate && bookingInfo.endDate) {
      lines.push(`• Rencana Sewa: ${bookingInfo.startDate} s/d ${bookingInfo.endDate}`)
    } else if (bookingInfo.startDate) {
      lines.push(`• Mulai Sewa: ${bookingInfo.startDate}`)
    }
    if (bookingInfo.passengers && bookingInfo.passengers > 0) {
      lines.push(`• Estimasi Penumpang: ${bookingInfo.passengers} Orang`)
    }
    if (lines.length > 0) {
      customBookingInfo = '\n' + lines.join('\n')
    }
  }

  let text = ''
  if (vehicle.status === 'available') {
    text = `Halo ${rentalName}, saya ingin menanyakan ketersediaan armada:
• Unit: ${vehicle.name} (${vehicle.model_year})
• Transmisi: ${vehicle.transmission_label} (${vehicle.capacity} Kursi)
• Tarif: ${vehicle.daily_rate_formatted} / hari${customBookingInfo}

Apakah unit ini tersedia untuk jadwal tersebut? Terima kasih.`
  } else if (vehicle.status === 'rented') {
    text = `Halo ${rentalName}, saya melihat di website unit ${vehicle.name} (${vehicle.model_year}) saat ini sedang disewa.
${customBookingInfo ? customBookingInfo + '\n\n' : ''}Apakah saya bisa booking unit ini untuk jadwal tanggal berikutnya? Terima kasih.`
  } else {
    text = `Halo ${rentalName}, saya tertarik dengan armada ${vehicle.name} (${vehicle.model_year}).
${customBookingInfo ? customBookingInfo + '\n\n' : ''}Apakah ada unit sejenis yang sedang siap pakai? Terima kasih.`
  }

  return `https://wa.me/${cleanPhone}?text=${encodeURIComponent(text)}`
}

export function generateGeneralWhatsAppUrl(phone: string, rentalName: string, customMessage?: string): string {
  const cleanPhone = cleanPhoneNumber(phone)
  if (!cleanPhone) return '#'

  const text = customMessage || `Halo ${rentalName}, saya ingin menanyakan informasi sewa mobil. Terima kasih.`
  return `https://wa.me/${cleanPhone}?text=${encodeURIComponent(text)}`
}
