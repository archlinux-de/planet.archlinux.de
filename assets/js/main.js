import Vue from 'vue'
import VueMeta from 'vue-meta'

import { LayoutPlugin, NavbarPlugin } from 'bootstrap-vue'

import VueObserveVisibility from 'vue-observe-visibility'
import App from './App'
import router from './router'
import createApiService from './services/ApiService'

Vue.config.productionTip = false
Vue.use(VueMeta)

Vue.use(LayoutPlugin)
Vue.use(NavbarPlugin)

Vue.use(VueObserveVisibility)

new Vue({
  router,
  render: h => h(App),
  provide: {
    apiService: createApiService(fetch)
  }
}).$mount('#app')
