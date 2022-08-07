<template>
  <div>
    <template v-if="isFetching">
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

    <div v-if="isFinished">
      <div class="mb-5" :key="id" v-for="(item, id) in data.items" :data-test="`item item-${id}`">
        <div
          class="d-lg-flex justify-content-between align-items-baseline border border-top-0 border-start-0 border-end-0 mb-2">
          <h2 class="p-0 text-break">
            <a :href="item.link">{{ item.title }}</a>
          </h2>
          <div class="p-0">{{ new Date(item.lastModified).toLocaleDateString('de-DE') }}</div>
        </div>
        <div class="item-description text-break mw-100" v-html="item.description" data-test="item-description"></div>
        <div class="fst-italic" v-if="item.author.name">
          <a v-if="item.author.uri" :href="item.author.uri" rel="nofollow">{{ item.author.name }}</a>
          <span v-else>{{ item.author.name }}</span>
        </div>
      </div>
      <div ref="end"></div>
    </div>

    <div class="alert alert-danger" v-if="error">{{ error }}</div>
  </div>
</template>

<style>
.item-description img {
  max-width: 100%;
  display: block;
  margin: 0 auto;
  padding: 10px 0;
}

[ref="end"] {
  min-height: 1px;
}
</style>

<script setup>
import { defineProps, onBeforeUnmount, ref } from 'vue'
import { useIntersectionObserver } from '@vueuse/core'
import { useItemsFetch } from '../composables/useApiFetch'

const props = defineProps({
  limit: {
    type: Number,
    required: true
  }
})

const offset = ref(0)

const { isFinished, isFetching, data, error } = useItemsFetch(offset, props.limit)

const end = ref(null)

const { stop } = useIntersectionObserver(
  end,
  ([entry]) => {
    if (entry.intersectionRatio <= 0) {
      return
    }
    if (error.value || data.value.count >= data.value.total) {
      stop()
      return
    }
    offset.value += props.limit
  },
  { rootMargin: '0px 0px 640px 0px' }
)

onBeforeUnmount(() => {
  stop()
})
</script>
