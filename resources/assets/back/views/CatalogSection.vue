<template>
	<v-container fluid v-if="section.id || !id">
		<h1 class="mb-3">{{ section.name || $tc('new') + ' ' + $tc('section').toLowerCase() }}</h1>

		<v-form @submit.prevent="saveSection($event.target)">
			<v-row>
				<v-col cols="12" lg="" order-lg="1">

					<ImageInput
						name="image"
						:label="$tc('cover')"
						:src="section.imageSrc"
					></ImageInput>

				</v-col>
				<v-col cols="12" lg="8" class="d-flex flex-column flex-children-grow-0">
					
					<v-row justify="space-between" justify-lg="space-between" class="flex-wrap-reverse">
						<v-col class="flex-grow-1 d-flex flex-column flex-children-grow-0">
							
							<v-text-field
								dense outlined filled
								v-model="section.name"
								name="name"
								:label="$tc('title')"
							></v-text-field>
							
							<v-autocomplete
								outlined
								v-model="tags"
								:items="tags"
								:label="$tc('tag', 2)"
								multiple
								chips
								append-icon=""
								disabled
							></v-autocomplete>
							
							<v-row align="center" class="my-0">
								<v-col cols="12" lg order="last" order-lg="first" class="py-0">
									
									<v-checkbox
										v-model="section.active"
										name="active"
										:label="$tc('activeF')"
									></v-checkbox>
									
								</v-col>
								<v-col cols="12" lg class="py-0">
									
									<v-text-field
										dense outlined filled
										v-model="section.order"
										name="order"
										:label="$tc('sortOrder')"
									></v-text-field>
									
								</v-col>

								<v-col cols="12" lg class="py-0">
									
									<ColorInput
										dense outlined filled
										v-model="section.bgColor"
										name="bgColor"
										:label="$tc('bgColor')"
									/>
									
								</v-col>
							</v-row>

							<v-row class="align-self-start mt-auto mb-0">
								<v-col cols="auto">

									<v-btn
										type="submit"
										color="success"
									>
										<v-icon left>
											save
										</v-icon>
										{{ $tc('save') }}
									</v-btn>

								</v-col>
								
								<v-col cols="auto" v-if="section.id">

									<v-btn
										color="error"
										@click="deleteSection"
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
					
				</v-col>
			</v-row>
			
		</v-form>
		
		<ConfirmDialog
			:show="sectionDeleteDialog"
			:title="$tc('deletion')"
			agreeIcon="delete"
			@agree="confirmSectionDeleteDialog"
			@disagree="sectionDeleteDialog = false"
		>
			{{ $tc('areYouSure') + ' ' + $tc('delete').toLowerCase() + ' ' +  $tc('sectionEdit') }} <br /> <b>{{ section.name }}</b>?
		</ConfirmDialog>
		
	</v-container>
</template>

<script>
import ColorInput from '@bo/components/ColorInput'
import ImageInput from '@bo/components/ImageInput'
import ConfirmDialog from '@bo/components/ConfirmDialog'

export default {
	props: ['id'],
	
	components: {
		ColorInput,
		ImageInput,
		ConfirmDialog,
	},
	
	data() { return {
		section: {},
		newSection: {
			active: true,
			order: 100,
			bgColor: '',
		},
		sectionDeleteDialog: false,
	}},
	
	methods: {
		saveSection(form) {
			let data = new FormData(form)
			data.set('active', form.active.checked ? '1' : '0')
			this.id ? this.$store.dispatch('catalog/updateSection', {id: this.id, data}) : this.$store.dispatch('catalog/createSection', data)
		},
		
		deleteSection() {
			this.sectionDeleteDialog = true
		},
		
		confirmSectionDeleteDialog() {
			this.$store.dispatch('catalog/deleteSection', this.section.id)
			this.sectionDeleteDialog = false
		},
		
	},
	
	computed: {
		tags() {
		if (!this.section.tags) return []
			let tags = []
			this.section.tags.forEach(tag => {
				tags.push(tag[1])
			})
			return tags
		},
	},
	
	watch: {
		'$store.state.catalog.section': {
			immediate: true,
			handler(v) {
				this.section = JSON.parse(JSON.stringify(v.id ? v : this.newSection))
			},
		},
		
	},
	
	created() {
		this.$store.dispatch('catalog/getSection', {id: this.id})
		
	},
	
}
</script>
