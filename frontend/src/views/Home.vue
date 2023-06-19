<template>
    <div class="container">
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

    @media screen and (min-width: $--breakpoint-large-devices) {
        .container {
            flex-direction: row;
            flex-wrap: wrap;
        }
    }
</style>