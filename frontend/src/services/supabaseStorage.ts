export interface StorageUploadResult {
  path: string
  url: string
}

/**
 * Direct browser upload to Supabase Storage bucket 'fleet'.
 * Completely bypasses Railway backend, enforcing client-side validation
 * and tenant-aware path formatting: {userId}/{vehicleId}/{timestamp}.ext
 */
export async function uploadFleetPhoto(
  file: File,
  userId: number,
  vehicleId: number | string = 'new'
): Promise<StorageUploadResult> {
  // 1. Client-Side Validation
  const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/avif']
  if (!validTypes.includes(file.type)) {
    throw new Error('Format file tidak valid. Gunakan format JPG, PNG, WebP, atau AVIF.')
  }

  const maxSizeBytes = 3 * 1024 * 1024 // 3 MB
  if (file.size > maxSizeBytes) {
    throw new Error('Ukuran foto terlalu besar. Maksimal 3 MB.')
  }

  const supabaseUrl = (import.meta.env.VITE_SUPABASE_URL || '').replace(/\/$/, '')
  const anonKey = import.meta.env.VITE_SUPABASE_ANON_KEY || ''
  const bucket = import.meta.env.VITE_SUPABASE_BUCKET || 'fleet'

  if (!supabaseUrl || !anonKey) {
    throw new Error(
      'VITE_SUPABASE_URL atau VITE_SUPABASE_ANON_KEY belum diatur di file .env frontend.'
    )
  }

  // 2. Generate Tenant-Aware Path: {userId}/{vehicleId}/{timestamp}.ext
  const ext = file.name.split('.').pop()?.toLowerCase() || 'webp'
  const filename = `${Date.now()}_${Math.random().toString(36).substring(2, 7)}.${ext}`
  const objectPath = `${userId}/${vehicleId}/${filename}`

  const uploadEndpoint = `${supabaseUrl}/storage/v1/object/${bucket}/${objectPath}`

  const res = await fetch(uploadEndpoint, {
    method: 'POST',
    headers: {
      apikey: anonKey,
      Authorization: `Bearer ${anonKey}`,
      'Content-Type': file.type,
      'x-upsert': 'true',
    },
    body: file,
  })

  if (!res.ok) {
    const errorText = await res.text()
    throw new Error(`Upload gagal: ${errorText || res.statusText}`)
  }

  const publicUrl = `${supabaseUrl}/storage/v1/object/public/${bucket}/${objectPath}`

  return {
    path: objectPath,
    url: publicUrl,
  }
}
