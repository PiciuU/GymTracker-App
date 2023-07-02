<template>
    <div class="container">
        <SectionCard v-for="workout in dataStore.getWorkouts" :path="`workout/${workout.dayOfWeek.toLowerCase()}`" :image="`overlay/workout/${workout.dayOfWeek.toLowerCase()}`">
            {{ workout.dayOfWeek }}
        </SectionCard>

        <FixedButton @click="isModalVisible = true"/>

        <ModalAddWorkout v-if="isModalVisible" @close="isModalVisible = false"/>
    </div>
</template>

<script setup>
    import { ref } from 'vue';
    import { useDataStore } from '@/stores/DataStore';
    import SectionCard from "@/components/SectionCard.vue";
    import FixedButton from "@/components/FixedButton.vue";
    import ModalAddWorkout from '@/components/ModalAddWorkout.vue';

    const dataStore = useDataStore();

    let isModalVisible = ref(false);
</script>

<style lang="scss" scoped>
    @media screen and (min-width: $--breakpoint-large-devices) {
        .container {
            flex-direction: row;
            flex-wrap: wrap;
        }
    }
</style>