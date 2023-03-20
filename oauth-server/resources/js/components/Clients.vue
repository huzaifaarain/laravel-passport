<style scoped>
.action-link {
  cursor: pointer;
}
</style>

<template>
  <div>
    <div class="card card-default">
      <div class="card-header">
        <div style="display: flex; justify-content: space-between; align-items: center">
          <span> OAuth Clients </span>

          <a
            class="action-link"
            tabindex="-1"
            @click="showCreateClientForm">
            Create New Client
          </a>
        </div>
      </div>

      <div class="card-body">
        <!-- Current Clients -->
        <p
          class="mb-0"
          v-if="data.clients.length === 0">
          You have not created any OAuth clients.
        </p>

        <table
          class="table table-responsive table-borderless mb-0"
          v-if="data.clients.length > 0">
          <thead>
            <tr>
              <th>Client ID</th>
              <th>Name</th>
              <th>Redirect</th>
              <th></th>
              <th></th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="client in data.clients">
              <!-- ID -->
              <td style="vertical-align: middle">
                {{ client.id }}
              </td>

              <!-- Name -->
              <td style="vertical-align: middle">
                {{ client.name }}
              </td>

              <!-- Redirect -->
              <td style="vertical-align: middle">
                <code>{{ client.redirect }}</code>
              </td>

              <!-- Edit Button -->
              <td style="vertical-align: middle">
                <a
                  class="action-link"
                  tabindex="-1"
                  @click="edit(client)">
                  Edit
                </a>
              </td>

              <!-- Delete Button -->
              <td style="vertical-align: middle">
                <a
                  class="action-link text-danger"
                  @click="destroy(client)">
                  Delete
                </a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <!-- Edit Client Modal -->
    <div
      class="modal"
      id="modal-create-client"
      tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Create Client</h5>
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
              v-if="data.createForm.errors.length > 0">
              <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
              <br />
              <ul>
                <li v-for="error in data.createForm.errors">
                  {{ error }}
                </li>
              </ul>
            </div>

            <!-- Create Client Form -->
            <form role="form">
              <!-- Name -->
              <div class="form-group row">
                <label class="col-md-3 col-form-label">Name</label>

                <div class="col-md-9">
                  <input
                    id="create-client-name"
                    type="text"
                    class="form-control"
                    @keyup.enter="store"
                    v-model="data.createForm.name" />

                  <span class="form-text text-muted"> Something your users will recognize and trust. </span>
                </div>
              </div>

              <!-- Redirect URL -->
              <div class="form-group row">
                <label class="col-md-3 col-form-label">Redirect URL</label>

                <div class="col-md-9">
                  <input
                    type="text"
                    class="form-control"
                    name="redirect"
                    @keyup.enter="store"
                    v-model="data.createForm.redirect" />

                  <span class="form-text text-muted"> Your application's authorization callback URL. </span>
                </div>
              </div>

              <!-- Confidential -->
              <div class="form-group row">
                <label class="col-md-3 col-form-label">Confidential</label>

                <div class="col-md-9">
                  <div class="checkbox">
                    <label>
                      <input
                        type="checkbox"
                        v-model="data.createForm.confidential" />
                    </label>
                  </div>

                  <span class="form-text text-muted">
                    Require the client to authenticate with a secret. Confidential clients can hold credentials in a
                    secure way without exposing them to unauthorized parties. Public applications, such as native
                    desktop or JavaScript SPA applications, are unable to hold secrets securely.
                  </span>
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

    <!-- Edit Client Modal -->
    <div
      class="modal"
      id="modal-edit-client"
      tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Edit Client</h4>

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
              v-if="data.editForm.errors.length > 0">
              <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
              <br />
              <ul>
                <li v-for="error in data.editForm.errors">
                  {{ error }}
                </li>
              </ul>
            </div>

            <!-- Edit Client Form -->
            <form role="form">
              <!-- Name -->
              <div class="form-group row">
                <label class="col-md-3 col-form-label">Name</label>

                <div class="col-md-9">
                  <input
                    id="edit-client-name"
                    type="text"
                    class="form-control"
                    @keyup.enter="update"
                    v-model="data.editForm.name" />

                  <span class="form-text text-muted"> Something your users will recognize and trust. </span>
                </div>
              </div>

              <!-- Redirect URL -->
              <div class="form-group row">
                <label class="col-md-3 col-form-label">Redirect URL</label>

                <div class="col-md-9">
                  <input
                    type="text"
                    class="form-control"
                    name="redirect"
                    @keyup.enter="update"
                    v-model="data.editForm.redirect" />

                  <span class="form-text text-muted"> Your application's authorization callback URL. </span>
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
              @click="update">
              Save Changes
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Client Secret Modal -->
    <div
      class="modal fade"
      id="modal-client-secret"
      tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Client Secret</h4>

            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <p>
              Here is your new client secret. This is the only time it will be shown so don't lose it! You may now use
              this secret to make API requests.
            </p>

            <input
              type="text"
              class="form-control"
              v-model="data.clientSecret" />
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
import { onMounted, reactive, ref, computed } from "vue";

let data = reactive({
  clients: [],
  clientSecret: null,
  createForm: {
    errors: [],
    name: null,
    redirect: null,
    confidential: true,
  },
  editForm: {
    errors: [],
    name: null,
    redirect: null,
  },
});

let createModal = null;
let editModal = null;
let clientSecretModal = null;

onMounted(() => {
  createModal = new bootstrap.Modal("#modal-create-client");
  editModal = new bootstrap.Modal("#modal-edit-client");
  clientSecretModal = new bootstrap.Modal("#modal-client-secret");

  getClients();

  $("#modal-create-client").on("shown.bs.modal", () => {
    $("#create-client-name").focus();
  });

  $("#modal-edit-client").on("shown.bs.modal", () => {
    $("#edit-client-name").focus();
  });
});

function getClients() {
  axios.get("/oauth/clients").then((response) => {
    data.clients = response.data;
  });
}

/**
 * Show the form for creating new clients.
 */
function showCreateClientForm() {
  createModal.show();
  //   $("#modal-create-client").show();
}

/**
 * Create a new OAuth client for the user.
 */
function store() {
  persistClient("post", "/oauth/clients", data.createForm, createModal);
}

/**
 * Edit the given client.
 */
function edit(client) {
  data.editForm = {
    id: client.id,
    name: client.name,
    redirect: client.redirect,
    errors: [],
  };

  editModal.show();
}

/**
 * Update the client being edited.
 */
function update() {
  persistClient("put", "/oauth/clients/" + data.editForm.id, data.editForm, editModal);
}

/**
 * Persist the client to storage using the given form.
 */
function persistClient(method, uri, form, modal) {
  form.errors = [];

  axios[method](uri, form)
    .then((response) => {
      getClients();

      form.name = "";
      form.redirect = "";
      form.errors = [];

      modal.hide();

      if (response.data.plainSecret) {
        showClientSecret(response.data.plainSecret);
      }
    })
    .catch((error) => {
      if (typeof error.response.data === "object") {
        form.errors = _.flatten(_.toArray(error.response.data.errors));
      } else {
        form.errors = ["Something went wrong. Please try again."];
      }
    });
}

/**
 * Show the given client secret to the user.
 */
function showClientSecret(clientSecretArg) {
  data.clientSecret = clientSecretArg;

  clientSecretModal.show();
}

/**
 * Destroy the given client.
 */
function destroy(client) {
  axios.delete("/oauth/clients/" + client.id).then((response) => {
    getClients();
  });
}
</script>
