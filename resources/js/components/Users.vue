<template>
    <div class="mx-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-12">
                        <div class="card" v-if="$gate.isAdminOrAuthor()">
                            <div class="card-header">
                                <h3 class="card-title">Users Table</h3>

                                <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 150px;">
                                    <button class="btn btn-primary" @click="newModal">Add new <i class="fa fa-user-plus"></i> </button>
                                </div>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Type</th>
                                        <th>Registered at</th>
                                        <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="user in users.data" :key="user.id">
                                            <td>{{user.id}}</td>
                                            <td>{{user.name}}</td>
                                            <td>{{user.email}}</td>
                                            <td><span class="tag tag-success">{{user.type | capitalize}}</span></td>
                                            <td>{{user.created_at | dateFormat}}</td>
                                            <td>
                                                <button class="btn btn-white shadow" @click="editModal(user)">
                                                    <i class="fa fa-edit text-info"></i>
                                                </button>
                                                <button class="btn btn-white shadow" @click="deleteUser(user.id)">
                                                    <i class="fa fa-trash text-danger"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <pagination :data="users" @pagination-change-page="getResults"></pagination>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>

            </div>
        </div>

        <page-not-found v-if="!$gate.isAdminOrAuthor()" />

        <!-- Modal -->
        <div class="modal fade" id="user_modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="user_modalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="user_modalLabel" v-show="!edit_mode">Add new user</h5>
                    <h5 class="modal-title" id="user_modalLabel" v-show="edit_mode">Update user info</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="edit_mode ? updateUser() : createUser()">
                <div class="modal-body">
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
                        <label>Password</label>
                        <input v-model="form.password" type="password" name="password" placeholder="password"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                        <has-error :form="form" field="password"></has-error>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" :class="{'btn btn-success' : edit_mode}" class="btn btn-primary">{{modal_button}}</button>
                </div>
                </form>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        data () {
            return {
                users: {},
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: ''
                }),
                edit_mode: false,
                modal_button: "",
            }
        },
        methods: {
            loadUsers () {
                // console.log(this.$gate.isAdminOrAuthor())
                if(this.$gate.isAdminOrAuthor()){
                    axios.get('api/user').then( ({data}) => (this.users = data) ) // arrow function with one arguement and return object syntax
                }
            },
            getResults(page = 1) {
                axios.get('api/user?page=' + page)
                .then(response => {
                    this.users = response.data;
                });
            },
            newModal () {
                this.edit_mode = false
                this.modal_button = "Add"
                this.form.reset()
                this.form.clear()
                $('#user_modal').modal('show')
            },
            editModal (user) {
                this.edit_mode = true
                this.modal_button = "Update"
                this.form.reset()
                this.form.clear()
                $('#user_modal').modal('show')
                this.form.fill(user)
            },
            createUser () {
                this.$Progress.start()

                this.form.post('api/user')
                .then( () => {
                    VueGlobal.$emit('loadTable') // creating event using $emit method
                    $('#user_modal').modal('hide')
                    Toast.fire({
                        icon: 'success',
                        title: 'Created successfully'
                    })

                this.$Progress.finish()
                })
                .catch( () => {
                    this.$Progress.finish()
                })

            },
            updateUser () {
                this.$Progress.start()

                this.form.put(`api/user/${this.form.id}`)
                .then( () => {
                    VueGlobal.$emit('loadTable') // creating event using $emit method
                    $('#user_modal').modal('hide')
                    Toast.fire({
                        icon: 'success',
                        title: 'Updated successfully'
                    })
                    this.$Progress.finish()
                })
                .catch( () => {
                    this.$Progress.finish()
                })
            },
            deleteUser (id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if(result.value) {
                        this.form.delete(`api/user/${id}`)
                        .then( () => {
                            VueGlobal.$emit('loadTable')
                            Swal.fire(
                                'Deleted!',
                                'Your file has been deleted.',
                                'success'
                            )
                        } )
                        .catch ( (result) => {
                            console.log(result.message)
                            Swal.fire(
                                'Failed!',
                                result.message,
                                'warning'
                                )
                        })
                    }
                })
            }
        },
        created () {
            VueGlobal.$on('searching', () => { // listening...
                let query = this.$parent.search // refering to the search data in root component
                axios.get(`api/find-user?q=${query}`)
                .then( (result) => {
                    this.users = result.data
                } )
                .catch( () => {

                } )
            })
            this.loadUsers()
            VueGlobal.$on('loadTable', () => {
                this.loadUsers()
            })
            //  setInterval( () => this.loadUsers(),  3000 )
        }
    }
</script>
