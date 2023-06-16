<template>
    <div class="workout-container">
        <div class="workout">
            <div class="workout__banner">
                <div class="workout__banner-group">
                    <h1 class="workout__title">{{ currentDay }}</h1>
                    <font-awesome-icon v-if="groupedExercises.length > 0" class="workout__icon" icon="fa-solid fa-pen-to-square" :class="{ 'workout__icon--active': editMode }" @click="editMode = !editMode"/>
                </div>
                <h2 class="workout__subtitle">{{ musclesTargeted }}</h2>
            </div>

            <div class="workout__group" v-for="(exercises, index) in groupedExercises" :key="index">
                <div class="workout__label">{{ exercises.muscleGroup }}</div>
                <div class="workout__items">
                    <WorkoutExercisePreview v-for="exercise in exercises.items" :key="exercise.id" :data="[exercise.muscleGroup, exercise.name]" :editMode="editMode" :isLoading="dataStore.isLoading" @click="handleClick(exercise)">
                        <template v-slot:title>{{ exercise.name }}</template>
                        <template v-slot:subtitle>{{ exercise.sets }} series, {{ exercise.reps }} reps</template>
                    </WorkoutExercisePreview>
                </div>
            </div>
        </div>

        <ActionButton @click="isModalVisible = true" v-if="!editMode"/>

        <ModalAddExerciseToWorkout v-if="isModalVisible" :workout="workout" :exercises="dataStore.exercises" @close="isModalVisible = false"/>

        <ModalGetWorkoutExercise v-if="isDetailsVisible" :workout="workout" :exercise="selectedExercise" @close="isDetailsVisible = false"/>
    </div>
</template>

<script setup>
    import { ref, computed } from 'vue';
    import { useRoute } from 'vue-router';
    import { useDataStore } from '@/stores/DataStore';
    import WorkoutExercisePreview from "@/components/WorkoutExercisePreview.vue";
    import ModalGetWorkoutExercise from "@/components/ModalGetWorkoutExercise.vue";
    import ModalAddExerciseToWorkout from "@/components/ModalAddExerciseToWorkout.vue";
    import ActionButton from "@/components/FixedButton.vue";

    const dataStore = useDataStore();
    const route = useRoute();

    let editMode = ref(false);
    let workout = dataStore.getWorkouts.find(workout => workout.id === route.meta.workoutId);

    let groupedExercises = computed(() => {
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

    let currentDay = computed(() => route.meta.breadcrumb.split("/").pop())

    let musclesTargeted = computed(() => groupedExercises.value.filter((group) => group.muscleGroup !== "Warmup").map((group) => group.muscleGroup).join(", "))

    let selectedExercise = ref(null);
    let isModalVisible = ref(false);
    let isDetailsVisible = ref(false);

    function handleClick(item) {
        if (editMode.value === true) {
            dataStore.deleteWorkoutExercise(workout.id, item.id)
            .catch((e) => {
                console.log('Couldnt delete exercise: ', e);
            })
        } else {
            selectedExercise.value = item;
            isDetailsVisible.value = true;
        }
    }

</script>

<style lang="scss" scoped>
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

    &__banner {
        background: var(--color-overlay);
        border-radius: 5px;
        padding: 10px 15px;

        &-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    }


    &__title {
        font-size: 1.6rem;
        font-weight: bold;
    }

    &__subtitle {
        color: var(--color-text-muted-2);
        font-size: 1.2rem;
    }

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

    &__icon {
        cursor: pointer;

        &--active {
            color:var(--color-primary);
        }
    }
}
</style>