export const siteConfig = {
  rentalName: (import.meta.env.VITE_RENTAL_NAME as string) || 'Rental Mobil',
  rentalTagline: (import.meta.env.VITE_RENTAL_TAGLINE as string) || 'Katalog Armada Terawat & Reservasi Langsung',
  rentalPhone: (import.meta.env.VITE_RENTAL_PHONE as string) || '',
  rentalCity: (import.meta.env.VITE_RENTAL_CITY as string) || '',
  rentalAddress: (import.meta.env.VITE_RENTAL_ADDRESS as string) || '',
  rentalMapsUrl: (import.meta.env.VITE_RENTAL_MAPS_URL as string) || '',
  apiUrl: (import.meta.env.VITE_PUBLIC_FLEET_API_URL as string) || 'http://127.0.0.1:8000/api/public/fleet',
}
