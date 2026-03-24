import { createRouter, createWebHistory } from 'vue-router'
import { bootstrapAuth, useAuth } from '../services/auth'

import Login from '../pages/Login.vue'
import Home from '../pages/Home.vue'
import Register from '../pages/Register.vue'
import Groups from '../pages/Groups.vue'
import Schedule from '../pages/Schedule.vue'
import Payments from '../pages/Payments.vue'
import Attendance from '../pages/Attendance.vue'

const routes = [
  {
    path: '/',
    redirect: '/login'
  },
  {
    path: '/login',
    component: Login,
    meta: { guestOnly: true }
  },
  {
    path: '/home',
    component: Home,
    meta: { requiresAuth: true }
  },
  {
    path: '/register',
    component: Register,
    meta: { guestOnly: true }
  },
  {
    path: '/groups',
    component: Groups,
    meta: { requiresAuth: true }
  },
  {
    path: '/schedule',
    component: Schedule,
    meta: { requiresAuth: true }
  },
  {
    path: '/payments',
    component: Payments,
    meta: { requiresAuth: true }
  },
  {
    path: '/attendance',
    component: Attendance,
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to) => {
  const { isAuthenticated } = useAuth()

  if (!isAuthenticated.value) {
    if (to.meta?.requiresAuth) {
      return '/login'
    }

    return true
  }

  await bootstrapAuth()

  const { user } = useAuth()

  if (!user.value && to.meta?.requiresAuth) {
    return '/login'
  }

  if (
    user.value?.role === 'admin'
    && to.meta?.requiresAuth
    && to.path !== '/home'
  ) {
    return '/home'
  }

  if (to.meta?.guestOnly && user.value) {
    return '/home'
  }

  return true
})

export default router
