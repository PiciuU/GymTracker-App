<template>
    <div class="modal-overlay" @click="$emit('close')"></div>
    <div class="modal">
        <div class="modal__close" @click="$emit('close')">
            <font-awesome-icon icon="fa-solid fa-xmark" size="lg" />
        </div>
        <div class="modal__header">
            <div class="modal__title">
                Add exercise to workout
            </div>
            <div class="modal__subtitle">
                Fill in the details below to add a new exercise to your workout plan.
            </div>
        </div>
        <div class="modal__content">
            <form class="modal__form" id="modalForm" @submit.prevent="submitForm">
                <div class="modal__group">
                    <div class="modal__label">
                        Choose exercise from list:
                    </div>
                    <select class="modal__select" v-model="formData.exerciseId" required>
                        <option value="">Select desired exercise...</option>
                        <optgroup v-for="(group, index) in groupedExercises" :key="index" :label="group.muscleGroup">
                            <option v-for="item in group.items" :key="item.id" :value="item.id">{{ item.name }}</option>
                        </optgroup>
                    </select>
                    <div class="modal__hint">
                        Cant find your exercise? <router-link to="/">Add it by yourself.</router-link>
                    </div>
                </div>
                <div v-if="formData.exerciseId">
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
                </div>
            </form>
        </div>
        <div class="modal__footer">
            <div v-if="error" class="modal__error">{{ error }}</div>
            <button class="modal__button" form="modalForm" :disabled='dataStore.isLoading'>
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
        workout: { type: Object, required: true, default: '{}' },
        exercises: { type: Object, required: true, default: '{}' }
    });

    const formData = reactive({
        workoutId: props.workout.id,
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
        props.exercises.forEach((exercise) => {
            if (!existingExerciseIds.includes(exercise.id)) {
            if (!groups[exercise.muscleGroup]) {
                groups[exercise.muscleGroup] = {
                muscleGroup: exercise.muscleGroup,
                items: [],
                };
            }
            groups[exercise.muscleGroup].items.push(exercise);
            }
        });

        return Object.values(groups).sort((a, b) => {
            if (a.muscleGroup < b.muscleGroup) return -1;
            if (a.muscleGroup > b.muscleGroup) return 1;
            return 0;
        });
    });

    const submitForm = () => {
        dataStore.addWorkoutExercise(formData)
            .then(() => {
                console.log('THEN');
                emit('close');
            })
            .catch((e) => {
                console.log(e);
                error.value = e.message
            })
    };
</script>

<style lang="scss" scoped>
    @import '@/assets/styles/modal.scss';
</style>