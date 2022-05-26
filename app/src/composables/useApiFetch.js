import { createFetch } from '@vueuse/core'
import { computed, unref } from 'vue'

const useApiFetch = createFetch({
  options: {
    timeout: 5000
  },
  fetchOptions: {
    headers: { Accept: 'application/json' },
    credentials: 'omit'
  }
})

const useChunkedApiFetch = (path, offset, limit) => {
  const url = computed(() => `${unref(path)}?limit=${unref(limit)}&offset=${unref(offset)}`)

  const result = useApiFetch(
    url,
    {
      initialData: { items: [] },
      refetch: true,
      afterFetch: (ctx) => {
        ctx.data.items = [...result.data.value.items, ...ctx.data.items]
        ctx.data.count = ctx.data.items.length
        ctx.data.offset = 0
        ctx.data.limit = ctx.data.count

        return ctx
      }
    }
  ).get().json()

  return result
}

export const useFeedsFetch = (limit) => useChunkedApiFetch('/api/feeds', 0, limit)

export const useItemsFetch = (offset, limit) => useChunkedApiFetch('/api/items', offset, limit)
