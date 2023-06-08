import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/AuthStore';

export const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            meta: { requiresAuth: true },
            component: () => import(/* webpackChunkName: "group-authorized" */ '@/layouts/Authorized.vue'),
            children: [
                {
                    name: 'Home',
                    path: '',
                    component: () => import(/* webpackChunkName: "group-authorized" */ '@/views/HomeView.vue'),
                    meta: { breadcrumb: '' }
                },
                {
                    path: '/workout',
                    name: 'Workout',
                    component: () => import(/* webpackChunkName: "group-authorized" */ '@/views/WorkoutView.vue'),
                    meta: { breadcrumb: 'Home/Workout' }
                },
                {
                    path: 'workout/monday',
                    name: 'WorkoutDayMonday',
                    component: () => import(/* webpackChunkName: "group-authorized" */ '@/views/WorkoutDayView.vue'),
                    meta: { breadcrumb: 'Home/Workout/Monday' }
                },
                {
                    name: 'Exercises',
                    path: 'exercises',
                    component: () => import(/* webpackChunkName: "group-authorized" */ '@/views/WorkoutView.vue'),
                    meta: { breadcrumb: 'Home/Exercises' }
                },
                {
                    name: 'Progress',
                    path: 'progress',
                    component: () => import(/* webpackChunkName: "group-authorized" */ '@/views/WorkoutView.vue'),
                    meta: { breadcrumb: 'Home/Progress' }
                },
            ],
        },
        {
            path: '/',
            meta: { requiresAuth: false },
            component: () => import(/* webpackChunkName: "group-default" */ '@/layouts/Default.vue'),
            children: [
                {
                    name: 'Login',
                    path: 'login',
                    component: () => import(/* webpackChunkName: "group-default" */ '@/views/auth/Login.vue')
                },
                {
                    name: 'Register',
                    path: 'register',
                    component: () => import(/* webpackChunkName: "group-default" */ '@/views/auth/Register.vue')
                },
                {
                    name: 'RecoverPassword',
                    path: 'recover-password',
                    component: () => import(/* webpackChunkName: "group-default" */ '@/views/auth/RecoverPassword.vue')
                },
                {
                    name: 'ResetPassword',
                    path: 'recover-password/:hash',
                    component: () => import(/* webpackChunkName: "group-default" */ '@/views/auth/ResetPassword.vue'),
                    beforeEnter: (to, from, next) => {
                        const authStore = useAuthStore();
                        authStore.validatePasswordResetHash(to.params.hash)
                            .then(() => next())
                            .catch(() => next('/'))
                    },
                },
            ]
        },
    ]
})

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();

    if (to.meta.requiresAuth && !authStore.isLogged) next('/login'); // User not logged in, redirect to the login page
    else if (!to.meta.requiresAuth && authStore.isLogged) next('/'); // User logged in, redirect to the Home view
    else next(); // Redirect to the intended view
});

export default router
