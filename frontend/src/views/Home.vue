<template>
    <div class="card-container">
        <SectionCard path="workout" image="overlay/workout">
            Workout
        </SectionCard>

        <SectionCard path="exercises" image="overlay/exercises">
            Exercises
        </SectionCard>

        <SectionCard path="progress" image="overlay/progress">
            Progress
        </SectionCard>
    </div>
</template>

<script setup>
    import SectionCard from "@/components/SectionCard.vue";
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