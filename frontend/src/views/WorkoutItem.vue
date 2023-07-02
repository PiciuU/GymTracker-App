<template>
    <div class="container">
        <div class="section">
            <div class="section__card">
                <div class="card__title">
                    <h1>{{ currentDay }}</h1>
                    <font-awesome-icon v-if="groupedExercises.length > 0" icon="pen-to-square" :class="{ 'active': editMode }" @click="editMode = !editMode"/>
                </div>
                <h2 class="card__subtitle">{{ musclesTargeted }}</h2>
            </div>

            <div class="section__group" v-for="(exercises, index) in groupedExercises" :key="index">
                <div class="group__label">
                    <p>{{ exercises.muscleGroup }}</p>
                </div>
                <div class="group__items">
                    <ExercisePreview v-for="exercise in exercises.items" :key="exercise.id" :exercise="exercise" :editMode="editMode" :isLoading="dataStore.isLoading" @click="handleClick(exercise)">
                        <template v-slot:title>{{ exercise.name }}</template>
                        <template v-slot:subtitle>{{ exercise.sets }} series, {{ exercise.reps }} reps</template>
                    </ExercisePreview>
                </div>
            </div>
        </div>

        <FixedButton @click="isModalVisible = true" v-if="!editMode"/>

        <ModalAddWorkoutExercise v-if="isModalVisible" :workout="workout" @close="isModalVisible = false"/>

        <ModalGetWorkoutExercise v-if="isDetailsVisible" :workout="workout" :exercise="selectedExercise" @close="isDetailsVisible = false"/>
    </div>
</template>

<script setup>
    import { ref, computed } from 'vue';
    import { useRoute } from 'vue-router';
    import { useDataStore } from '@/stores/DataStore';
    import ExercisePreview from "@/components/ExercisePreview.vue";
    import ModalGetWorkoutExercise from "@/components/ModalGetWorkoutExercise.vue";
    import ModalAddWorkoutExercise from "@/components/ModalAddWorkoutExercise.vue";
    import FixedButton from "@/components/FixedButton.vue";

    const dataStore = useDataStore();
    const route = useRoute();

    const editMode = ref(false);

    const workout = dataStore.getWorkouts.find(workout => workout.id === route.meta.workoutId);
    const currentDay = route.meta.breadcrumb.split("/").pop()

    const groupedExercises = computed(() => {
        // Group exercises by muscleGroup field
        if (!workout.exercises) return {};
        const groups = {};
        workout.exercises.forEach((exercise) => {
            if (!groups[exercise.muscleGroup]) {
                groups[exercise.muscleGroup] = {
                    muscleGroup: exercise.muscleGroup,
                    items: [],
                };
            }
            groups[exercise.muscleGroup].items.push(exercise);
        });
        // Convert object to array and sort by muscleGroup
        return Object.values(groups).sort((a, b) => {
            if (a.muscleGroup < b.muscleGroup) return -1;
            if (a.muscleGroup > b.muscleGroup) return 1;
            return 0;
        })
    });

    const musclesTargeted = computed(() => groupedExercises.value.filter((group) => group.muscleGroup !== "Warmup").map((group) => group.muscleGroup).join(", "))

    const selectedExercise = ref(null);
    const isModalVisible = ref(false);
    const isDetailsVisible = ref(false);

    const handleClick = (item) => {
        if (editMode.value === true) {
            dataStore.deleteWorkoutExercise(workout.id, item.id)
                .catch((e) => {
                    console.log('Couldnt delete exercise: ', e);
                })
        } else {
            selectedExercise.value = item;
            isDetailsVisible.value = true;
        }
    };
</script>

<style lang="scss" scoped>
    @import '@/assets/styles/sections.scss';

    .section__card > .card__title > h1 {
        font-weight: bold;
    }
</style>