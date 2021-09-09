<template>
  <div>
    <template v-if="data.items.length === 0">
      <div class="mb-5 placeholder-glow" :key="'placeholder-' + id" v-for="id in 10" aria-hidden="true">
        <div
          class="d-lg-flex justify-content-between align-items-baseline border border-top-0 border-start-0 border-end-0 mb-2">
          <h2 class="p-0 text-break placeholder col-10 bg-primary"></h2>
          <div class="p-0 placeholder col-1"></div>
        </div>
        <div class="item-description text-break mw-100">
          <p :key="'placeholder--p-' + id" v-for="id in 4"><span class="placeholder col-12"></span>
            <span class="placeholder col-12"></span>
            <span class="placeholder col-12"></span>
            <span class="placeholder col-4"></span></p>
        </div>
        <div class="fst-italic">
          <span class="placeholder col-2"></span>
        </div>
      </div>
    </template>

    <div class="mb-5" :key="id" v-for="(item, id) in data.items">
      <div
        class="d-lg-flex justify-content-between align-items-baseline border border-top-0 border-start-0 border-end-0 mb-2">
        <h2 class="p-0 text-break">
          <a :href="item.link">{{ item.title }}</a>
        </h2>
        <div class="p-0">{{ new Date(item.lastModified).toLocaleDateString('de-DE') }}</div>
      </div>
      <div class="item-description text-break mw-100" v-html="item.description"></div>
      <div class="fst-italic" v-if="item.author.name">
        <a v-if="item.author.uri" :href="item.author.uri" rel="nofollow">{{ item.author.name }}</a>
        <span v-else>{{ item.author.name }}</span>
      </div>
    </div>

    <div id="items-end"></div>
  </div>
</template>

<style>
.item-description img {
  max-width: 100%;
  display: block;
  margin: 0 auto;
  padding: 10px 0;
}

#items-end {
  min-height: 1px;
}
</style>

<script>
import { inject, onBeforeUnmount, onMounted, reactive, ref } from 'vue'

export default {
  props: {
    limit: {
      type: Number,
      required: false
    }
  },
  setup (props) {
    const loading = ref(true)
    const offset = ref(0)
    const data = reactive({
      count: props.limit,
      total: props.limit,
      limit: props.limit,
      offset: 0,
      items: []
    })
    const apiService = inject('apiService')

    const fetchData = () => {
      loading.value = true
      const oldOffset = offset.value
      return apiService
        .fetchItems({
          limit: props.limit,
          offset: offset.value
        })
        .then(fetchedData => {
          if (oldOffset === offset.value) {
            if (oldOffset === 0) {
              data.items = fetchedData.items
              data.count = fetchedData.count
              data.total = fetchedData.total
              data.limit = fetchedData.limit
              data.offset = fetchedData.offset
            } else {
              data.count += fetchedData.count
              data.items.push(...fetchedData.items)
            }
          }
        })
        .catch(() => {
        })
        .finally(() => {
          loading.value = false
        })
    }

    const visibilityChanged = () => {
      if (!loading.value) {
        if (data.count < data.total) {
          offset.value += props.limit
          fetchData()
        }
      }
    }

    const observeItemsEnd = () => {
      const observer = new IntersectionObserver(entries => {
        if (entries[0].intersectionRatio <= 0) {
          return
        }
        visibilityChanged()
      }, { rootMargin: '0px 0px 640px 0px' })
      observer.observe(document.getElementById('items-end'))

      onBeforeUnmount(() => {
        observer.disconnect()
      })
    }

    onMounted(() => {
      fetchData()
      observeItemsEnd()
    })

    return {
      loading,
      data,
      offset
    }
  }
}
</script>
