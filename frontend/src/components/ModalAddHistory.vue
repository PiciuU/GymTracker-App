<template>
    <div class="modal-overlay" @click="$emit('close')"></div>
    <div class="modal">
        <div class="modal__close" @click="$emit('close')">
            <font-awesome-icon icon="fa-solid fa-xmark" size="lg" />
        </div>
        <div class="modal__header">
            <div class="modal__title">
                {{ mode == 'add' ? "Add new Entry" : "Edit entry" }}
            </div>
            <div class="modal__subtitle">
                Fill in the details below to store a entry for exercise history.
            </div>
        </div>
        <div class="modal__content">
            <form class="modal__form" id="modalForm" @submit.prevent="submitForm">
                <div class="modal__group">
                    <div class="modal__label">
                        Enter weight:
                    </div>
                    <div class="modal__input-group">
                        <input type="number" class="modal__input" v-model="formData.weight" placeholder="Weight (kg)" required/>
                    </div>
                </div>
                <div class="modal__group">
                    <div class="modal__label">
                        Enter date:
                    </div>
                    <div class="modal__input-group">
                        <input type="date" class="modal__input" v-model="formData.date" placeholder="Date" required/>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal__footer">
            <div v-if="error" class="modal__error">{{ error }}</div>
            <button class="modal__button" form="modalForm" :disabled='dataStore.isLoading'>
                {{ mode == 'add' ? "Add Entry" : "Edit entry" }}
            </button>
        </div>
    </div>
</template>

<script setup>
    import { ref, reactive, onMounted, onUnmounted } from 'vue';
    import { useDataStore } from '@/stores/DataStore';

    onMounted(() => {
        document.body.classList.add('disableScroll');
        if (props.historyEntry) {
            formData.weight = props.historyEntry.weight;
            formData.date = props.historyEntry.date;
        }
    });

    onUnmounted(() => document.body.classList.remove('disableScroll'));

    const dataStore = useDataStore();

    const emit = defineEmits(['close', 'add', 'update']);

    const props = defineProps({
        mode: { type: String, required: true, default: 'add' },
        exercise: { type: Object, required: true, default: '{}' },
        historyEntry: { type: Object, required: false, default: null },
    });

    const formData = reactive({
        exerciseId: props.exercise.id,
        weight: null,
        date: null,
    });

    const error = ref('');

    const submitForm = () => {
        if (props.mode === 'add') {
            dataStore.addExerciseHistory(formData)
                .then((entry) => {
                    console.log('THEN: ', entry);
                    emit('close');
                    emit('add', entry);
                })
                .catch((e) => {
                    console.log(e);
                    error.value = e.message
                })
        }
        else {
            dataStore.updateExerciseHistory(formData)
                .then((entry) => {
                    console.log('THEN: ', entry);
                    emit('close');
                    emit('update', entry);
                })
                .catch((e) => {
                    console.log(e);
                    error.value = e.message
                })
        }
    };
</script>

<style lang="scss" scoped>
    @import '@/assets/styles/modal.scss';
</style>