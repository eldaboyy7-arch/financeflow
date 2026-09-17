export const siteConfig = {
  rentalName: (import.meta.env.VITE_RENTAL_NAME as string) || '3 Putri Mulya',
  rentalTagline: (import.meta.env.VITE_RENTAL_TAGLINE as string) || 'Rental Mobil Lepas Kunci & Driver',
  rentalPhone: (import.meta.env.VITE_RENTAL_PHONE as string) || '6281372371120',
  rentalPhoneSecondary: (import.meta.env.VITE_RENTAL_PHONE_SECONDARY as string) || '6285263267909',
  rentalCity: (import.meta.env.VITE_RENTAL_CITY as string) || 'Tanjung Pinang & Bintan',
  rentalAddress: (import.meta.env.VITE_RENTAL_ADDRESS as string) || 'Jln. Indunsuri, Tlk. Lobam, Tj. Uban Sel., Kec. Seri Kuala Lobam, Kabupaten Bintan, Kepulauan Riau 29153',
  rentalMapsUrl: (import.meta.env.VITE_RENTAL_MAPS_URL as string) || 'https://maps.app.goo.gl/YFNQBbrynGodE23x8',
  apiUrl: (import.meta.env.VITE_PUBLIC_FLEET_API_URL as string) || 'https://api.3putrimulya.com/api/public/fleet',
  tourPackagesApiUrl: (import.meta.env.VITE_PUBLIC_TOUR_PACKAGES_API_URL as string) || 'https://api.3putrimulya.com/api/public/tour-packages',
}

