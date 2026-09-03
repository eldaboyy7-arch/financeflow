export interface SmartInsightItem {
  id: string
  type: 'primary' | 'warning' | 'danger' | 'success'
  icon: string
  title: string
  message: string
  category?: string
}

export interface SmartInsightsResponse {
  data: SmartInsightItem[]
  month: number
  year: number
}
