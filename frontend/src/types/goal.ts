export interface GoalContribution {
  id: number
  amount: number
  type: 'deposit' | 'withdraw'
  date: string
  notes?: string | null
}

export interface Goal {
  id: number
  name: string
  target_amount: number
  current_amount: number
  remaining_amount: number
  percentage: number
  target_date?: string | null
  target_date_formatted?: string | null
  days_remaining?: number | null
  is_overdue: boolean
  icon: string
  color: string
  is_completed: boolean
  account?: {
    id: number
    name: string
    icon: string
  } | null
  recent_contributions?: GoalContribution[]
}

export interface CreateGoalPayload {
  name: string
  target_amount: number
  target_date?: string
  icon?: string
  color?: string
  account_id?: number
}

export interface ContributeGoalPayload {
  amount: number
  type: 'deposit' | 'withdraw'
  account_id?: number
  date?: string
  notes?: string
}
