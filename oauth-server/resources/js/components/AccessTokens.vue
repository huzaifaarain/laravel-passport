<style scoped>
.action-link {
  cursor: pointer;
}
</style>

<template>
  <div>
    <div>
      <div class="card card-default">
        <div class="card-header">
          <div style="display: flex; justify-content: space-between; align-items: center">
            <span> Personal Access Tokens </span>

            <a
              class="action-link"
              tabindex="-1"
              @click="showCreateTokenForm">
              Create New Token
            </a>
          </div>
        </div>

        <div class="card-body">
          <!-- No Tokens Notice -->
          <p
            class="mb-0"
            v-if="data.tokens.length === 0">
            You have not created any personal access tokens.
          </p>

          <!-- Personal Access Tokens -->
          <table
            class="table table-responsive table-borderless mb-0"
            v-if="data.tokens.length > 0">
            <thead>
              <tr>
                <th>Name</th>
                <th></th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="token in data.tokens">
                <!-- Client Name -->
                <td style="vertical-align: middle">
                  {{ token.name }}
                </td>

                <!-- Delete Button -->
                <td style="vertical-align: middle">
                  <a
                    class="action-link text-danger"
                    @click="revoke(token)">
                    Delete
                  </a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Create Token Modal -->
    <div
      class="modal fade"
      id="modal-create-token"
      tabindex="-1"
      role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Create Token</h4>

            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <!-- Form Errors -->
            <div
              class="alert alert-danger"
              v-if="data.form.errors.length > 0">
              <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
              <br />
              <ul>
                <li v-for="error in data.form.errors">
                  {{ error }}
                </li>
              </ul>
            </div>

            <!-- Create Token Form -->
            <form
              role="form"
              @submit.prevent="store">
              <!-- Name -->
              <div class="form-group row">
                <label class="col-md-4 col-form-label">Name</label>

                <div class="col-md-6">
                  <input
                    id="create-token-name"
                    type="text"
                    class="form-control"
                    name="name"
                    v-model="data.form.name" />
                </div>
              </div>

              <!-- Scopes -->
              <div
                class="form-group row"
                v-if="data.scopes.length > 0">
                <label class="col-md-4 col-form-label">Scopes</label>

                <div class="col-md-6">
                  <div v-for="scope in data.scopes">
                    <div class="checkbox">
                      <label>
                        <input
                          type="checkbox"
                          @click="toggleScope(scope.id)"
                          :checked="scopeIsAssigned(scope.id)" />

                        {{ scope.id }}
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>

          <!-- Modal Actions -->
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal">
              Close
            </button>

            <button
              type="button"
              class="btn btn-primary"
              @click="store">
              Create
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Access Token Modal -->
    <div
      class="modal fade"
      id="modal-access-token"
      tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Personal Access Token</h4>

            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <p>
              Here is your new personal access token. This is the only time it will be shown so don't lose it! You may
              now use this token to make API requests.
            </p>

            <textarea
              class="form-control"
              rows="10"
              >{{ data.accessToken }}</textarea
            >
          </div>

          <!-- Modal Actions -->
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive, computed, ref } from "vue";

let data = reactive({
  accessToken: ref(null),
  tokens: ref([]),
  scopes: ref([]),
  form: ref({
    name: "",
    scopes: [],
    errors: [],
  }),
});
let createTokenModal = computed(() => null);
let accessTokenModal = computed(() => null);

onMounted(() => {
  createTokenModal = new bootstrap.Modal("#modal-create-token");
  accessTokenModal = new bootstrap.Modal("#modal-access-token");

  getTokens();
  getScopes();
});

function getTokens() {
  axios.get("/oauth/personal-access-tokens").then((response) => {
    data.tokens = response.data;
  });
}

function getScopes() {
  axios.get("/oauth/scopes").then((response) => {
    data.scopes = response.data;
  });
}

function showCreateTokenForm() {
  createTokenModal.show();
}

function store() {
  data.form.errors = [];

  axios
    .post("/oauth/personal-access-tokens", data.form)
    .then((response) => {
      data.form.name = "";
      data.form.scopes = [];
      data.form.errors = [];

      data.tokens.push(response.data.token);

      showAccessToken(response.data.accessToken);
    })
    .catch((error) => {
      console.log(error);
      if (typeof error.response.data === "object") {
        data.form.errors = _.flatten(_.toArray(error.response.data.errors));
      } else {
        data.form.errors = ["Something went wrong. Please try again."];
      }
    });
}

function toggleScope(scope) {
  if (scopeIsAssigned(scope)) {
    data.form.scopes = _.reject(data.form.scopes, (s) => s == scope);
  } else {
    data.form.scopes.push(scope);
  }
}

function scopeIsAssigned(scope) {
  return _.indexOf(data.form.scopes, scope) >= 0;
}

function showAccessToken(accessTokenArg) {
  createTokenModal.hide();

  data.accessToken = accessTokenArg;

  accessTokenModal.show();
}

function revoke(token) {
  axios.delete("/oauth/personal-access-tokens/" + token.id).then((response) => {
    getTokens();
  });
}
</script>
