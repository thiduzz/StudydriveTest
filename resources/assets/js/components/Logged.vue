<template>

    <div class="row">

        <div class="col-sm-12 col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-circle" v-bind:class="{activated: user.active == 1, disabled: user.active == 0 }"></i>	&nbsp;	&nbsp;<span v-if="user.name == '' || user.name == null">{{ user.email }}</span><span v-if="user.name != '' && user.name != null">{{ user.name }}</span></div>
                <div class="panel-body">
                    <p>
                    <strong class="primary-font">User #{{ user.id }}</strong>
                    </p>
                    <p v-if="user.name != '' && user.name != null">
                        <strong class="primary-font">Name:</strong><br>
                        {{ user.name }}
                    </p>
                    <p>
                        <strong class="primary-font">E-mail:</strong><br>
                        {{ user.email }}
                    </p>
                    <p>

                        <strong class="primary-font">Register Date:</strong><br>
                        {{ user.created_at  }}
                    </p>
                    <p v-if="user.active == 0">
                        In case you want to send the activation email again, <a href="/resend_activation">click here</a>.
                    </p>
                    <p>
                        To <b>logout</b>, click on the e-mail address on the top-right of the screen.
                    </p>

                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-6">
            <div class="panel panel-default" v-if="user.active == true">
                <div class="panel-heading">Edit Profile</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-4 col-lg-offset-4">
                            <a href="javascript:;"  class="thumbnail highlighted_holder_sm" style="margin-bottom: 10px;" @click="avatarUpload">
                                <div v-if="edit_profile.avatar_name != ''" class="highlighted_holderImage" alt="User Avatar" style="height: 170px; width: 170px; margin:0 auto; display: block;"  v-bind:style="{ 'background-image': 'url(' + edit_profile.avatar + ')' }"></div>
                                <gravatar v-if="edit_profile.avatar_name == '' && user.avatar_url == ''" :href="user.email" default="mm" />
                                <div v-if="edit_profile.avatar_name == '' && user.avatar_url != ''" class="highlighted_holderImage" alt="User Avatar" style="height: 170px; width: 170px; margin:0 auto; display: block;"  v-bind:style="{ 'background-image': 'url('+ user.avatar_url +')' }"></div>
                            </a>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom: 30px;">
                        <div class="col-lg-12" style="text-align: center;">
                            <input  type="file" id="fileinput" ref="fileinput" style="display:none;" @change="avatarChanged" accept="image/png, image/x-png, image/gif, image/jpeg" />
                            <a href="javascript:;" @click="avatarUpload">Select your profile picture</a><br>
                            <span v-if="edit_profile.avatar_name != ''">Selected file: {{ edit_profile.avatar_name }}</span><br>
                            <span style="font-size:10px;">By default, your gravatar will be used. If you don't have a gravatar and want to know more <a href="https://pt.gravatar.com/">click here</a>.</span>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-12">

                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="type" id="name" name="name" class="form-control" v-model="edit_profile.name" v-on:keyup.enter="updateUser">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-primary btn-md center-block" @click="updateUser" v-bind:class="{ disabled: save }" v-bind:disabled="save">
                                <span v-if="save == false">Update</span>
                                <span v-if="save == true"><i class="fa fa-circle-o-notch fa-spin"></i> Wait...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-orange"  v-if="user.active == false">
                <div class="panel-heading dark-overlay">Edit Profile</div>
                <div class="panel-body">
                    <p>You need to activate your account so you can update your profile.</p>
                </div>
            </div>
        </div>
    </div>

</template>
<style>
    .remove-link{
        color:#f9243f;
    }
    .remove-link:hover, .remove-link:active{
        color: #a94442;
    }

</style>
<script>
    export default {
        mounted() {
            this.user.created_at = moment(this.user.created_at).format("DD/MM/YYYY H:mm");
            this.edit_profile.name = this.user.name;
        },

        data() {
            return{
                errors:[],
                save: false,
                user: Studydrive.user,
                edit_profile:{
                    name:'',
                    avatar:'',
                    avatar_name:''
                },
            }
        },
        methods: {
            updateUser() {
                var that = this;
                this.save = true;
                var data = new FormData();
                if(this.$refs.fileinput.files != null) {
                    if (this.$refs.fileinput.files.length > 0) {
                        if (this.$refs.fileinput.files[0].type == 'image/png' || this.$refs.fileinput.files[0].type == 'image/x-png' || this.$refs.fileinput.files[0].type == 'image/gif' || this.$refs.fileinput.files[0].type == 'image/jpeg') {
                            var files = this.$refs.fileinput.files;
                            data.append('avatar', files[0]);
                        } else {
                            swal({
                                type: "error",
                                title: "Error!",
                                text: 'Invalid avatar format, please try again with a different file.',
                                html: true
                            });
                        }
                    }
                }
                data.append('profile_name', this.edit_profile.name);
                this.$http.post('/profile/update/'+this.user.id, data, {
                            contentType: false,
                            processData: false
                        })
                        .then(response => {
                    that.save = false;
                    that.errors = [];
                    swal("Profile updated!", "Yeih, your profile was successfully updated!", "success")
                    this.user.name = this.edit_profile.name;
                })
                .catch(response => {
                    that.save = false;
                    if (typeof response.data === 'object') {
                        that.errors = _.flatten(_.toArray(response.data));
                        var html_message = '';
                        $(that.errors).each(function( index, error ) {
                            html_message += '<li>'+error+'</li>';
                        });
                        swal({    type: "error", title: "Error!",   text: '<ul class="alert-list">'+html_message+'</ul>',   html: true });
                    } else {
                        swal({  type: "error", title: "Error!",   text: 'Something went wrong. Please try again.',   html: true });
                    }
                });
            },
            avatarUpload() {
                this.$refs.fileinput.click();
            },
            avatarChanged() {
                if(this.$refs.fileinput.files != null)
                {
                    if(this.$refs.fileinput.files.length > 0)
                    {
                        if(this.$refs.fileinput.files[0].type == 'image/png' || this.$refs.fileinput.files[0].type == 'image/x-png' ||  this.$refs.fileinput.files[0].type == 'image/gif' ||  this.$refs.fileinput.files[0].type == 'image/jpeg')
                        {
                            this.edit_profile.avatar_name = this.$refs.fileinput.files[0].name;
                            var reader = new FileReader();
                            var that = this;
                            reader.onload = function (e) {
                                that.edit_profile.avatar = e.target.result;
                            }
                            reader.readAsDataURL(this.$refs.fileinput.files[0]);
                        }else{
                            this.edit_profile.avatar_name = '';
                            this.edit_profile.avatar = '';
                            this.errors = ['Invalid avatar format, please try again with a different file.'];

                        }
                    }else{
                        this.edit_profile.avatar_name = '';
                        this.edit_profile.avatar = '';
                    }
                }else{
                    this.edit_profile.avatar_name = '';
                    this.edit_profile.avatar = '';
                }

            },
        }

    }
</script>
