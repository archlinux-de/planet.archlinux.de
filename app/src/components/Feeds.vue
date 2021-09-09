<template>
  <div>
    <template v-if="feeds.length === 0">
      <ul class="ps-4 placeholder-glow" aria-hidden="true">
        <li :key="'placeholder-'+id" v-for="id in 9" class="placeholder col-8 bg-primary"></li>
      </ul>
    </template>

    <ul class="ps-4">
      <li :key="id" v-for="(feed, id) in feeds"><a :href="feed.link" :title="feed.description">{{ feed.title }}</a></li>
    </ul>
  </div>
</template>

<script setup>
import { inject, onMounted, ref, defineProps } from 'vue'

const props = defineProps({
  limit: {
    type: Number,
    required: false
  }
})
const offset = ref(0)
const feeds = ref([])

const apiService = inject('apiService')

const fetchData = () => {
  return apiService
    .fetchFeeds({
      limit: props.limit,
      offset: offset.value
    })
    .then(data => {
      feeds.value = data.items
    })
    .catch(() => {
    })
}

onMounted(() => { fetchData() })
</script>
