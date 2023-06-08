<template>
    <div class="auth-container">
        <h1 class="auth__title">Recover your Account</h1>

        <form class="auth__form" @submit.prevent="submitForm" v-if="isRecovered.status == false">
                <label for="password" class="auth__label">New Password</label>
                <div class="auth__input-group" :class="{ 'auth__has-error': errors.password }">
                    <input type="text" id="password" class="auth__input" v-model="credentials.password" />
                    <div v-if="errors.password" class="auth__error-message">{{ errors.password }}</div>
                </div>

                <label for="passwordConfirmation" class="auth__label">Confirm Password</label>
                <div class="auth__input-group" :class="{ 'auth__has-error': errors.passwordConfirmation }">
                    <input type="text" id="passwordConfirmation" class="auth__input" v-model="credentials.passwordConfirmation" />
                    <div v-if="errors.passwordConfirmation" class="auth__error-message">{{ errors.passwordConfirmation }}</div>
                </div>

                <button class="auth__button" :disabled='authStore.isLoading'>Reset Password</button>
                <div v-if="errors.critical" class="auth__critical-error-message">{{ errors.critical }}</div>
        </form>
        <div class="auth__message-box" v-else>
            <div class="auth__message">{{ isRecovered.message }}</div>
            <router-link to="/login">
                <button class="auth__success">Back to login</button>
            </router-link>
        </div>

        <div class="auth__options">
            <div class="auth__hint">Remembered your password? <router-link to="/login">Sign in here.</router-link></div>
        </div>
    </div>
</template>

<script setup>
    import { ref, reactive } from 'vue';
    import { useRoute } from 'vue-router';
    import { useAuthStore } from '@/stores/AuthStore';

    const route = useRoute();
    const authStore = useAuthStore();

    const credentials = reactive({
        hash: route.params.hash,
        password: '',
        passwordConfirmation: '',
    });

    const errors = ref('');

    let isRecovered = reactive({
        status: false,
        message: ''
    });

    const submitForm = () => {
        errors.value = {};

        if (!credentials.password) errors.value.password = 'Please enter your password.';
        if (!credentials.passwordConfirmation) errors.value.passwordConfirmation = 'Please confirm your password.';
        if (credentials.password !== credentials.passwordConfirmation) errors.value.passwordConfirmation = 'Passwords do not match.';

        if (Object.keys(errors.value).length > 0) return;

        authStore.passwordReset(credentials)
            .then((response) => {
                isRecovered.status = true;
                isRecovered.message = response.message;
            })
            .catch((error) => {
                if (error.data) {
                    const responseErrors = error.data;

                    Object.keys(responseErrors).forEach((field) => {
                        errors.value[field] = responseErrors[field][0];
                    });
                }
                else {
                    errors.value.critical = error.message;
                }
            })
    };

</script>
