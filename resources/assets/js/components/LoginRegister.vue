<template>

    <div class="form-horizontal">
        <div class="panel panel-blue">
            <div class="panel-heading dark-overlay">
                <svg class="glyph stroked clipboard-with-paper"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#stroked-clipboard-with-paper"></use></svg>Login/Register</div>
            <div class="panel-body white-panel">
                <div class="form-group">
                    <label for="email" class="col-md-4 control-label">E-mail</label>

                    <div class="col-md-6">
                        <input id="email" type="email" class="form-control" name="email" v-model="login.email" autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="col-md-4 control-label">Password</label>

                    <div class="col-md-6">
                        <input id="password" type="password" class="form-control" name="password" v-model="login.password" v-on:keyup.enter="attemptLoginRegister($event)">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="remember"  v-model="login.remember"> Remember Me
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <div class="row">

                    <div class="col-md-6 text-center">
                        <a class="btn btn-link" href="/password/reset">
                            Forgot Your Password?
                        </a>

                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-primary btn-md center-block" @click="attemptLoginRegister($event)" v-bind:class="{ disabled: save }" v-bind:disabled="save">
                            <span v-if="save == false">Login</span>
                            <span v-if="save == true"><i class="fa fa-circle-o-notch fa-spin"></i> Wait...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    export default {
        mounted() {
        },

        data() {
            return{
                errors:[],
                save: false,
                login:{
                    email:'',
                    password:'',
                    remember:false
                }
            }
        },
        methods: {
            attemptLoginRegister(event)
            {
                $(event.currentTarget).trigger('blur');
                if(this.save == false)
                {                    
                    var that = this;
                    this.save = true;
                    this.$http.post('/login', this.login)
                            .then(response =>{
                            that.save = false;
                            that.errors = [];
                            window.location.href = response.data.url;
                        })
                        .catch(response=>{
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
                }
            }
        }

    }
</script>
