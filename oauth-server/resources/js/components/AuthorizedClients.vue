<style scoped>
.action-link {
  cursor: pointer;
}
</style>

<template>
  <div>
    <div>
      <div class="card card-default">
        <div class="card-header">Authorized Applications</div>

        <div class="card-body">
          <p
            class="mb-0"
            v-if="data.tokens.length === 0">
            No applications are authorized yet.
          </p>
          <!-- Authorized Tokens -->
          <table
            class="table table-responsive table-borderless mb-0"
            v-if="data.tokens.length > 0">
            <thead>
              <tr>
                <th>Name</th>
                <th>Scopes</th>
                <th></th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="token in data.tokens">
                <!-- Client Name -->
                <td style="vertical-align: middle">
                  {{ token.client.name }}
                </td>

                <!-- Scopes -->
                <td style="vertical-align: middle">
                  <span v-if="token.scopes.length > 0">
                    {{ token.scopes.join(", ") }}
                  </span>
                </td>

                <!-- Revoke Button -->
                <td style="vertical-align: middle">
                  <a
                    class="action-link text-danger"
                    @click="revoke(token)">
                    Revoke
                  </a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive } from "vue";

let data = reactive({
  tokens: [],
});

onMounted(() => {
  getTokens();
});

function getTokens() {
  axios.get("/oauth/tokens").then((response) => {
    data.tokens = response.data;
  });
}

function revoke(token) {
  axios.delete("/oauth/tokens/" + token.id).then((response) => {
    getTokens();
  });
}
</script>
