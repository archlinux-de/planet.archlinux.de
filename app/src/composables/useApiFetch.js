import { createFetch } from '@vueuse/core'

export const useApiFetch = createFetch({
  options: {
    timeout: 5000
  },
  fetchOptions: {
    headers: { Accept: 'application/json' },
    credentials: 'omit'
  }
})
