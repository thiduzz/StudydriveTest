<template>
    <div class="highlighted_holderImage" alt="User Avatar" style="height: 170px; width: 170px; margin:0 auto; display: block;"  v-bind:style="{ 'background-image': 'url('+ url +')' }"></div>
</template>

<script>
    import md5 from 'md5'
    // url du service
    const ENDPOINT = 'http://www.gravatar.com/avatar/'
    export default {
        props: {
            href: {         // user's profile email
                type: String
            },
            default: {      // default picture
                type: String,
                default: 'mm' // Displays a shape of someone
            },
            size: {         // Size of the image
                type: Number,
                default: 170   // Default size : 80 (squarre shape)
            }
        },
        data () {
        return {
            endpoint: ENDPOINT
        }
    },
    computed: {
        url () {
            let hash = md5(this.href.trim())
            let size = '?s=' + this.size
            let defPicture = '&d=' + this.default
            if (this.default === 'gravatar') {
                defPicture = ''
            }
            // Génère une URL --> 'http://www.gravatar.com/avatar/hashToutMoche?s=80&d=mm'
            return this.endpoint + hash + size + defPicture
        }
    }
    }
</script>