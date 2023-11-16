<template>
	<v-card outlined>
		<v-card-title>
			<template v-if="uploadTitle">{{ uploadTitle }}</template>
			<template v-else>{{ $tc('images', multiple ? 2 : 1) }}</template>
		</v-card-title>

		<v-card-subtitle v-if="uploadSubtitle && uploadSubtitle.length">{{ uploadSubtitle }}</v-card-subtitle>

		<v-card-text>
			<file-pond
				v-show="multiple || !toUpload.length"
				ref="pond"
				class-name="images-pond"
				:label-idle="pondLabel"
				:allow-multiple="multiple"
				accepted-file-types="image/jpeg, image/jpg, image/png"
				:files="uploadedImages"
				@addfile="addImage"
				styleImageEditButtonEditItemPosition="top right"
			/>
			<draggable
				tag="div" v-model="toUpload"
				v-bind="{ animation: 200, disabled: false, ghostClass: 'ghost'}"
				@start="toggleDragImage(true)" @end="toggleDragImage(false)"
			 >
				<transition-group type="transition" :name="!dragImage ? 'flip-list' : null" class="image-cards-container ma-n3 mb-0">
					<v-card v-for="image in toUpload" :key="image.idLocal" class="img-card ma-3" :class="{'img-card--single': !multiple}">
						<div class="img-card__img" :style="{ backgroundImage: 'url(' + image.preview + ')' }"></div>
						<div class="img-card__actions ma-n1">
							<v-btn
								fab x-small color="primary" class="ma-1"
								@click="editImage(image.idLocal, image.file)"
							><v-icon>edit</v-icon></v-btn>
							<v-btn
								fab x-small color="error" class="ma-1"
								@click="removeImage(image.idLocal)"
							><v-icon>delete</v-icon></v-btn>
						</div>
					</v-card>
				</transition-group>
			</draggable>
		</v-card-text>

		<v-dialog v-model="showEditorDialog" persistent max-width="900">
			<v-card tile>
				<v-card-text>
					<section class="cropper-area">
						<vue-cropper
							ref="cropper"
							:aspect-ratio="aspectRatio"
							:src="editableImageSrc"
							class="mb-4"
						/>
						<div class="d-flex flex-wrap justify-center ma-n2 mt-4">
							<v-btn icon outlined color="primary" class="ma-2" @click="$refs.cropper.clear()"><v-icon>crop_din</v-icon></v-btn>
							<v-btn icon outlined color="primary" class="ma-2" @click="zoom(0.2)"><v-icon>zoom_in</v-icon></v-btn>
							<v-btn icon outlined color="primary" class="ma-2" @click="zoom(-0.2)"><v-icon>zoom_out</v-icon></v-btn>
							<v-btn icon outlined color="primary" class="ma-2" @click="move(-10, 0)"><v-icon>arrow_back</v-icon></v-btn>
							<v-btn icon outlined color="primary" class="ma-2" @click="move(10, 0)"><v-icon>arrow_forward</v-icon></v-btn>
							<v-btn icon outlined color="primary" class="ma-2" @click="move(0, -10)"><v-icon>arrow_upward</v-icon></v-btn>
							<v-btn icon outlined color="primary" class="ma-2" @click="move(0, 10)"><v-icon>arrow_downward</v-icon></v-btn>
							<v-btn icon outlined color="primary" class="ma-2" @click="rotate(-90)"><v-icon>rotate_left</v-icon></v-btn>
							<v-btn icon outlined color="primary" class="ma-2" @click="rotate(90)"><v-icon>rotate_right</v-icon></v-btn>
							<v-btn icon outlined color="primary" class="ma-2" @click="flipX"><v-icon>flip</v-icon></v-btn>
						</div>
					</section>
				</v-card-text>
				<v-card-actions class="pb-4" :style="{'overflow': 'hidden'}">

				<v-row justify="space-between" class="mx-0">
					<v-col>
						
						<v-btn
							block
							color="success"
							large
							min-width="220"
							@click="saveEdit"
						>
							<v-icon left>
								save
							</v-icon>
							{{ $tc('save') }}
						</v-btn>

					</v-col>
					<v-col>

						<v-btn
							block
							text
							color="info"
							large
							min-width="220"
							@click="cancelEdit"
						>
							<v-icon left>
								cancel
							</v-icon>
							{{ $tc('cancel') }}
						</v-btn>
					</v-col>

				</v-row>
			</v-card-actions>
			</v-card>
		</v-dialog>

	</v-card>
</template>

<script>
import vueFilePond from 'vue-filepond';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.esm.js'
import 'filepond/dist/filepond.min.css'
const FilePond = vueFilePond( FilePondPluginFileValidateType )

import VueCropper from 'vue-cropperjs';
import 'cropperjs/dist/cropper.css';

import draggable from 'vuedraggable'

export default {
	props: {
		uploaded: {
			default: () => { return [] }
		},
		multiple: {
			default: false
		},
		aspectRatio: {
			default: NaN
		},
		uploadTitle: {
			type: String
		},
		uploadSubtitle: {
			type: String
		},
		outputFileType: {
			type: String,
			default: 'image/jpeg'
		},
		// inputName: {
		//     type: String,
		//     default: '',
		// },
	},

	components: {
		FilePond,
		VueCropper,
		draggable
	},

	created () {
		// Обрабатываем массив загруженных изображений
		if (typeof this.uploaded === 'string') {
			this.uploadedImages = [this.uploaded]
		} else if (this.uploaded.length) {
			this.uploadedImages = this.uploaded.map(e => e.src)
		}
	},

	watch: {
		uploaded (uploaded) {
			let th = this
			let counter = ++th.uploadedSetCounter

			th.toUpload = []
			th.enableUpload()
			// Если картинка - это строка-урл, то получим blob и сформируем объект
			uploaded.forEach(function (item, i) {
				if (counter != th.uploadedSetCounter) return false
				if (item.file) {
					item.idLocal = ++th.imagesCounter
				} else if (item.src) {
					let xhr = new XMLHttpRequest()
					xhr.open('GET', item.src)
					xhr.responseType = "blob"
					xhr.send()
					xhr.onload = function () {
						if (counter != th.uploadedSetCounter) return false
						else th.toUpload.push({
							idLocal: ++th.imagesCounter,
							preview: item.src,
							file: new File([this.response], item.src.replace(/^.*[\\\/]/, ''), {type: this.response.type, lastModified: Date.now()})
						})
					}
				}
			})
		},

		toUpload (toUpload) {
			this.$emit('update:toUpload', toUpload)
		}
	},

	data() {
		return {
			uploadedImages: [],
			uploadedSetCounter: 0,
			toUpload: [],
			imagesCounter: 0,
			dragImage: false,
			showEditorDialog: false,
			editableImageSrc: '',
			editableImageId: 0,
			flipScale: 1,
		}
	},

	computed: {
		pondLabel() {
			return '<div data-v-0ed9c044="" class="d-flex justify-center align-center text-caption text-center d-flex" style="height: 100%;"><i data-v-0ed9c044="" aria-hidden="true" class="v-icon notranslate material-icons theme--light">image</i>' + this.$tc('upload') + '</div>'
		},
	},

	methods: {
		addImage(fieldName, file) {

			let imageItem = {
				idLocal: ++this.imagesCounter,
				file: file.file,
				preview: undefined
			}

			if (typeof file.source === "string" || file.source instanceof String) {
				// Если файл был загружен ранее и принят в виде строки с урлом
				imageItem.preview = file.source
				this.toUpload.push(imageItem)
			} else {
				this.$emit('changed')
				let reader = new FileReader()
				reader.readAsDataURL(file.source)
				reader.onload = (e) => {
					imageItem.preview = e.target.result
					this.toUpload.push(imageItem)
				}
			}
		},

		removeImage(idLocal) {
			this.$emit('changed')
			let i = this.toUpload.findIndex(e => e.idLocal === idLocal)
			this.enableUpload()
			this.toUpload.splice(i, 1)

		},

		enableUpload() {
			document.querySelectorAll('.filepond--action-remove-item').forEach((item) => item.click())
		},

		editImage(idLocal, file) {
			if (!this.$refs.cropper) {
				this.editableImageSrc = URL.createObjectURL(file)
			} else {
				this.$refs.cropper.replace(URL.createObjectURL(file))
			}
			this.editableImageId = idLocal
			this.showEditorDialog = true
		},

		cancelEdit() {
			this.showEditorDialog = false
		},

		saveEdit () {
			this.$emit('changed')

			let th = this
			let canvas = th.$refs.cropper.getCroppedCanvas()
			let i = th.toUpload.findIndex(e => e.idLocal === th.editableImageId)
			// Преобразуем в blob для отправки на сервер
			if (typeof canvas.toBlob !== "undefined") {
				canvas.toBlob(function(blob) {
					th.toUpload[i].file = new File([blob], th.toUpload[i].file.name.replace(/^.*[\\\/]/, ''), {type: blob.type, lastModified: Date.now()})
				}, this.outputFileType)
			} else if (typeof canvas.msToBlob !== "undefined") {
				let blob = canvas.msToBlob()
				th.toUpload[i].file = new File([blob], th.toUpload[i].file.name.replace(/^.*[\\\/]/, ''), {type: blob.type, lastModified: Date.now()})
			}
			else {
				th.$store.dispatch('general/setAlert', {
					type: 'error',
					text: th.$tc('blobNotSupported')
				})
				th.showEditorDialog = false
				return false
			}

			th.toUpload[i].preview = canvas.toDataURL()
			th.showEditorDialog = false
		},

		zoom(percent) {
			this.$refs.cropper.relativeZoom(percent);
		},

		move(offsetX, offsetY) {
			this.$refs.cropper.move(offsetX, offsetY);
		},

		rotate(deg) {
			this.$refs.cropper.rotate(deg);
		},

		flipX() {
			this.flipScale > 0 ? this.flipScale = -1 : this.flipScale = 1
			this.$refs.cropper.scaleX(this.flipScale)
		},

		toggleDragImage(val) {
			this.$emit('changed')
			this.dragImage = val
		},

	},

}
</script>

<style lang="scss" scoped>
.image-cards-container{
	display: flex;
	flex-wrap: wrap;
}
.img-card{
	width: calc(50% - 24px);
	position: relative;
	padding-bottom: 30%;
	overflow: hidden;
	transition: opacity .3s;
	background-color: #ddd;
	background-image: url('/back/img/transparent-bg.png');
	background-repeat: repeat;
	cursor: move;
	&--single {
		width: 100%;
	}
	&.ghost {
		opacity: 0.6;
	}
	&:hover .img-card__actions{
		right: 8px;
	}
}
.img-card__img{
	position: absolute;
	top: 0;
	bottom: 0;
	left: 0;
	right: 0;
	background-position: center;
	background-size: contain;
}
.img-card__actions{
	position: absolute;
	display: flex;
	flex-direction: column;
	right: -40px;
	top: 8px;
	height: 100%;
	transition: .3s;
}

.cropper-area {
	width: 100%;
}
.v-card__subtitle{
	word-break: break-all;
}
</style>

<style lang="scss">
.images-pond{
	margin-bottom: 5px;
	z-index: 1;
	min-height: 105px;
	.filepond--panel-center{
		border-radius: .5em !important;
		height: 80px !important;
		transform: none !important;
	}
	.filepond--item, .filepond--panel-bottom, .filepond--panel-top{
		display: none !important;
	}
	.filepond--drop-label{
		height: 76px !important;
		transform: none !important;
		opacity: 1 !important;
		visibility: visible !important;
		&,
		label {
			cursor: pointer;
		}
	}
	.filepond--browser{
		pointer-events: none;
	}
	.filepond--credits {
		display: none;
	}
}
.cropper-bg{
	background-repeat: repeat;
}
</style>
