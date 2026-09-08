export const siteConfig = {
  rentalName: (import.meta.env.VITE_RENTAL_NAME as string) || '3 Putri Mulya',
  rentalTagline: (import.meta.env.VITE_RENTAL_TAGLINE as string) || 'Rental Mobil Lepas Kunci & Driver',
  rentalPhone: (import.meta.env.VITE_RENTAL_PHONE as string) || '6281372371120',
  rentalPhoneSecondary: (import.meta.env.VITE_RENTAL_PHONE_SECONDARY as string) || '6285263267909',
  rentalCity: (import.meta.env.VITE_RENTAL_CITY as string) || 'Tanjung Pinang & Bintan',
  rentalAddress: (import.meta.env.VITE_RENTAL_ADDRESS as string) || '',
  rentalMapsUrl: (import.meta.env.VITE_RENTAL_MAPS_URL as string) || '',
  apiUrl: (import.meta.env.VITE_PUBLIC_FLEET_API_URL as string) || 'http://127.0.0.1:8000/api/public/fleet',
  tourPackagesApiUrl: (import.meta.env.VITE_PUBLIC_TOUR_PACKAGES_API_URL as string) || 'http://127.0.0.1:8000/api/public/tour-packages',
}

