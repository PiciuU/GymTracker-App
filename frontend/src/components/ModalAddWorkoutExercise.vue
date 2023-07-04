<template>
    <div class="modal-overlay" @click="$emit('close')"></div>
    <div class="modal">
        <div class="modal__header">
            <div class="header__close" @click="$emit('close')">
                <font-awesome-icon icon="fa-solid fa-xmark" size="lg" />
            </div>
            <div class="header__title">
                Add exercise to workout
            </div>
            <div class="header__subtitle">
                Fill in the details below to add a new exercise to your workout plan.
            </div>
        </div>
        <div class="modal__content">
            <form class="form form--alternative" id="modalForm" @submit.prevent="submitForm">
                <div class="form__group">
                    <label for="exercise">Choose exercise from list: </label>
                    <div class="form__input-group">
                        <select id="exercise" v-model="formData.exerciseId" required>
                            <option value="" selected>Select desired exercise...</option>
                            <optgroup v-for="(group, index) in groupedExercises" :key="index" :label="group.muscleGroup">
                                <option v-for="exercise in group.exercises" :key="exercise.id" :value="exercise.id">{{ exercise.name }}</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="form__hint">
                        <p>Cant find your exercise? <router-link to="/exercises">Add it by yourself.</router-link></p>
                    </div>
                </div>
                <div v-if="formData.exerciseId">
                    <div class="form__group">
                        <label for="exercise">Enter number of sets and reps: </label>
                        <div class="form__input-group">
                            <input type="number" v-model.number="formData.sets" placeholder="Sets" required>
                        </div>
                        <div class="form__input-group">
                            <input type="number" v-model.number="formData.reps" placeholder="Reps" required>
                        </div>
                    </div>

                    <div class="form__group">
                        <label for="exercise">Enter rest time between sets: </label>
                        <div class="form__input-group">
                            <input type="text" v-model="formData.restTime" pattern="[0-5][0-9]:[0-5][0-9]" placeholder="(Optional) Rest Time (mm:ss)">
                        </div>
                    </div>

                    <div class="form__group">
                        <label for="exercise">Enter weight for exercise: </label>
                        <div class="form__input-group">
                            <input type="number" v-model.number="formData.weight" placeholder="(Optional) Weight (kg)" step="any">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal__footer">
            <div v-if="error" class="footer__error">{{ error }}</div>
            <button class="footer__button" form="modalForm" :disabled='dataStore.isLoading'>
                Add exercise
            </button>
        </div>
    </div>
</template>

<script setup>
    import { ref, reactive, computed, onMounted, onUnmounted } from 'vue';
    import { useDataStore } from '@/stores/DataStore';

    onMounted(() => document.body.classList.add('disableScroll'));

    onUnmounted(() => document.body.classList.remove('disableScroll'));

    const dataStore = useDataStore();

    const emit = defineEmits(['close'])

    const props = defineProps({
        workout: { type: Object, required: true, default: '{}' }
    });

    const formData = reactive({
        exerciseId: '',
        sets: null,
        reps: null,
        weight: null,
        restTime: null,
    });

    const error = ref('');

    const groupedExercises = computed(() => {
        const existingExerciseIds = props.workout.exercises.map((exercise) => exercise.id);

        const groups = {};
        dataStore.getExercises.forEach((exercise) => {
            if (!existingExerciseIds.includes(exercise.id)) {
                if (!groups[exercise.muscleGroup]) {
                    groups[exercise.muscleGroup] = {
                        muscleGroup: exercise.muscleGroup,
                        exercises: [],
                    };
                }
                groups[exercise.muscleGroup].exercises.push(exercise);
            }
        });

        return Object.values(groups).sort((a, b) => {
            if (a.muscleGroup < b.muscleGroup) return -1;
            if (a.muscleGroup > b.muscleGroup) return 1;
            return 0;
        });
    });

    const submitForm = () => {
        dataStore.addWorkoutExercise(props.workout.id, formData)
            .then(() => {
                emit('close');
            })
            .catch((e) => {
                error.value = e.message
            })
    };
</script>

<style lang="scss" scoped>
    @import '@/assets/styles/forms.scss';
    @import '@/assets/styles/modals.scss';

    .form__hint {
        margin-top: -10px;
        margin-bottom: 10px;

        p {
            font-size: 1rem;
        }
    }
</style>