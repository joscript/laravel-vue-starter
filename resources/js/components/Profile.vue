<template>
    <div class="">

        <div class="row">
          <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle" :src="!newp ? getPhoto() : getUpdatedPhoto()" alt="User profile picture">
                </div>

                <h3 class="profile-username text-center">{{form.name}}</h3>

                <p class="text-muted text-center">{{form.type}}</p>

              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">

                  <div class="tab-pane" id="settings">
                    <form class="form-horizontal" @submit.prevent="updateUserInfo">
                      <div class="form-group">
                        <label>Name</label>
                        <input v-model="form.name" type="text" name="name" placeholder="Name"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                        <has-error :form="form" field="name"></has-error>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input v-model="form.email" type="text" name="email" placeholder="Email"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                        <has-error :form="form" field="email"></has-error>
                    </div>
                    <div class="form-group">
                        <label>Bio</label>
                        <textarea v-model="form.bio" type="text" name="bio" placeholder="Bio" cols="30" rows="3"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }">
                        </textarea>
                        <has-error :form="form" field="bio"></has-error>
                    </div>
                    <div class="form-group">
                        <label>type</label>
                        <select v-model="form.type" type="text" name="type" placeholder="type"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                            <option value="admin">admin</option>
                            <option value="user">user</option>
                            <option value="author">author</option>
                        </select>
                        <has-error :form="form" field="type"></has-error>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputFile">profile pict</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="form-input" @change="updateProfilePicture">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input v-model="form.password" type="password" name="password" placeholder="password"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                        <has-error :form="form" field="password"></has-error>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                    </form>
                  </div>

              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>
          <!-- /.col -->
        </div>

    </div>
</template>

<style scoped>
    .widget-user-header {
        background-position: center center;
        background-size: cover;
    }
</style>

<script>
    export default {
        data () {
            return {
                form: new Form({
                        id: '',
                        name: '',
                        email: '',
                        password: '',
                        type: '',
                        bio: '',
                        photo: '',
                    }),
                newp: false
            }
        },
        methods: {
            getPhoto () {
                let photo = this.form.photo.length > 200 ? this.form.photo : `img/profile/${this.form.photo}`
                return photo
            },
            getUpdatedPhoto () {
                // this.$Progress.start()
                // this.form.put('api/get-updated-photo')
                // .then( (data) => {
                //     // console.log(data.data.photo)
                //     this.form.photo = data.data.photo
                //     return `img/profile/${this.form.photo}`
                //     this.$Progress.finish()
                // })
                // .catch( () => {
                //     this.$Progress.finish()
                // })
                this.newp = false
            },
            updateProfilePicture (e) {  // encoding to base 64 string
                let file = e.target.files[0]; //actual file
                    // console.log(file)
                let reader = new FileReader()
                reader.onloadend = (file) => {
                    // console.log('RESULT', reader.result)
                    this.form.photo = reader.result
                    this.newp = true
                    // console.log(this.form.photo)
                }
                reader.readAsDataURL(file)
            },
            updateUserInfo () {
                this.$Progress.start()
                this.form.put('api/update-profile')
                .then( () => {
                    Toast.fire({
                        icon: 'success',
                        title: 'Updated successfully'
                    })
                    this.$Progress.finish()
                })
                .catch( () => {
                    this.$Progress.finish()
                })
            }
        },
        mounted () {
            console.log('Component mounted.')
        },
        created () { //wen the component created
            console.log('Component created.')
            axios.get('api/profile')
            .then( ({ data }) => (this.form.fill(data)) )
        }
    }
</script>
