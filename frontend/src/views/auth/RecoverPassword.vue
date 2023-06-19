<template>
    <div class="container">
        <h1>Recover your Account</h1>

        <form class="form" @submit.prevent="submitForm" v-if="isRecovered.status === false">
            <div class="form__group">
                <label for="email">Email</label>
                <div class="form__input-group-with-error" :class="{ 'form__input-group-has-error': errors.email }">
                    <input type="email" id="email" v-model="credentials.email" />
                    <p v-if="errors.email">{{ errors.email }}</p>
                </div>
            </div>

            <button :disabled='authStore.isLoading'>Recover Password</button>
            <div v-if="errors.critical" class="form__error"><p>{{ errors.critical }}</p></div>
        </form>
        <div class="form__success" v-else>
            <p>{{ isRecovered.message }}</p>
            <router-link to="/login">
                <button>Back to login</button>
            </router-link>
        </div>

        <div class="form__hint">
            <p>Remembered your password? <router-link to="/login">Sign in here.</router-link></p>
        </div>
    </div>
</template>

<script setup>
    import { ref, reactive } from 'vue';
    import { useAuthStore } from '@/stores/AuthStore';

    const authStore = useAuthStore();

    const credentials = reactive({
        email: ''
    });

    const errors = ref({});

    const isRecovered = reactive({
        status: false,
        message: ''
    });

    const submitForm = () => {
        errors.value = {};

        if (!credentials.email) errors.value.email = 'Please enter your email.';

        if (Object.keys(errors.value).length > 0) return;

        authStore.passwordRecover(credentials)
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
                    errors.value.critical = error.message
                }
            })
    };
</script>

<style lang="scss" scoped>
	@import '@/assets/styles/forms.scss';
</style>