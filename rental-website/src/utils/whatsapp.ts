import type { PublicVehicle, RentalServiceType } from '@/types/fleet'

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
  bookingInfo?: BookingFilterParams | null,
  serviceType?: RentalServiceType,
  currencyInfo?: { formatted: string; isConverted: boolean } | null
): string {
  const cleanPhone = cleanPhoneNumber(phone)
  if (!cleanPhone) return '#'

  const isWithDriver = serviceType === 'with_driver' || (serviceType !== 'self_drive' && (vehicle.capacity >= 9 || (vehicle.daily_rate <= 0 && !!vehicle.daily_rate_driver)))
  const serviceLabel = isWithDriver ? 'Dengan Supir' : 'Lepas Kunci'
  let rateFormatted = isWithDriver && vehicle.daily_rate_driver_formatted
    ? vehicle.daily_rate_driver_formatted
    : vehicle.daily_rate_formatted

  if (currencyInfo?.isConverted) {
    rateFormatted = `${rateFormatted} (est. ${currencyInfo.formatted})`
  }

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
• Paket: Sewa ${serviceLabel}
• Transmisi: ${vehicle.transmission_label} (${vehicle.capacity} Kursi)
• Estimasi Tarif: ${rateFormatted} / hari${customBookingInfo}

Apakah unit ini tersedia untuk jadwal tersebut? Terima kasih.`
  } else if (vehicle.status === 'rented') {
    text = `Halo ${rentalName}, saya melihat di website unit ${vehicle.name} (${vehicle.model_year}) [Paket ${serviceLabel}] saat ini sedang disewa.
${customBookingInfo ? customBookingInfo + '\n\n' : ''}Apakah saya bisa booking unit ini untuk jadwal tanggal berikutnya? Terima kasih.`
  } else {
    text = `Halo ${rentalName}, saya tertarik dengan armada ${vehicle.name} (${vehicle.model_year}) [Paket ${serviceLabel}].
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
