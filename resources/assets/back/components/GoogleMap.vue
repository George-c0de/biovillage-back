<template>
	<div></div>
</template>

<script>
import { Loader } from '@googlemaps/js-api-loader'

export default {
	props: {
		settings: {
			type: Object,
			required: true,
		},

		options: {
			type: Object,
			required: true,
		},

		markers: {
			type: Array,
			default: ()=> [],
		},

		markerClick: {
			type: Function,
			default: ()=> {},
		},

		popupContent: {
			type: HTMLElement,
			default: ()=> null,
		},

	},

	data() { return {
		loader: null,
		map: null,
		popup: null,
		mapMarkers: [],

	}},

	methods: {
		update(f) {
			// Ожидание отрисовки шаблона
			this.$nextTick(()=> {
				if (window.google?.maps) {
					f()
				} else {
					this.loader.load().then(()=> {
						f()
					})
				}
			})
		},

		newMap(o = this.options) {
			this.map = new google.maps.Map(this.$el, o)
		},

		newPopup(p = this.popupContent) {
			this.popup = new google.maps.InfoWindow({
				content: p,
				// maxWidth: 320,
			})
		},

		newMarkers(markers = this.markers) {
			this.mapMarkers.forEach(m => {
				m.setMap(null)
				m = null
			})
			this.mapMarkers = []

			markers.forEach(m => {
				let marker = new google.maps.Marker({
					map: this.map,
					title: m.id.toString(),
					position: {
						lat: m.lat,
						lng: m.lng,
					},
				})
				marker.addListener('click', ()=> this.markerClick(m.id, this.popup, this.map, marker))

				this.mapMarkers.push(marker)
			})
		},

	},

	watch: {
		settings: {
			immediate: true,
			handler(s) {
				const reinit = Loader.instance != null
				
				if (reinit) {
					document.querySelectorAll('script[src^="https://maps.googleapis.com"]').forEach(script => {
						script.remove()
					})
					if (window.google) delete window.google.maps
					Loader.instance = null
				}

				this.loader = new Loader(s)

				if (reinit) this.update(()=> {
					this.newMap()
					this.newPopup()
					this.newMarkers()
				})
				
			}
		},

		options: {
			immediate: true,
			handler(o) {
				if (this.map) {
					this.map.setOptions(o)
				} else {
					this.update(()=> {
						this.newMap()
					})
				}
			}
		},

		popupContent: {
			immediate: true,
			handler(p) {
				this.update(()=> {
					this.newPopup()
				})
			}
		},

		markers: {
			immediate: true,
			handler(markers) {
				this.update(()=> {
					this.newMarkers()
				})
			}
		},

	},

}
</script>