import Vue from 'vue'
import Router from 'vue-router'

import Index from './views/Index'

Vue.use(Router)

export default new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  linkActiveClass: 'active',
  routes: [
    { path: '/', name: 'index', component: Index }
  ]
})
