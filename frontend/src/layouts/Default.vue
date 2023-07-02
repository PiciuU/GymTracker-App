<template>
	<main>
		<div class="wrapper">
			<router-view v-slot="{ Component }">
				<keep-alive>
					<component :is="Component"></component>
				</keep-alive>
			</router-view>
		</div>
	</main>
</template>

<style lang="scss" scoped>
	.container {
		align-items: center;
		background-color: $--color-background;
		color: $--color-text;
		display: flex;
		flex-direction: column;
		height: 100%;
		justify-content: center;
		margin: 0 auto;
		max-width: 500px;
		padding: 10px;
		width: 100%;
    }
</style>