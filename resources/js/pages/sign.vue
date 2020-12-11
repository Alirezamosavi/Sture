<template>
  <div id="app" class="bg-secondary">
    <div class="row bg-secondary" style="margin-right: 0px; margin-left: 0px">
      <div class="col-sm-6 bg-secondary">
        <div class="card">
          <div class="card-body" style="background: darkgray">
            <form
              action=""
              class="card-img-top"
              @submit.prevent="sign"
              method="POST"
              autocomplete="off"
            >
              <vueSignature
                ref="signature"
                id="sig"
                class="bg-warning"
                :sigOption="option"
                :disabled="disabled"
                :defaultUrl="dataUrl"
              >
              </vueSignature>
              <!-- <button  class="btn btn-success" >Save</button> -->
              <!-- <button @click="clear">Clear</button> -->
            </form>
            <!-- <button @click="sign">save</button> -->
            <b-button @click="sign" variant="success" class="m-1">
              save
            </b-button>
            <b-button @click="clear" variant="info" class="m-1">Clear</b-button>
          </div>
          <div>
            <b-alert
              :show="dismissCountDown"
              dismissible
              variant="success"
              @dismissed="dismissCountDown = 0"
              @dismiss-count-down="countDownChanged"
            >
              success {{ dismissCountDown }} seconds...
            </b-alert>
          </div>
        </div>
      </div>
      <div class="col-sm-6 bg-secondary">
        <div class="card bg-secondary">
          <div class="card-body">
            <!-- <h5 class="card-title">Special title treatment</h5> -->
            <div v-if="books.length">
              <table class="table table-bordered">
                <div v-for="book in books" :key="book.id">
                  <div v-if="!book.id == 0">
                    <ul>
                      <li>
                        <!-- <img :src="'{{ activity.image }}'"> -->
                        <img
                          :src="
                            'http://localhost:8000/api/store_image/fetch_imag/' +
                            book.id
                          "
                          width="100%"
                          height=""
                          alt="image"
                        />

                        <div class="btn-group" role="group">
                          <!-- <router-link :to="{name: 'edit', params: { id: book.id }}" class="btn btn-primary">Edit
                        </router-link> -->
                          <button
                            class="btn btn-danger"
                            @click="deleteBook(book.id)"
                          >
                            Delete
                          </button>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
              </table>
              
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li :class="{ disabled: !pagination.prev_page_url }"
                    @click.prevent="getPosts(pagination.prev_page_url)" 
                    class="page-item">
                    <a class="page-link" href="#">Назад</a>
                </li>
                <li class="page-item disabled">
                    <a class="page-link" href="#">
                        Страница {{ pagination.current_page }} из {{ pagination.last_page }}
                    </a>
                </li>
                <li :class="{ disabled: !pagination.next_page_url }"
                    @click.prevent="getPosts(pagination.next_page_url)"
                    class="page-item">
                    <a class="page-link" href="#">Следующая</a>
                </li>
            </ul>
        </nav>
            </div>

            <h1 class="bg-warning text-center" v-else>
              No Record Signature left!
            </h1>
          </div>
        </div>
      </div>
    </div>
    <!-- <div class="float-center">

  <div class="card border-primary mb-3">
     <div class="card-body">
    <div class="alert alert-danger" v-if="has_error && !success">
      <p v-if="error == 'login_error'">Validation Errors.</p>
      <p v-else>Error, unable to connect with these credentials.</p>
    </div>
         
    <form action="" @submit.prevent="sign" method="POST" autocomplete="off">
        <vueSignature ref="signature" id='sig'  :sigOption="option" :w="'800px'" :h="'400px'" :disabled="disabled" :defaultUrl="dataUrl">
        </vueSignature>
        <button  class="btn btn-success" >Save</button>
    </form>
            <button @click="clear">Clear</button>
     </div>
  </div>
  </div> -->
  </div>
</template>


<script>
import vueSignature from "vue-signature";
export default {
  components: {
    vueSignature,
  },
  data() {
    return {
      dismissSecs: 5,
      dismissCountDown: 0,
      books: [],
      png: null,
      user_image: null,
      success: false,
      has_error: false,
      error: "",
      option: {
        penColor: "rgb(0, 0, 0)",
        backgroundColor: "#ffed4a",
      },

      disabled: false,
      dataUrl: "",
    };
  },
  // created() {
  //   this.axios.get("http://localhost:8000/api/books").then((response) => {
  //     this.books = response.data;
  //   });
  // },
  mounted() {
    this.getPosts()
  },
  methods: {
    getPosts(page_url) {
            page_url = page_url || 'http://localhost:8000/api/posts'
            
            axios
                .get(page_url)
                .then(response => {
                    this.books = response.data.data
                    this.makePagination(response.data)
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true;
                })
                .finally(() => this.loading = false)
        },
        makePagination(response) {
            let pagination = {
                current_page: response.current_page,
                last_page: response.last_page,
                prev_page_url: response.prev_page_url,
                next_page_url: response.next_page_url
            }

            this.pagination = pagination
        },
    countDownChanged(dismissCountDown) {
      this.dismissCountDown = dismissCountDown;
    },

    sign() {
      var redirect = this.$auth.redirect();
      var _this = this;
      var png = _this.$refs.signature.save();
      let currentObj = this;
      this.$http.post("auth/sign", { user_image: png }).then((result) => {
        _this.has_error = true;

        this.axios.get("http://localhost:8000/api/books").then((response) => {
          this.books = response.data;
          this.dismissCountDown = this.dismissSecs;
        });
      });
    },
    clear() {
      var _this = this;
      _this.$refs.signature.clear();
    },
    deleteBook(id) {
      this.axios
        .post(`http://localhost:8000/api/store_image/delete/${id}`)
        .then((response) => {
          let i = this.books.map((item) => item.id).indexOf(id); // find index of your object
          this.books.splice(i, 1);
        });
    },
  },
};
</script>


