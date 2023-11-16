<template>
	<v-container fluid>
		<v-row justify="space-between">
			<v-col class="col-12 col-lg-5">				
				<v-list subheader>
					<v-row justify="space-between" align="end" class="mb-0">
						<v-col>

							<h2>{{ $tc('section', 2) }}</h2>

						</v-col>
						
						<v-col cols="auto">

							<v-btn
								color="success"
								:to="{name: 'CatalogSectionCreate'}"
							>
								<v-icon left>
									add
								</v-icon>
								{{ $tc('create') }}
							</v-btn>

						</v-col>
					</v-row>

					<v-row align="center" class="table-filters">
						<v-col cols="auto" class="flex-grow-1">

							<v-text-field
								dense outlined filled
								v-model="sectionsFilter.search"
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
								v-model="sectionsFilter.onlyActive"
								:label="$tc('activeOnly')"
								single-line
								hide-details
								class="v-input--selection-controls--label-left mt-0"
							></v-checkbox>

						</v-col>
					</v-row>
					
					<v-list-item-group
						ref="sectionsCol"
						v-model="sectionsFilter.selected"
						color="primary"
						class="catalog-sections"
						:class="{'catalog-sections--selected': sectionsFilter.selected !== undefined}"
						style="overflow: auto;"
						:style="{'max-height': sectionsHeight}"
						@change="getProducts()"
					>
						<v-list-item
							v-for="section in filteredSections"
							:key="section.id"
							:value="section.id"
							dense
						>
							<v-list-item-icon>
								<v-icon>{{ section.id === sectionsFilter.selected ? 'folder_special' : 'folder' }}</v-icon>
								<v-icon
									v-if="!section.active"
									small
									:title="$tc('notActiveF')"
									class="ml-2 mr-n8"
								>
									visibility_off
								</v-icon>
							</v-list-item-icon>
							<v-list-item-content>
								<v-list-item-title :title="section.name">{{ section.name }}</v-list-item-title>
							</v-list-item-content>
							
							<v-list-item-action class="flex-row my-1">
								
								<v-btn
									icon
									small
									color="primary"
									:title="$tc('edit')"
									:to="{name: 'CatalogSectionEdit', params: {id: section.id}}"
								>
									<v-icon>
										edit
									</v-icon>
								</v-btn>
								
								<v-btn
									icon
									small
									color="error"
									:title="$tc('delete')"
									@click="deleteSection(section)"
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
			
			<v-col class="py-0 d-lg-none"><v-divider horizontal></v-divider></v-col>
			<v-divider vertical class="d-none d-lg-block"></v-divider>
			
			<v-col class="col-12 col-lg">				
				<v-list subheader>
					<v-row justify="space-between" align="end" class="mb-0">
						<v-col>

							<h2>{{ $tc('product', 2) }}</h2>

						</v-col>
						
						<v-col cols="auto">

							<v-btn
								color="success"
								:to="{name: 'CatalogProductCreate'}"
							>
								<v-icon left>
									add
								</v-icon>
								{{ $tc('create') }}
							</v-btn>
							
						</v-col>
					</v-row>

					<v-row align="center" class="table-filters">
						<v-col cols="auto" class="flex-grow-1">

							<v-text-field
								dense outlined filled
								v-model="productsFilter.name"
								:label="$tc('search')"
								prepend-inner-icon="search"
								single-line
								hide-details
								clearable
								class="pt-0 mt-0"
								@change.native="getProducts()"
								@click:clear="getProducts()"
							></v-text-field>

						</v-col>
						<v-col cols="auto">

							<v-checkbox
								v-model="productsFilter.onlyActive"
								:label="$tc('activeOnly')"
								single-line
								hide-details
								class="v-input--selection-controls--label-left mt-0"
								@change="getProducts()"
							></v-checkbox>

						</v-col>
					</v-row>
					
					<v-list-item-group>
						<v-virtual-scroll
							ref="productsCol"
						 	#default="{item: product, index}"
							:items="products"
							item-height="40"
							:height="productsHeight"
						>
							<v-list-item
								:key="product.id"
								dense
								:ripple="{class: 'primary--text'}"
								:to="{name: 'CatalogProductEdit', params: {id: product.id}}"
								@hook:created="scrollProducts(index)"
							>
								<!-- <v-list-item-icon>
									<v-img
										height="24"
										width="24"
										:src="product.imageSrc"
									>
										<template #placeholder>
											<v-icon color="primary">
												fastfood
											</v-icon>
										</template>
									</v-img>
									<v-icon
										v-if="!product.active"
										small
										:title="$tc('notActiveM')"
										class="ml-2 mr-n8"
									>
										visibility_off
									</v-icon>
								</v-list-item-icon> -->
								<v-icon
									small
									:title="$tc('notActiveM')"
									class="mr-2"
									:style="{'visibility': product.active ? 'hidden' : undefined}"
								>
									visibility_off
								</v-icon>
								<v-list-item-content>
									<v-list-item-title :title="product.name" class="primary--text">
										{{ product.name }}
										({{ numberByThousands(product.price) + $store.state.general.settings.paymentCurrencySign }})
										{{ autoFormatUnits({ item: product }) }}
									</v-list-item-title>
								</v-list-item-content>
								
								<v-list-item-action class="flex-row my-1">

									<v-btn
										icon
										small
										color="error"
										:title="$tc('delete')"
										@click.prevent="deleteProduct(product)"
									>
										<v-icon>
											delete
										</v-icon>
									</v-btn>
									
								</v-list-item-action>
							</v-list-item>
						</v-virtual-scroll>
						
					</v-list-item-group>
					
				</v-list>
			</v-col>
		</v-row>
		
		<ConfirmDialog
			:show="sectionDeleteDialog"
			:title="$tc('deletion')"
			agreeIcon="delete"
			@agree="confirmSectionDeleteDialog"
			@disagree="sectionDeleteDialog = false"
		>
			{{ $tc('areYouSure') + ' ' + $tc('delete').toLowerCase() + ' ' +  $tc('sectionEdit') }} <br /> <b>{{ editedSection.name }}</b>?
		</ConfirmDialog>
		
		<ConfirmDialog
			:show="productDeleteDialog"
			:title="$tc('deletion')"
			agreeIcon="delete"
			@agree="confirmProductDeleteDialog"
			@disagree="productDeleteDialog = false"
		>
			{{ $tc('areYouSure') + ' ' + $tc('delete').toLowerCase() + ' ' +  $tc('productEdit') }} <br /> <b>{{ editedProduct.name }}</b>?
		</ConfirmDialog>
		
	</v-container>
</template>

<script>
import ConfirmDialog from '@bo/components/ConfirmDialog'
import {numberByThousands, formatUnits} from '@bo/mixins.js'

export default {
	
	components: {
		ConfirmDialog,

	},

	mixins: [numberByThousands, formatUnits],
	
	data() {
		return {
			sections: [],
			products: [],
			sectionDeleteDialog: false,
			productDeleteDialog: false,
			editedSection: {},
			editedProduct: {},
			productsHeight: '400px',
			sectionsHeight: '400px',

		}
	},
	
	computed: {
		sectionsFilter() {
			return this.$store.state.catalog.sectionsFilter
		},

		productsFilter() {
			return this.$store.state.catalog.productsFilter
		},

		productsPager() {
			return this.$store.state.catalog.productsPager
		},

		filteredSections() {
			return this.sections.filter(v => Boolean(
				(this.sectionsFilter.selected === v.id) || (
					(!this.sectionsFilter.search || v.name.toLowerCase().includes(this.sectionsFilter.search.toLowerCase()))
					&& (!this.sectionsFilter.onlyActive || v.active)
				)
			))
		},
		
	},
	
	methods: {
		getProducts({force = true, add = false} = {}) {
			let then

			setTimeout(() => {
				if (add) {
					this.productsFilter.page++
				} else {
					this.productsFilter.page = 1
					then = ()=> {
						setTimeout(() => {
							this.$refs.productsCol.$el.scrollTo({top: 0})
						})
					}
				}

				this.$store.dispatch('catalog/getProducts', {force, add, then})
			})
		},

		deleteSection(section) {
			this.editedSection = Object.assign({}, section)
			this.sectionDeleteDialog = true
		},
		
		confirmSectionDeleteDialog() {
			this.$store.dispatch('catalog/deleteSection', this.editedSection.id)
			this.sectionDeleteDialog = false
		},
		
		deleteProduct(product) {
			this.editedProduct = Object.assign({}, product)
			this.productDeleteDialog = true
		},
		
		confirmProductDeleteDialog() {
			this.$store.dispatch('catalog/deleteProduct', {id: this.editedProduct.id})
			this.productDeleteDialog = false
		},

		resizeCols() {
			const height = (ref) => {
				let height = document.documentElement.clientHeight - scrollY - 20 - this.$refs[ref].$el.getBoundingClientRect().top
				if (height < 200) height = 200
				return String(height) + 'px'
			}

			this.sectionsHeight = height('sectionsCol')
			this.productsHeight = this.$vuetify.breakpoint.lgAndUp ? height('productsCol') : this.sectionsHeight
		},

		scrollProducts(index) {
			if (!this.productsPager.hasMorePages) return

			if (index === this.products.length - 1) {
				this.getProducts({add: true})
			}
		}
		
	},

	watch: {
		'$store.state.catalog.sections': {
			immediate: true,
			handler(v) {
				this.sections = JSON.parse(JSON.stringify(v))
			},
		},

		'$store.state.catalog.products': {
			handler(v) {
				this.products = JSON.parse(JSON.stringify(v))
			},
		},

	},
	
	created() {
		this.$store.dispatch('catalog/getSections')
		this.getProducts()

	},

	mounted() {
		this.resizeCols()
		addEventListener('resize', this.resizeCols)

	},

	beforeDestroy() {
		removeEventListener('resize', this.resizeCols)
	},
	
}
</script>

<style lang="scss">
@import 'vuetify/src/styles/styles.sass';

.catalog-sections--selected {
	.v-list-item--active {
		position: sticky;
		top: 0;
		bottom: 0;
		background: #fff;
		z-index: 1;
	}
}

@media (max-width: map-get($grid-breakpoints, 'lg') - 1) {
	.catalog-sections--selected {
		.v-list-item:not(.v-list-item--active) {
			display: none;
		}
	}
}

</style>