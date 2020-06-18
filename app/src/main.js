import Vue from 'vue'
import App from './App.vue'
import router from './router'
import VueMeta from 'vue-meta'
import { LayoutPlugin, VBVisiblePlugin } from 'bootstrap-vue'
import createApiService from './services/ApiService'

Vue.use(VueMeta)
Vue.use(LayoutPlugin)
Vue.use(VBVisiblePlugin)

Vue.config.productionTip = false
new Vue({
  router,
  render: h => h(App),
  provide: {
    apiService: createApiService(fetch)
  }
}).$mount('#app')
