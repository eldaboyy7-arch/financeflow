export interface ApiPagination {
  current_page: number
  last_page: number
  per_page: number
  total: number
  from: number
  to: number
}

export interface ApiCollectionResponse<T> {
  data: T[]
  meta: ApiPagination
  links: {
    first: string
    last: string
    prev: string | null
    next: string | null
  }
}

export interface ApiSingleResponse<T> {
  data: T
}

export interface ApiMessageResponse {
  message: string
}

export interface ApiErrorResponse {
  message: string
  errors?: Record<string, string[]>
}
