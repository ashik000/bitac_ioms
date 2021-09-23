<template>

    <div style="margin: 0 auto; background: #1C2B36; min-height: 100vh;">
        <div class="container">
            <div class="row" style="padding-top: 8%; margin: 0px;">
                <div class="col-md-2 col-sm-0"></div>
                <div class="col-md-8 col-sm-12">
                    <div class="row" style="background: #fff; border-radius: 10px; box-shadow: 0 5px 5px 0 rgba(0, 0, 0, 0.25);">
<!--                        <div class="left-image-div">
                            <img src="storage/images/walton-logo.png" alt="inovace logo" height="60" style="margin-top: 20px;">
                        </div>-->
                        <div class="col-md-3"></div>
                        <div class="col-md-6">
                            <div class="form-group" align="center" style="margin: 0px; padding: 0px;">
                                <img src="storage/images/walton-logo.png" alt="inovace logo" height="60" style="margin-top: 20px;">
                            </div>
                            <h2 class="pt-5" style="font-size: 30px; font-family:Roboto, -apple-system, BlinkMacSystemFont, Segoe UI, Oxygen, Ubuntu, Cantarell, Fira Sans, Droid Sans, Helvetica Neue, sans-serif; color: black; text-align: center;">IOMS</h2>
                            <form action="#" @submit.prevent = 'login' style="margin-top: 30px;">
                                <div class="form-group">
                                    <label for="username">Email</label>
                                    <input type="email" class="form-control login input-underlinebox" id="username" placeholder="Enter your email"
                                           v-model="username" autocomplete="on">
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control login input-underlinebox" id="password" placeholder="Enter your password"
                                           v-model="password">
                                </div>
                                <!--<div class="checkbox">-->
                                <!--<label><input type="checkbox"> Remember me</label>-->
                                <!--</div>-->
                                <div class="form-group" align="center" style="margin-top: 30px;">
                                    <button type="submit" class="btn btn-primary" style="padding: 5px 50px; border-radius: 20px;">LOG IN</button>
                                </div>
                            </form>
                            <div class="form-group pt-5" align="center" style="margin: 0px; padding: 0px;">
                                <img src="storage/images/inovace-black-logo.png" alt="Inovace Logo" height="50" style="margin-bottom: 20px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import loginService from '../../services/LoginService';
    import ToastrService from '../../services/ToastrService';
    export default {
        name: "login",
        data: () => ({
            username: '',
            password: ''
        }),
        methods: {
            login() {
                loginService.login({
                    username: this.username,
                    password: this.password
                }, response => {
                    let authProperties = {
                        token: response.access_token,
                        refreshToken: response.refresh_token,
                        tokenExpiration: response.expires_in,
                        role: response.role
                    };
                    this.$store.commit('addAuthProperties', authProperties);
                    // window.localStorage.setItem('access_token', response.access_token);
                    // window.localStorage.setItem('refresh_token', response.refresh_token);
                    // window.localStorage.setItem('token_expiration', response.expires_in);
                    // window.localStorage.setItem('role', response.role);
                    this.$router.push({
                        name: 'lineview'
                    })
                }, error => {
                    let authProperties = {
                        token: null,
                        refreshToken: null,
                        tokenExpiration: null,
                        role: null
                    };
                    this.$store.commit('addAuthProperties', authProperties);
                    ToastrService.showErrorToast('Bad Credentials');
                    // this.$toasted.show('Bad Credentials', {duration: 1000, type: "error"});
                })
            },
        }
    }
</script>

<style scoped>
    .input-underlinebox {
        border:none!important;
        border-bottom: 1px solid #cbcbcb !important;
        border-radius: 0px;
        padding: 6px 0px;
    }
    .form-group > label {
        color: black!important;
    }
    input:focus {
        outline: none;
    }
    .left-image-div {
        width: 60%;
        float: left;
        background: url("") no-repeat top right;
        border-radius: 10px 0px 0px 10px;
        text-align: center;
        min-height: 520px;
    }
    .right-form-div {
        width: 100%;
        padding: 25px 20px 0px;
        text-align: left;
        background: url("") no-repeat bottom;
        min-height: 520px;
    }
    .right-form-div > div > img {
        /*display: none;*/
    }
    .form-control:focus {
        border-color: #66afe9;
        outline: 0;
        -webkit-box-shadow: none;
        box-shadow: none;
    }
    @media screen and (max-width: 760px) {
        .left-image-div {
            width: 30%;
        }
        .right-form-div {
            width: 100%;
        }
    }
    @media screen and (max-width: 560px) {
        .left-image-div {
            width: 0%;
        }
        .left-image-div > img {
            display: none;
        }
        .right-form-div {
            width: 100%;
        }
        .right-form-div > div > img {
            display: block;
        }
    }
</style>
