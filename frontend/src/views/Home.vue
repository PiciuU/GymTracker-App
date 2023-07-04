<template>
    <div class="container container-wrap">
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