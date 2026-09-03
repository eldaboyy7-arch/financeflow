export interface ReceiptItem {
  name: string
  price: number
  qty: number
}

export interface ReceiptDraft {
  merchant: string
  amount: number
  date: string
  type: 'expense' | 'income'
  category_id: number | null
  account_id: number | null
  description: string
  items: ReceiptItem[]
  confidence: 'high' | 'medium' | 'low'
  notes?: string | null
}

export interface ReceiptScanResponse {
  message: string
  receipt_path: string
  receipt_url: string
  draft: ReceiptDraft
}
