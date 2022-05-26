import { createRouter, createWebHistory } from 'vue-router'
import Index from './views/Index'
import NotFound from './views/NotFound'

export default createRouter({
  history: createWebHistory(),
  linkActiveClass: 'active',
  routes: [
    { path: '/', name: 'index', component: Index },
    { path: '/:pathMatch(.*)*', component: NotFound }
  ],
  scrollBehavior (to, from, savedPosition) {
    return savedPosition ?? { x: 0, y: 0 }
  }
})
