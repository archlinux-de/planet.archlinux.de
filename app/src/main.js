import Vue from 'vue'
import App from './App.vue'
import router from './router'
import VueMeta from 'vue-meta'
import { LayoutPlugin } from 'bootstrap-vue'
import VueObserveVisibility from 'vue-observe-visibility'
import createApiService from './services/ApiService'

Vue.use(VueMeta)
Vue.use(LayoutPlugin)
Vue.use(VueObserveVisibility)

Vue.config.productionTip = false
new Vue({
  router,
  render: h => h(App),
  provide: {
    apiService: createApiService(fetch)
  }
}).$mount('#app')
