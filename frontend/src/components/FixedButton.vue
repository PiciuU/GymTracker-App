<template>
    <button class="fixed-button">
        <font-awesome-icon icon="plus" size="xl" fixed-width/>
    </button>
</template>

<style lang="scss" scoped>
.fixed-button {
    align-items: center;
    background: $--color-primary;
    border: none;
    border-radius: 50%;
    bottom: 20px;
    box-shadow: 0 0 15px rgb(0 0 0 / 30%);
    cursor: pointer;
    display: flex;
    justify-content: center;
    height: 50px;
    outline: none;
    position: fixed;
    right: 20px;
    transition: all .25s ease-in-out;
    width: 50px;

    &:hover {
        transform: scale(1.1);
    }
}
</style>