<template>

	<v-menu
		v-model="menu"
		:disabled="readonly"
		offset-y
		:close-on-content-click="false"
		min-width="290"
		max-width="290"
	>
		<template #activator="{ on, attrs }">
			<v-text-field
				v-bind="{...$attrs, ...attrs}"
				:value="value"
				size="8"
				readonly
				:class="readonly ? '' : 'v-input--not-readonly'"
				:style="inputStyle"
				v-on="on"
				@click:clear="$emit('input', null)"
			></v-text-field>
		</template>

		<v-date-picker
			v-model="date"
			first-day-of-week="1"
			full-width
			class="mx-auto"
		></v-date-picker>
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

		inputStyle: {
			type: String,
			default: '',
		}

	},

	data() { return {
		menu: false,

	}},

	computed: {
		date: {
			get() {
				return this.value ? this.value.split('.').reverse().join('-') : ''
			},
			set(v) {
				this.$emit('input', v.split('-').reverse().join('.'))
				this.menu = false
			}
		},

	},

}
</script>
