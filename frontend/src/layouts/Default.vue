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