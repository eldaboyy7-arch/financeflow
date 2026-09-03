export interface RecurringItem {
  id: number
  name: string
  amount: number
  type: 'income' | 'expense'
  frequency: 'daily' | 'weekly' | 'monthly' | 'yearly'
  start_date: string
  next_due_date: string
  last_run_date?: string | null
  is_active: boolean
  auto_create: boolean
  notes?: string | null
  days_until_due: number
  is_due_today: boolean
  is_overdue: boolean
  account?: {
    id: number
    name: string
    icon: string
  } | null
  category?: {
    id: number
    name: string
    icon: string
    color: string
  } | null
}

export interface CreateRecurringPayload {
  name: string
  amount: number
  type: 'income' | 'expense'
  frequency: 'daily' | 'weekly' | 'monthly' | 'yearly'
  account_id: number
  category_id: number
  start_date: string
  auto_create?: boolean
  notes?: string
}
