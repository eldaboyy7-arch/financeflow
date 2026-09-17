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
  currencyInfo?: { formatted: string; isConverted: boolean } | null,
  lang?: 'en' | 'id'
): string {
  const cleanPhone = cleanPhoneNumber(phone)
  if (!cleanPhone) return '#'

  const activeLang = lang || (typeof window !== 'undefined' ? (localStorage.getItem('3pm_user_lang') as 'en' | 'id') || 'en' : 'en')
  const isEn = activeLang === 'en'

  const isWithDriver = serviceType === 'with_driver' || (serviceType !== 'self_drive' && (vehicle.capacity >= 9 || (vehicle.daily_rate <= 0 && !!vehicle.daily_rate_driver)))
  
  let rateFormatted = isWithDriver && vehicle.daily_rate_driver_formatted
    ? vehicle.daily_rate_driver_formatted
    : vehicle.daily_rate_formatted

  if (currencyInfo?.isConverted) {
    rateFormatted = `${rateFormatted} (est. ${currencyInfo.formatted})`
  }

  if (isEn) {
    const serviceLabel = isWithDriver ? 'With Chauffeur / Driver' : 'Self-Drive (Car Only)'
    const transLabel = vehicle.transmission === 'matic' ? 'Automatic' : 'Manual'

    let customBookingInfo = ''
    if (bookingInfo) {
      const lines: string[] = []
      if (bookingInfo.startDate && bookingInfo.endDate) {
        lines.push(`• Rental Dates: ${bookingInfo.startDate} to ${bookingInfo.endDate}`)
      } else if (bookingInfo.startDate) {
        lines.push(`• Pickup Date: ${bookingInfo.startDate}`)
      }
      if (bookingInfo.passengers && bookingInfo.passengers > 0) {
        lines.push(`• Estimated Passengers: ${bookingInfo.passengers} Guests`)
      }
      if (lines.length > 0) {
        customBookingInfo = '\n' + lines.join('\n')
      }
    }

    let text = ''
    if (vehicle.status === 'available') {
      text = `Hello ${rentalName}, I would like to check the availability for:
• Vehicle: ${vehicle.name} (${vehicle.model_year})
• Option: ${serviceLabel}
• Transmission: ${transLabel} (${vehicle.capacity} Seats)
• Estimated Rate: ${rateFormatted} / day${customBookingInfo}

Is this vehicle available for my schedule? Thank you!`
    } else if (vehicle.status === 'rented') {
      text = `Hello ${rentalName}, I saw on your website that ${vehicle.name} (${vehicle.model_year}) [${serviceLabel}] is currently rented.
${customBookingInfo ? customBookingInfo + '\n\n' : ''}Can I book this unit for my upcoming travel dates? Thank you!`
    } else {
      text = `Hello ${rentalName}, I am interested in ${vehicle.name} (${vehicle.model_year}) [${serviceLabel}].
${customBookingInfo ? customBookingInfo + '\n\n' : ''}Do you have a similar car ready for booking? Thank you!`
    }

    return `https://wa.me/${cleanPhone}?text=${encodeURIComponent(text)}`
  }

  // Bahasa Indonesia template
  const serviceLabel = isWithDriver ? 'Dengan Supir' : 'Lepas Kunci'
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

export function generateGeneralWhatsAppUrl(phone: string, rentalName: string, customMessage?: string, lang?: 'en' | 'id'): string {
  const cleanPhone = cleanPhoneNumber(phone)
  if (!cleanPhone) return '#'

  const activeLang = lang || (typeof window !== 'undefined' ? (localStorage.getItem('3pm_user_lang') as 'en' | 'id') || 'en' : 'en')
  const defaultMsg = activeLang === 'en'
    ? `Hello ${rentalName}, I would like to inquire about car rental and tour services in Bintan Island. Thank you!`
    : `Halo ${rentalName}, saya ingin menanyakan informasi sewa mobil dan paket tour di Bintan. Terima kasih.`

  const text = customMessage || defaultMsg
  return `https://wa.me/${cleanPhone}?text=${encodeURIComponent(text)}`
}
