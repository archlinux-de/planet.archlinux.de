<template>
  <div>
    <template v-if="isFetching">
      <ul class="ps-4 placeholder-glow" aria-hidden="true">
        <li :key="'placeholder-'+id" v-for="id in 8" class="placeholder col-8 bg-primary"></li>
      </ul>
    </template>

    <ul class="ps-4" v-if="isFinished">
      <li :key="id" v-for="(feed, id) in data.items"><a :href="feed.link" :title="feed.description">{{ feed.title }}</a></li>
    </ul>

    <div class="alert alert-danger" v-show="error">{{ error }}</div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'
import { useApiFetch } from '../composables/useApiFetch'

const props = defineProps({
  limit: {
    type: Number,
    required: true
  }
})

const { isFinished, isFetching, data, error } = useApiFetch(
  `/api/feeds?limit=${props.limit}&offset=0`,
  {
    initialData: { items: [] },
    afterFetch: ctx => {
      ctx.data = { items: ctx.data.items }; return ctx
    }
  }
).get().json()
</script>
