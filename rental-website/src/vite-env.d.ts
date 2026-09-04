/// <reference types="vite/client" />

interface ImportMetaEnv {
  readonly VITE_PUBLIC_FLEET_API_URL: string
  readonly VITE_RENTAL_NAME: string
  readonly VITE_RENTAL_TAGLINE: string
  readonly VITE_RENTAL_PHONE: string
  readonly VITE_RENTAL_CITY: string
  readonly VITE_RENTAL_ADDRESS: string
  readonly VITE_RENTAL_MAPS_URL: string
}

interface ImportMeta {
  readonly env: ImportMetaEnv
}
