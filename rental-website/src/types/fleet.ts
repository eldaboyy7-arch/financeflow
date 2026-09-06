export interface PublicVehicle {
  id: number;
  name: string;
  brand: string | null;
  model_year: number | string;
  transmission: 'matic' | 'manual';
  transmission_label: string;
  capacity: number;
  fuel_type: 'bensin' | 'diesel';
  fuel_type_label: string;
  daily_rate: number;
  daily_rate_formatted: string;
  status: 'available' | 'rented' | 'maintenance';
  status_label: string;
  color?: string;
  photo_url: string | null;
  video_url: string | null;
  safe_video_embed_url: string | null;
  description: string | null;
  is_featured?: boolean;
}

export type TransmissionFilter = 'all' | 'matic' | 'manual';
