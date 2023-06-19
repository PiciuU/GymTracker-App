<template>
    <div class="container">
        <h1>Welcome back!</h1>

        <form class="form" @submit.prevent="submitForm">
            <div class="form__group">
                <label for="login">Username</label>
                <div class="form__input-group-with-error" :class="{ 'form__input-group-has-error': errors.login }">
                    <input type="text" id="login" v-model="credentials.login" />
                    <p v-if="errors.login">{{ errors.login }}</p>
                </div>
            </div>

            <div class="form__group">
                <label for="password">Password</label>
                <div class="form__input-group-with-error" :class="{ 'form__input-group-has-error': errors.password }">
                    <input type="password" id="password" v-model="credentials.password" />
                    <p v-if="errors.password">{{ errors.password }}</p>
                </div>
            </div>

            <button :disabled='authStore.isLoading'>Login</button>
            <div v-if="errors.critical" class="form__error"><p>{{ errors.critical }}</p></div>
        </form>

        <div class="form__hint">
            <p>Don't have an account? <router-link to="/register">Sign up here.</router-link></p>
            <p>Forgot your password? <router-link to="/recover-password">Reset it here.</router-link></p>
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

    const errors = ref({});

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

<style lang="scss" scoped>
	@import '@/assets/styles/forms.scss';
</style>