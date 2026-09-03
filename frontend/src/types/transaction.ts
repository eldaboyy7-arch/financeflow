import type { Account } from './account'
import type { Category } from './category'

export type TransactionType = 'income' | 'expense'

export interface Transaction {
  id: number
  type: TransactionType
  amount: number
  date: string
  description: string | null
  transfer_id: number | null
  account: Account
  category: Category
  created_at: string
}

export interface TransactionPayload {
  type: TransactionType
  amount: number
  date: string
  category_id: number
  account_id: number
  description?: string
}

export interface TransferPayload {
  from_account_id: number
  to_account_id: number
  amount: number
  fee?: number
  date: string
  description?: string
}

export interface Transfer {
  id: number
  amount: number
  fee: number
  date: string
  description: string | null
  from_account: Account
  to_account: Account
  created_at: string
}

export interface TransactionFilters {
  type?: TransactionType | ''
  account_id?: number | ''
  category_id?: number | ''
  date_from?: string
  date_to?: string
  search?: string
  sort?: 'date' | 'amount'
  order?: 'asc' | 'desc'
  per_page?: number
  page?: number
}
