<template>
	<v-container fluid>
		<v-row justify="space-between">
			<v-col class="col-12 col-md-5">				
				<v-list subheader>
					<h1 class="mb-3">{{ $tc('gift', 2) }}</h1>
					
					<v-row align="center" class="table-filters">
						<v-col class="flex-grow-1">

							<v-text-field
								dense outlined filled
								v-model="filter.search"
								:label="$tc('search')"
								prepend-inner-icon="search"
								single-line
								hide-details
								clearable
								class="pt-0 mt-0"
							></v-text-field>

						</v-col>
						<v-col cols="auto">

							<v-checkbox
								v-model="filter.activeState"
								:label="$tc('activeOnly')"
								single-line
								hide-details
								class="v-input--selection-controls--label-left mt-0"
							></v-checkbox>

						</v-col>
					</v-row>

					<v-list-item-group
						v-model="filter.selected"
						mandatory
						color="primary"
					>
						<v-list-item
							:value="newGift.id"
							dense
						>
							<v-list-item-icon>
								<v-icon>add</v-icon>
							</v-list-item-icon>
							<v-list-item-content>
								<v-list-item-title :title="newGiftTitle">{{ newGiftTitle }}</v-list-item-title>
							</v-list-item-content>
						</v-list-item>

						<v-list-item
							v-for="gift in filteredGifts"
							:key="gift.id"
							:value="gift.id"
							dense
						>
							<v-list-item-icon>
								<v-icon>card_giftcard</v-icon>
								<v-icon
									v-if="!gift.active"
									small
									:title="$tc('notActiveF')"
									class="ml-2 mr-n8"
								>
									visibility_off
								</v-icon>
							</v-list-item-icon>
							<v-list-item-content>
								<v-list-item-title :title="gift.fixedTitle">{{ gift.fixedTitle }}</v-list-item-title>
							</v-list-item-content>
							
							<v-list-item-action class="flex-row my-1">
								
								<v-btn
									icon
									small
									color="error"
									:title="$tc('delete')"
									@click="deleteDialog = true"
								>
									<v-icon>
										delete
									</v-icon>
								</v-btn>
								
							</v-list-item-action>
							
						</v-list-item>
					</v-list-item-group>
					
				</v-list>
			</v-col>
			
			<v-col class="py-0 d-md-none"><v-divider horizontal></v-divider></v-col>
			<v-divider vertical class="d-none d-md-block"></v-divider>
			
			<v-col v-if="editedGift" class="col-12 col-md">
				<h2 class="mb-3">{{ editedGift.fixedTitle || newGiftTitle}}</h2>			
				<v-form @submit.prevent="saveGift($event.target)">
					<v-row>
				
						<v-col cols="12" lg="" order-lg="1">
							<ImageInput
								name="image"
								:label="$tc('cover')"
								:src="editedGift.imageSrc"
								:file.sync="editedGift.image"
							></ImageInput>
						</v-col>
						
						<v-col cols="12" lg="8" class="d-flex flex-column flex-children-grow-0">
									
							<v-text-field
								dense outlined filled
								v-model="editedGift.name"
								name="name"
								:label="$tc('title')"
							></v-text-field>

							<v-textarea
								outlined filled
								v-model="editedGift.description"
								name="description"
								:label="$tc('description')"
								rows="1"
							></v-textarea>

							<v-text-field
								dense outlined filled
								v-model="editedGift.price"
								name="price"
								:label="$tc('price')"
							></v-text-field>
							
							<v-row align="center" class="mb-3">
								<v-col cols="12" lg order="last" order-lg="first">
									
									<v-checkbox
										v-model="editedGift.active"
										name="active"
										:label="$tc('activeM')"
										hide-details
										class="mt-0"
									></v-checkbox>
									
								</v-col>
								<v-col cols="12" lg>
									
									<v-text-field
										dense outlined filled
										v-model="editedGift.order"
										name="order"
										:label="$tc('sortOrder')"
										hide-details
									></v-text-field>
									
								</v-col>
							</v-row>

							<v-row class="align-self-start">
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
								
								<v-col cols="auto" v-if="editedGift.id !== -1">
									<v-btn
										color="error"
										@click="deleteDialog = true"
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
			</v-col>
		</v-row>
		
		<ConfirmDialog
			:show="deleteDialog"
			:title="$tc('deletion')"
			agreeIcon="delete"
			@agree="confirmDeleteDialog"
			@disagree="deleteDialog = false"
		>
			{{ $tc('areYouSure') + ' ' + $tc('delete').toLowerCase() + ' ' + $tc('gift').toLowerCase() }} <br /> <b>{{ editedGift.name }}</b>?
		</ConfirmDialog>
		
	</v-container>
</template>

<script>
import ImageInput from '@bo/components/ImageInput'
import ConfirmDialog from '@bo/components/ConfirmDialog'

export default {

	props: ['giftId'],
	
	components: {
		ImageInput,
		ConfirmDialog,
	},
	
	data() {
		return {
			deleteDialog: false,
			defaultGift: {
				id: -1,
				order: 100,
				active: true,
			},
			newGift: {},
			gifts: [],
			selectedByProp: false,
		}
	},
	
	methods: {
		confirmDeleteDialog() {
			this.$store.dispatch('catalog/deleteGift', this.filter.selected)
			this.deleteDialog = false
		},

		saveGift(form) {
			let data = new FormData(form)
			data.set('active', form.active.checked ? '1' : '0')
			if (this.filter.selected === -1) {
				this.$store.dispatch('catalog/addGift', {data, then: ()=> {
					this.newGift = JSON.parse(JSON.stringify(this.defaultGift))
				},})
			} else {
				this.$store.dispatch('catalog/updateGift', {data, id: this.filter.selected})
			}
		},

		selectGiftFromProp() {
			if (this.selectedByProp || !this.giftId || !this.gifts.length) return
			let i = this.gifts.findIndex(gift => gift.id == this.giftId)
			if (i < 0) return
			this.filter.selected = this.giftId
			this.selectedByProp = true
		},
		
	},
	
	computed: {
		filter() {
			return this.$store.state.catalog.giftsFilter
		},

		filteredGifts() {
			return this.gifts.filter(v => Boolean(
				(!this.filter.search || v.name.toLowerCase().includes(this.filter.search.toLowerCase()))
				&& (!this.filter.activeState || v.active)
			))
		},

		editedGift() {
			return this.gifts.find(e => e.id === this.filter.selected) || this.newGift
		},

		newGiftTitle() {
			return this.$tc('new') + ' ' + this.$tc('gift').toLowerCase()
		},
		
	},
	
	watch: {
		'$store.state.catalog.gifts': {
			handler() {
				let gifts = JSON.parse(JSON.stringify(this.$store.state.catalog.gifts)).sort((a, b) => a.order - b.order)
				gifts.forEach((e) => {
					e.fixedTitle = e.name
				})
				this.gifts = gifts
				this.selectGiftFromProp()
			},
			immediate: true,
		},

	},
	
	created() {
		this.selectGiftFromProp()
		this.$store.dispatch('catalog/getGifts')
		this.newGift = JSON.parse(JSON.stringify(this.defaultGift))
	},
	
}
</script>
