export type AccountType = 'cash' | 'bank' | 'e_wallet' | 'credit_card' | 'other'

export interface Account {
  id: number
  name: string
  type: AccountType
  type_label: string
  icon: string
  color: string
  opening_balance: number
  current_balance: number
  is_active: boolean
  created_at: string
}

export interface AccountPayload {
  name: string
  type: AccountType
  icon?: string
  color?: string
  opening_balance?: number
  is_active?: boolean
}
