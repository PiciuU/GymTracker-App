<template>
    <div class="auth-container">
        <h1 class="auth__title">Welcome back!</h1>

        <form class="auth__form" @submit.prevent="submitForm">
            <label for="username" class="auth__label">Username</label>
            <div class="auth__input-group" :class="{ 'auth__has-error': errors.login }">
                <input type="text" id="username" class="auth__input" v-model="credentials.login" />
                <div v-if="errors.login" class="auth__error-message">{{ errors.login }}</div>
            </div>

            <label for="password" class="auth__label">Password</label>
            <div class="auth__input-group" :class="{ 'auth__has-error': errors.password }">
                <input type="password" id="password" class="auth__input" v-model="credentials.password" />
                <div v-if="errors.password" class="auth__error-message">{{ errors.password }}</div>
            </div>

            <button class="auth__button" :disabled='authStore.isLoading'>Login</button>
            <div v-if="errors.critical" class="auth__critical-error-message">{{ errors.critical }}</div>
        </form>

        <div class="auth__options">
            <div class="auth__hint">Don't have an account? <router-link to="/register">Sign up here.</router-link></div>
            <div class="auth__hint">Forgot your password? <router-link to="/recover-password">Reset it here.</router-link></div>
        </div>
    </div>
</template>

<script setup>
    import { ref, reactive } from 'vue';
    import { useAuthStore } from '@/stores/AuthStore';

    const authStore = useAuthStore();

    const credentials = reactive({
        login: '',
        password: ''
    });

    const errors = ref('');

    const submitForm = () => {
        errors.value = {};

        if (!credentials.login) errors.value.login = 'Please enter your username.';
        if (!credentials.password) errors.value.password = 'Please enter your password.';

        if (Object.keys(errors.value).length > 0) return;

        authStore.login(credentials)
            .catch((error) => {
                if (error.data) {
                    const responseErrors = error.data;

                    Object.keys(responseErrors).forEach((field) => {
                        errors.value[field] = responseErrors[field][0];
                    });
                }
                else {
                    errors.value.critical = error.message
                }
            })
    };
</script>
