<template>
  <ul class="ps-4">
    <li :key="id" v-for="(feed, id) in feeds"><a :href="feed.link" :title="feed.description">{{ feed.title }}</a></li>
  </ul>
</template>

<script>
export default {
  name: 'Feeds',
  inject: ['apiService'],
  props: {
    limit: {
      type: Number,
      required: false
    }
  },
  data () {
    return {
      feeds: [],
      offset: 0
    }
  },
  methods: {
    fetchData () {
      return this.apiService
        .fetchFeeds({
          limit: this.limit,
          offset: this.offset
        })
        .then(data => { this.feeds = data.items })
        .catch(() => { })
    }
  },
  mounted () {
    this.fetchData()
  }
}
</script>
