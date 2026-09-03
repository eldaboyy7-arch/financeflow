export type CategoryType = 'income' | 'expense'

export interface Category {
  id: number
  name: string
  type: CategoryType
  icon: string
  color: string
  is_default: boolean
  user_id: number | null
}

export interface CategoryPayload {
  name: string
  type: CategoryType
  icon?: string
  color?: string
}
