<template>
  <div>
    <div class="mb-5" :key="id" v-for="(item, id) in data.items">
      <div
        class="d-lg-flex justify-content-between align-items-baseline border border-top-0 border-left-0 border-right-0 mb-2">
        <h2 class="p-0 text-break">
          <a :href="item.link">{{ item.title }}</a>
        </h2>
        <div class="p-0">{{ new Date(item.lastModified).toLocaleDateString('de-DE') }}</div>
      </div>
      <div class="item-description text-break mw-100" v-html="item.description"></div>
      <div class="font-italic" v-if="item.author.name">
        <a v-if="item.author.uri" :href="item.author.uri" rel="nofollow">{{ item.author.name }}</a>
        <span v-else>{{ item.author.name }}</span>
      </div>
    </div>
    <div v-b-visible.300="visibilityChanged"></div>
  </div>
</template>

<style>
  .item-description img {
    max-width: 100%;
    display: block;
    margin: 0 auto;
    padding: 10px 0;
  }
</style>

<script>
export default {
  name: 'Items',
  inject: ['apiService'],
  props: {
    limit: {
      type: Number,
      required: false
    }
  },
  data () {
    return {
      loading: true,
      data: {
        count: this.limit,
        total: this.limit,
        limit: this.limit,
        offset: 0,
        items: []
      },
      offset: 0
    }
  },
  methods: {
    fetchData () {
      this.loading = true
      const offset = this.offset
      return this.apiService
        .fetchItems({
          limit: this.limit,
          offset: this.offset
        })
        .then(data => {
          if (offset === this.offset) {
            if (offset === 0) {
              this.data = data
            } else {
              this.data.count += data.count
              this.data.items.push(...data.items)
            }
          }
        })
        .catch(() => { })
        .finally(() => {
          this.loading = false
        })
    },
    visibilityChanged (isVisible) {
      if (!this.loading && isVisible) {
        if (this.data.count < this.data.total) {
          this.offset += this.limit
          this.fetchData()
        }
      }
    }
  },
  mounted () {
    this.fetchData()
  }
}
</script>
