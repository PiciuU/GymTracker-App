<template>
    <div class="modal-overlay" @click="$emit('close')"></div>
    <div class="modal" ref="modal">
        <div class="modal__close" @click="$emit('close')">
            <font-awesome-icon icon="fa-solid fa-xmark" size="lg" />
        </div>
        <div class="modal__header">
            <div class="modal__image">
                <div class="modal__title">
                    {{ exercise.name }}
                </div>
                <div class="modal__subtitle">
                    {{ exercise.sets ?? 0 }} series, {{ exercise.reps ?? 0 }} reps
                </div>
            </div>
        </div>
        <div class="modal__content" v-if="!editMode">
            <div class="modal__group">
                <div class="modal__label">
                    Description:
                </div>
                <div class="modal__text  modal__text-area">
                    {{ exercise.description }}
                </div>
            </div>
            <div class="modal__group">
                <div class="modal__label">
                    Muscles Targeted:
                </div>
                <div class="modal__text">
                    {{ exercise.muscleGroup }}
                </div>
            </div>
            <div class="modal__group" v-if="exercise.attachmentUrl">
                <div class="modal__label">
                    Demonstration:
                </div>
                <video class="modal__video" controls>
                    <source src="@/assets/videos/exercises/biceps/uginanie.mp4" type="video/mp4" loading="lazy"/>
                    Your browser does not support mp4 videos.
                </video>
            </div>
            <div class="modal__group">
                <div class="modal__label">
                    Rest time:
                </div>
                <div class="modal__text">
                    {{ exercise.restTime ?? 0 }} min
                </div>
            </div>
            <div class="modal__group">
                <div class="modal__label">
                    Personal Record:
                </div>
                <div class="modal__text">
                    {{ exercise.weight ?? 0 }} kg
                </div>
                <div class="modal__hint">
                    <router-link to="/">View personal history</router-link>
                </div>
            </div>
        </div>
        <div class="modal__content" v-else>
            <form class="modal__form" id="modalForm" @submit.prevent="submitForm">
                <div class="modal__group">
                    <div class="modal__label">
                        Enter number of sets and reps:
                    </div>
                    <div class="modal__input-group">
                        <input type="number" class="modal__input" v-model.number="formData.sets" placeholder="Sets" required>
                        <input type="number" class="modal__input" v-model.number="formData.reps" placeholder="Reps" required>
                    </div>
                </div>
                <div class="modal__group">
                    <div class="modal__label">
                        Enter your current weight record:
                    </div>
                    <div class="modal__input-group">
                        <input type="number" class="modal__input" v-model.number="formData.weight" placeholder="Weight (kg)">
                    </div>
                </div>
                <div class="modal__group">
                    <div class="modal__label">
                        Enter rest time between sets:
                    </div>
                    <div class="modal__input-group">
                        <input type="text" class="modal__input" v-model="formData.restTime" pattern="[0-5][0-9]:[0-5][0-9]" placeholder="Rest Time (mm:ss)">
                    </div>
                </div>
            </form>
        </div>
        <div class="modal__footer">
            <button class="modal__button" @click="toggleEditMode(true)" v-if="!editMode">
                Edit details
            </button>
            <div class="modal__button-group" v-else>
                <button class="modal__button modal__button--secondary" @click="toggleEditMode(false)" :disabled="dataStore.isLoading">
                    Discard changes
                </button>
                <button class="modal__button" form="modalForm" :disabled="dataStore.isLoading" >
                    Save changes
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, reactive, onMounted, onUnmounted } from 'vue';
    import { useDataStore } from '@/stores/DataStore';

    const dataStore = useDataStore();

    let editMode = ref(false);

    onUnmounted(() => document.body.classList.remove('disableScroll'));

    const emit = defineEmits(['close'])

    const props = defineProps({
        workout: { type: Object, required: true, default: '{}' },
        exercise: { type: Object, required: true, default: '{}' }
    });

    let formData = reactive({
        sets: props.exercise.sets,
        reps: props.exercise.reps,
        weight: props.exercise.weights,
        restTime: props.exercise.restTime
    });

    let modal = ref(null);

    function toggleEditMode(status) {
        editMode.value = status;
        if (status === true) {
            formData = {
                sets: props.exercise.sets,
                reps: props.exercise.reps,
                weight: props.exercise.weight,
                restTime: props.exercise.restTime
            };
        }
    }

    onMounted(() => {
        document.body.classList.add('disableScroll');
        let elementRect = modal.value.getBoundingClientRect();
        let newHeight = Math.ceil(elementRect.height) % 2 === 0 ? Math.ceil(elementRect.height) : Math.ceil(elementRect.height) + 1;
        modal.value.style.height = newHeight.toString() + "px";
        modal.value.style.maxHeight = newHeight.toString() + "px";
    });

    const submitForm = () => {
        dataStore.updateWorkoutExercise(props.workout.id, props.exercise.id, formData)
            .then(() => {
                toggleEditMode(false);
            })
            .catch((e) => {
                console.log(e);
            })
    };
</script>

<style lang="scss" scoped>
    @import '@/assets/styles/modal.scss';

    .modal__title {
        font-size: 2.5rem;
        font-weight: bold;
        letter-spacing: 0.2rem;
    }

    .modal__subtitle {
        color: var(--color-text-muted-1);
    }
</style>