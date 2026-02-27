<script setup>
import { computed, reactive, ref } from "vue";

const form = reactive({
  title: "Facture #2026-001",
  client_name: "ACME SARL",
  client_email: "contact@acme.fr",
  tax_rate: 20, // %
  items: [
    { label: "Prestation dev", qty: 1, unit_price: 500 },
    { label: "Maintenance", qty: 1, unit_price: 80 },
  ],
});

const loading = ref(false);
const error = ref(null);
const success = ref(null);

function addItem() {
  form.items.push({ label: "", qty: 1, unit_price: 0 });
}
function removeItem(index) {
  if (form.items.length <= 1) return;
  form.items.splice(index, 1);
}

function money(n) {
  const v = Number(n || 0);
  return new Intl.NumberFormat("fr-FR", { style: "currency", currency: "EUR" }).format(v);
}

const subtotal = computed(() =>
  form.items.reduce((sum, it) => sum + Number(it.qty || 0) * Number(it.unit_price || 0), 0)
);
const taxAmount = computed(() => subtotal.value * (Number(form.tax_rate || 0) / 100));
const total = computed(() => subtotal.value + taxAmount.value);

async function submit() {
  loading.value = true;
  error.value = null;
  success.value = null;

  try {
    const res = await fetch("/invoices", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "",
      },
      body: JSON.stringify(form),
    });

    if (!res.ok) {
      const data = await res.json().catch(() => ({}));
      throw data;
    }

    const data = await res.json().catch(() => ({}));
    success.value = data;
    if (data?.pdf_url) window.open(data.pdf_url, "_blank");
  } catch (e) {
    // Laravel renvoie souvent { message, errors }
    const msg = e?.message || "Une erreur est survenue.";
    const errors = e?.errors ? Object.values(e.errors).flat().join(" ") : null;
    error.value = errors ? `${msg} ${errors}` : msg;
  } finally {
    loading.value = false;
  }
}
</script>

<template>
  <div class="max-w-3xl mx-auto p-6">
    <div class="bg-white rounded-xl shadow border border-gray-100 p-6">
      <div class="mb-6">
        <h2 class="text-2xl font-semibold">Créer une facture</h2>
        <p class="text-gray-500 text-sm">
          <a href="https://thibault-chazottes.fr/" target="_blank" rel="noopener" class="underline hover:text-gray-700">Thibault Chazottes</a>
          –
          <a href="https://formations.thibault-chazottes.fr/" target="_blank" rel="noopener" class="underline hover:text-gray-700">Formateur web</a>
          (<a href="https://www.youtube.com/@formateurwebThibaultChazottes?sub_confirmation=1" target="_blank" rel="noopener" class="underline hover:text-gray-700">Chaine youtube</a>).
        </p>
      </div>

      <div v-if="error" class="mb-4 rounded-lg bg-red-50 text-red-700 px-4 py-3 text-sm">
        {{ error }}
      </div>
      <div v-if="success" class="mb-4 rounded-lg bg-green-50 text-green-700 px-4 py-3 text-sm">
        Facture créée ✅
        <a
          v-if="success.pdf_url"
          :href="success.pdf_url"
          target="_blank"
          rel="noopener"
          class="ml-2 font-medium underline"
        >
          Télécharger le PDF
        </a>
      </div>

      <form class="space-y-6" @submit.prevent="submit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nom de la facture</label>
            <input
              v-model.trim="form.title"
              type="text"
              class="w-full rounded-lg border border-gray-200 px-3 py-2 focus:outline-none focus:ring"
              placeholder="Facture #2026-001"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">TVA (%)</label>
            <input
              v-model.number="form.tax_rate"
              type="number"
              min="0"
              max="100"
              step="0.01"
              class="w-full rounded-lg border border-gray-200 px-3 py-2 focus:outline-none focus:ring"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Client</label>
            <input
              v-model.trim="form.client_name"
              type="text"
              class="w-full rounded-lg border border-gray-200 px-3 py-2 focus:outline-none focus:ring"
              placeholder="ACME SARL"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email client (optionnel)</label>
            <input
              v-model.trim="form.client_email"
              type="email"
              class="w-full rounded-lg border border-gray-200 px-3 py-2 focus:outline-none focus:ring"
              placeholder="contact@acme.fr"
            />
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-lg font-semibold">Lignes</h3>
            <button
              type="button"
              @click="addItem"
              class="rounded-lg border border-gray-200 px-3 py-2 text-sm hover:bg-gray-50"
            >
              + Ajouter une ligne
            </button>
          </div>

          <div class="space-y-3">
            <div
              v-for="(item, i) in form.items"
              :key="i"
              class="grid grid-cols-12 gap-2 bg-gray-50 rounded-lg p-3"
            >
              <div class="col-span-12 md:col-span-6">
                <label class="block text-xs text-gray-600 mb-1">Libellé</label>
                <input
                  v-model.trim="item.label"
                  type="text"
                  class="w-full rounded-lg border border-gray-200 px-3 py-2 focus:outline-none focus:ring bg-white"
                  placeholder="Ex: Développement module facturation"
                />
              </div>

              <div class="col-span-6 md:col-span-2">
                <label class="block text-xs text-gray-600 mb-1">Qté</label>
                <input
                  v-model.number="item.qty"
                  type="number"
                  min="1"
                  step="1"
                  class="w-full rounded-lg border border-gray-200 px-3 py-2 focus:outline-none focus:ring bg-white"
                />
              </div>

              <div class="col-span-6 md:col-span-3">
                <label class="block text-xs text-gray-600 mb-1">PU (€)</label>
                <input
                  v-model.number="item.unit_price"
                  type="number"
                  min="0"
                  step="0.01"
                  class="w-full rounded-lg border border-gray-200 px-3 py-2 focus:outline-none focus:ring bg-white"
                />
              </div>

              <div class="col-span-12 md:col-span-1 flex md:flex-col items-end justify-between">
                <div class="text-sm font-medium text-gray-800">
                  {{ money((item.qty || 0) * (item.unit_price || 0)) }}
                </div>
                <button
                  type="button"
                  @click="removeItem(i)"
                  class="text-xs text-red-600 hover:underline mt-1"
                >
                  Suppr
                </button>
              </div>
            </div>
          </div>

          <div class="mt-4 flex justify-end">
            <div class="w-full md:w-1/2 bg-white rounded-lg border border-gray-100 p-4">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Total HT</span>
                <span class="font-medium">{{ money(subtotal) }}</span>
              </div>
              <div class="flex justify-between text-sm mt-2">
                <span class="text-gray-600">TVA</span>
                <span class="font-medium">{{ money(taxAmount) }}</span>
              </div>
              <div class="flex justify-between text-base mt-3 pt-3 border-t">
                <span class="font-semibold">Total TTC</span>
                <span class="font-semibold">{{ money(total) }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <button
            type="submit"
            :disabled="loading"
            class="rounded-lg bg-black text-white px-4 py-2 text-sm font-medium disabled:opacity-60"
          >
            {{ loading ? "Création..." : "Créer la facture" }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
