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
        .catch(() => {
        })
        .finally(() => {
          this.loading = false
        })
    },
    visibilityChanged () {
      if (!this.loading) {
        if (this.data.count < this.data.total) {
          this.offset += this.limit
          this.fetchData()
        }
      }
    },
    observeItemsEnd () {
      new IntersectionObserver(entries => {
        if (entries[0].intersectionRatio <= 0) {
          return
        }
        this.visibilityChanged()
      }).observe(this.$el.querySelector('#items-end'))
    }
  },
  mounted () {
    this.fetchData()
    this.observeItemsEnd()
  }
}
</script>
