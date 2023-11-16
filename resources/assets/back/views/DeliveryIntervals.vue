<template>
	<v-container fluid>
		<h1 class="mb-3"> {{ $tc('titles.deliveryIntervals') }}</h1>
				
		<v-row align="center" class="table-filters">
			<v-col cols="auto">

				<v-select
					dense outlined filled
					v-model="filter.dayOfWeek"
					:items="daysOfWeek"
					:label="$tc('dayOfWeek')"
					prepend-inner-icon="today"
					hide-details
					clearable
					class="v-select--size"
				></v-select>

			</v-col>
			<v-col cols="auto">

				<v-checkbox
					v-model="filter.activeState"
					:label="$tc('activeOnly')"
					hide-details
					class="v-input--selection-controls--label-left"
				></v-checkbox>

			</v-col>
		</v-row>
		
		<v-data-table
			:headers="headers"
			:items="filteredDeliveryIntervals"
			item-key="id"
			:sort-by="['fixedDayOfWeek', 'fixedStartTime']"
			must-sort
			multi-sort
			:search="filter.dayOfWeek ? filter.dayOfWeek.toString() : ''"
			:items-per-page="deliveryIntervals.length"
			:loading="loading"
			hide-default-footer
			fixed-header
			class="v-data-table--editable v-data-table--hide-sort-badge"
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
							<v-select
								v-model="newDeliveryInterval.dayOfWeek"
								:items="daysOfWeek"
								size="15"
								hide-details
								append-outer-icon="clear"
								class="v-select--size v-input--changed v-input--hide-append success--text"
							></v-select>
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
								v-model="newDeliveryInterval.startTime"
								type="time"
								size="5"
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
								v-model="newDeliveryInterval.endTime"
								type="time"
								size="5"
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
							{{ headers[3].text }}
						</div>

						<div :class="{'v-data-table__mobile-row__cell': isMobile}">
							<v-checkbox
								v-model="newDeliveryInterval.active"
								hide-details
								class="v-input--changed success--text mt-0 mb-n1"
							></v-checkbox>
						</div>

					</td>
					<td class="text-end" :class="{'v-data-table__mobile-row': isMobile}">

						<div :class="{
							'v-data-table__mobile-row__header': isMobile,
							'd-none': !isMobile,
						}">
							{{ headers[4].text }}
						</div>

						<div :class="{'v-data-table__mobile-row__cell': isMobile}">
							<v-icon
								color="success"
								:title="$tc('add')"
								:disabled="newDeliveryInterval.dayOfWeek === undefined || newDeliveryInterval.dayOfWeek === null || !newDeliveryInterval.startTime || !newDeliveryInterval.endTime"
								@click="addDeliveryInterval(newDeliveryInterval)"
							>
								add
							</v-icon>
						</div>

					</td>
				</tr>

			</template>
			<template #item.fixedDayOfWeek="{ item }">

				<v-select
					v-model="item.dayOfWeek"
					:items="daysOfWeek"
					size="15"
					hide-details
					append-outer-icon="clear"
					class="v-select--size primary--text"
					:class="[item.dayOfWeek === item.fixedDayOfWeek ? 'v-input--unchanged' : 'v-input--changed']"
					@click:append-outer="item.dayOfWeek = item.fixedDayOfWeek"
				></v-select>

			</template>
			<template #item.fixedStartTime="{ item }">

				<v-text-field
					v-model="item.startTime"
					type="time"
					size="5"
					hide-details
					append-outer-icon="clear"
					class="primary--text"
					:class="[item.startTime === item.fixedStartTime ? 'v-input--unchanged' : 'v-input--changed']"
					@click:append-outer="item.startTime = item.fixedStartTime"
				></v-text-field>

			</template>
			<template #item.fixedEndTime="{ item }">

				<v-text-field
					v-model="item.endTime"
					type="time"
					size="5"
					hide-details
					append-outer-icon="clear"
					class="primary--text"
					:class="[item.endTime === item.fixedEndTime ? 'v-input--unchanged' : 'v-input--changed']"
					@click:append-outer="item.endTime = item.fixedEndTime"
				></v-text-field>

			</template>
			<template #item.fixedActive="{ item }">

				<v-checkbox
					v-model="item.active"
					hide-details
					class="primary--text mt-0 mb-n1"
					:class="[item.active === item.fixedActive ? 'v-input--unchanged' : 'v-input--changed']"
				></v-checkbox>

			</template>
			
			<template #item.actions="{ item }">
				<v-icon
					color="success"
					:title="$tc('save')"
					class="mr-2"
					@click="updateDeliveryInterval(item)"
				>
					save
				</v-icon>
				<v-icon
					color="error"
					:title="$tc('delete')"
					@click="deleteDeliveryInterval(item)"
				>
					delete
				</v-icon>
			</template>
		</v-data-table>
		
		<ConfirmDialog
			:show="dialogDelete"
			:title="$tc('deletion')"
			agreeIcon="delete"
			@agree="confirmDeleteDialog"
			@disagree="dialogDelete = false"
		>
			{{ $tc('areYouSure') + ' ' + $tc('delete').toLowerCase() + ' ' +  $tc('interval').toLowerCase() }} <br />

			<b>{{ editedDeliveryIntervalString }}</b>?
		</ConfirmDialog>
		
	</v-container>
</template>

<script>
import ConfirmDialog from '@bo/components/ConfirmDialog'
import {timeFormats} from '@bo/mixins'

function defaultDeliveryInterval() { return {
	id: -1,
	active: true,
	startTime: '00:00',
	endTime: '23:00',
}}

export default {
	mixins: [timeFormats],
	
	components: {
		ConfirmDialog,
	},
	
	data() { return {
		loading: false,
		dialogDelete: false,
		newDeliveryInterval: defaultDeliveryInterval(),
		editedDeliveryInterval: {},
		deliveryIntervals: [],
		headers: [
			{
				value: 'fixedDayOfWeek',
				text: this.$tc('dayOfWeek'),
			},
			{
				value: 'fixedStartTime',
				text: this.$tc('start'),
				filterable: false,
			},
			{
				value: 'fixedEndTime',
				text: this.$tc('end'),
				sortable: false,
				filterable: false,
			},
			{
				value: 'fixedActive',
				text: this.$tc('activity'),
				sortable: false,
				filterable: false,
			},
			{
				value: 'actions',
				text: '',
				sortable: false,
				filterable: false,
				align: 'end',
			},
		],
		
	}},
	
	methods: {
		deleteDeliveryInterval(di) {
			this.editedDeliveryInterval = di
			this.dialogDelete = true
		},
		
		confirmDeleteDialog() {
			this.$store.dispatch('general/deleteDeliveryInterval', {
				id:  this.editedDeliveryInterval.id, 
				then: () => this.dialogDelete = false
			})
		},

		setDeliveryIntervalTime(di) {
			let startTime = this.timeToInt(di.startTime),
				endTime = this.timeToInt(di.endTime)
				
			di.startHour = startTime.hour
			di.startMinute = startTime.minute
			di.endHour = endTime.hour
			di.endMinute = endTime.minute

			return di
		},

		addDeliveryInterval(di) {
			this.setDeliveryIntervalTime(di)
			this.$store.dispatch('general/addDeliveryInterval', {
				data: di,
				then: () => this.newDeliveryInterval = defaultDeliveryInterval(),
			})
		},
		
		updateDeliveryInterval(di) {
			this.setDeliveryIntervalTime(di)
			this.$store.dispatch('general/updateDeliveryInterval', {data: di})
		},
		
	},
	
	computed: {
		filter() {
			return this.$store.state.general.deliveryIntervalsFilter
		},

		filteredDeliveryIntervals() {
			return this.deliveryIntervals.filter((item)=> Boolean(
				!this.filter.activeState || item.fixedActive
			))
		},

		editedDeliveryIntervalString() {
			return this.deliveryIntervalToString(this.editedDeliveryInterval)
		},
		
	},
	
	watch: {
		'$store.state.general.deliveryIntervals': {
			immediate: true,
			handler(v) {
				let dis = JSON.parse(JSON.stringify(v))
				// для отключения сортировки и фильтра "на лету"
				dis.forEach(item => {
					item.fixedDayOfWeek = item.dayOfWeek
					item.fixedActive = item.active
					
					item.fixedStartTime = item.startTime = this.timeToString(item.startHour) + ':' + this.timeToString(item.startMinute)

					item.fixedEndTime = item.endTime = this.timeToString(item.endHour) + ':' + this.timeToString(item.endMinute)
				})
				this.deliveryIntervals = dis
			}
		},
		
	},
	
	created() {
		this.$store.dispatch('general/getDeliveryIntervals', {
			start: () => this.loading = true,
			finish: () => this.loading = false,
		})
		
	},
	
}
</script>
