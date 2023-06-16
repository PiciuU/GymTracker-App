<template>
    <div class="workout-container">
        <div class="workout">
            <div class="workout__group">
                <div class="workout__label">Your exercises</div>
                <div class="workout__items">
                    <WorkoutExercisePreview v-for="exercise in filteredExercises" :key="exercise.id" :data="[exercise.muscleGroup, exercise.name]" @click="handleClick(exercise)">
                        <template v-slot:title>{{ exercise.name }}</template>
                        <template v-slot:subtitle>Targets {{ exercise.muscleGroup.toLowerCase() }}</template>
                    </WorkoutExercisePreview>
                </div>
            </div>

            <div class="workout__group">
                <div class="workout__label">All exercises</div>
                <div class="workout__items">
                    <WorkoutExercisePreview v-for="exercise in dataStore.exercises" :key="exercise.id" :data="[exercise.muscleGroup, exercise.name]" @click="handleClick(exercise)">
                        <template v-slot:title>{{ exercise.name }}</template>
                        <template v-slot:subtitle>Targets {{ exercise.muscleGroup.toLowerCase() }}</template>
                    </WorkoutExercisePreview>
                </div>
            </div>
        </div>

        <ActionButton @click="isModalVisible = true"/>

        <ModalAddExercise v-if="isModalVisible" @close="isModalVisible = false"/>

        <ModalGetExercise v-if="isDetailsVisible" :exercise="selectedExercise" @close="isDetailsVisible = false"/>
    </div>
</template>

<script setup>
    import { ref, computed } from 'vue';
    import { useAuthStore } from '@/stores/AuthStore';
    import { useDataStore } from '@/stores/DataStore';
    import ModalAddExercise from '@/components/ModalAddExercise.vue';
    import ModalGetExercise from '@/components/ModalGetExercise.vue';
    import WorkoutExercisePreview from "@/components/WorkoutExercisePreview.vue";
    import ActionButton from "@/components/FixedButton.vue";

    const authStore = useAuthStore();
    const dataStore = useDataStore();

    let selectedExercise = ref(null);
    let isModalVisible = ref(false);
    let isDetailsVisible = ref(false);

    const filteredExercises = computed(() => {
        return dataStore.exercises.filter(exercise => exercise.userId === authStore.user.id);
    });

    function handleClick(item) {
        selectedExercise.value = item;
        isDetailsVisible.value = true;
    }
</script>

<style lang="scss" scoped>
    .card-container {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        align-content: center;
        gap: 50px;
        padding: 20px 0px;
    }

    @media screen and (min-width: $--breakpoint-large-devices) {
        .card-container {
            flex-direction: row;
            flex-wrap: wrap;
        }
    }

    .workout-container {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        align-content: center;
        gap: 50px;
        padding: 20px 0px;
    }


    .workout {
        display: flex;
        flex: 1 1 auto;
        flex-direction: column;
        max-width: 600px;
        width: 100%;
        gap: 10px;

        &__label {
            display: inline-flex;
            margin-bottom: 15px;

            &:after {
                background: var(--color-primary);
                bottom: -5px;
                content: '';
                height: 1px;
                left: 0;
                position: absolute;
                width: 150%;
            }
        }

        &__items {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
    }
</style>
