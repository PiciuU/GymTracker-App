import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/AuthStore';
import { useDataStore } from '@/stores/DataStore';

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
                    component: () => import(/* webpackChunkName: "group-authorized" */ '@/views/Home.vue'),
                    meta: { breadcrumb: '' }
                },
                {
                    name: 'Settings',
                    path: '/settings',
                    component: () => import(/* webpackChunkName: "group-authorized" */ '@/views/Settings.vue'),
                    meta: { breadcrumb: 'Home/Settings' }
                },
                {
                    name: 'Workout',
                    path: '/workout',
                    component: () => import(/* webpackChunkName: "group-authorized" */ '@/views/WorkoutList.vue'),
                    meta: { breadcrumb: 'Home/Workout' }
                },
                {
                    name: 'WorkoutItem',
                    path: 'workout/:day',
                    component: () => import(/* webpackChunkName: "group-authorized" */ '@/views/WorkoutItem.vue'),
                    meta: { breadcrumb: 'Home/Workout/:day' },
                    beforeEnter: (to, from, next) => {
                        const dataStore = useDataStore();
                        const workout = dataStore.workouts.find(w => w.dayOfWeek.toLowerCase() === to.params.day)
                        if (workout) {
                            to.meta.breadcrumb = to.meta.breadcrumb.replace(':day', workout.dayOfWeek);
                            to.meta.workoutId = workout.id;
                            next();
                        }
                        else next('/workout');
                    },
                },
                {
                    name: 'Exercises',
                    path: 'exercises',
                    component: () => import(/* webpackChunkName: "group-authorized" */ '@/views/Exercises.vue'),
                    meta: { breadcrumb: 'Home/Exercises' }
                },
                {
                    name: 'Progress',
                    path: 'progress',
                    component: () => import(/* webpackChunkName: "group-authorized" */ '@/views/Progress.vue'),
                    meta: { breadcrumb: 'Home/Progress' }
                },
                {
                    name: 'ProgressHistory',
                    path: 'progress/history',
                    component: () => import(/* webpackChunkName: "group-authorized" */ '@/views/ProgressHistory.vue'),
                    meta: { breadcrumb: 'Home/Progress/History' }
                },
                {
                    name: 'ProgressHistory',
                    path: 'progress/history',
                    component: () => import(/* webpackChunkName: "group-authorized" */ '@/views/progress/History.vue'),
                    meta: { breadcrumb: 'Home/Progress/History' }
                },
                {
                    name: 'ProgressCalculator',
                    path: 'progress/calculator/:name',
                    component: () => import(/* webpackChunkName: "group-authorized" */ '@/views/progress/Calculator.vue'),
                    meta: { breadcrumb: 'Home/Progress/Calculator' },
                    beforeEnter: (to, from, next) => {
                        to.meta.calculatorType = to.params.name;
                        next();
                    },
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
