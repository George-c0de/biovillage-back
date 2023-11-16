<template>
	<v-container fluid>
		<h1 class="mb-3">{{ $tc('titles.slider') }}</h1>

		<v-expansion-panels multiple class="mb-10">
			
			<v-expansion-panel
				v-for="slide in slider"
				:key="slide.id"
			>
				<v-expansion-panel-header>

					<h3>{{ slide.fixedTitle }}</h3>

					<v-icon
						v-if="!slide.fixedActive"
						small
						:title="$tc('notActiveF')"
						class="flex-grow-0 px-2 mr-auto"
					>
						visibility_off
					</v-icon>

				</v-expansion-panel-header>
				<v-expansion-panel-content>
					<v-form @submit.prevent="saveSlide($event.target, slide.id)">
						
						<v-row>
							<v-col cols="12" lg order-lg="1">
								
								<ImageInput
									name="image"
									:label="$tc('cover')"
									:src="slide.imageSrc"
									:file.sync="slide.image"
								></ImageInput>
								
							</v-col>
							<v-col cols="12" lg="8" class="d-flex flex-column flex-children-grow-0">
								
								<v-text-field
									dense outlined filled
									v-model="slide.name"
									name="name"
									:label="$tc('caption')"
								></v-text-field>

								<v-text-field
									dense outlined filled
									v-model="slide.description"
									name="description"
									:label="$tc('subcaption')"
								></v-text-field>
								
								<v-row justify="space-between" align="center" class="my-0">
									<v-col cols="12" lg class="py-0">
										
										<v-checkbox
											v-model="slide.active"
											:label="$tc('activeM')"
										></v-checkbox>

										<input type="hidden" name="active" :value="slide.active ? 1 : 0">
										
									</v-col>
									<v-col cols="12" lg class="py-0">
										
										<v-text-field
											dense outlined filled
											v-model="slide.order"
											name="order"
											:label="$tc('sortOrder')"
										></v-text-field>
										
									</v-col>
									<v-col cols="12" lg class="py-0">
										
										<ColorInput
											dense outlined filled
											v-model="slide.bgColor"
											name="bgColor"
											:label="$tc('bgColor')"
										/>
										
									</v-col>
								</v-row>
								
								<v-row class="mt-auto">
									<v-col lg="auto">
										
										<v-btn
											type="submit"
											color="success"
											block
										>
											<v-icon left>
												{{ slide.id === -1 ? 'add' : 'save' }}
											</v-icon>
											{{ slide.id === -1 ? $tc('add') : $tc('save') }}
										</v-btn>
										
									</v-col>
									<v-col v-if="slide.id !== -1" lg="auto">
										
										<v-btn
											color="error"
											block
											@click="deleteSlide(slide)"
										>
											<v-icon left>
												delete
											</v-icon>
											{{ $tc('delete') }}
										</v-btn>
										
									</v-col>
								</v-row>
								
							</v-col>
						</v-row>
						
					</v-form>
				</v-expansion-panel-content>
			</v-expansion-panel>
		</v-expansion-panels>
		
		<ConfirmDialog
			:show="deleteDialog"
			:title="$tc('deletion')"
			agreeIcon="delete"
			@agree="confirmDeleteDialog"
			@disagree="this.deleteDialog = false"
		>

			{{ $tc('areYouSure') + ' ' + $tc('delete').toLowerCase() + ' ' +  $tc('slide') }} <br /> <b>{{ deleteDialogText }}</b>?
		</ConfirmDialog>

	</v-container>
</template>

<script>
import ColorInput from '@bo/components/ColorInput'
import ImageInput from '@bo/components/ImageInput'
import ConfirmDialog from '@bo/components/ConfirmDialog'

export default {
	components: {
		ColorInput,
		ImageInput,
		ConfirmDialog,
	},
	
	data() { return {
		newSlide: this.resetSlide(),
		slider: [this.newSlide],
		deleteDialog: false,
		deleteDialogId: null,
		deleteDialogText: '',
			
	}},
	
	methods: {
		resetSlide() { return {
			id: -1,
			fixedTitle: '+ ' + this.$tc('new') + ' ' + this.$tc('slide').toLowerCase(),
			fixedActive: true,
			active: true,
			order: '100',
			bgColor: '#FFFFFF',
		}},

		saveSlide(form, id) {
			let data = new FormData(form)

			id === -1
			? this.$store.dispatch('general/addSlide', {
				data,
				then: ()=> this.newSlide = this.resetSlide()
			})
			: this.$store.dispatch('general/updateSlide', {data, id})
		},
		
		deleteSlide(slide) {
			this.deleteDialogId = slide.id
			this.deleteDialogText = slide.name
			this.deleteDialog = true
		},
		
		confirmDeleteDialog() {
			this.$store.dispatch('general/deleteSlide', this.deleteDialogId)
			this.deleteDialog = false
		},
		
	},
	
	watch: {
		'$store.state.general.slider': {
			immediate: true,
			handler(v) {
				let slider = JSON.parse(JSON.stringify(v)).sort((a, b) => a.order - b.order)
				slider.forEach((e) => {
					e.fixedTitle = e.name
					e.fixedActive = e.active
				})
				this.slider = [this.newSlide, ...slider]
			},
		},
		
	},
	
	created() {
		this.$store.dispatch('general/getSlider')

	},
	
}
</script>
