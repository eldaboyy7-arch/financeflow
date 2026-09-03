import api from './axios'
import type { ReceiptScanResponse } from '@/types/receipt'

export const receiptApi = {
  scan: (formData: FormData) =>
    api.post<ReceiptScanResponse>('/transactions/scan-receipt', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }),
}
