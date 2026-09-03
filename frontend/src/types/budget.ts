export type BudgetStatus = 'normal' | 'warning' | 'exceeded'

export interface BudgetCategory {
  id: number
  name: string
  icon: string
  color: string
}

export interface Budget {
  id: number
  category_id: number
  category: BudgetCategory
  amount: number
  spent: number
  remaining: number
  overspent: number
  percentage: number
  status: BudgetStatus
  month: number
  year: number
  created_at: string
}

export interface BudgetSummary {
  month: number
  year: number
  total_budgeted: number
  total_spent: number
  total_remaining: number
  overall_percentage: number
  warning_count: number
  exceeded_count: number
  budget_count: number
}

export interface BudgetPayload {
  category_id: number
  amount: number
  month: number
  year: number
}

export interface BudgetImpact {
  category_name: string
  budget_amount: number
  current_spent: number
  projected_spent: number
  projected_percentage: number
  status: BudgetStatus
  message: string
}
