<template>
    <div class="modal-overlay" @click="$emit('close')"></div>
    <div class="modal">
        <div class="modal__close" @click="$emit('close')">
            <font-awesome-icon icon="fa-solid fa-xmark" size="lg" />
        </div>
        <div class="modal__header">
            <div class="modal__title">
                Create new workout
            </div>
            <div class="modal__subtitle">
                Choose a day of the week for your workout plan.
            </div>
        </div>
        <div class="modal__content">
            <form class="modal__form" id="modalForm" @submit.prevent="submitForm">
                <div class="modal__group">
                    <div class="modal__label">
                        Select a day of the week:
                    </div>
                    <select class="modal__select" v-model="selectedDayOfWeek" required>
                        <option value="" selected>Select day of the week...</option>
                        <option v-for="(day, index) in dataStore.availableDaysOfWeek" :key="index" :value="day.id">
                            {{ day.text }}
                        </option>
                    </select>
                </div>
            </form>
        </div>
        <div class="modal__footer">
            <div v-if="error" class="modal__error">{{ error }}</div>
            <button class="modal__button" form="modalForm" :disabled='dataStore.isLoading'>
                Create workout
            </button>
        </div>
    </div>
</template>

<script setup>
    import { ref, onMounted, onUnmounted } from 'vue';
    import { useDataStore } from '@/stores/DataStore';

    onMounted(() => document.body.classList.add('disableScroll'));

    onUnmounted(() => document.body.classList.remove('disableScroll'));

    const dataStore = useDataStore();

    const emit = defineEmits(['close'])

    const error = ref('');

    const selectedDayOfWeek = ref('');

    const submitForm = () => {
        error.value = null;

        if (!selectedDayOfWeek.value) {
            error.value = 'Please choose a day of the week.';
            return;
        }

        dataStore.createWorkout(selectedDayOfWeek.value)
            .then(() => {
                selectedDayOfWeek.value = '';
                emit('close');
            })
            .catch((e) => {
                error.value = e.message
            })
    };
</script>

<style lang="scss" scoped>
    @import '@/assets/styles/modal.scss';
</style>