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
                    <ExercisePreview v-for="exercise in exercises.items" :key="exercise.id" :image="exercise.attachmentUrl" :editMode="editMode" :isLoading="dataStore.isLoading" @click="handleClick(exercise)">
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
    .container {
        align-content: center;
        align-items: center;
        display: flex;
        flex-direction: column;
        gap: 50px;
        height: 100%;
        justify-content: center;
        padding: 20px 0px;
        width: 100%;
    }

    .section {
        display: flex;
        flex: 1 1 auto;
        flex-direction: column;
        gap: 10px;
        max-width: 600px;
        width: 100%;

        &__card {
            background: $--color-overlay;
            border-radius: 5px;
            padding: 10px 15px;

            .card {
                &__title {
                    align-items: center;
                    display: flex;
                    justify-content: space-between;

                    h1 {
                        font-size: 1.6rem;
                        font-weight: bold;
                    }

                    svg {
                        cursor: pointer;
                        &.active {
                            color: $--color-primary;
                        }
                    }
                }

                &__subtitle {
                    color: $--color-text-muted-2;
                    font-size: 1.2rem;
                }
            }
        }

        &__group {
            .group {
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
        }
    }
</style>