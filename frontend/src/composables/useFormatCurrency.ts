import dayjs from 'dayjs'
import 'dayjs/locale/id'

dayjs.locale('id')

/**
 * Format number as Indonesian Rupiah.
 * e.g. 1500000 => "Rp1.500.000"
 */
export function useFormatCurrency() {
  function formatCurrency(amount: number, currency = 'IDR'): string {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency,
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    }).format(amount)
  }

  function formatAmount(amount: number, type?: 'income' | 'expense'): string {
    const formatted = formatCurrency(Math.abs(amount))
    if (type === 'income') return `+${formatted}`
    if (type === 'expense') return `-${formatted}`
    return formatted
  }

  function formatDate(date: string, format = 'D MMM YYYY'): string {
    return dayjs(date).format(format)
  }

  function formatPercent(value: number): string {
    const sign = value > 0 ? '+' : ''
    return `${sign}${value.toFixed(1)}%`
  }

  return { formatCurrency, formatAmount, formatDate, formatPercent }
}
