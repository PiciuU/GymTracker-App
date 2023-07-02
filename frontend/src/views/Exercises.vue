<template>
    <div class="container">
        <div class="section">
            <div class="section__group">
                <div class="group__label">
                    <p>Your exercises</p>
                </div>
                <div class="group__items">
                    <ExercisePreview v-for="exercise in dataStore.getUserExercises" :key="exercise.id" :exercise="exercise" @click="handleClick(exercise)">
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
                    <ExercisePreview v-for="exercise in dataStore.getExercises" :key="exercise.id" :exercise="exercise" @click="handleClick(exercise)">
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
    @import '@/assets/styles/sections.scss';
</style>
