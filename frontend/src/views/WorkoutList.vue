<template>
    <div class="card-container">
        <SectionCard v-for="workout in dataStore.getWorkouts" :path="`workout/${workout.dayOfWeek.toLowerCase()}`" :image="`overlay/workout/${workout.dayOfWeek.toLowerCase()}`">
            {{ workout.dayOfWeek }}
        </SectionCard>
        <ActionButton @click="isModalVisible = true"/>

        <ModalAddWorkout v-if="isModalVisible" @close="isModalVisible = false"/>
    </div>
</template>

<script setup>
    import { ref } from 'vue';
    import { useDataStore } from '@/stores/DataStore';
    import SectionCard from "@/components/SectionCard.vue";
    import ActionButton from "@/components/FixedButton.vue";
    import ModalAddWorkout from '@/components/ModalAddWorkout.vue';

    const dataStore = useDataStore();

    let isModalVisible = ref(false);
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
</style>
