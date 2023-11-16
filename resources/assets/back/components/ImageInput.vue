<template>
	<label class="d-block" :style="{'cursor': disabled ? false : 'pointer'}">
		<div v-if="label" class="v-label theme--light mb-2">
			{{label}}
		</div>
		
		<input
			type="file"
			accept="image/*"
			:name="name"
			:disabled="disabled"
			hidden
			@input="input"
		/>
		
		<v-card outlined>
			<v-img
				:src="previewSrc || src"
				contain
				max-height="300"
				:aspect-ratio="16/9"
				class="img-card"
				:class="{'img-card--loaded': previewSrc || src}"
				@load="$emit('preview-loaded')"
			>
				<template #placeholder>
					<div
						class="d-flex justify-center align-center text-caption text-center d-flex"
						:style="{'height': '100%'}"
					>
						<v-icon>image</v-icon>
						{{ $tc('upload') }}
					</div>
				</template>
				
			</v-img>
		</v-card>
		
	</label>
</template>

<script>
export default {
	props: {
		file: File,
		name: {
			type: String,
			default: 'image',
		},
		src: String,
		label: String,
		disabled: {
			type: Boolean,
			default: false,
		},
		
	},
	
	data() { return {
		previewSrc: '',
		editedFile: this.file,
	}},
	
	methods: {
		input(e) {
			this.editedFile = e.target.files[0]
			this.$emit('update:file', this.editedFile)
			
		},
		
	},

	watch: {
		src(v) {
			this.previewSrc = ''
		},

		file: {
			handler(v) {
				if (v) {
					this.previewSrc = URL.createObjectURL(v)

					// очистка памяти
					this.$once('preview-loaded', ()=> {
						URL.revokeObjectURL(this.previewSrc)
					})
				} else {
					this.previewSrc = ''
				}
			},
			immediate: true,
		},
		
	},
	
}
</script>

<style lang="scss" scoped>
.img-card {
	background-color: #ddd;

	&--loaded {
		background-image: url('/back/img/transparent-bg.png');
		background-repeat: repeat;
	}
}
</style>
