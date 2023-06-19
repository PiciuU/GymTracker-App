<template>
    <div class="container">
        <div class="section">
            <div class="section__group">
                <div class="group__label">
                    <p>Your exercises</p>
                </div>
                <div class="group__items">
                    <ExercisePreview v-for="exercise in dataStore.getUserExercises" :key="exercise.id" :image="exercise.attachmentUrl" @click="handleClick(exercise)">
                        <template v-slot:title>{{ exercise.name }}</template>
                        <template v-slot:subtitle>Targets {{ exercise.muscleGroup.toLowerCase() }}</template>
                    </ExercisePreview>
                </div>
            </div>

            <div class="section__group">
                <div class="group__label">
                    <p>All exercises</p>
                </div>
                <div class="group__items">
                    <ExercisePreview v-for="exercise in dataStore.getExercises" :key="exercise.id" :image="exercise.attachmentUrl" @click="handleClick(exercise)">
                        <template v-slot:title>{{ exercise.name }}</template>
                        <template v-slot:subtitle>Targets {{ exercise.muscleGroup.toLowerCase() }}</template>
                    </ExercisePreview>
                </div>
            </div>
        </div>

        <FixedButton @click="isModalVisible = true"/>

        <ModalAddExercise v-if="isModalVisible" @close="isModalVisible = false"/>

        <ModalGetExercise v-if="isDetailsVisible" :exercise="selectedExercise" @close="isDetailsVisible = false"/>
    </div>
</template>

<script setup>
    import { ref } from 'vue';
    import { useDataStore } from '@/stores/DataStore';
    import ModalAddExercise from '@/components/ModalAddExercise.vue';
    import ModalGetExercise from '@/components/ModalGetExercise.vue';
    import ExercisePreview from "@/components/ExercisePreview.vue";
    import FixedButton from "@/components/FixedButton.vue";

    const dataStore = useDataStore();

    const selectedExercise = ref(null);
    const isModalVisible = ref(false);
    const isDetailsVisible = ref(false);

    function handleClick(item) {
        selectedExercise.value = item;
        isDetailsVisible.value = true;
    }
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
