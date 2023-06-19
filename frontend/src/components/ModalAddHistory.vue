<template>
    <div class="modal-overlay" @click="$emit('close')"></div>
    <div class="modal">
        <div class="modal__header">
            <div class="header__close" @click="$emit('close')">
                <font-awesome-icon icon="xmark"/>
            </div>
            <div class="header__title">
                {{ mode == 'add' ? "Add new Entry" : "Edit entry" }}
            </div>
            <div class="header__subtitle">
                Fill in the details below to store a entry for exercise history.
            </div>
        </div>
        <div class="modal__content">
            <form class="form form--alternative" id="modalForm" @submit.prevent="submitForm">
                <div class="form__group">
                    <label for="weight">Enter weight: </label>
                    <div class="form__input-group">
                        <input type="text" id="weight" v-model="formData.weight" placeholder="Weight (kg)" required/>
                    </div>
                </div>

                <div class="form__group">
                    <label for="date">Enter date: </label>
                    <div class="form__input-group">
                        <input type="date" id="date" v-model="formData.date" placeholder="Date" required/>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal__footer">
            <div v-if="error" class="footer__error">{{ error }}</div>
            <button class="footer__button" form="modalForm" :disabled='dataStore.isLoading'>
                {{ mode == 'add' ? "Add Entry" : "Edit entry" }}
            </button>
        </div>
    </div>
</template>

<script setup>
    import { ref, reactive, onMounted, onUnmounted } from 'vue';
    import { useDataStore } from '@/stores/DataStore';

    onMounted(() => document.body.classList.add('disableScroll'));

    onUnmounted(() => document.body.classList.remove('disableScroll'));

    const dataStore = useDataStore();

    const emit = defineEmits(['close', 'add', 'update']);

    const props = defineProps({
        mode: { type: String, required: true, default: 'add' },
        exercise: { type: Object, required: true, default: {} },
        historyEntry: { type: Object, required: false, default: {} },
    });

    const formData = reactive({
        exerciseId: props.exercise.id,
        weight: props.historyEntry?.weight ?? null,
        date: props.historyEntry?.date ?? null,
    });

    const error = ref('');

    const submitForm = () => {
        if (props.mode === 'add') {
            dataStore.addExerciseHistory(formData)
                .then((entry) => {
                    emit('close');
                    emit('add', entry);
                })
                .catch((e) => {
                    error.value = e.message
                })
        }
        else {
            dataStore.updateExerciseHistory(formData)
                .then((entry) => {
                    emit('close');
                    emit('update', entry);
                })
                .catch((e) => {
                    error.value = e.message
                })
        }
    };
</script>

<style lang="scss" scoped>
    @import '@/assets/styles/forms.scss';
    @import '@/assets/styles/modals.scss';
</style>