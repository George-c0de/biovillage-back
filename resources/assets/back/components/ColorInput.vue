<template>

	<v-menu
		offset-y
		:disabled="readonly"
		:close-on-content-click="false"
		return-value="true"
		min-width="auto"
	>
		<template #activator="{ on, attrs }">
			<v-text-field
				v-bind="{...$attrs, ...attrs}"
				:value="color"
				size="7"
				readonly
				:class="readonly ? '' : 'v-input--not-readonly'"
				v-on="on"
				@click:clear="$emit('input', null)"
			></v-text-field>
		</template>

		<v-color-picker
			v-model="color"
			mode="hexa"
			hide-mode-switch
			class="mt-1"
		></v-color-picker>
	</v-menu>

</template>
	
<script>
export default {
	props: {
		value: {
			type: String,
			default: '',
		},

		readonly: {
			type: Boolean,
		},

	},

	computed: {
		color: {
			get() {
				return this.value ? this.value.substring(0, 7) : ''
			},
			set(v) {
				this.$emit('input', v)
			}
		},
		
	},

}
</script>
