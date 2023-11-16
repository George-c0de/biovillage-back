<template>
	<v-container fluid>
		<h1 class="mb-3">{{ $tc('titles.units') }}</h1>

		<v-data-table
			:headers="headers"
			:items="units"
			item-key="id"
			must-sort
			:items-per-page="units.length"
			:loading="loading"
			hide-default-footer
			fixed-header
			class="v-data-table--editable"
		>
		<template #body.prepend="{ isMobile }">

				<tr :class="{'v-data-table__mobile-table-row': isMobile}">
					<td :class="{'v-data-table__mobile-row': isMobile}">

						<div :class="{
							'v-data-table__mobile-row__header': isMobile,
							'd-none': !isMobile,
						}">
							{{ headers[0].text }}
						</div>

						<div :class="{'v-data-table__mobile-row__cell': isMobile}">
							<v-text-field
								v-model="newUnit.fullName"
								:placeholder="$tc('unit')"
								size="40"
								hide-details
								append-outer-icon="clear"
								class="v-input--changed v-input--hide-append success--text"
							></v-text-field>
						</div>

					</td>
					<td :class="{'v-data-table__mobile-row': isMobile}">

						<div :class="{
							'v-data-table__mobile-row__header': isMobile,
							'd-none': !isMobile,
						}">
							{{ headers[1].text }}
						</div>

						<div :class="{'v-data-table__mobile-row__cell': isMobile}">
							<v-text-field
								v-model="newUnit.shortName"
								:placeholder="$tc('unitShort')"
								size="10"
								hide-details
								append-outer-icon="clear"
								class="v-input--changed v-input--hide-append success--text"
							></v-text-field>
						</div>

					</td>
					<td :class="{'v-data-table__mobile-row': isMobile}">

						<div :class="{
							'v-data-table__mobile-row__header': isMobile,
							'd-none': !isMobile,
						}">
							{{ headers[2].text }}
						</div>

						<div :class="{'v-data-table__mobile-row__cell': isMobile}">
							<v-text-field
								v-model="newUnit.shortDerName"
								size="10"
								hide-details
								append-outer-icon="clear"
								class="v-input--changed v-input--hide-append text-right success--text"
							></v-text-field>
						</div>

					</td>
					<td :class="{'v-data-table__mobile-row': isMobile}">
						
						<div :class="{
							'v-data-table__mobile-row__header': isMobile,
							'd-none': !isMobile,
						}">
							{{ headers[3].text }}
						</div>
						<div :class="{'v-data-table__mobile-row__cell': isMobile}">
							<v-text-field
								v-model="newUnit.factor"
								size="8"
								hide-details
								append-outer-icon="clear"
								class="v-input--changed v-input--hide-append text-right success--text"
							></v-text-field>
						</div>

					</td>
					<td :class="{'v-data-table__mobile-row': isMobile}">

						<div :class="{
							'v-data-table__mobile-row__header': isMobile,
							'd-none': !isMobile,
						}">
							{{ headers[4].text }}
						</div>

						<div :class="{'v-data-table__mobile-row__cell': isMobile}">
							<v-text-field
								v-model="newUnit.step"
								size="8"
								hide-details
								append-outer-icon="clear"
								class="v-input--changed v-input--hide-append text-right success--text"
							></v-text-field>
						</div>

					</td>
					<td class="text-end" :class="{'v-data-table__mobile-row': isMobile}">
						<div></div>
						<div :class="{'v-data-table__mobile-row__cell': isMobile}">
							<v-icon
								color="success"
								:title="$tc('add')"
								:disabled="!newUnit.fullName || !newUnit.shortName || !newUnit.shortDerName || !newUnit.factor || !newUnit.step"
								@click="addUnit(newUnit)"
							>
								add
							</v-icon>
						</div>

					</td>
				</tr>

			</template>
			<template #item.fullName="{ item }">

				<v-text-field
					v-model="item.fullName"
					size="40"
					hide-details
					append-outer-icon="clear"
					:class="[item.fullName === item.fullName ? 'v-input--unchanged' : 'v-input--changed primary--text']"
					@click:append-outer="item.fullName = item.fullName"
				></v-text-field>

			</template>
			<template #item.shortName="{ item }">

				<v-text-field
					v-model="item.shortName"
					size="10"
					hide-details
					append-outer-icon="clear"
					:class="[item.shortName === item.shortName ? 'v-input--unchanged' : 'v-input--changed primary--text']"
					@click:append-outer="item.shortName = item.shortName"
				></v-text-field>

			</template>
			<template #item.shortDerName="{ item }">
				
				<v-text-field
					v-model="item.shortDerName"
					size="10"
					hide-details
					append-outer-icon="clear"
					:class="[item.shortDerName === item.shortDerName ? 'v-input--unchanged' : 'v-input--changed primary--text']"
					@click:append-outer="item.shortDerName = item.shortDerName"
				></v-text-field>

			</template>
			<template #item.factor="{ item }">
				<v-text-field
					v-model="item.factor"
					size="8"
					hide-details
					append-outer-icon="clear"
					class="text-right"
					:class="[item.factor === item.factor ? 'v-input--unchanged' : 'v-input--changed primary--text']"
					@click:append-outer="item.factor = item.factor"
				></v-text-field>

			</template>
			<template #item.step="{ item }">
				<v-text-field
					v-model="item.step"
					size="8"
					hide-details
					append-outer-icon="clear"
					class="text-right"
					:class="[item.step === item.step ? 'v-input--unchanged' : 'v-input--changed primary--text']"
					@click:append-outer="item.step = item.step"
				></v-text-field>

			</template>
			
			<template #item.actions="{ item }">

				<div class="d-flex">
					<v-icon
						color="success"
						:title="$tc('save')"
						class="mr-2"
						@click="updateUnit(item)"
					>
						save
					</v-icon>
					<v-icon
						color="error"
						:title="$tc('delete')"
						@click="deleteUnit(item)"
					>
						delete
					</v-icon>
				</div>

			</template>
		</v-data-table>
		
		<ConfirmDialog
			:show="dialogDelete"
			:title="$tc('deletion')"
			agreeIcon="delete"
			@agree="confirmDeleteDialog"
			@disagree="dialogDelete = false"
		>
			{{ $tc('areYouSure') + ' ' + $tc('delete').toLowerCase() + ' ' +  $tc('unitEdit') }} <br /> <b>{{ editedUnit.fullName }}</b>?
		</ConfirmDialog>

	</v-container>
</template>

<script>
import ConfirmDialog from '@bo/components/ConfirmDialog'

function defaultUnit() { return {
	step: '1000',
	factor: '1000',
}}

export default {
	components: {
		ConfirmDialog,
	},
	
	data() { return {
		units: [],
		loading: false,
		dialogDelete: false,
		newUnit: defaultUnit(),
		editedUnit: {},

		headers: [
			{
				value: 'fullName',
				text: this.$tc('unitFullTitle'),
			},
			{
				value: 'shortName',
				text: this.$tc('unitShortTitle'),
			},
			{
				value: 'shortDerName',
				text: this.$tc('unitShortDerTitle'),
			},
			{
				value: 'factor',
				text: this.$tc('unitFactorTitle'),
			},
			{
				value: 'step',
				text: this.$tc('unitBaseStepTitle'),
			},
			{
				value: 'actions',
				text: '',
				sortable: false,
				align: 'end',
			},
		],

	}},

	methods: {
		deleteUnit(unit) {
			this.editedUnit = unit
			this.dialogDelete = true
		},

		confirmDeleteDialog() {
			this.$store.dispatch('general/deleteUnit', {
				id: this.editedUnit.id,
				then: () => this.dialogDelete = false,
			})
		},

		addUnit(unit) {
			this.$store.dispatch('general/addUnit', {
				data: unit,
				then: () => this.newUnit = defaultUnit(),
			})
		},

		updateUnit(unit) {
			this.$store.dispatch('general/updateUnit', {data: unit})
		},

	},

	watch: {
		'$store.state.general.units': {
			immediate: true,
			handler(v) {
				let units = JSON.parse(JSON.stringify(v))
				// для отключения сортировки и фильтра "на лету"
				units.forEach(item => {
					item.fullName = item.fullName
					item.shortName = item.shortName
					item.shortDerName = item.shortDerName
					item.step = item.step
					item.factor = item.factor
				})
				this.units = units
			}
		},

	},

	created() {
		this.$store.dispatch('general/getUnits', {
			start: () => this.loading = true,
			finish: () => this.loading = false,
		})
		
	},

}
</script>
