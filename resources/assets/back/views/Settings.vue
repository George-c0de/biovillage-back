<template>
	<v-container fluid>
		<h1 class="mb-3">{{ $tc('titles.settings') }}</h1>

		<v-expansion-panels
			multiple
		>
			<v-expansion-panel
				v-for="(group, key) in settings"
				:key="key"
			>
				<v-expansion-panel-header>
					<h3>{{ group.title }}</h3>
				</v-expansion-panel-header>
				<v-expansion-panel-content>
					<v-form @submit.prevent="editGroup($event.target)">
						<div
							v-for="option in group.options"
							:key="option.name"
							class="mb-4"
						>
							<v-text-field
								dense outlined filled
								v-if="option.type === undefined"
								v-model="option.value"
								v-bind="optionAttrs(option)"
								:type="option.inputType"
							></v-text-field>

							<v-select
								dense outlined filled
								v-else-if="option.type === 'select'"
								v-model="option.value"
								v-bind="optionAttrs(option)"
								:items="option.items"
								class="v-select--size"
							></v-select>

							<template v-else-if="option.type === 'check'">

								<v-checkbox
									v-model="option.value"
									v-bind="optionAttrs(option)"
									:false-value="0"
									:true-value="1"
									name=""
								></v-checkbox>
								<input type="hidden" :name="option.name" :value="option.value">

							</template>

							<ImageInput
								v-else-if="option.type === 'img'"
								v-bind="optionAttrs(option)"
								:src="option.value"
							></ImageInput>

							<ColorInput
								dense outlined filled
								v-else-if="option.type === 'color'"
								v-model="option.value"
								v-bind="optionAttrs(option)"
								type="text"
							/>

							<v-textarea
								outlined filled
								v-if="option.type === 'area'"
								v-model="option.value"
								v-bind="optionAttrs(option)"
								:rows="option.rows"
							></v-textarea>

						</div>

						<v-btn
							type="submit"
							color="success"
							class="mt-2"
						>
							<v-icon left>
								save
							</v-icon>
							{{ $tc('save') }}
						</v-btn>

					</v-form>
				</v-expansion-panel-content>
			</v-expansion-panel>
		</v-expansion-panels>

	</v-container>
</template>

<script>
import settings from '@bo/js/settings'
import ColorInput from '@bo/components/ColorInput'
import ImageInput from '@bo/components/ImageInput'

export default {

	data() { return {
		settings,
	}},
	
	components: {
		ColorInput,
		ImageInput,
	},

	methods: {
		editGroup(form) {
			this.$store.dispatch('general/updateSettings', new FormData(form))
		},

		// задание общих атрибутов
		optionAttrs(attrs) { return {...{
			persistentHint: true,
			hideDetails: 'auto',
		}, ...attrs}},
		
	},

	watch: {
		'$store.state.general.settings': {
			immediate: true,
			handler(v) {
				for (let group in this.settings) {
					let groupTexts = this.$t('settingsListObject.' + group)
					this.settings[group].title = groupTexts.title ? this.$tc('settingsListObject.' + group + '.title') : group

					for (let option in this.settings[group].options) {
						let optionObject = this.settings[group].options[option]
						optionObject.value = v[optionObject.name]
						optionObject.label = option
						
						if (groupTexts.options) {
							let optionTexts = groupTexts.options[option]
							for (let text in optionTexts) {
								optionObject[text] = this.$tc('settingsListObject.' + group + '.options.' + option + '.' + text)
							}
						}
					}
				}
			},
		},

	},

	created() {
		this.$store.dispatch('general/getSettings')
		
	},

}
</script>
