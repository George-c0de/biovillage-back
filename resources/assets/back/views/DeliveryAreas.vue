<template>
	<v-container fluid>
		<h1 class="mb-3">{{ $tc('deliveryArea', 2) }}</h1>
		<v-row align="center" class="mb-3">
			<v-col cols="12" lg="auto">

				<v-btn
					text
					color="info"
					href="https://www.google.com/maps/d/u/0/?hl=ru"
					target="_blank"
				>
					{{ $tc('mapEditor') }}
				</v-btn>
				
			</v-col>
			<v-col cols="12" lg="auto">

				<v-tooltip bottom>
					<template #activator="{ on, attrs }">
						<v-form ref="kmlForm">
							<label
								v-bind="attrs"
								v-on="on"
								class="d-block"
							>
								<v-btn
									tag="div"
									color="primary"
									class="cursor-pointer"
								>
									<v-icon left>
										upload_file
									</v-icon>
									{{ $tc('uploadKML') }}
								</v-btn>
								<input
									type="file"
									name="kmlFile"
									hidden
									accept="application/vnd.google-earth.kml+xml"
									@input="parseKml"
								>
							</label>
						</v-form>
					</template>

					<div class="text-center" v-html="$tc('uploadKMLHint')"></div>
				</v-tooltip>
				
			</v-col>
		</v-row>
		
		<v-expansion-panels
			multiple
			v-model="panels"
			class="mb-6"
			
		>
			<v-expansion-panel
				v-for="(area, i) in deliveryAreas"
				:key="area.id"
			>
				<v-expansion-panel-header>
					<h3>
						{{ fixedMeta[area.id ? area.id.toString() : i].title }}
					</h3>
					<h4 class="flex-grow-0 ml-2 mr-1">
						<span :title="$tc('price')">
							{{ fixedMeta[area.id ? area.id.toString() : i].price }} {{ $store.state.general.settings.paymentCurrencySign }}
						</span>
						<span> / </span>
						<span :title="$tc('deliveryFreeSum')">
							{{ fixedMeta[area.id ? area.id.toString() : i].deliveryFreeSum }} {{ $store.state.general.settings.paymentCurrencySign }}
						</span>
					</h4>
					
					<v-icon
						v-if="!fixedMeta[area.id ? area.id.toString() : i].active"
						small
						:title="$tc('activeF')"
						class="flex-grow-0 px-2"
					>
						visibility_off
					</v-icon>

				</v-expansion-panel-header>
				<v-expansion-panel-content>
					<v-form @submit.prevent="updateDeliveryArea($event.target, area.id)">
						
						<v-text-field
							dense outlined filled
							v-model="area.name"
							name="name"
							:label="$tc('title')"
						></v-text-field>
								
						<v-row align="center" class="mt-0 mb-3">
							<v-col cols="auto" class="flex-grow-1 py-0">

								<v-checkbox
									v-model="area.active"
									:label="$tc('activeF')"
								></v-checkbox>

								<input type="hidden" name="active" v-model="area.active">

							</v-col>
							<v-col cols="auto" class="flex-grow-1 py-0">

								<v-text-field
									dense outlined filled
									v-model="area.price"
									name="price"
									:label="$tc('price')"
									:suffix="$store.state.general.settings.paymentCurrencySign"
								></v-text-field>

							</v-col>
							<v-col cols="auto" class="flex-grow-1 py-0">

								<v-text-field
									dense outlined filled
									v-model="area.deliveryFreeSum"
									name="deliveryFreeSum"
									:label="$tc('minPrice')"
									:suffix="$store.state.general.settings.paymentCurrencySign"
									:hint="$tc('forFreeDelivery')"
									persistent-hint
								></v-text-field>

							</v-col>
							<v-col cols="auto" class="flex-grow-1 py-0">

								<ColorInput
									dense outlined filled
									v-model="area.color"
									name="color"
									:label="$tc('bgColor')"
								/>

							</v-col>
						</v-row>
								
						<v-btn
							v-if="area.id"
							type="submit"
							color="success"
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
		
		<v-btn
			v-if="deliveryAreas.length"
			color="success"
			class="align-self-start"
			@click="saveDeliveryAreas"
		>
			<v-icon left>
				save
			</v-icon>
			{{ $tc('save') + ' ' + $tc('all') }}
		</v-btn>
		
	</v-container>
</template>

<script>
import ColorInput from '@bo/components/ColorInput'

export default {

	components: {
		ColorInput,
	},
	
	data() { return {
		kmlFile: null,
		deliveryAreas: [],
		panels: [],
		
	}},
	
	methods: {
		parseKml() {
			this.$store.dispatch('general/parseKml', new FormData(this.$refs.kmlForm.$el)).then(()=> {
				this.panels = []
				this.deliveryAreas.forEach((e, i) => {
					this.panels.push(i)
				})
			})
		},
		
		saveDeliveryAreas() {
			let data = new FormData()
			this.deliveryAreas.forEach((area, i) => {
				for (let name in area) data.append(name + 's[' + i.toString() + ']', area[name].toString())
			})
			this.$store.dispatch('general/saveDeliveryAreas', {data, then: ()=> { this.panels = [] },})
			
		},
		
		updateDeliveryArea(form, id) {
			let data = new FormData(form)
			this.$store.dispatch('general/updateDeliveryArea', {data, id})
		},
		
	},
	
	computed: {
		fixedMeta() {
			let meta = {},
				da = this.$store.state.general.deliveryAreas
			da.forEach((area, i) => {
				meta[area.id ? area.id.toString() : i.toString()] = {
					title: area.name,
					active: area.active,
					price: area.price,
					deliveryFreeSum: area.deliveryFreeSum,
				}
			})
			
			return meta
		}
	},
	
	watch: {
		'$store.state.general.deliveryAreas': {
			handler(v) {
				this.deliveryAreas = JSON.parse(JSON.stringify(v))
			},
			immediate: true,
		},
		
	},
	
	created() {
		this.$store.dispatch('general/getDeliveryAreas')
		
	},
	
}
</script>
